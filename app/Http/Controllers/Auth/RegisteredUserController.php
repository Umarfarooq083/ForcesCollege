<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

use App\Models\Tenant;
use Stancl\Tenancy\Tenant as TenancyTenant;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
    
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        

    //     $existing = Tenant::query()
    //     ->where('data->domain', 'tenant1')
    //     ->exists();

    //     if ($existing) {
    //         return back()->withErrors([
    //             'domain' => 'This domain is already taken.',
    //         ]);
    //     }

    //    $tenant= Tenant::create([
    //         'domain' => 'tenant1',
    //         'name' => $request->name,
            
    //     ]);

    //     $tenant->domains()->create([
    //         'domain' => 'tenant1.test',
    //     ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            // 'tenant_id' => $tenant->id, 
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
