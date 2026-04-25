@extends('layouts.superadmin')

@section('title', $school->name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('superadmin.schools.index') }}" 
               class="w-10 h-10 bg-white border border-gray-200 rounded-xl flex items-center justify-center text-gray-600 hover:bg-gray-50 transition">
                <i class="fas fa-arrow-right"></i>
            </a>
            <div class="flex items-center gap-4">
                @if($school->logo)
                    <img src="{{ $school->logo_url }}" alt="" class="w-16 h-16 rounded-xl object-cover">
                @else
                    <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-xl flex items-center justify-center">
                        <i class="fas fa-school text-2xl text-white"></i>
                    </div>
                @endif
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">{{ $school->name }}</h1>
                    <p class="text-gray-500 font-mono">{{ $school->subdomain }}.{{ config('app.domain', 'schoolpla.test') }}</p>
                </div>
            </div>
        </div>
        
        <div class="flex items-center gap-2">
            <a href="{{ $school->full_url }}" target="_blank"
               class="px-4 py-2 border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition">
                <i class="fas fa-external-link-alt ml-2"></i>
                فتح المدرسة
            </a>
            <a href="{{ route('superadmin.schools.edit', $school) }}" 
               class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl font-medium hover:opacity-90 transition">
                <i class="fas fa-edit ml-2"></i>
                تعديل
            </a>
        </div>
    </div>

    <!-- Status Banner -->
    @if(!$school->is_verified)
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
                <div>
                    <p class="font-semibold text-yellow-800">هذه المدرسة بانتظار الموافقة</p>
                    <p class="text-sm text-yellow-600">قم بمراجعة البيانات ثم قم بالموافقة أو الرفض</p>
                </div>
            </div>
            <div class="flex gap-2">
                <form action="{{ route('superadmin.schools.approve', $school) }}" method="POST" class="inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        <i class="fas fa-check ml-1"></i>
                        موافقة
                    </button>
                </form>
                <form action="{{ route('superadmin.schools.reject', $school) }}" method="POST" class="inline"
                      onsubmit="return confirm('هل أنت متأكد من رفض هذه المدرسة؟')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                        <i class="fas fa-times ml-1"></i>
                        رفض
                    </button>
                </form>
            </div>
        </div>
    @elseif(!$school->is_active)
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <i class="fas fa-ban text-red-600 text-xl"></i>
                <div>
                    <p class="font-semibold text-red-800">هذه المدرسة معطّلة</p>
                    <p class="text-sm text-red-600">لا يمكن لمستخدمي المدرسة الوصول إلى النظام</p>
                </div>
            </div>
            <form action="{{ route('superadmin.schools.approve', $school) }}" method="POST" class="inline">
                @csrf
                @method('PATCH')
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    <i class="fas fa-redo ml-1"></i>
                    إعادة تفعيل
                </button>
            </form>
        </div>
    @endif

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">الطلاب</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $school->students()->count() }}</p>
                    @if($school->max_students)
                        <p class="text-xs text-gray-400">من {{ $school->max_students }}</p>
                    @endif
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-user-graduate text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">المعلمين</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $school->teachers()->count() }}</p>
                    @if($school->max_teachers)
                        <p class="text-xs text-gray-400">من {{ $school->max_teachers }}</p>
                    @endif
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-chalkboard-teacher text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">الفصول</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $school->classrooms()->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-door-open text-amber-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">أولياء الأمور</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $school->guardians()->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-user-friends text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- معلومات المدرسة -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">
                    <i class="fas fa-info-circle ml-2 text-indigo-600"></i>
                    معلومات المدرسة
                </h2>
                
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">النوع</p>
                            <p class="font-medium text-gray-800">{{ $school->type_name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">المرحلة</p>
                            <p class="font-medium text-gray-800">{{ $school->level_name }}</p>
                        </div>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-500">رقم الهاتف</p>
                        <p class="font-medium text-gray-800 font-mono" dir="ltr">{{ $school->phone }}</p>
                    </div>
                    
                    @if($school->email)
                        <div>
                            <p class="text-sm text-gray-500">البريد الإلكتروني</p>
                            <p class="font-medium text-gray-800" dir="ltr">{{ $school->email }}</p>
                        </div>
                    @endif
                    
                    @if($school->address)
                        <div>
                            <p class="text-sm text-gray-500">العنوان</p>
                            <p class="font-medium text-gray-800">{{ $school->address }}</p>
                        </div>
                    @endif
                    
                    <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-100">
                        <div>
                            <p class="text-sm text-gray-500">تاريخ التسجيل</p>
                            <p class="font-medium text-gray-800">{{ $school->created_at->format('Y/m/d') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">آخر تحديث</p>
                            <p class="font-medium text-gray-800">{{ $school->updated_at->format('Y/m/d') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- مدراء المدرسة -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">
                    <i class="fas fa-user-shield ml-2 text-indigo-600"></i>
                    مدراء المدرسة
                </h2>
                
                <div class="space-y-3">
                    @forelse($school->users()->whereHas('role', fn($q) => $q->where('slug', 'admin'))->get() as $admin)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                            <div class="flex items-center gap-3">
                                <img src="{{ $admin->avatar_url }}" alt="" class="w-10 h-10 rounded-full">
                                <div>
                                    <p class="font-medium text-gray-800">{{ $admin->name }}</p>
                                    <p class="text-sm text-gray-500 font-mono" dir="ltr">{{ $admin->phone }}</p>
                                </div>
                            </div>
                            <span class="px-2 py-1 bg-indigo-100 text-indigo-600 rounded-lg text-xs font-medium">مدير</span>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">لا يوجد مدراء للمدرسة</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- الاشتراك -->
        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">
                    <i class="fas fa-crown ml-2 text-amber-500"></i>
                    الاشتراك
                </h2>
                
                @php
                    $subscription = $school->activeSubscription;
                @endphp
                
                @if($subscription)
                    <div class="space-y-4">
                        <div class="p-4 bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl">
                            <p class="text-sm text-gray-600 mb-1">الباقة الحالية</p>
                            <p class="text-xl font-bold text-indigo-700">
                                @php
                                    $planNames = ['free' => 'مجانية', 'basic' => 'أساسية', 'premium' => 'متقدمة', 'enterprise' => 'مؤسسية'];
                                @endphp
                                {{ $planNames[$subscription->plan] ?? $subscription->plan }}
                            </p>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <p class="text-sm text-gray-500">تاريخ البدء</p>
                                <p class="font-medium text-gray-800">{{ $subscription->starts_at->format('Y/m/d') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">تاريخ الانتهاء</p>
                                <p class="font-medium text-gray-800">
                                    @if($subscription->ends_at)
                                        {{ $subscription->ends_at->format('Y/m/d') }}
                                    @else
                                        غير محدد
                                    @endif
                                </p>
                            </div>
                        </div>
                        
                        @if($subscription->ends_at && $subscription->ends_at->isPast())
                            <div class="p-3 bg-red-50 border border-red-200 rounded-xl">
                                <p class="text-red-700 text-sm">
                                    <i class="fas fa-exclamation-circle ml-1"></i>
                                    الاشتراك منتهي الصلاحية
                                </p>
                            </div>
                        @elseif($subscription->ends_at && $subscription->ends_at->diffInDays(now()) <= 30)
                            <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-xl">
                                <p class="text-yellow-700 text-sm">
                                    <i class="fas fa-exclamation-triangle ml-1"></i>
                                    ينتهي خلال {{ $subscription->ends_at->diffInDays(now()) }} يوم
                                </p>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="text-center py-6">
                        <div class="w-16 h-16 bg-gray-100 rounded-full mx-auto mb-3 flex items-center justify-center">
                            <i class="fas fa-crown text-2xl text-gray-400"></i>
                        </div>
                        <p class="text-gray-500">لا يوجد اشتراك نشط</p>
                    </div>
                @endif
            </div>

            <!-- إجراءات سريعة -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">
                    <i class="fas fa-bolt ml-2 text-amber-500"></i>
                    إجراءات سريعة
                </h2>
                
                <div class="space-y-2">
                    @if($school->is_active)
                        <form action="{{ route('superadmin.schools.suspend', $school) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                    class="w-full px-4 py-2.5 border border-red-200 text-red-600 rounded-xl hover:bg-red-50 transition text-right"
                                    onclick="return confirm('هل أنت متأكد من تعليق هذه المدرسة؟')">
                                <i class="fas fa-ban ml-2"></i>
                                تعليق المدرسة
                            </button>
                        </form>
                    @else
                        <form action="{{ route('superadmin.schools.approve', $school) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                    class="w-full px-4 py-2.5 border border-green-200 text-green-600 rounded-xl hover:bg-green-50 transition text-right">
                                <i class="fas fa-redo ml-2"></i>
                                إعادة تفعيل المدرسة
                            </button>
                        </form>
                    @endif
                    
                    <form action="{{ route('superadmin.schools.destroy', $school) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="w-full px-4 py-2.5 border border-gray-200 text-gray-600 rounded-xl hover:bg-gray-50 transition text-right"
                                onclick="return confirm('هل أنت متأكد من حذف هذه المدرسة؟ هذا الإجراء لا يمكن التراجع عنه.')">
                            <i class="fas fa-trash-alt ml-2"></i>
                            حذف المدرسة
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
