<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Bus;
use App\Models\Student;
use App\Models\TransportRoute;
use App\Models\TransportStudent;
use Illuminate\Http\Request;

class TransportController extends Controller
{
    public function index()
    {
        $buses = Bus::withCount('routes')->latest()->get();
        $routes = TransportRoute::with(['bus', 'transportStudents.student'])->latest()->get();
        $students = Student::where('is_active', true)->get();

        return view('admin.transport.index', compact('buses', 'routes', 'students'));
    }

    // ==================== الحافلات ====================
    public function busStore(Request $request)
    {
        $validated = $request->validate([
            'bus_number' => ['required', 'string', 'max:50', 'unique:buses,bus_number'],
            'plate_number' => ['nullable', 'string', 'max:50'],
            'capacity' => ['required', 'integer', 'min:1'],
            'driver_name' => ['nullable', 'string', 'max:255'],
            'driver_phone' => ['nullable', 'string', 'max:20'],
            'supervisor_name' => ['nullable', 'string', 'max:255'],
            'supervisor_phone' => ['nullable', 'string', 'max:20'],
        ]);

        $bus = Bus::create([
            'school_id' => auth()->user()->school_id,
            'bus_number' => $validated['bus_number'],
            'plate_number' => $validated['plate_number'] ?? null,
            'capacity' => $validated['capacity'],
            'driver_name' => $validated['driver_name'] ?? null,
            'driver_phone' => $validated['driver_phone'] ?? null,
            'supervisor_name' => $validated['supervisor_name'] ?? null,
            'supervisor_phone' => $validated['supervisor_phone'] ?? null,
        ]);

        ActivityLog::log('add_bus', "إضافة حافلة: {$bus->bus_number}", $bus);
        return back()->with('success', 'تمت إضافة الحافلة بنجاح.');
    }

    public function busDestroy(Bus $bus)
    {
        $bus->delete();
        return back()->with('success', 'تم حذف الحافلة.');
    }

    // ==================== المسارات ====================
    public function routeStore(Request $request)
    {
        $validated = $request->validate([
            'bus_id' => ['required', 'exists:buses,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'pickup_time' => ['nullable', 'date_format:H:i'],
            'dropoff_time' => ['nullable', 'date_format:H:i'],
        ]);

        $route = TransportRoute::create([
            'school_id' => auth()->user()->school_id,
            'bus_id' => $validated['bus_id'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'pickup_time' => $validated['pickup_time'] ?? null,
            'dropoff_time' => $validated['dropoff_time'] ?? null,
        ]);

        ActivityLog::log('add_transport_route', "إضافة مسار نقل: {$route->name}", $route);
        return back()->with('success', 'تمت إضافة المسار بنجاح.');
    }

    public function routeDestroy(TransportRoute $route)
    {
        $route->delete();
        return back()->with('success', 'تم حذف المسار.');
    }

    // ==================== ربط الطلاب ====================
    public function assignStudent(Request $request)
    {
        $validated = $request->validate([
            'route_id' => ['required', 'exists:transport_routes,id'],
            'student_id' => ['required', 'exists:students,id'],
            'pickup_point' => ['nullable', 'string'],
            'dropoff_point' => ['nullable', 'string'],
        ]);

        $exists = TransportStudent::where('route_id', $validated['route_id'])
            ->where('student_id', $validated['student_id'])
            ->exists();

        if ($exists) {
            return back()->with('info', 'الطالب مسجل في هذا المسار مسبقاً.');
        }

        TransportStudent::create([
            'school_id' => auth()->user()->school_id,
            'route_id' => $validated['route_id'],
            'student_id' => $validated['student_id'],
            'pickup_point' => $validated['pickup_point'] ?? null,
            'dropoff_point' => $validated['dropoff_point'] ?? null,
        ]);

        return back()->with('success', 'تم ربط الطالب بالمسار بنجاح.');
    }

    public function removeStudent(TransportStudent $transportStudent)
    {
        $transportStudent->delete();
        return back()->with('success', 'تم إلغاء ربط الطالب بالمسار.');
    }
}
