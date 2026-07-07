<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SectionTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('section_types')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $sectionTypes = [
            ['name' => 'Boys', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Girls', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Co-education', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('section_types')->insert($sectionTypes);
    }
}
