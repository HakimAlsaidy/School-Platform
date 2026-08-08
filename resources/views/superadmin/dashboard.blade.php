@extends('layouts.superadmin')

@section('title', 'لوحة التحكم - Super Admin')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800">👋 مرحباً بك في لوحة التحكم الرئيسية</h1>
    <p class="text-gray-600 mt-2">إدارة جميع المدارس المسجلة في المنصة</p>
</div>

<!-- الإحصائيات العامة -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6 mb-8">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">إجمالي المدارس</p>
                <p class="text-3xl font-bold text-gray-800">{{ $stats['schools_count'] }}</p>
            </div>
            <div class="w-14 h-14 bg-indigo-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-school text-2xl text-indigo-600"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">المدارس النشطة</p>
                <p class="text-3xl font-bold text-green-600">{{ $stats['active_schools'] }}</p>
            </div>
            <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-check-circle text-2xl text-green-600"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">طلبات معلقة</p>
                <p class="text-3xl font-bold text-amber-600">{{ $stats['pending_schools'] }}</p>
            </div>
            <div class="w-14 h-14 bg-amber-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-clock text-2xl text-amber-600"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">إجمالي الطلاب</p>
                <p class="text-3xl font-bold text-blue-600">{{ $stats['total_students'] }}</p>
            </div>
            <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-user-graduate text-2xl text-blue-600"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">إجمالي المعلمين</p>
                <p class="text-3xl font-bold text-purple-600">{{ $stats['total_teachers'] }}</p>
            </div>
            <div class="w-14 h-14 bg-purple-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-chalkboard-teacher text-2xl text-purple-600"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">أولياء الأمور</p>
                <p class="text-3xl font-bold text-pink-600">{{ $stats['total_guardians'] }}</p>
            </div>
            <div class="w-14 h-14 bg-pink-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-users text-2xl text-pink-600"></i>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- الطلبات المعلقة -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-lg font-bold text-gray-800">
                <i class="fas fa-clock text-amber-500 ml-2"></i>
                طلبات التسجيل المعلقة
            </h2>
            <a href="{{ route('superadmin.schools.index', ['status' => 'pending']) }}" class="text-indigo-600 hover:text-indigo-700 text-sm">
                عرض الكل <i class="fas fa-arrow-left mr-1"></i>
            </a>
        </div>
        <div class="p-6">
            @forelse($pendingSchools as $school)
                <div class="flex items-center justify-between py-4 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                    <div class="flex items-center gap-4">
                        <img src="{{ $school->logo_url }}" alt="{{ $school->name }}" class="w-12 h-12 rounded-xl object-cover">
                        <div>
                            <h3 class="font-semibold text-gray-800">{{ $school->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $school->subdomain }}.platform.com</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <form action="{{ route('superadmin.schools.approve', $school) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="p-2 bg-green-100 text-green-600 rounded-lg hover:bg-green-200 transition" title="قبول">
                                <i class="fas fa-check"></i>
                            </button>
                        </form>
                        <form action="{{ route('superadmin.schools.reject', $school) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="p-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition" title="رفض">
                                <i class="fas fa-times"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-gray-400">
                    <i class="fas fa-check-circle text-4xl mb-2"></i>
                    <p>لا توجد طلبات معلقة</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- آخر المدارس المسجلة -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-lg font-bold text-gray-800">
                <i class="fas fa-school text-indigo-500 ml-2"></i>
                آخر المدارس المسجلة
            </h2>
            <a href="{{ route('superadmin.schools.index') }}" class="text-indigo-600 hover:text-indigo-700 text-sm">
                عرض الكل <i class="fas fa-arrow-left mr-1"></i>
            </a>
        </div>
        <div class="p-6">
            @forelse($recentSchools as $school)
                <div class="flex items-center justify-between py-4 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                    <div class="flex items-center gap-4">
                        <img src="{{ $school->logo_url }}" alt="{{ $school->name }}" class="w-12 h-12 rounded-xl object-cover">
                        <div>
                            <h3 class="font-semibold text-gray-800">{{ $school->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $school->city ?? 'غير محدد' }} • {{ $school->type_name }}</p>
                        </div>
                    </div>
                    <div>
                        @if($school->is_active && $school->is_verified)
                            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm">نشطة</span>
                        @else
                            <span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-sm">معلقة</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-gray-400">
                    <i class="fas fa-school text-4xl mb-2"></i>
                    <p>لا توجد مدارس مسجلة</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- الإجراءات السريعة -->
<div class="mt-8">
    <h2 class="text-lg font-bold text-gray-800 mb-4">
        <i class="fas fa-bolt text-yellow-500 ml-2"></i>
        إجراءات سريعة
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="{{ route('superadmin.schools.create') }}" class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-2xl p-6 hover:shadow-lg transition group">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition">
                    <i class="fas fa-plus text-xl"></i>
                </div>
                <div>
                    <h3 class="font-bold">إضافة مدرسة</h3>
                    <p class="text-sm text-white/80">إنشاء مدرسة جديدة</p>
                </div>
            </div>
        </a>

        <a href="{{ route('superadmin.schools.index') }}" class="bg-gradient-to-r from-blue-500 to-cyan-600 text-white rounded-2xl p-6 hover:shadow-lg transition group">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition">
                    <i class="fas fa-list text-xl"></i>
                </div>
                <div>
                    <h3 class="font-bold">إدارة المدارس</h3>
                    <p class="text-sm text-white/80">عرض جميع المدارس</p>
                </div>
            </div>
        </a>

<a href="{{ route('superadmin.reports.index') }}" class="bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-2xl p-6 hover:shadow-lg transition group">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition">
                    <i class="fas fa-chart-bar text-xl"></i>
                </div>
                <div>
                    <h3 class="font-bold">التقارير</h3>
                    <p class="text-sm text-white/80">إحصائيات شاملة</p>
                </div>
            </div>
        </a>

        <a href="{{ route('superadmin.features.index') }}" class="bg-gradient-to-r from-purple-500 to-fuchsia-600 text-white rounded-2xl p-6 hover:shadow-lg transition group">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition">
                    <i class="fas fa-layer-group text-xl"></i>
                </div>
                <div>
                    <h3 class="font-bold">الميزات</h3>
                    <p class="text-sm text-white/80">نظرة عامة على الوحدات</p>
                </div>
            </div>
        </a>

        <a href="{{ route('superadmin.settings.index') }}" class="bg-gradient-to-r from-amber-500 to-orange-600 text-white rounded-2xl p-6 hover:shadow-lg transition group">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition">
                    <i class="fas fa-cog text-xl"></i>
                </div>
                <div>
                    <h3 class="font-bold">الإعدادات</h3>
                    <p class="text-sm text-white/80">إعدادات المنصة</p>
                </div>
            </div>
        </a>
    </div>
</div>
@endsection
