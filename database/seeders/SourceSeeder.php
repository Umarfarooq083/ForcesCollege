<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sources')->truncate();
        DB::table('sources')->insert([
            ['SourceName' => 'Banner', 'created_at' => now(), 'updated_at' => now()],
            ['SourceName' => 'Cable Ad',       'created_at' => now(), 'updated_at' => now()],
            ['SourceName' => 'Hoarding',  'created_at' => now(), 'updated_at' => now()],
            ['SourceName' => 'Reference',   'created_at' => now(), 'updated_at' => now()],
            ['SourceName' => 'Road Side Advertisment',   'created_at' => now(), 'updated_at' => now()],
            ['SourceName' => 'Facebook Ad',   'created_at' => now(), 'updated_at' => now()],
            ['SourceName' => 'Google Ad',   'created_at' => now(), 'updated_at' => now()],
            ['SourceName' => 'Youtube Ad',   'created_at' => now(), 'updated_at' => now()],
            ['SourceName' => 'Newspaper Ad',   'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
