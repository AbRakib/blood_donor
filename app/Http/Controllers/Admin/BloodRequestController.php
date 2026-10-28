<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BloodDonor;
use App\Models\BloodRequest;
use App\Models\BloodRequestStatusLog;
use App\Models\RequestDonor;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

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
        return $this->modelResponse($this->createRequest($this->validateBloodRequest($request))->load('bloodGroup'), $this->formatBloodRequest(...));
    }

    public function show(BloodRequest $bloodRequest): JsonResponse
    {
        return $this->modelResponse($bloodRequest->load(['bloodGroup', 'statusLogs', 'requestDonors.bloodDonor.bloodGroup', 'donations']), $this->formatBloodRequest(...));
    }

    public function update(Request $request, BloodRequest $bloodRequest): JsonResponse
    {
        return $this->modelResponse($this->updateRequest($bloodRequest, $this->validateBloodRequest($request, $bloodRequest))->load('bloodGroup'), $this->formatBloodRequest(...));
    }

    public function match(BloodRequest $bloodRequest): JsonResponse
    {
        return $this->collectionResponse($this->matchDonors($bloodRequest), $this->formatRequestDonor(...));
    }

    public function verify(BloodRequest $bloodRequest): JsonResponse
    {
        return $this->modelResponse($this->updateRequestStatus($bloodRequest, 'verified')->load('bloodGroup'), $this->formatBloodRequest(...));
    }

    public function changeStatus(Request $request, BloodRequest $bloodRequest): JsonResponse
    {
        $data = $request->validate(['request_status' => ['required', Rule::in(['verified', 'in_progress', 'fulfilled', 'cancelled', 'expired'])]]);

        return $this->modelResponse($this->updateRequestStatus($bloodRequest, $data['request_status'])->load('bloodGroup'), $this->formatBloodRequest(...));
    }

    public function assignDonor(Request $request, BloodRequest $bloodRequest): JsonResponse
    {
        $data = $request->validate(['blood_donor_id' => ['required', 'integer', 'exists:blood_donors,id']]);

        return $this->modelResponse($this->assignMatchingDonor($bloodRequest, BloodDonor::findOrFail($data['blood_donor_id']))->load('bloodDonor.bloodGroup'), $this->formatRequestDonor(...));
    }

    public function respond(Request $request, BloodRequest $bloodRequest, int $requestDonor): JsonResponse
    {
        $data = $request->validate(['response_status' => ['required', Rule::in(['pending', 'contacted', 'accepted', 'declined'])], 'notes' => ['nullable', 'string', 'max:2000']]);

        return DB::transaction(function () use ($bloodRequest, $requestDonor, $data): JsonResponse {
            $bloodRequest = BloodRequest::query()->lockForUpdate()->findOrFail($bloodRequest->id);
            abort_unless(in_array($bloodRequest->request_status, ['verified', 'in_progress'], true) && ! $bloodRequest->expires_at?->isPast(), 422, 'The request is closed.');
            $match = $bloodRequest->requestDonors()->whereKey($requestDonor)->lockForUpdate()->firstOrFail();
            abort_if($match->response_status === 'donated', 422, 'Completed donations cannot be changed.');
            $match->response_status = $data['response_status'];
            $data += $match->only(['notes']);
            $match->notes = $data['notes'];
            $match->responded_at = now();
            $match->contacted_at = $match->contacted_at ?? now();
            $match->save();

            return response()->json(['data' => $match]);
        });
    }

    /** @param array<string, mixed> $data */
    private function createRequest(array $data): BloodRequest
    {
        return DB::transaction(function () use ($data): BloodRequest {
            $record = new BloodRequest;
            $record->blood_group_id = $data['blood_group_id'];
            $record->district_id = $data['district_id'] ?? null;
            $record->upazila_id = $data['upazila_id'] ?? null;
            $record->union_id = $data['union_id'] ?? null;
            $record->patient_name = $data['patient_name'];
            $record->contact_name = $data['contact_name'];
            $record->contact_phone = $data['contact_phone'];
            $record->hospital_name = $data['hospital_name'];
            $record->hospital_address = $data['hospital_address'] ?? null;
            $record->bags_needed = $data['bags_needed'];
            $record->needed_at = $data['needed_at'];
            $record->urgency = $data['urgency'] ?? 'normal';
            $record->expires_at = $data['expires_at'] ?? null;
            $record->notes = $data['notes'] ?? null;
            $record->status = $data['status'] ?? 1;
            $record->request_status = 'pending';
            $record->save();
            $record->refresh();
            $statusLog = new BloodRequestStatusLog;
            $statusLog->blood_request_id = $record->id;
            $statusLog->to_status = 'pending';
            $statusLog->save();

            return $record;
        });
    }

    /** @param array<string, mixed> $data */
    private function updateRequest(BloodRequest $bloodRequest, array $data): BloodRequest
    {
        return DB::transaction(function () use ($bloodRequest, $data): BloodRequest {
            $record = BloodRequest::query()->active()->lockForUpdate()->findOrFail($bloodRequest->id);
            if (in_array($record->request_status, ['fulfilled', 'cancelled', 'expired'], true)) {
                throw ValidationException::withMessages(['request_status' => 'Closed requests cannot be edited.']);
            }
            $data += $record->only(['blood_group_id', 'district_id', 'upazila_id', 'union_id', 'patient_name', 'contact_name', 'contact_phone', 'hospital_name', 'hospital_address', 'bags_needed', 'needed_at', 'urgency', 'expires_at', 'notes', 'status']);
            if ($record->requestDonors()->exists() || $record->donations()->exists()) {
                foreach (['blood_group_id', 'bags_needed', 'district_id', 'upazila_id', 'union_id'] as $field) {
                    if ($data[$field] != $record->{$field}) {
                        throw ValidationException::withMessages([$field => 'Assigned request requirements cannot be changed.']);
                    }
                }
            }
            $next = $data['request_status'] ?? $record->request_status;
            unset($data['request_status']);
            $record->blood_group_id = $data['blood_group_id'];
            $record->district_id = $data['district_id'];
            $record->upazila_id = $data['upazila_id'];
            $record->union_id = $data['union_id'];
            $record->patient_name = $data['patient_name'];
            $record->contact_name = $data['contact_name'];
            $record->contact_phone = $data['contact_phone'];
            $record->hospital_name = $data['hospital_name'];
            $record->hospital_address = $data['hospital_address'];
            $record->bags_needed = $data['bags_needed'];
            $record->needed_at = $data['needed_at'];
            $record->urgency = $data['urgency'];
            $record->expires_at = $data['expires_at'];
            $record->notes = $data['notes'];
            $record->status = $data['status'];
            $record->save();

            return $this->transitionRequest($record, $next);
        });
    }

    /** @return Collection<int, RequestDonor> */
    private function matchDonors(BloodRequest $bloodRequest): Collection
    {
        return DB::transaction(function () use ($bloodRequest): Collection {
            $bloodRequest = BloodRequest::query()->active()->lockForUpdate()->findOrFail($bloodRequest->id);
            if (! in_array($bloodRequest->request_status, ['verified', 'in_progress'], true) || $bloodRequest->expires_at?->isPast()) {
                throw ValidationException::withMessages(['blood_request' => 'Only verified, unexpired requests can be matched.']);
            }
            $query = $this->availableDonors($this->donorQuery($bloodRequest->only(['blood_group_id', 'district_id', 'upazila_id', 'union_id'])));
            $query->orderBy('id')->chunkById(100, function (Collection $donors) use ($bloodRequest): void {
                foreach ($donors as $donor) {
                    $match = RequestDonor::query()->where('blood_request_id', $bloodRequest->id)->where('blood_donor_id', $donor->id)->first();
                    if ($match === null) {
                        $match = new RequestDonor;
                        $match->blood_request_id = $bloodRequest->id;
                        $match->blood_donor_id = $donor->id;
                        $match->save();
                    }
                }
            });
            if ($bloodRequest->requestDonors()->exists() && $bloodRequest->request_status === 'verified') {
                $this->updateRequestStatus($bloodRequest, 'in_progress');
            }

            return $bloodRequest->requestDonors()->with('bloodDonor.bloodGroup')->orderBy('id')->get();
        });
    }

    private function assignMatchingDonor(BloodRequest $bloodRequest, BloodDonor $donor): RequestDonor
    {
        return DB::transaction(function () use ($bloodRequest, $donor): RequestDonor {
            $record = BloodRequest::query()->active()->lockForUpdate()->findOrFail($bloodRequest->id);
            if (! in_array($record->request_status, ['verified', 'in_progress'], true) || $record->expires_at?->isPast()) {
                throw ValidationException::withMessages(['blood_request_id' => 'Verify the request before assigning donors.']);
            }
            $query = $this->availableDonors($this->donorQuery($record->only(['blood_group_id', 'district_id', 'upazila_id', 'union_id'])));
            if (! $query->whereKey($donor->id)->lockForUpdate()->first()) {
                throw ValidationException::withMessages(['blood_donor_id' => 'The donor must be active, available, consenting and match the request location and blood group.']);
            }
            $match = RequestDonor::query()->where('blood_request_id', $record->id)->where('blood_donor_id', $donor->id)->first();
            if ($match === null) {
                $match = new RequestDonor;
                $match->blood_request_id = $record->id;
                $match->blood_donor_id = $donor->id;
                $match->save();
            }
            if ($record->request_status === 'verified') {
                $this->updateRequestStatus($record, 'in_progress');
            }

            return $match;
        });
    }
}
