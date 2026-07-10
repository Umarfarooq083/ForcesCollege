<?php

namespace App\Services;

use App\Models\Campus;
use App\Models\Roles;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class CampusService
{

    public function index()
    {
        $campusList = Campus::query();
        return $campusList = $campusList->where('tenant_id', tenant('id'))->with('zone','region')->orderby('id', 'desc')->paginate(25)->withQueryString();    
    }

    public function submit($validated, $request): void
    {
        DB::transaction(function () use ($validated, $request) {
            $rawName = $request->DomainName;
            // $tenantSlug = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $rawName));
            $tenantSlug = strtolower(preg_replace('/[^a-zA-Z0-9-]/', '', $rawName));
            $baseDomain = env('BASE_TENANT_DOMAIN');
            $fullDomain = "{$tenantSlug}.{$baseDomain}";
            $existing = Tenant::query()
                ->where('data->domain', $tenantSlug)
                ->exists();
            if ($existing) {
                throw ValidationException::withMessages([
                    'DomainName' => 'This domain is already taken.',
                ]);
            }
            // Campus Creation 
            $campusCreate = Campus::create([
                ...$validated,
                'CreatedBy' => Auth::id(),
                'CreatedDate' => now(),
                'DomainName' => $tenantSlug,
            ]);
            // Tenant Creation
            $tenant = Tenant::create([
                'domain' => $tenantSlug,
                'name' => $request->SchoolName,
            ]);
            // Assign Tenant to Created Campus 
            $campusCreate->update([
                'tenant_id' => $tenant->id,
            ]);

            // Create Domin for campus 
            $tenant->domains()->create([
                'domain' => $fullDomain,
            ]);
            // Check User Exist or not and then Create an Admin User For Campus 
            $userExist = User::where('email', $request->EmailAddress)->where('tenant_id', $tenant->id)->first();
            if ($userExist) {
                throw ValidationException::withMessages([
                    'EmailAddress' => 'This email address is already taken.',
                ]);
            } else {
                $password = Hash::make('Admin@123!@#$');
                $createUser = new User();
                $createUser->name = $request->SchoolName;
                $createUser->email = $request->EmailAddress;
                $createUser->password = $password;
                $createUser->tenant_id = $tenant->id;
                $createUser->save();
                $role = Roles::where('name', 'Campus Admin')->first();
                DB::table('role_user')->insert([
                    'user_id' => $createUser->id,
                    'role_id' => $role->id,
                ]);
            }
        });
    }
}
