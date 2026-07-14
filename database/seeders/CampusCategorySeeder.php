<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CampusCategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('campus_categories')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $categories = [
            [
                'SchoolId' => 0,
                'IsActive' => true,
                'IsDeleted' => false,
                'CreatedBy' => 1,
                'CreatedDate' => now(),
                'ModifiedBy' => null,
                'ModifiedDate' => null,
                'SessionId' => 1,
                'CategoryName' => 'Forces College',
                'CategoryCode' => 'FORCES_COLLEGE',
                'CategoryType' => 'College',
                'Description' => 'Forces College category',
                'tenant_id' => 'default',
                'SortOrder' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'SchoolId' => 0,
                'IsActive' => true,
                'IsDeleted' => false,
                'CreatedBy' => 1,
                'CreatedDate' => now(),
                'ModifiedBy' => null,
                'ModifiedDate' => null,
                'SessionId' => 1,
                'CategoryName' => 'Forces Coaching Center',
                'CategoryCode' => 'FORCES_COACHING_CENTER',
                'CategoryType' => 'Coaching Center',
                'Description' => 'Forces Coaching Center category',
                'tenant_id' => 'default',
                'SortOrder' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'SchoolId' => 0,
                'IsActive' => true,
                'IsDeleted' => false,
                'CreatedBy' => 1,
                'CreatedDate' => now(),
                'ModifiedBy' => null,
                'ModifiedDate' => null,
                'SessionId' => 1,
                'CategoryName' => 'Forces College of Nursing & Allied Health Sciences',
                'CategoryCode' => 'FORCES_COLLEGE_NURSING_ALLIED_HEALTH',
                'CategoryType' => 'College of Nursing & Allied Health Sciences',
                'Description' => 'Forces College of Nursing & Allied Health Sciences category',
                'tenant_id' => 'default',
                'SortOrder' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'SchoolId' => 0,
                'IsActive' => true,
                'IsDeleted' => false,
                'CreatedBy' => 1,
                'CreatedDate' => now(),
                'ModifiedBy' => null,
                'ModifiedDate' => null,
                'SessionId' => 1,
                'CategoryName' => 'Forces College of Law',
                'CategoryCode' => 'FORCES_COLLEGE_LAW',
                'CategoryType' => 'College of Law',
                'Description' => 'Forces College of Law category',
                'tenant_id' => 'default',
                'SortOrder' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'SchoolId' => 0,
                'IsActive' => true,
                'IsDeleted' => false,
                'CreatedBy' => 1,
                'CreatedDate' => now(),
                'ModifiedBy' => null,
                'ModifiedDate' => null,
                'SessionId' => 1,
                'CategoryName' => 'Forces College of Pharmacy',
                'CategoryCode' => 'FORCES_COLLEGE_PHARMACY',
                'CategoryType' => 'College of Pharmacy',
                'Description' => 'Forces College of Pharmacy category',
                'tenant_id' => 'default',
                'SortOrder' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'SchoolId' => 0,
                'IsActive' => true,
                'IsDeleted' => false,
                'CreatedBy' => 1,
                'CreatedDate' => now(),
                'ModifiedBy' => null,
                'ModifiedDate' => null,
                'SessionId' => 1,
                'CategoryName' => 'Forces College of Paramedical Sciences',
                'CategoryCode' => 'FORCES_COLLEGE_PARAMEDICAL',
                'CategoryType' => 'College of Paramedical Sciences',
                'Description' => 'Forces College of Paramedical Sciences category',
                'tenant_id' => 'default',
                'SortOrder' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('campus_categories')->insert($categories);
    }
}
