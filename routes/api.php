<?php

use App\Http\Controllers\DeviceController;
use App\Http\Controllers\MobileAPI\StudentAppController;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');



Route::post('/device-verification', [DeviceController::class, 'verify']);
Route::post('/device-issue-token', [DeviceController::class, 'issueAutoLogin']);

// Route::post('/device-issue-token', function (Request $request) {
//     $deviceKey = $request->input('device_key');

//     // Example: find tenant based on device key or other logic
//     // $tenant = Tenant::whereHas('devices', fn($q) => $q->where('device_key', $deviceKey))->first();

//     // if (!$tenant) {
//     //     return response()->json(['status' => 'error', 'message' => 'Tenant not found'], 404);
//     // }

//     $encryptedKey = Crypt::encryptString($deviceKey);
//     $tenant_name = 'b17';
//     return response()->json([
//         'status' => 'ok',
//         'tenant' => 'b17',
//         'url' => "http://{$tenant_name}.lms/login",
//     ]);
// });


// Route::get('/sections/{class_id}', [\App\Http\Controllers\SMS\SendSMSController::class, 'getByClass']);

Route::get('/login', [StudentAppController::class, 'Login']);
Route::get('/campuses', [StudentAppController::class, 'Campuses']);
Route::get('/diary', [StudentAppController::class, 'homeWork']);
Route::get('/class-time-table', [StudentAppController::class, 'ClassTimeTable']);
Route::get('/student-attendance', [StudentAppController::class, 'studentAttendance']);
Route::get('/student-fee', [StudentAppController::class, 'getStudentFee']);
Route::get('/student-exam', [StudentAppController::class, 'getStudentExams']);
Route::get('/student-exam-detail', [StudentAppController::class, 'getStudentExamsDetail']);
Route::get('/reset-password', [StudentAppController::class, 'resetPassword']);
