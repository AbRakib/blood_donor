<?php

namespace App\Models;

use Database\Factories\SettingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[Fillable(['name', 'email', 'phone', 'address', 'logo', 'footer_logo', 'meta_icon', 'facebook', 'twitter', 'instagram', 'linkedin', 'status'])]
class Setting extends DomainModel
{
    /** @use HasFactory<SettingFactory> */
    use HasFactory;
}
