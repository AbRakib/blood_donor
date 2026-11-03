<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class BangladeshLocationSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedThrough('unions');
    }

    public function seedThrough(string $lastTable): void
    {
        $tables = ['divisions' => null, 'districts' => 'division_id', 'upazilas' => 'district_id', 'unions' => 'upazila_id'];
        if (! array_key_exists($lastTable, $tables)) {
            throw new InvalidArgumentException('Unknown location table: '.$lastTable);
        }

        $data = json_decode(file_get_contents(__DIR__.'/bangladesh-locations.json'), true, flags: JSON_THROW_ON_ERROR);

        DB::transaction(function () use ($tables, $data, $lastTable): void {
            $parentIds = [];
            foreach ($tables as $table => $parentColumn) {
                $parentIds = $this->seedLocations($table, $data[$table], $parentColumn, $parentIds);
                if ($table === $lastTable) {
                    break;
                }
            }
        });
    }

    /**
     * @param  list<array{id: int, name: string, name_bn: string, division_id?: int, district_id?: int, upazila_id?: int}>  $locations
     * @param  array<int, int>  $parentIds
     * @return array<int, int>
     */
    private function seedLocations(string $table, array $locations, ?string $parentColumn, array $parentIds): array
    {
        $existingByName = [];
        $existingByBanglaName = [];
        foreach (DB::table($table)->get() as $record) {
            $parentId = $parentColumn === null ? 0 : $record->{$parentColumn};
            $existingByName[$parentId.'|'.mb_strtolower($record->name)] = $record;
            if ($record->name_bn !== null && $record->name_bn !== '') {
                $existingByBanglaName[$parentId.'|'.$record->name_bn] = $record;
            }
        }

        $ids = [];
        foreach ($locations as $location) {
            $parentId = $parentColumn === null ? 0 : $parentIds[$location[$parentColumn]];
            $existing = $existingByName[$parentId.'|'.mb_strtolower($location['name'])]
                ?? $existingByBanglaName[$parentId.'|'.$location['name_bn']]
                ?? null;
            if ($existing === null && $table === 'districts') {
                $existing = $existingByName['|'.mb_strtolower($location['name'])]
                    ?? $existingByBanglaName['|'.$location['name_bn']]
                    ?? null;
            }
            $attributes = ['name' => $location['name'], 'name_bn' => $location['name_bn']];
            if ($parentColumn !== null) {
                $attributes[$parentColumn] = $parentId;
            }

            if ($existing === null) {
                if ($table === 'divisions' || $table === 'districts') {
                    $attributes['code'] = ($table === 'divisions' ? 'BD-DIV-' : 'BD-DIST-').$location['id'];
                }
                $ids[$location['id']] = DB::table($table)->insertGetId([
                    ...$attributes,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $changes = array_filter($attributes, fn (mixed $value, string $column): bool => $existing->{$column} != $value, ARRAY_FILTER_USE_BOTH);
                if ($changes !== []) {
                    DB::table($table)->where('id', $existing->id)->update([...$changes, 'updated_at' => now()]);
                }
                $ids[$location['id']] = (int) $existing->id;
            }
        }

        return $ids;
    }
}
