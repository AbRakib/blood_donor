<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBloodDonorRequest;
use App\Http\Requests\UpdateBloodDonorRequest;
use App\Http\Resources\BloodDonorResource;
use App\Models\BloodDonor;
use App\Services\DonorService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class BloodDonorController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection|View
    {
        Gate::authorize('viewAny', BloodDonor::class);

        if (! $request->expectsJson()) {
            $common_data = (object) [
                'page_title' => __('Blood donors'),
                'title' => __('Blood donors'),
            ];

            return view('admin.donors')->with(compact('common_data'));
        }

        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'], 'area' => ['nullable', 'string', 'max:255'],
            'blood_group_id' => ['nullable', 'integer'], 'availability' => ['nullable', 'in:available,busy'],
        ]);
        $query = BloodDonor::with('bloodGroup');
        foreach (['area', 'blood_group_id'] as $field) {
            if (! empty($filters[$field])) {
                $query->where($field, $filters[$field]);
            }
        }
        if (! empty($filters['q'])) {
            $query->where(function ($query) use ($filters): void {
                $query->where('name', 'like', '%'.$filters['q'].'%')->orWhere('phone', 'like', '%'.$filters['q'].'%')->orWhere('area', 'like', '%'.$filters['q'].'%');
            });
        }
        if (! empty($filters['availability'])) {
            $query->where('is_available', $filters['availability'] === 'available');
        }

        return BloodDonorResource::collection($query->orderByDesc('id')->paginate(20));
    }

    public function store(StoreBloodDonorRequest $request, DonorService $donors): BloodDonorResource
    {
        Gate::authorize('create', BloodDonor::class);

        return new BloodDonorResource($donors->create($request->validated())->load('bloodGroup'));
    }

    public function show(BloodDonor $bloodDonor): BloodDonorResource
    {
        Gate::authorize('view', $bloodDonor);

        return new BloodDonorResource($bloodDonor->load('bloodGroup'));
    }

    public function update(UpdateBloodDonorRequest $request, BloodDonor $bloodDonor, DonorService $donors): BloodDonorResource
    {
        return new BloodDonorResource($donors->update($bloodDonor, $request->validated())->load('bloodGroup'));
    }

    public function destroy(BloodDonor $bloodDonor): Response
    {
        Gate::authorize('delete', $bloodDonor);
        DB::transaction(fn () => $bloodDonor->delete());

        return response()->noContent();
    }

    public function changeAvailability(Request $request, BloodDonor $bloodDonor, DonorService $donors): BloodDonorResource
    {
        Gate::authorize('update', $bloodDonor);
        $data = $request->validate(['is_available' => ['required', 'boolean']]);

        return new BloodDonorResource($donors->update($bloodDonor, $data)->load('bloodGroup'));
    }
}
