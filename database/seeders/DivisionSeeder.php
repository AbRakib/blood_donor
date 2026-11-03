<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DivisionSeeder extends Seeder
{
    public function run(): void
    {
        (new BangladeshLocationSeeder)->seedThrough('divisions');
    }
}
