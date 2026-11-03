<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UpazilaSeeder extends Seeder
{
    public function run(): void
    {
        (new BangladeshLocationSeeder)->seedThrough('upazilas');
    }
}
