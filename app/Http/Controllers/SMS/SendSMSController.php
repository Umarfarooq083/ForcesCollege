<?php

namespace App\Http\Controllers\SMS;

use App\Helpers\SmsHelper;
use App\Http\Controllers\Controller;
use App\Models\Classes;
use App\Models\Department;
use App\Models\PhoneBook;
use App\Models\PhonebookGroup;
use App\Models\Section;
use App\Models\SMSLog;
use App\Models\Staff;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class SendSMSController extends Controller
{
    public function createSMS(Request $request)
    {
        $tenantId = tenant('id');
        if(session('switched_tenant_id')){
            $tenantId = session('switched_tenant_id');
        } 
        $remainingCredit = SmsHelper::getRemainingCredits($tenantId);
        $data['phonebook'] = PhoneBook::where('tenant_id', $tenantId)->get();
        $data['phonebook_groups'] = PhonebookGroup::where('tenant_id', $tenantId)->get();
        $data['departments'] = Department::get();
        $data['classes'] = Classes::get();
        $data['sections'] = [];
        $data['remainingCredit'] = $remainingCredit;
        return Inertia::render('SendSMS/Create', $data);
    }
    public function getContacts(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string|in:phonebook,phone,staff,father,mother,guardian,student',
            'group_id' => 'nullable|integer',
            'department_id' => 'nullable|integer',
            'class_id' => 'nullable|integer',
            'section_id' => 'nullable|integer',
            'phone' => 'nullable|string'
        ]);
        $tenantId = tenant('id');
        if(session('switched_tenant_id')){
            $tenantId = session('switched_tenant_id');
        }

        $type = $validated['type'];
        $getAll = $request->boolean('get_all', false);
        $contacts = collect();

        $normalizePhone = function ($phone) {
            $phone = trim($phone);
            $phone = str_replace([' ', '-', '+'], '', $phone);

            if (str_starts_with($phone, '92')) {
                return $phone;
            }

            if (str_starts_with($phone, '0')) {
                return '92' . substr($phone, 1);
            }

            if (preg_match('/^[3]\d{9}$/', $phone)) {
                return '92' . $phone;
            }

            return $phone; // fallback
        };

        try {
            switch ($type) {
                case 'phonebook':
                    if ($request->filled('group_id')) {
                        $contacts = PhoneBook::where('phonebook_group_id', $request->group_id)
                            ->select('name', 'contact_no as phone')
                            ->where('tenant_id',$tenantId)
                            ->get();
                    } else {
                        $contacts = collect();
                    }
                    break;

                case 'phone':
                    if ($request->filled('phone')) {
                        $contacts = collect([
                            ['name' => 'Manual Entry', 'phone' => $request->phone],
                        ]);
                    }
                    break;

                case 'staff':
                    $query = Staff::query();
                    $query->where('tenant_id',$tenantId);
                    if (!$getAll && $request->filled('department_id')) {
                        $query->where('DepartmentId', $request->department_id);
                    }
                    $contacts = $query->select('FirstName as name', 'Phone as phone')
                        ->whereNotNull('Phone')
                        ->where('tenant_id',$tenantId)
                        ->where('Phone', '!=', '')
                        ->get();
                    break;

                case 'father':
                case 'mother':
                case 'guardian':
                case 'student':
                    $query = Student::query();
                    $query->where('tenant_id',$tenantId);
                    if (!$getAll) {
                        if ($request->filled('class_id')) {
                            $query->where('ClassId', $request->class_id);
                        }
                        if ($request->filled('section_id')) {
                            $query->where('SectionId', $request->section_id);
                        }
                    }

                    $contacts = $query->get()->map(function ($student) use ($type) {
                        switch ($type) {
                            case 'father':
                                return ['name' => $student->FatherName, 'phone' => $student->FatherPhone];
                            case 'mother':
                                return ['name' => $student->MotherName, 'phone' => $student->MotherPhone];
                            case 'guardian':
                                return ['name' => $student->GuardianName, 'phone' => $student->GuardianPhone];
                            case 'student':
                                return ['name' => $student->FirstName . '-' . $student->LastName, 'phone' => $student->MobileNumber];
                            default:
                                return null;
                        }
                    })->filter(fn($contact) => $contact && !empty(trim($contact['phone'] ?? '')));
                break;
            }

            $contacts = $contacts->map(function ($contact) use ($normalizePhone) {
                return [
                    'name' => $contact['name'] ?? 'Unknown',
                    'phone' => $normalizePhone($contact['phone'] ?? ''),
                ];
            })->filter(fn($contact) => preg_match('/^92\d{10}$/', $contact['phone'] ?? ''));

        \Log::info($contacts);

        return response()->json($contacts->values());

    } catch (\Exception $e) {
            \Log::error('Error fetching contacts: ' . $e->getMessage(), [
                'type' => $type,
                'request' => $request->all()
            ]);

            return response()->json([], 500);
        }
    }
    public function getByClass($class_id)
    {
        $tenant_id = tenant('id');
        if(session('switched_tenant_id')){
            $tenant_id = session('switched_tenant_id');
        } 
        
        $sections = Section::where('ClassId', $class_id)
            ->where('tenant_id',$tenant_id)
            ->get(['id', 'SectionName']); 

        return response()->json($sections);
    }
  
    public function sendSms(Request $request)
    {
        $tenantId = tenant('id');
        $client = new \GuzzleHttp\Client();
        $contacts = $request->input('selectedContacts', []);
        $message = $request->input('smsContent', '');

        // Validate input
        $validator = Validator::make($request->all(), [
            'selectedContacts' => 'required|array|min:1',
            'selectedContacts.*' => 'required|string',
            'smsContent' => 'required|string|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $successCount = 0;
        $errorCount = 0;
        $detailedResults = [];
        $remainingCredit = SmsHelper::getRemainingCredits($tenantId);
        $totalSmsCount = ceil(strlen($message) / 160);
        $requiredCredit = count($contacts) * $totalSmsCount;

        // Check credit before processing
        if ($remainingCredit < $requiredCredit) {
            return response()->json([
                'success' => false,
                'message' => "Insufficient SMS credits. Required: {$requiredCredit}, Available: {$remainingCredit}",
                'remainingCredit' => $remainingCredit
            ], 400);
        }

        foreach ($contacts as $mobile) {
            $result = [
                'mobile' => $mobile,
                'success' => false,
                'message' => '',
                'transactionID' => null,
                'apiCode' => null
            ];

            try {
                $body = [
                    'loginId' => '923158673184',
                    'loginPassword' => '3184#Blue',
                    'Destination' => $mobile,
                    'Mask' => 'FORCES.SCHL',
                    'Message' => $message,
                    'UniCode' => '0',
                    'ShortCodePrefered' => 'N',
                ];

                $response = $client->post('https://cbs.zong.com.pk/reachrestapi/home/SendQuickSMS', [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Cookie' => 'TS01684df9=01f7c6c54da359d52a44f491c8242f648dddef21240b868113d0e1da6041e492bb5fe4ca3206e0b3b8a92a874a9bc5f24c98d2acc0',
                    ],
                    'body' => json_encode($body),
                    'verify' => false,
                    'timeout' => 30,
                ]);

                $rawResponse = trim((string) $response->getBody());
                $parts = explode('|', $rawResponse);

                if (count($parts) >= 2) {
                    $code = trim($parts[0]);
                    $text = trim($parts[1]);
                    $transactionID = $parts[2] ?? null;

                    if ($code === '0') {
                        $status = 1;
                        $code = 300;
                        $successCount++;
                        $result['success'] = true;
                        $result['message'] = $text;
                        $result['transactionID'] = $transactionID;
                        $result['apiCode'] = $code;
                    } else {
                        $status = 2;
                        $errorCount++;
                        $result['message'] = $text;
                        $result['apiCode'] = $code;
                    }

                    // Save to DB
                    SMSLog::create([
                        'tenant_id' => $tenantId,
                        'mobileNo' => $mobile,
                        'body' => $message,
                        'characterLength' => strlen($message),
                        'smsCount' => $totalSmsCount,
                        'status' => $status,
                        'apiCode' => $code,
                        'apiType' => 'ZONG',
                        'apiResponseText' => $text,
                        'apiTransactionID' => $transactionID,
                        'createdBy' => auth()->id(),
                        'isActive' => 1,
                    ]);
                } else {
                    throw new \Exception('Invalid API response format: ' . $rawResponse);
                }

            } catch (\Exception $e) {
                $errorCount++;
                $result['message'] = $e->getMessage();

                SMSLog::create([
                    'tenant_id' => $tenantId,
                    'mobileNo' => $mobile,
                    'body' => $message,
                    'characterLength' => strlen($message),
                    'smsCount' => $totalSmsCount,
                    'status' => 2,
                    'apiType' => 'ZONG',
                    'apiResponseText' => $e->getMessage(),
                    'createdBy' => auth()->id(),
                    'isActive' => 1,
                ]);
            }

            $detailedResults[] = $result;
        }

        // Update remaining credit after sending
        $newRemainingCredit = $remainingCredit - $requiredCredit;

        // Prepare response
        $responseData = [
            'success' => true,
            'summary' => [
                'total' => count($contacts),
                'success' => $successCount,
                'failed' => $errorCount,
                'requiredCredit' => $requiredCredit,
                'remainingCredit' => $newRemainingCredit,
            ],
            'detailedResults' => $detailedResults,
        ];

        if ($successCount === count($contacts)) {
            $responseData['message'] = "SMS sent successfully to {$successCount} contacts";
        } elseif ($successCount > 0) {
            $responseData['message'] = "SMS sent to {$successCount} contacts, but failed for {$errorCount} contacts";
        } else {
            $responseData['message'] = "Failed to send SMS to all {$errorCount} contacts";
        }

        return response()->json($responseData);
    }
}
