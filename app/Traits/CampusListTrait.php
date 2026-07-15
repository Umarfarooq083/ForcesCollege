<?php

namespace App\Traits;

use App\Models\Campus;

trait CampusListTrait
{
    public function getCampusList()
    {
        return Campus::select('id', 'SchoolName', 'DomainName', 'tenant_id')->get();
    }
}
