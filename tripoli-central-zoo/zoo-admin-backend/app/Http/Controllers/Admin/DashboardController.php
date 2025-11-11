<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Animal;
use App\Models\Facility;
use App\Models\Activity;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_animals' => Animal::count(),
            'active_animals' => Animal::active()->count(),
            'total_facilities' => Facility::count(),
            'open_facilities' => Facility::open()->count(),
            'total_activities' => Activity::count(),
            'scheduled_activities' => Activity::scheduled()->count(),
        ];

        // Get recent activities (last 10)
        $recentActivities = Activity::with(['animal', 'facility'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentActivities'));
    }
}
