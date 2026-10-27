<?php

namespace App\Models;

use Database\Factories\SystemSettingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[Fillable(['key', 'value', 'description', 'status'])]
class SystemSetting extends DomainModel
{
    /** @use HasFactory<SystemSettingFactory> */
    use HasFactory;
}
