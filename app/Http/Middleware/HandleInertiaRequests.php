<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request)
    {
        // return [
        //     ...parent::share($request),
        //     'auth' => [
        //         'user' => $request->user(),
        //     ],
        //     [
        //         'toast' => fn () => $request->session()->get('toast'),
        //     ],
        // ];

        $user = auth()->user();
        if ($user) {
            if (is_null($user->user_permissions)) {
                Auth::logout();
                throw ValidationException::withMessages([
                    'email' => "Access denied you don't have permission",
                ]);
            }
        }

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user(),
            ],
            'toast' => fn () => $request->session()->get('toast'),
            'flash' => [
                'receipt' => fn () => $request->session()->get('receipt'),
            ],

        ]);
    }

    public function handle($request, \Closure $next)
    {
        $response = parent::handle($request, $next);

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Content-Security-Policy', "frame-ancestors 'self'");

        return $response;
    }
}
