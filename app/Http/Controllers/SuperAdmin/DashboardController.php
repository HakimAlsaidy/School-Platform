<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;
use App\Models\SchoolSubscription;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'schools_count' => School::count(),
            'active_schools' => School::where('is_active', true)->count(),
            'pending_schools' => School::where('is_active', false)->orWhere('is_verified', false)->count(),
            'total_students' => \App\Models\Student::withoutGlobalScope('school')->count(),
            'total_teachers' => \App\Models\Teacher::withoutGlobalScope('school')->count(),
            'total_guardians' => \App\Models\Guardian::withoutGlobalScope('school')->count(),
        ];

        $recentSchools = School::latest()->take(5)->get();
        $pendingSchools = School::where('is_active', false)
            ->orWhere('is_verified', false)
            ->latest()
            ->take(10)
            ->get();

        return view('superadmin.dashboard', compact('stats', 'recentSchools', 'pendingSchools'));
    }
}
