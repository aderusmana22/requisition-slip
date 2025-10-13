<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ApprovalPathSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Data JSON untuk Free Goods
        $freeGoodsSequence = [
            "5300" => [ // Alur untuk requester dari SnM (kode 5300)
                [
                    "level" => 1,
                    "type" => "atasan" // SnM Manager (atasan requester)
                ],
                [
                    "level" => 2,
                    "type" => "role",
                    "value" => "user-approval" // Business Controller (AG3333)
                ]
            ],
            "NON-5300" => [ // Alur untuk requester dari departemen lain
                [
                    "level" => 1,
                    "type" => "role",
                    "value" => "head-HCD" // HCD Dept. Head (HDHCD01)
                ],
                [
                    "level" => 2,
                    "type" => "role",
                    "value" => "user-approval" // Business Controller (AG3333)
                ]
            ]
        ];

        DB::table('approval_paths')->updateOrInsert(
            [
                'category' => 'FREE GOODS',
                'sub_category' => 'General Request'
            ],
            [
                'category' => 'FREE GOODS',
                'sub_category' => 'General Request',
                'sequence_approvers' => json_encode($freeGoodsSequence),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

    }
}