<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BloodDonor;
use App\Models\BloodRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BloodRequestController extends Controller
{
    public function index(Request $request): JsonResponse|View
    {
        if (! $request->expectsJson()) {
            $common_data = (object) [
                'page_title' => __('Blood requests'),
                'title' => __('Blood requests'),
            ];

            return view('admin.requests')->with(compact('common_data'));
        }

        return $this->collectionResponse(BloodRequest::with('bloodGroup')->orderByDesc('id')->paginate(20), $this->formatBloodRequest(...));
    }

    public function store(Request $request): JsonResponse
    {
        return $this->modelResponse(BloodRequest::submit($this->validateBloodRequest($request))->load('bloodGroup'), $this->formatBloodRequest(...));
    }

    public function show(BloodRequest $bloodRequest): JsonResponse
    {
        return $this->modelResponse($bloodRequest->load(['bloodGroup', 'statusLogs', 'requestDonors.bloodDonor.bloodGroup', 'donations']), $this->formatBloodRequest(...));
    }

    public function update(Request $request, BloodRequest $bloodRequest): JsonResponse
    {
        return $this->modelResponse($bloodRequest->updateDetails($this->validateBloodRequest($request, $bloodRequest))->load('bloodGroup'), $this->formatBloodRequest(...));
    }

    public function match(BloodRequest $bloodRequest): JsonResponse
    {
        return $this->collectionResponse($bloodRequest->matchDonors(), $this->formatRequestDonor(...));
    }

    public function verify(BloodRequest $bloodRequest): JsonResponse
    {
        return $this->modelResponse($bloodRequest->changeStatus('verified')->load('bloodGroup'), $this->formatBloodRequest(...));
    }

    public function changeStatus(Request $request, BloodRequest $bloodRequest): JsonResponse
    {
        $data = $request->validate(['request_status' => ['required', Rule::in(['verified', 'in_progress', 'fulfilled', 'cancelled', 'expired'])]]);

        return $this->modelResponse($bloodRequest->changeStatus($data['request_status'])->load('bloodGroup'), $this->formatBloodRequest(...));
    }

    public function assignDonor(Request $request, BloodRequest $bloodRequest): JsonResponse
    {
        $data = $request->validate(['blood_donor_id' => ['required', 'integer', 'exists:blood_donors,id']]);

        return $this->modelResponse($bloodRequest->assignDonor(BloodDonor::findOrFail($data['blood_donor_id']))->load('bloodDonor.bloodGroup'), $this->formatRequestDonor(...));
    }

    public function respond(Request $request, BloodRequest $bloodRequest, int $requestDonor): JsonResponse
    {
        $data = $request->validate(['response_status' => ['required', Rule::in(['pending', 'contacted', 'accepted', 'declined'])], 'notes' => ['nullable', 'string', 'max:2000']]);

        return response()->json(['data' => $bloodRequest->respondToDonor($requestDonor, $data)]);
    }
}
