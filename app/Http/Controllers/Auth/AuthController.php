<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\Role;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'phone' => ['required', 'string'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            if (!auth()->user()->is_active) {
                Auth::logout();
                return back()->withErrors([
                    'phone' => 'تم تعطيل حسابك. يرجى التواصل مع الإدارة.',
                ]);
            }

            ActivityLog::log('login', 'تسجيل دخول المستخدم');

            return $this->redirectBasedOnRole();
        }

        return back()->withErrors([
            'phone' => 'البيانات المدخلة غير صحيحة.',
        ])->onlyInput('phone');
    }

    public function logout(Request $request)
    {
        ActivityLog::log('logout', 'تسجيل خروج المستخدم');

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

public function showRegisterForm()
    {
        // جلب المدارس النشطة لعرضها في نموذج التسجيل
        $schools = School::active()->verified()->orderBy('name')->get();

        return view('auth.register', compact('schools'));
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20', 'unique:users,phone'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', 'in:teacher,parent'],
            'school_id' => ['required', 'exists:schools,id'],
        ]);

        $roleId = Role::where('slug', $validated['role'])->first()?->id;

        $user = User::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'role_id' => $roleId,
            'school_id' => $validated['school_id'], // المدرسة التي اختارها المستخدم
            'is_active' => false, // يحتاج موافقة إدارة المدرسة المختارة
        ]);

        ActivityLog::log('register', 'تسجيل مستخدم جديد', $user, $user->id);

        // إرسال إشعار لمديري المدرسة المختارة بطلب التسجيل الجديد
        $roleName = $user->isTeacher() ? 'معلم' : 'ولي أمر';
        $school = School::find($validated['school_id']);
        $admins = User::where('school_id', $validated['school_id'])
            ->whereHas('role', fn($q) => $q->where('slug', Role::ADMIN))
            ->where('is_active', true)
            ->get();

        foreach ($admins as $admin) {
            Notification::send(
                userId: $admin->id,
                title: 'طلب تسجيل جديد',
                message: "{$user->name} طلب التسجيل كـ {$roleName} في {$school->name}",
                type: 'info',
                actionUrl: route('admin.pending-users'),
                actionText: 'مراجعة الطلب'
            );
        }

        return redirect()->route('login')
            ->with('success', 'تم إنشاء حسابك بنجاح. سيتم إشعار إدارة المدرسة بطلبك، ويمكنك تسجيل الدخول بعد الموافقة.');
    }

    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'phone' => ['required', 'string', 'exists:users,phone'],
        ]);

        // في الإنتاج، يمكن إرسال رمز OTP عبر SMS
        return back()->with('success', 'سيتم إرسال رمز التحقق إلى رقم هاتفك.');
    }

    protected function redirectBasedOnRole()
    {
        $user = auth()->user();

        // التحقق من Super Admin أولاً
        if ($user->isSuperAdmin()) {
            return redirect()->route('superadmin.dashboard');
        } elseif ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isTeacher()) {
            return redirect()->route('teacher.dashboard');
        } elseif ($user->isParent()) {
            return redirect()->route('parent.dashboard');
        }

        return redirect()->route('home');
    }
}
