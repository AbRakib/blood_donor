<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDonationRequest;
use App\Http\Resources\DonationResource;
use App\Models\BloodDonation;
use App\Models\BloodDonor;
use App\Services\DonationService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DonationController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = $request->validate(['blood_donor_id' => ['nullable', 'integer'], 'donation_status' => ['nullable', 'in:pending,verified']]);
        $query = BloodDonation::with('bloodDonor.bloodGroup');
        foreach ($filters as $key => $value) {
            if ($value !== null) {
                $query->where($key, $value);
            }
        }

        return DonationResource::collection($query->orderByDesc('donated_at')->orderByDesc('id')->paginate(20));
    }

    public function store(StoreDonationRequest $request, DonationService $donations): DonationResource
    {
        return new DonationResource($donations->record($request->validated())->load('bloodDonor'));
    }

    public function verify(BloodDonation $donation, DonationService $service): DonationResource
    {
        return new DonationResource($service->verify($donation)->load('bloodDonor.bloodGroup'));
    }

    public function history(BloodDonor $bloodDonor): AnonymousResourceCollection
    {
        return DonationResource::collection($bloodDonor->donations()->active()->where('donation_status', 'verified')->with('bloodDonor.bloodGroup')->orderByDesc('donated_at')->orderByDesc('id')->paginate(20));
    }
}
