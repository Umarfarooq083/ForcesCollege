<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('regions')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $regions = [
            ['name' => 'Central Punjab',    'created_at' => now(), 'updated_at' => now()],
            ['name' => 'South Punjab',      'created_at' => now(), 'updated_at' => now()],
            ['name' => 'KPK',               'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Winter Zone Campuses', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Federal / Islamabad', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Aug Operative', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Punjab with GK', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Kpk with WA', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('regions')->insert($regions);
    }
}
