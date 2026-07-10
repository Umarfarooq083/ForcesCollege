<?php

use App\Models\Campus;
use App\Models\Classes;
use App\Models\GenerateFeeChallan;
use App\Models\LmsSession;
use App\Models\Permissions;
use App\Models\RolePermission;
use App\Models\Section;
use App\Models\SiteSetting;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Google\Auth\Credentials\ServiceAccountCredentials;
use GuzzleHttp\Client;

if (!function_exists('getTenantSubDomain')) {
    function getTenantSubDomain()
    {
        $host = request()->getHost();
        return explode('.', $host)[0];
    }
}

if (!function_exists('checkTenantExist')) {
    function checkTenantExist()
    {
        $domainName = getTenantSubDomain();
        $tenant = Tenant::where('domain', $domainName)->first();
        if ($tenant) {
            return $tenant;
        } else {
            abort(404, 'Tenant not found');
        }
    }
}

if (!function_exists('getDomainTenantId')) {
    function getDomainTenantId()
    {
        $subdomain =  getTenantSubDomain();
        $tenant = Tenant::where('domain', $subdomain)->first();
        return $tenant->id;
    }
}


if (!function_exists('getRolesPermissions')) {
    function getRolesPermissions($role_ids, $user_id)
    {
        $rolePermissions = RolePermission::whereIn('role_id', $role_ids)
            ->get()
            ->pluck('permission_id')
            ->unique()
            ->values();
        $userPermissions = Permissions::whereIn('id', $rolePermissions)->get()->pluck('route_names');
        $flattenedRoutes = $userPermissions->flatten()->unique();
        $routesJson = $flattenedRoutes->values()->toJson();
        $userupdate = User::findOrFail($user_id);
        $userupdate->user_permissions = $routesJson;
        return $userupdate->save();
    }
}

if (!function_exists('removeUserPermission')) {
    function removeUserPermission($role_id)
    {
        $roleUsers = DB::table('role_user')
            ->where('role_id', $role_id)
            ->get()->pluck('user_id')->toArray();

        $userList = User::whereIn('id', $roleUsers)->update([
            'user_permissions' => null,
        ]);
    }
}

if (!function_exists('isTenantSwitched')) {
    function isTenantSwitched($object)
    {
        $IsHeadoffice = getTenantSubDomain();
        $isCampusSwithed = false;
        if ($IsHeadoffice === 'headoffice') {
            $switchdTenandId = session('switched_tenant_id');
            if (tenant('id') != $switchdTenandId) {
                if ($switchdTenandId == null) {
                    $isCampusSwithed = false;
                } else {
                    $isCampusSwithed = true;
                }
            }
        }
        if ($isCampusSwithed === true) {
            return $object->where('tenant_id', session('switched_tenant_id'));
        }
    }
}

if (!function_exists('campusClassList')) {
    function campusClassList()
    {
        $tenant_id = tenant('id');
        if(session('switched_tenant_id')){
            $tenant_id = session('switched_tenant_id');
        }
        return Classes::select('id', 'tenant_id', 'ClassName')->where('tenant_id', $tenant_id)->where('IsActive', 1)->get()->pluck('id', 'id')->toArray();
    }
}

if (!function_exists('campusClass')) {
    function campusClass()
    {
        $tenant_id = tenant('id');
        if(session('switched_tenant_id')){
            $tenant_id = session('switched_tenant_id');
        }
        return Classes::select('id', 'tenant_id', 'ClassName')->where('tenant_id', $tenant_id)->where('IsActive', 1)->get();
    }
}

// if (!function_exists('campusAssignClassesList')) {
//     function campusAssignClassesList()
//     {
//         $campusData = Campus::select('id','tenant_id','SchoolName')->Tenant()->with('campusClasses.classType')->first();
//         $campusClassesData = data_get($campusData, 'campusClasses');
//         return array_map(fn($item) => data_get($item, 'class_type'), $campusClassesData->toArray());
//     }
// }


// if (!function_exists('addWheretenant')) {
//     function addWhereForTenant($object)
//     {
//         return $object->where('tenant_id', session('switched_tenant_id'));
//     }
// }

if (!function_exists('getExistingAttendance')) {
    function getExistingAttendance($request, $items, $model, $foreignKey, $attendanceField)
    {
        $existingAttendance = [];

        $date = $request->input('date');
        if ($date && !$items->isEmpty()) {
            $ids = $items->pluck('id')->toArray();
            return $model::where('AttendanceDate', $date)->where('tenant_id', tenant('id'))
                ->whereIn($foreignKey, $ids)
                ->pluck($attendanceField, $foreignKey)
                ->toArray();
        }

        return $existingAttendance;
    }
}

if (!function_exists('fetchCurrentSession')) {
    function fetchCurrentSession()
    {
        $campusData = Campus::where('tenant_id', tenant('id'))->first('zoneid');
        return LmsSession::where('zoneid', $campusData->zoneid)->where('status', 1)->first();
    }
}

if (!function_exists('classAndSections')) {
    function classAndSections()
    {
        $classList = Classes::select('id', 'tenant_id', 'ClassName')->where('tenant_id', tenant('id'))->where('IsActive', 1)->get();
        $class_ids = collect($classList);
        $data['classList'] = $classList;
        $classIdsSection = $class_ids->pluck('id')->toArray();
        $data['sectionList'] = Section::select('id', 'tenant_id', 'SectionName', 'ClassId')->whereIn('ClassId', $classIdsSection)->where('tenant_id',tenant('id'))->get();
        return $data;
    }
}


 
if (!function_exists('createOrUpdateSetting')) {
    function createOrUpdateSetting($name, $key, $value)
    {
        $setting = SiteSetting::firstOrNew([
            'tenant_id' => tenant('id'),
            'key'       => $key,
        ]);

        $setting->name = $name;
        $setting->value = $value;
        $setting->created_by = $setting->exists ? $setting->created_by : auth()->id();
        $setting->modified_by = $setting->exists ? auth()->id() : null;
        $setting->save();
        return $setting;
    }
}

if (!function_exists('generateChallanNo')) {
    function generateChallanNo()
    {
        $lastGeneratedChallan = GenerateFeeChallan::where('tenant_id',tenant('id'))
            ->orderByDesc('challan_no')
            ->first();
        $num = 1;
        if($lastGeneratedChallan)
        {
            $num = $num + $lastGeneratedChallan->challan_no;
        }
        return $num;
    }
}


if (!function_exists('getSiteMeta')) {
    function getSiteMeta($key)
    {
        $setting = SiteSetting::where('key', $key)->first();
        return $setting ? (int) $setting->value : null;
    }
}

if (!function_exists('getSiteSettingValue')) {
    function getSiteSettingValue($key)
    {
        $setting = SiteSetting::where('key', $key)->first();
        return $setting ? $setting->value : null;
    }
}

// user activity logs 
// if (!function_exists('userActivityLogs')) {
//     function userActivityLogs($message, $model)
//     {
//         $model::create([
//             'user_id' => auth()->id(),
//             'tenant_id' => tenant('id'),
//             'action'    => $message,
//             'user_name' => auth()->user()->name,
//         ]);
//     }
// }

if (!function_exists('userActivityLogs')) {
    function userActivityLogs($message, $model)
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return;
            }
            
            if (!class_exists($model)) {
                return;
            }

            $model::create([
                'user_id'   => $user->id,
                'tenant_id' => tenant('id') ?? null,
                'action'    => $message,
                'user_name' => $user->name,
            ]);
        } catch (\Throwable $e) {
            \Log::error("userActivityLogs failed: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'message' => $message,
                'model' => $model,
            ]);
        }
    }
}

if (!function_exists('MobileTenantVerifaction')) {
    function MobileTenantVerifaction($request)
    {
       return Tenant::where('domain', $request->campus)->first();
    }
}

if (!function_exists('APIfetchCurrentSession')) {
    function APIfetchCurrentSession($tenant)
    {
        $campusData = Campus::where('tenant_id', $tenant->id)->first();
        return LmsSession::where('zoneid', $campusData->zoneid)->where('status', 1)->first();
    }
}
if (!function_exists('sendTestNotification')) {
    function sendTestNotification($request,$title,$body,$studentData)
    {
        // dd($studentData?->userFCMToken?->fcm_token);
        $deviceToken = $studentData?->userFCMToken?->fcm_token;
        $credentialsPath = storage_path('app/firebase-service-account.json');
        $scopes = ['https://www.googleapis.com/auth/firebase.messaging'];

        
        $credentials = new ServiceAccountCredentials(
            $scopes,
            json_decode(file_get_contents($credentialsPath), true)
        );

        $client = new Client(['verify' => false]);

        $accessToken = $credentials->fetchAuthToken(fn($request) => $client->send($request))['access_token'];
        if($deviceToken == null){
            return;
        }
        $response = (new Client(['verify' => false]))->post(
            "https://fcm.googleapis.com/v1/projects/" . env('FIREBASE_PROJECT_ID') . "/messages:send",
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type'  => 'application/json',
                ],
                'json' => [
                    'message' => [
                        'token' => $deviceToken,
                        'notification' => [
                            'title' => $title,
                            'body'  => $body,
                        ],
                    ]
                ]
            ]
        );

        return json_decode($response->getBody(), true);
    }
}