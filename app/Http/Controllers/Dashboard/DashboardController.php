<?php

namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use Inertia\Response;
use Inertia\Inertia;

class DashboardController extends Controller
{
    
    public function getDashboardData(): Response
    {  
        return Inertia::render('Dashboard');
    }
}
