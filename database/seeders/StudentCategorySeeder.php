<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('student_categories')->truncate();

        DB::table('student_categories')->insert([
            [
                'id' => 1,
                'tenant_id' => 'default',
                'SchoolId' => 0,
                'IsActive' => true,
                'CreatedBy' => 1,
                'CreatedDate' => '2020-11-09 20:40:14',
                'ModifiedBy' => null,
                'ModifiedDate' => null,
                'SessionId' => 1,
                'CategoryName' => 'Regular',
                'StudentCategoryGroupId' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ],
            [
                'id' => 2,
                'tenant_id' => 'default',
                'SchoolId' => 0,
                'IsActive' => true,
                'CreatedBy' => 1,
                'CreatedDate' => '2020-11-09 20:40:24',
                'ModifiedBy' => null,
                'ModifiedDate' => null,
                'SessionId' => 1,
                'CategoryName' => 'Martyr',
                'StudentCategoryGroupId' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ],
        ]);
    }
}
