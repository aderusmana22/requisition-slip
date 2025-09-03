<?php

namespace Database\Seeders\Master;

use Illuminate\Database\Seeder;
use App\Models\Master\Department;
use Carbon\Carbon;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $departments = [
            [
                'id' => 1,
                'name' => 'Engineering & Maintenance',
                'slug' => 'engineering-maintainance',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'name' => 'Finance Admin',
                'slug' => 'finance-admin',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'name' => 'HCD',
                'slug' => 'hcd',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 4,
                'name' => 'Manufacturing',
                'slug' => 'manufacturing',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 5,
                'name' => 'QM & HSE',
                'slug' => 'qm-hse',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 6,
                'name' => 'R&D',
                'slug' => 'rd',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 7,
                'name' => 'Sales & Marketing',
                'slug' => 'sales-marketing',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 8,
                'name' => 'Supply Chain',
                'slug' => 'supply-chain',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 11,
                'name' => 'Supply & Maintenance',
                'slug' => 'supply-and-maintenance',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($departments as $dept) {
            Department::updateOrCreate(['id' => $dept['id']], $dept);
        }
    }
}
