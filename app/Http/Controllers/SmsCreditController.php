<?php

namespace App\Http\Controllers;

use App\Http\Requests\SMSCreditRequest;
use App\Models\SmsCredits;
use App\Models\SMSLog;
use App\Services\SMSCreditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SmsCreditController extends Controller
{
    protected $SMSCreditService;
  
    public function __construct(SMSCreditService $SMSCreditService)
    {
        $this->SMSCreditService = $SMSCreditService;
    }

    public function index(): Response
    {
        $SmsCredits = $this->SMSCreditService->list();
        return Inertia::render('SMS/List',[
            'SmsCredits' => $SmsCredits
        ]);
    }
    
    public function create(): Response
    {
        return Inertia::render('SMS/Create');
    }
    
    public function submit(SMSCreditRequest $request): RedirectResponse
    {
        $this->SMSCreditService->submit($request);
        return $this->redirectSuccess('SMS Credit created successfully.', 'smscredit.index');
    }
    
    public function logList(): Response
    {
        $SmsLog = $this->SMSCreditService->Loglist();
        return Inertia::render('SMS/LogList',[
            'SmsLog' => $SmsLog
        ]);
    }
   
    public function logDetail(Request $request): Response
    {
        $SmsLogDetail = SMSLog::find($request->id);
        return Inertia::render('SMS/LogDetail',[
            'SmsLogDetail' => $SmsLogDetail
        ]);
    }
}
