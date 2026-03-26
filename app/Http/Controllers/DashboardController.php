<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Setting;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('dashboard', [
            'stats' => [
                'users' => User::count(),
                'branches' => Branch::count(),
                'settings' => Setting::count(),
            ],
        ]);
    }
}
