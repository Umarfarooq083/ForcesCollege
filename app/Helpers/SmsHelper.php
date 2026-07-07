<?php

namespace App\Helpers;

use App\Models\SmsCredits;
use App\Models\SMSLog;


class SmsHelper
{
    /**
     * Get remaining SMS credits for a tenant.
     *
     * @param  int  $tenantId
     * @return int
     */
    public static function getRemainingCredits($tenantId)
    {
        $smsCredits = SmsCredits::where('tenant_id', $tenantId)->value('smsCreditCount') ?? 0;

        $usedCredits = SMSLog::where('tenant_id', $tenantId)
            ->where('apiCode', 300)
            ->sum('smsCount');

        return max($smsCredits - $usedCredits, 0);
    }
}
