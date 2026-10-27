<?php

namespace App\Models;

use Database\Factories\BloodRequestStatusLogFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['blood_request_id', 'from_status', 'to_status', 'reason'])]
class BloodRequestStatusLog extends DomainModel
{
    /** @use HasFactory<BloodRequestStatusLogFactory> */
    use HasFactory;

    public function bloodRequest(): BelongsTo
    {
        return $this->belongsTo(BloodRequest::class, 'blood_request_id');
    }
}
