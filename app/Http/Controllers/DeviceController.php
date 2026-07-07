<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\DeviceToken;

class DeviceController extends Controller
{ 
   public function issueAutoLogin(Request $request)
    {
        $request->validate(['device_key' => 'required|string']);
        $deviceKey = $request->device_key;
        $token = Str::random(48);
        $expires = Carbon::now()->addMinutes(3);
        DeviceToken::create([
            'token' => $token,
            'device_key' => $deviceKey,
            'expires_at' => $expires,
        ]);
        $tenantDomain = 'headoffice';
        $loginUrl = "http://{$tenantDomain}.lms/auto-login?token={$token}";
        return response()->json([
            'status'     => 'ok',
            'tenant'     => 'headoffice',
            'url'        => $loginUrl,
            'expires_at' => $expires->toIso8601String(),
        ]);
    }

    public function autoLogin(Request $request)
    {
        $token = $request->query('token');
        if (!$token) abort(400, 'Token required');

        $record = DeviceToken::where('token',$token)
            ->where('used',false)
            ->where('expires_at','>=',now())
            ->first();

        if (!$record) abort(403, 'Invalid or expired token');
        $record->update(['used'=>true]); 
        session(['register_device_key' => $record->device_key]);
        return redirect('/login'); 
    }




}
