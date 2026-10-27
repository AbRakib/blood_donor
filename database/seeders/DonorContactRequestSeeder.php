<?php

namespace Database\Seeders;

use App\Models\DonorContactRequest;
use Illuminate\Database\Seeder;

class DonorContactRequestSeeder extends Seeder
{
    public function run(): void
    {
        DonorContactRequest::factory()->create();
    }
}
