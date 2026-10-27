<?php

namespace App\Services;

use App\Models\BloodDonor;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class DonorSearchService
{
    /** @param array<string, mixed> $filters */
    public function query(array $filters = []): Builder
    {
        $query = BloodDonor::query()->active()->whereHas('bloodGroup', fn (Builder $group): Builder => $group->active());
        foreach (['blood_group_id', 'district_id', 'upazila_id', 'union_id'] as $field) {
            if (! empty($filters[$field])) {
                $query->where($field, $filters[$field]);
            }
        }
        if (! empty($filters['area'])) {
            $query->where('area', $filters['area']);
        }
        if (! empty($filters['q'])) {
            $query->where('name', 'like', '%'.$filters['q'].'%');
        }

        return $query;
    }

    public function available(Builder $query): Builder
    {
        return $query->where('is_available', true)->where('consent_to_contact', true);
    }

    /** @param array<string, mixed> $filters */
    public function search(array $filters): LengthAwarePaginator
    {
        return $this->available($this->query($filters))->with(['bloodGroup', 'district', 'upazila', 'union'])->orderBy('id')->paginate(12);
    }
}
