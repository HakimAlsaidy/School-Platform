@extends('layouts.dashboard')

@section('page-title', 'طلبات التسجيل المعلقة')
@section('page-description', 'مراجعة طلبات التسجيل الجديدة والموافقة عليها')

@section('dashboard-content')
<div class="mb-6">
    <div class="flex items-center gap-2 text-amber-600 bg-amber-50 px-4 py-3 rounded-xl">
        <i class="fas fa-clock text-xl"></i>
        <span>هناك <strong>{{ $pendingUsers->total() }}</strong> طلب تسجيل في انتظار المراجعة</span>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    @if($pendingUsers->isEmpty())
        <div class="p-12 text-center">
            <i class="fas fa-check-circle text-6xl text-green-400 mb-4"></i>
            <h3 class="text-xl font-bold text-gray-700 mb-2">لا توجد طلبات معلقة</h3>
            <p class="text-gray-500">جميع طلبات التسجيل تمت مراجعتها</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-600">المستخدم</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-600">البريد الإلكتروني</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-600">الهاتف</th>
                        <th class="px-6 py-4 text-center text-sm font-semibold text-gray-600">الدور</th>
                        <th class="px-6 py-4 text-center text-sm font-semibold text-gray-600">تاريخ التسجيل</th>
                        <th class="px-6 py-4 text-center text-sm font-semibold text-gray-600">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($pendingUsers as $user)
                        <tr class="hover:bg-gray-50" id="user-row-{{ $user->id }}">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white font-bold">
                                        {{ mb_substr($user->name, 0, 1) }}
                                    </div>
                                    <span class="font-semibold text-gray-800">{{ $user->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-600">{{ $user->email }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $user->phone ?? '-' }}</td>
                            <td class="px-6 py-4 text-center">
                                @if($user->role?->slug === 'teacher')
                                    <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm">
                                        <i class="fas fa-chalkboard-teacher ml-1"></i>معلم
                                    </span>
                                @elseif($user->role?->slug === 'parent')
                                    <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm">
                                        <i class="fas fa-user-friends ml-1"></i>ولي أمر
                                    </span>
                                @else
                                    <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm">
                                        {{ $user->role?->name ?? 'غير محدد' }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center text-gray-500 text-sm">
                                {{ $user->created_at->diffForHumans() }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button type="button" 
                                            onclick="openApproveModal({{ $user->id }}, '{{ $user->name }}', '{{ $user->role?->slug }}')"
                                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center gap-2">
                                        <i class="fas fa-check"></i>
                                        <span class="hidden sm:inline">موافقة</span>
                                    </button>
                                    <form action="{{ route('admin.users.reject', $user) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition flex items-center gap-2"
                                                onclick="return confirm('هل تريد رفض وحذف هذا الطلب؟')">
                                            <i class="fas fa-times"></i>
                                            <span class="hidden sm:inline">رفض</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        @if($pendingUsers->hasPages())
            <div class="px-6 py-4 border-t">
                {{ $pendingUsers->links() }}
            </div>
        @endif
    @endif
</div>

<!-- Modal للموافقة -->
<div id="approveModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeApproveModal()"></div>
    
    <!-- Modal Content -->
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="relative bg-white w-full max-w-2xl rounded-2xl shadow-2xl transform transition-all">
            <!-- Modal Header -->
            <div class="p-6 border-b border-gray-100 bg-gradient-to-l from-green-50 to-emerald-50 rounded-t-2xl">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-user-check text-xl text-green-600"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-800">الموافقة على الطلب</h3>
                            <p class="text-sm text-gray-500" id="modalUserName">اسم المستخدم</p>
                        </div>
                    </div>
                    <button onclick="closeApproveModal()" class="p-2 hover:bg-gray-100 rounded-lg transition">
                        <i class="fas fa-times text-gray-500"></i>
                    </button>
                </div>
            </div>
            
            <!-- Modal Body -->
            <form id="approveForm" method="POST">
                @csrf
                <input type="hidden" name="redirect_to_edit" id="redirectToEdit" value="0">
                
                <div class="p-6 space-y-6">
                    <!-- معلومات التنبيه -->
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-info-circle text-blue-500 mt-1"></i>
                            <div>
                                <p class="text-sm text-blue-800 font-medium">ماذا سيحدث بعد الموافقة؟</p>
                                <ul class="text-sm text-blue-700 mt-2 space-y-1 list-disc list-inside">
                                    <li>سيتم تفعيل حساب المستخدم</li>
                                    <li>سيظهر في القائمة المناسبة (المعلمين / أولياء الأمور)</li>
                                    <li id="additionalInfo">يمكنه تسجيل الدخول مباشرة</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <!-- خيارات إضافية للمعلم -->
                    <div id="teacherOptions" class="hidden">
                        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-4">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-exclamation-triangle text-amber-500 mt-1"></i>
                                <div>
                                    <p class="text-sm text-amber-800 font-medium">تنبيه هام للمعلمين</p>
                                    <p class="text-sm text-amber-700 mt-1">
                                        المعلم يحتاج إلى تعيين المواد والفصول قبل أن يتمكن من استخدام النظام بشكل كامل.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- خيارات إضافية لولي الأمر -->
                    <div id="parentOptions" class="hidden">
                        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-4">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-exclamation-triangle text-amber-500 mt-1"></i>
                                <div>
                                    <p class="text-sm text-amber-800 font-medium">تنبيه هام لأولياء الأمور</p>
                                    <p class="text-sm text-amber-700 mt-1">
                                        ولي الأمر يحتاج إلى ربط أبنائه بحسابه ليتمكن من متابعتهم.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Modal Footer -->
                <div class="p-6 border-t border-gray-100 bg-gray-50 rounded-b-2xl">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button type="submit" name="action" value="approve_only"
                                class="flex-1 px-6 py-3 bg-green-600 text-white rounded-xl font-semibold hover:bg-green-700 transition flex items-center justify-center gap-2">
                            <i class="fas fa-check"></i>
                            موافقة فقط
                        </button>
                        <button type="submit" name="action" value="approve_and_edit"
                                class="flex-1 px-6 py-3 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-700 transition flex items-center justify-center gap-2">
                            <i class="fas fa-user-edit"></i>
                            موافقة وإكمال البيانات
                        </button>
                        <button type="button" onclick="closeApproveModal()"
                                class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transition">
                            إلغاء
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentUserId = null;
let currentUserRole = null;

function openApproveModal(userId, userName, userRole) {
    currentUserId = userId;
    currentUserRole = userRole;
    
    document.getElementById('modalUserName').textContent = userName;
    document.getElementById('approveForm').action = `/admin/users/${userId}/approve`;
    
    // إظهار/إخفاء الخيارات حسب نوع الحساب
    if (userRole === 'teacher') {
        document.getElementById('teacherOptions').classList.remove('hidden');
        document.getElementById('parentOptions').classList.add('hidden');
        document.getElementById('additionalInfo').textContent = 'يحتاج تعيين المواد والفصول لاستخدام النظام';
    } else if (userRole === 'parent') {
        document.getElementById('teacherOptions').classList.add('hidden');
        document.getElementById('parentOptions').classList.remove('hidden');
        document.getElementById('additionalInfo').textContent = 'يحتاج ربط الأبناء لمتابعتهم';
    } else {
        document.getElementById('teacherOptions').classList.add('hidden');
        document.getElementById('parentOptions').classList.add('hidden');
        document.getElementById('additionalInfo').textContent = 'يمكنه تسجيل الدخول مباشرة';
    }
    
    document.getElementById('approveModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeApproveModal() {
    document.getElementById('approveModal').classList.add('hidden');
    document.body.style.overflow = '';
}

// إغلاق بـ Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeApproveModal();
});
</script>
@endsection
