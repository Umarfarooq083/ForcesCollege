<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class StaffDisableReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('staff_disable_reasons')->truncate();
        DB::table('staff_disable_reasons')->insert([
            [
                'id' => 1,
                'IsActive' => 1,
                'CreatedBy' => 1,
                'CreatedDate' => Carbon::parse('2021-08-30 19:03:14.493099'),
                'ModifiedBy' => null,
                'ModifiedDate' => null,
                'SessionId' => 1,
                'DisableReasonName' => 'Active',
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ],
            [
                'id' => 2,
                'IsActive' => 1,
                'CreatedBy' => 1,
                'CreatedDate' => Carbon::parse('2021-08-31 16:24:38.660901'),
                'ModifiedBy' => null,
                'ModifiedDate' => null,
                'SessionId' => 1,
                'DisableReasonName' => 'Disable',
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ],
            [
                'id' => 3,
                'IsActive' => 1,
                'CreatedBy' => 1,
                'CreatedDate' => Carbon::parse('2021-08-31 16:24:38.660901'),
                'ModifiedBy' => null,
                'ModifiedDate' => null,
                'SessionId' => 1,
                'DisableReasonName' => 'Resigned',
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ],
            [
                'id' => 4,
                'IsActive' => 1,
                'CreatedBy' => 1,
                'CreatedDate' => Carbon::parse('2021-08-31 16:24:38.660901'),
                'ModifiedBy' => null,
                'ModifiedDate' => null,
                'SessionId' => 1,
                'DisableReasonName' => 'Terminated',
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ],
        ]);
    }
}
