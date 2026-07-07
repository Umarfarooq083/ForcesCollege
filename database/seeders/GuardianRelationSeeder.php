<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GuardianRelationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('guardian_relation')->truncate();
        DB::table('guardian_relation')->insert([
            ['relationName' => 'Brother', 'created_at' => now(), 'updated_at' => now()],
            ['relationName' => 'Sister', 'created_at' => now(), 'updated_at' => now()],
            ['relationName' => 'Cousin', 'created_at' => now(), 'updated_at' => now()],
            ['relationName' => 'Grand Father', 'created_at' => now(), 'updated_at' => now()],
            ['relationName' => 'Grand Mother', 'created_at' => now(), 'updated_at' => now()],
            ['relationName' => 'Father', 'created_at' => now(), 'updated_at' => now()],
            ['relationName' => 'Mother', 'created_at' => now(), 'updated_at' => now()],
            ['relationName' => 'Uncle', 'created_at' => now(), 'updated_at' => now()],
            ['relationName' => 'Aunt', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
