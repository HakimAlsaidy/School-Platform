<?php

namespace App\Http\Controllers\Guardian;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TransportController extends Controller
{
    public function index()
    {
        $students = Auth::user()->guardian->students()
            ->with(['transportAssignments.route.bus'])
            ->get();

        return view('guardian.transport.index', compact('students'));
    }
}
