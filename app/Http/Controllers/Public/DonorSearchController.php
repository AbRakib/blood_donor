<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\SearchDonorRequest;
use App\Http\Requests\StoreDonorContactRequest;
use App\Http\Resources\PublicDonorResource;
use App\Models\BloodDonor;
use App\Models\DonorContactRequest;
use App\Services\DonorSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class DonorSearchController extends Controller
{
    public function index(SearchDonorRequest $request, DonorSearchService $search): AnonymousResourceCollection
    {
        return PublicDonorResource::collection($search->search($request->validated()));
    }

    public function contact(StoreDonorContactRequest $request): JsonResponse
    {
        DB::transaction(function () use ($request): void {
            $donor = BloodDonor::query()->active()->where('is_available', true)->where('consent_to_contact', true)->lockForUpdate()->findOrFail($request->integer('blood_donor_id'));
            DonorContactRequest::create(array_merge($request->validated(), ['blood_donor_id' => $donor->id]));
        });

        return response()->json(['message' => 'Your contact request has been submitted for admin review.'], 201);
    }
}
