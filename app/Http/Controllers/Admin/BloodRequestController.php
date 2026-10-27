<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBloodRequestRequest;
use App\Http\Requests\UpdateBloodRequestRequest;
use App\Http\Resources\BloodRequestResource;
use App\Http\Resources\RequestDonorResource;
use App\Models\BloodDonor;
use App\Models\BloodRequest;
use App\Services\BloodRequestService;
use App\Services\DonorMatchingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class BloodRequestController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', BloodRequest::class);

        return BloodRequestResource::collection(BloodRequest::with('bloodGroup')->orderByDesc('id')->paginate(20));
    }

    public function store(StoreBloodRequestRequest $request, BloodRequestService $service): BloodRequestResource
    {
        Gate::authorize('create', BloodRequest::class);

        return new BloodRequestResource($service->create($request->validated())->load('bloodGroup'));
    }

    public function show(BloodRequest $bloodRequest): BloodRequestResource
    {
        Gate::authorize('view', $bloodRequest);

        return new BloodRequestResource($bloodRequest->load(['bloodGroup', 'statusLogs', 'requestDonors.bloodDonor.bloodGroup', 'donations']));
    }

    public function update(UpdateBloodRequestRequest $request, BloodRequest $bloodRequest, BloodRequestService $service): BloodRequestResource
    {
        return new BloodRequestResource($service->update($bloodRequest, $request->validated())->load('bloodGroup'));
    }

    public function match(BloodRequest $bloodRequest, DonorMatchingService $matching): AnonymousResourceCollection
    {
        Gate::authorize('update', $bloodRequest);

        return RequestDonorResource::collection($matching->match($bloodRequest));
    }

    public function verify(BloodRequest $bloodRequest, BloodRequestService $service): BloodRequestResource
    {
        Gate::authorize('update', $bloodRequest);

        return new BloodRequestResource($service->verify($bloodRequest)->load('bloodGroup'));
    }

    public function changeStatus(Request $request, BloodRequest $bloodRequest, BloodRequestService $service): BloodRequestResource
    {
        Gate::authorize('update', $bloodRequest);
        $data = $request->validate(['request_status' => ['required', Rule::in(['verified', 'in_progress', 'fulfilled', 'cancelled', 'expired'])]]);

        return new BloodRequestResource($service->changeStatus($bloodRequest, $data['request_status'])->load('bloodGroup'));
    }

    public function assignDonor(Request $request, BloodRequest $bloodRequest, DonorMatchingService $matching): RequestDonorResource
    {
        Gate::authorize('update', $bloodRequest);
        $data = $request->validate(['blood_donor_id' => ['required', 'integer', 'exists:blood_donors,id']]);

        return new RequestDonorResource($matching->assign($bloodRequest, BloodDonor::findOrFail($data['blood_donor_id']))->load('bloodDonor.bloodGroup'));
    }

    public function respond(Request $request, BloodRequest $bloodRequest, int $requestDonor): JsonResponse
    {
        Gate::authorize('update', $bloodRequest);
        $data = $request->validate(['response_status' => ['required', Rule::in(['pending', 'contacted', 'accepted', 'declined'])], 'notes' => ['nullable', 'string', 'max:2000']]);

        return DB::transaction(function () use ($bloodRequest, $requestDonor, $data): JsonResponse {
            $bloodRequest = BloodRequest::query()->lockForUpdate()->findOrFail($bloodRequest->id);
            abort_unless(in_array($bloodRequest->request_status, ['verified', 'in_progress'], true) && ! $bloodRequest->expires_at?->isPast(), 422, 'The request is closed.');
            $match = $bloodRequest->requestDonors()->whereKey($requestDonor)->lockForUpdate()->firstOrFail();
            abort_if($match->response_status === 'donated', 422, 'Completed donations cannot be changed.');
            $match->update(array_merge($data, ['responded_at' => now(), 'contacted_at' => $match->contacted_at ?? now()]));

            return response()->json(['data' => $match]);
        });
    }
}
