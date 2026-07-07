<?php

namespace App\Services;

use App\Models\SmsCredits;
use App\Models\SMSLog;

class SMSCreditService
{
   public function list()
   {
      return SmsCredits::where('tenant_id',tenant('id'))->where('isActive',1)->orderBy('id','desc')->paginate(25)->withQueryString();
   }
   
   public function submit($request)
   {
       
      $validated = $request->validated();
      $current_session = fetchCurrentSession();
      
        return SmsCredits::create([
            ...$validated,
            'tenant_id'     => tenant('id'),
            'isActive'     => 1,
            'sessionId'     => $current_session['id'],
            'createdBy'     => auth()->user()->id,
        ]);
   }

   public function Loglist()
   {
      return SMSLog::where('tenant_id',tenant('id'))->paginate(25)->withQueryString();
   }
   
}