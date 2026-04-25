@extends('layouts.app')

@section('title', 'تسجيل الدخول - منصة المدرسة التعليمية')

@section('content')
<div class="min-h-screen flex flex-col lg:flex-row">
    <!-- Left Side - Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-4 sm:p-8 order-2 lg:order-1">
        <div class="w-full max-w-md">
            <!-- Logo -->
            <div class="text-center mb-6 sm:mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 sm:w-20 sm:h-20 bg-indigo-100 rounded-2xl mb-4">
                    <i class="fas fa-graduation-cap text-3xl sm:text-4xl text-indigo-600"></i>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">مرحباً بعودتك</h1>
                <p class="text-gray-500 mt-2 text-sm sm:text-base">سجّل دخولك للوصول إلى حسابك</p>
            </div>
            
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-center">
                    {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-center">
                    {{ session('error') }}
                </div>
            @endif
            
            <!-- Login Form -->
            <form action="{{ route('login') }}" method="POST" class="space-y-5">
                @csrf
                
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">رقم الهاتف</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400">
                            <i class="fas fa-phone"></i>
                        </span>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required autofocus
                            class="w-full pr-12 pl-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('phone') border-red-500 @enderror"
                            placeholder="05xxxxxxxx">
                    </div>
                    @error('phone')
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
                
                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <span class="mr-2 text-sm text-gray-600">تذكرني</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="text-sm text-indigo-600 hover:text-indigo-700">نسيت كلمة المرور؟</a>
                </div>
                
                <button type="submit" class="w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl transition duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-sign-in-alt"></i>
                    تسجيل الدخول
                </button>
            </form>
            
            <!-- Divider -->
            <div class="flex items-center my-4 sm:my-6">
                <div class="flex-1 border-t border-gray-200"></div>
                <span class="px-4 text-sm text-gray-500">أو</span>
                <div class="flex-1 border-t border-gray-200"></div>
            </div>
            
            <!-- Register Link -->
            <p class="text-center text-gray-600 text-sm sm:text-base">
                ليس لديك حساب؟
                <a href="{{ route('register') }}" class="text-indigo-600 hover:text-indigo-700 font-semibold">سجّل الآن</a>
            </p>
        </div>
    </div>
    
    <!-- Top Banner for Mobile -->
    <div class="lg:hidden gradient-bg py-8 px-4 text-center text-white order-1">
        <i class="fas fa-school text-5xl mb-3 opacity-90"></i>
        <h2 class="text-xl font-bold">منصة المدرسة التعليمية</h2>
        <p class="text-sm text-indigo-200 mt-1">نظام متكامل لإدارة العملية التعليمية</p>
    </div>
    
    <!-- Right Side - Illustration (Desktop Only) -->
    <div class="hidden lg:flex w-1/2 gradient-bg items-center justify-center p-12 order-2">
        <div class="text-center text-white">
            <i class="fas fa-school text-9xl mb-8 opacity-90"></i>
            <h2 class="text-4xl font-bold mb-4">منصة المدرسة التعليمية</h2>
            <p class="text-xl text-indigo-200 max-w-md mx-auto">
                نظام متكامل لإدارة العملية التعليمية يربط بين الإدارة والمعلمين وأولياء الأمور
            </p>
            
            <div class="mt-12 grid grid-cols-3 gap-6">
                <div class="p-4 bg-white/10 rounded-xl backdrop-blur">
                    <i class="fas fa-user-graduate text-3xl mb-2"></i>
                    <p class="text-sm">متابعة الطلاب</p>
                </div>
                <div class="p-4 bg-white/10 rounded-xl backdrop-blur">
                    <i class="fas fa-chart-line text-3xl mb-2"></i>
                    <p class="text-sm">تقارير شاملة</p>
                </div>
                <div class="p-4 bg-white/10 rounded-xl backdrop-blur">
                    <i class="fas fa-comments text-3xl mb-2"></i>
                    <p class="text-sm">تواصل فعّال</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
