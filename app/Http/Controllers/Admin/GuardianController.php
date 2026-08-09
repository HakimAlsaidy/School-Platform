<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Guardian;
use App\Models\Notification;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class GuardianController extends Controller
{
    public function index(Request $request)
    {
        $query = Guardian::with(['user', 'students.classroom.grade']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $guardians = $query->latest()->paginate(15);

        return view('admin.guardians.index', compact('guardians'));
    }

    public function create()
    {
        $students = \App\Models\Student::with('classroom.grade')
            ->whereNull('guardian_id')
            ->get();

        return view('admin.guardians.create', compact('students'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20', 'unique:users,phone'],
            'password' => ['required', 'min:8', 'confirmed'],
            'relationship' => ['nullable', 'string', 'max:50'],
            'occupation' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'emergency_phone' => ['nullable', 'string', 'max:20'],
        ]);

        $roleId = Role::where('slug', Role::PARENT)->first()?->id;
        $schoolId = auth()->user()->school_id;

        $user = User::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'role_id' => $roleId,
            'school_id' => $schoolId,
            'is_active' => true,
        ]);

        $guardian = Guardian::create([
            'user_id' => $user->id,
            'school_id' => $schoolId,
            'phone' => $validated['phone'] ?? null,
            'relationship' => $validated['relationship'] ?? null,
            'occupation' => $validated['occupation'] ?? null,
            'address' => $validated['address'] ?? null,
            'emergency_phone' => $validated['emergency_phone'] ?? null,
        ]);

        ActivityLog::log('create_guardian', "إضافة ولي أمر جديد: {$user->name}", $guardian);

        return redirect()->route('admin.guardians.index')
            ->with('success', 'تم إضافة ولي الأمر بنجاح.');
    }

    public function show(Guardian $guardian)
    {
        $guardian->load([
            'user',
            'students.classroom.grade',
            'students.scores' => fn($q) => $q->latest()->take(10),
            'students.attendances' => fn($q) => $q->latest()->take(10),
        ]);

        return view('admin.guardians.show', compact('guardian'));
    }

    public function edit(Guardian $guardian)
    {
        return view('admin.guardians.edit', compact('guardian'));
    }

    public function update(Request $request, Guardian $guardian)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20', 'unique:users,phone,' . $guardian->user_id],
            'password' => ['nullable', 'min:8'],
            'relationship' => ['nullable', 'string', 'max:50'],
            'occupation' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'emergency_phone' => ['nullable', 'string', 'max:20'],
            'is_active' => ['nullable'],
        ]);

        $guardian->user->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'is_active' => $request->has('is_active'),
        ]);

        if (!empty($validated['password'])) {
            $guardian->user->update([
                'password' => Hash::make($validated['password']),
            ]);
        }

        $guardian->update([
            'phone' => $validated['phone'],
            'relationship' => $validated['relationship'],
            'occupation' => $validated['occupation'],
            'address' => $validated['address'],
            'emergency_phone' => $validated['emergency_phone'],
        ]);

        ActivityLog::log('update_guardian', "تحديث بيانات ولي الأمر: {$guardian->user->name}", $guardian);

        return redirect()->route('admin.guardians.index')
            ->with('success', 'تم تحديث بيانات ولي الأمر بنجاح.');
    }

    public function destroy(Guardian $guardian)
    {
        $name = $guardian->user->name;
        $guardian->user->delete();

        ActivityLog::log('delete_guardian', "حذف ولي الأمر: {$name}");

        return redirect()->route('admin.guardians.index')
            ->with('success', 'تم حذف ولي الأمر بنجاح.');
    }

public function pending()
    {
        $schoolId = auth()->user()->school_id;

        $pendingUsers = User::where('is_active', false)
            ->where('school_id', $schoolId) // طلبات مدرسة المدير فقط
            ->whereHas('role', function ($q) {
                $q->whereIn('slug', [Role::TEACHER, Role::PARENT]);
            })
            ->with('role')
            ->latest()
            ->paginate(15);

        return view('admin.guardians.pending', compact('pendingUsers'));
    }

    public function approve(User $user)
    {
        // التحقق من أن المستخدم ينتمي لنفس مدرسة المدير
        if ($user->school_id !== auth()->user()->school_id) {
            abort(403, 'لا يمكنك الموافقة على هذا الطلب لأنه من مدرسة أخرى.');
        }

        $user->update(['is_active' => true]);

        // إشعار المستخدم بأن حسابه تم تفعيله
        $roleName = $user->isTeacher() ? 'معلم' : 'ولي أمر';
        Notification::send(
            userId: $user->id,
            title: 'تم تفعيل حسابك',
            message: "أهلاً {$user->name}! تمت الموافقة على طلب تسجيلك كـ {$roleName}. يمكنك الآن تسجيل الدخول.",
            type: 'success',
            actionUrl: route('login'),
            actionText: 'تسجيل الدخول'
        );

        $redirectToEdit = request()->input('action') === 'approve_and_edit';

        // إنشاء سجل في الجدول المناسب حسب الدور
        if ($user->role?->slug === 'teacher') {
            // إذا كان معلم، أنشئ سجل في جدول teachers
            $teacher = \App\Models\Teacher::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'phone' => $user->phone,
                    'specialization' => null,
                    'school_id' => $user->school_id,
                    'hire_date' => now(),
                ]
            );
            
            ActivityLog::log('approve_user', "الموافقة على حساب معلم: {$user->name}", $user);
            
            if ($redirectToEdit) {
                return redirect()->route('admin.teachers.edit', $teacher)
                    ->with('success', 'تم الموافقة على الحساب. يرجى إكمال بيانات المعلم وتعيين المواد والفصول.');
            }
            
            return redirect()->route('admin.teachers.index')
                ->with('success', "تم الموافقة على حساب المعلم: {$user->name} ✓");
                
        } elseif ($user->role?->slug === 'parent') {
            // إذا كان ولي أمر، أنشئ سجل في جدول guardians
            $guardian = \App\Models\Guardian::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'phone' => $user->phone,
                    'relationship' => null,
                    'school_id' => $user->school_id,
                ]
            );
            
            ActivityLog::log('approve_user', "الموافقة على حساب ولي أمر: {$user->name}", $user);
            
            if ($redirectToEdit) {
                return redirect()->route('admin.guardians.edit', $guardian)
                    ->with('success', 'تم الموافقة على الحساب. يرجى إكمال بيانات ولي الأمر وربط الأبناء.');
            }
            
            return redirect()->route('admin.guardians.index')
                ->with('success', "تم الموافقة على حساب ولي الأمر: {$user->name} ✓");
        }

        ActivityLog::log('approve_user', "الموافقة على حساب: {$user->name}", $user);

        return back()->with('success', 'تم الموافقة على الحساب بنجاح.');
    }

    public function reject(User $user)
    {
        // التحقق من أن المستخدم ينتمي لنفس مدرسة المدير
        if ($user->school_id !== auth()->user()->school_id) {
            abort(403, 'لا يمكنك رفض هذا الطلب لأنه من مدرسة أخرى.');
        }

        $name = $user->name;
        $user->delete();

        ActivityLog::log('reject_user', "رفض حساب: {$name}");

        return back()->with('success', 'تم رفض الحساب وحذفه.');
    }
}
