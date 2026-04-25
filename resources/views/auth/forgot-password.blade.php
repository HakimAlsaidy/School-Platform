@extends('layouts.app')

@section('title', 'استعادة كلمة المرور - منصة المدرسة التعليمية')

@section('content')
<div class="min-h-screen flex items-center justify-center p-8 bg-gradient-to-br from-indigo-50 to-purple-50">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <!-- Logo -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-indigo-100 rounded-2xl mb-4">
                    <i class="fas fa-key text-4xl text-indigo-600"></i>
                </div>
                <h1 class="text-2xl font-bold text-gray-800">استعادة كلمة المرور</h1>
                <p class="text-gray-500 mt-2">أدخل رقم هاتفك لإرسال رمز التحقق</p>
            </div>
            
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-center">
                    {{ session('success') }}
                </div>
            @endif
            
            <!-- Form -->
            <form action="{{ route('password.email') }}" method="POST" class="space-y-5">
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
                
                <button type="submit" class="w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl transition duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-paper-plane"></i>
                    إرسال رمز التحقق
                </button>
            </form>
            
            <!-- Back to Login -->
            <div class="mt-6 text-center">
                <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-700 flex items-center justify-center gap-2">
                    <i class="fas fa-arrow-right"></i>
                    العودة لتسجيل الدخول
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
