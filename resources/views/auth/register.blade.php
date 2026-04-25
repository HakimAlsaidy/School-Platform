@extends('layouts.app')

@section('title', 'إنشاء حساب - منصة المدرسة التعليمية')

@section('content')
<div class="min-h-screen flex flex-col lg:flex-row">
    <!-- Top Banner for Mobile -->
    <div class="lg:hidden gradient-bg py-6 px-4 text-center text-white">
        <i class="fas fa-user-plus text-4xl mb-2 opacity-90"></i>
        <h2 class="text-xl font-bold">انضم إلينا اليوم</h2>
        <p class="text-sm text-indigo-200 mt-1">أنشئ حسابك وابدأ متابعة العملية التعليمية</p>
    </div>
    
    <!-- Left Side - Illustration (Desktop Only) -->
    <div class="hidden lg:flex w-1/2 gradient-bg items-center justify-center p-12">
        <div class="text-center text-white">
            <i class="fas fa-user-plus text-9xl mb-8 opacity-90"></i>
            <h2 class="text-4xl font-bold mb-4">انضم إلينا اليوم</h2>
            <p class="text-xl text-indigo-200 max-w-md mx-auto">
                أنشئ حسابك وابدأ في متابعة العملية التعليمية بكل سهولة
            </p>
            
            <div class="mt-12 space-y-4 text-right max-w-sm mx-auto">
                <div class="flex items-center gap-4 p-4 bg-white/10 rounded-xl backdrop-blur">
                    <i class="fas fa-check-circle text-2xl text-green-300"></i>
                    <span>متابعة فورية لأداء الطلاب</span>
                </div>
                <div class="flex items-center gap-4 p-4 bg-white/10 rounded-xl backdrop-blur">
                    <i class="fas fa-check-circle text-2xl text-green-300"></i>
                    <span>تواصل مباشر مع المعلمين</span>
                </div>
                <div class="flex items-center gap-4 p-4 bg-white/10 rounded-xl backdrop-blur">
                    <i class="fas fa-check-circle text-2xl text-green-300"></i>
                    <span>تقارير تفصيلية للدرجات والحضور</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Right Side - Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-4 sm:p-6 lg:p-8 overflow-y-auto">
        <div class="w-full max-w-md py-4">
            <!-- Logo -->
            <div class="text-center mb-4 sm:mb-6">
                <div class="inline-flex items-center justify-center w-14 h-14 sm:w-20 sm:h-20 bg-indigo-100 rounded-2xl mb-3 sm:mb-4">
                    <i class="fas fa-user-plus text-2xl sm:text-4xl text-indigo-600"></i>
                </div>
                <h1 class="text-xl sm:text-3xl font-bold text-gray-800">إنشاء حساب جديد</h1>
                <p class="text-gray-500 mt-1 sm:mt-2 text-sm sm:text-base">أكمل البيانات التالية للتسجيل</p>
            </div>
            
            <!-- Register Form -->
            <form action="{{ route('register') }}" method="POST" class="space-y-4">
                @csrf
                
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">الاسم الكامل</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400">
                            <i class="fas fa-user"></i>
                        </span>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                            class="w-full pr-12 pl-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('name') border-red-500 @enderror"
                            placeholder="أدخل اسمك الكامل">
                    </div>
                    @error('name')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">رقم الهاتف</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400">
                            <i class="fas fa-phone"></i>
                        </span>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required
                            class="w-full pr-12 pl-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('phone') border-red-500 @enderror"
                            placeholder="05xxxxxxxx">
                    </div>
                    @error('phone')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-2">نوع الحساب</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400">
                            <i class="fas fa-user-tag"></i>
                        </span>
                        <select id="role" name="role" required
                            class="w-full pr-12 pl-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition appearance-none @error('role') border-red-500 @enderror">
                            <option value="">اختر نوع الحساب</option>
                            <option value="teacher" {{ old('role') == 'teacher' ? 'selected' : '' }}>معلم</option>
                            <option value="parent" {{ old('role') == 'parent' ? 'selected' : '' }}>ولي أمر</option>
                        </select>
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400 pointer-events-none">
                            <i class="fas fa-chevron-down"></i>
                        </span>
                    </div>
                    @error('role')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">كلمة المرور</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" id="password" name="password" required
                            class="w-full pr-12 pl-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('password') border-red-500 @enderror"
                            placeholder="أدخل كلمة المرور">
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">تأكيد كلمة المرور</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                            class="w-full pr-12 pl-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            placeholder="أعد إدخال كلمة المرور">
                    </div>
                </div>
                
                <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-info-circle text-amber-500 mt-0.5"></i>
                        <p class="text-sm text-amber-700">
                            سيتم مراجعة طلبك من قبل الإدارة قبل تفعيل حسابك. ستتلقى إشعاراً عند الموافقة.
                        </p>
                    </div>
                </div>
                
                <button type="submit" class="w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl transition duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-user-plus"></i>
                    إنشاء الحساب
                </button>
            </form>
            
            <!-- Divider -->
            <div class="flex items-center my-4 sm:my-6">
                <div class="flex-1 border-t border-gray-200"></div>
                <span class="px-4 text-sm text-gray-500">أو</span>
                <div class="flex-1 border-t border-gray-200"></div>
            </div>
            
            <!-- Login Link -->
            <p class="text-center text-gray-600 text-sm sm:text-base pb-4">
                لديك حساب بالفعل؟
                <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-700 font-semibold">سجّل دخولك</a>
            </p>
        </div>
    </div>
</div>
@endsection
