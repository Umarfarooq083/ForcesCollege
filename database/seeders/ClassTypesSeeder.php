<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('class_types')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $sectionTypes = [
            ['Name' => 'Primary', 'created_at' => now(), 'updated_at' => now()],
            ['Name' => 'Middle', 'created_at' => now(), 'updated_at' => now()],
            ['Name' => 'High', 'created_at' => now(), 'updated_at' => now()],
            ['Name' => 'Pre-School', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('class_types')->insert($sectionTypes);
    }
}
