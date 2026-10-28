<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\BloodDonorResource;
use App\Models\DonorContactRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        return response()->json(DonorContactRequest::with('bloodDonor.bloodGroup')->orderByDesc('id')->paginate(20)->through(fn (DonorContactRequest $contact): array => array_merge($contact->toArray(), ['blood_donor' => new BloodDonorResource($contact->bloodDonor)])));
    }

    public function update(Request $request, DonorContactRequest $contactRequest): JsonResponse
    {
        $data = $request->validate(['contact_status' => ['required', Rule::in(['approved', 'rejected', 'completed'])]]);

        return DB::transaction(function () use ($contactRequest, $data): JsonResponse {
            $contactRequest = DonorContactRequest::query()->lockForUpdate()->findOrFail($contactRequest->id);
            $allowed = ['pending' => ['approved', 'rejected'], 'approved' => ['completed', 'rejected'], 'rejected' => [], 'completed' => []];
            abort_unless(in_array($data['contact_status'], $allowed[$contactRequest->contact_status], true), 422, 'This contact status transition is not allowed.');
            if ($data['contact_status'] === 'approved') {
                $donor = $contactRequest->bloodDonor()->active()->where('is_available', true)->where('consent_to_contact', true)->first();
                abort_unless($donor, 422, 'The donor is no longer available for contact.');
            }
            $contactRequest->update(array_merge($data, ['handled_at' => now()]));

            return response()->json(['data' => $contactRequest]);
        });
    }
}
