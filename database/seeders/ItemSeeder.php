<?php

namespace Database\Seeders;

use App\Models\Master\ItemDetail;
use App\Models\Master\ItemMaster;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        ItemMaster::factory()
            ->count(50)
            ->create()
            ->each(function ($master) {
                ItemDetail::factory()
                    ->count(5)
                    ->create([
                        'item_master_id' => $master->id,
                    ]);
            });
    }
}
