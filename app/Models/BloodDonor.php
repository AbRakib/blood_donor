<?php

namespace App\Models;

use Database\Factories\BloodDonorFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;

#[Fillable(['name', 'blood_group_id', 'phone', 'area', 'address', 'is_available', 'district_id', 'upazila_id', 'union_id', 'date_of_birth', 'gender', 'status'])]
#[Hidden(['phone'])]
class BloodDonor extends DomainModel
{
    /** @use HasFactory<BloodDonorFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return array_merge(parent::casts(), ['total_donations' => 'integer', 'last_donation_date' => 'date', 'date_of_birth' => 'date', 'is_available' => 'boolean']);
    }

    public function bloodGroup(): BelongsTo
    {
        return $this->belongsTo(BloodGroup::class, 'blood_group_id');
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function upazila(): BelongsTo
    {
        return $this->belongsTo(Upazila::class, 'upazila_id');
    }

    public function union(): BelongsTo
    {
        return $this->belongsTo(Union::class, 'union_id');
    }

    public function donations(): HasMany
    {
        return $this->hasMany(BloodDonation::class, 'blood_donor_id');
    }

    public function requestDonors(): HasMany
    {
        return $this->hasMany(RequestDonor::class, 'blood_donor_id');
    }

    public function availabilityLogs(): HasMany
    {
        return $this->hasMany(DonorAvailabilityLog::class, 'blood_donor_id');
    }

    public function contactRequests(): HasMany
    {
        return $this->hasMany(DonorContactRequest::class, 'blood_donor_id');
    }

    /** @param array<string, mixed> $filters */
    #[Scope]
    protected function matchingFilters(Builder $query, array $filters = []): Builder
    {
        $query->active()->whereHas('bloodGroup', fn (Builder $group): Builder => $group->active());
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

    #[Scope]
    protected function availableForContact(Builder $query): Builder
    {
        return $query->where('is_available', true)
            ->where(function (Builder $query): void {
                $query->whereNull('last_donation_date')
                    ->orWhereDate('last_donation_date', '<=', today()->subMonthsNoOverflow(3));
            });
    }

    /** @param array<string, mixed> $data */
    public function updateProfile(array $data): BloodDonor
    {
        return $this->getConnection()->transaction(function () use ($data): BloodDonor {
            $donor = BloodDonor::query()->lockForUpdate()->findOrFail($this->getKey());
            if ($donor->last_donation_date?->gt(today()->subMonthsNoOverflow(3))) {
                $eligibleDate = $donor->last_donation_date->copy()->addMonthsNoOverflow(3);
                $remainingDays = (int) ceil(today()->diffInDays($eligibleDate));
                foreach (['status', 'is_available'] as $field) {
                    if (! $donor->{$field} && ! empty($data[$field])) {
                        throw ValidationException::withMessages([
                            $field => __('This donor cannot be activated or marked available until three months after their last donation.').' '.__('Last donation: :last_date. Eligible from: :eligible_date. Remaining waiting time: :days days.', [
                                'last_date' => $donor->last_donation_date->format('d M Y'),
                                'eligible_date' => $eligibleDate->format('d M Y'),
                                'days' => $remainingDays,
                            ]),
                        ]);
                    }
                }
            }
            $wasAvailable = $donor->is_available;
            $donor->fill($data)->save();

            $donor->logAvailabilityChange($wasAvailable);

            return $donor;
        });
    }

    public function logAvailabilityChange(bool $wasAvailable, ?string $reason = null): void
    {
        if ($wasAvailable !== $this->is_available) {
            $this->availabilityLogs()->create([
                'was_available' => $wasAvailable,
                'is_available' => $this->is_available,
                'reason' => $reason,
            ]);
        }
    }

    /** @param array<string, mixed> $data */
    public static function register(array $data): BloodDonor
    {
        $donor = new BloodDonor;
        $donor->name = $data['name'];
        $donor->phone = $data['phone'];
        $donor->blood_group_id = $data['blood_group_id'];
        $donor->district_id = $data['district_id'] ?? null;
        $donor->upazila_id = $data['upazila_id'] ?? null;
        $donor->union_id = $data['union_id'] ?? null;
        $donor->area = $data['area'] ?? null;
        $donor->address = $data['address'] ?? null;
        $donor->date_of_birth = $data['date_of_birth'] ?? null;
        $donor->gender = $data['gender'] ?? null;
        $donor->is_available = $data['is_available'] ?? true;
        $donor->status = $data['status'] ?? 1;
        $donor->save();
        $donor->refresh();

        return $donor;
    }

    /** @param array<string, mixed> $filters */
    #[Scope]
    protected function filteredForAdmin(Builder $query, array $filters): Builder
    {
        foreach (['area', 'blood_group_id'] as $field) {
            if (! empty($filters[$field])) {
                $query->where($field, $filters[$field]);
            }
        }
        if (! empty($filters['q'])) {
            $query->where(function (Builder $query) use ($filters): void {
                $query->where('name', 'like', '%'.$filters['q'].'%')->orWhere('phone', 'like', '%'.$filters['q'].'%')->orWhere('area', 'like', '%'.$filters['q'].'%');
            });
        }
        if (! empty($filters['availability'])) {
            $query->where('is_available', $filters['availability'] === 'available');
        }

        return $query;
    }
}
