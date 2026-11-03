<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UnionSeeder extends Seeder
{
    public function run(): void
    {
        (new BangladeshLocationSeeder)->seedThrough('unions');
    }
}
