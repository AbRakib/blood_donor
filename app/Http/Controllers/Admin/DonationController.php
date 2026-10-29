<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BloodDonation;
use App\Models\BloodDonor;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DonationController extends Controller
{
    public function index(Request $request): JsonResponse|View
    {
        if (! $request->expectsJson()) {
            $common_data = (object) [
                'page_title' => __('Donation history'),
                'title' => __('Donation history'),
            ];

            return view('admin.donations')->with(compact('common_data'));
        }

        $filters = $request->validate(['blood_donor_id' => ['nullable', 'integer'], 'donation_status' => ['nullable', 'in:pending,verified']]);
        $query = BloodDonation::with('bloodDonor.bloodGroup');
        foreach ($filters as $key => $value) {
            if ($value !== null) {
                $query->where($key, $value);
            }
        }

        return $this->collectionResponse($query->orderByDesc('donated_at')->orderByDesc('id')->paginate(20), $this->formatDonation(...));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'blood_donor_id' => ['required', 'integer', Rule::exists('blood_donors', 'id')->where('status', 1)->where('deleted', 0)->whereNull('deleted_at')],
            'blood_request_id' => ['nullable', 'integer', Rule::exists('blood_requests', 'id')->where('status', 1)->where('deleted', 0)->whereNull('deleted_at')],
            'donated_at' => ['required', 'date_format:Y-m-d', 'before_or_equal:today'],
            'bags' => ['required', 'integer', 'in:1'],
            'hospital_name' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);

        return $this->modelResponse(BloodDonation::record($data)->load('bloodDonor'), $this->formatDonation(...));
    }

    public function show(BloodDonation $donation): JsonResponse
    {
        return $this->modelResponse($donation->load('bloodDonor.bloodGroup'), $this->formatDonation(...));
    }

    public function verify(BloodDonation $donation): JsonResponse
    {
        return $this->modelResponse($donation->verify()->load('bloodDonor.bloodGroup'), $this->formatDonation(...));
    }

    public function history(BloodDonor $bloodDonor): JsonResponse
    {
        return $this->collectionResponse($bloodDonor->donations()->active()->verified()->with('bloodDonor.bloodGroup')->orderByDesc('donated_at')->orderByDesc('id')->paginate(20), $this->formatDonation(...));
    }
}
