<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonorContactRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DonorContactRequestController extends Controller
{
    public function index(Request $request): JsonResponse|View
    {
        if (! $request->expectsJson()) {
            $common_data = (object) [
                'page_title' => __('Contact requests'),
                'title' => __('Contact requests'),
            ];

            return view('admin.contacts')->with(compact('common_data'));
        }

        return $this->collectionResponse(DonorContactRequest::with('bloodDonor.bloodGroup')->orderByDesc('id')->paginate(20), $this->formatDonorContactRequest(...));
    }

    public function show(DonorContactRequest $contactRequest): JsonResponse
    {
        return $this->modelResponse($contactRequest->load('bloodDonor.bloodGroup'), $this->formatDonorContactRequest(...));
    }

    public function update(Request $request, DonorContactRequest $contactRequest): JsonResponse
    {
        $data = $request->validate(['contact_status' => ['required', Rule::in(['approved', 'rejected', 'completed'])]]);

        return response()->json(['data' => $contactRequest->changeStatus($data['contact_status'])]);
    }
}
