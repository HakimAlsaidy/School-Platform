<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>تسجيل مدرسة جديدة - SchoolPla</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'tajawal': ['Tajawal', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <style>
        body { font-family: 'Tajawal', sans-serif; }
        .gradient-bg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="bg-white border-b border-gray-200 py-4">
            <div class="max-w-6xl mx-auto px-4">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <div class="w-10 h-10 gradient-bg rounded-xl flex items-center justify-center">
                        <i class="fas fa-graduation-cap text-white"></i>
                    </div>
                    <span class="font-bold text-xl text-gray-800">SchoolPla</span>
                </a>
            </div>
        </header>

        <!-- Content -->
        <main class="flex-1 py-8">
            <div class="max-w-4xl mx-auto px-4">
                <!-- Title -->
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">سجّل مدرستك الآن</h1>
                    <p class="text-gray-600">انضم إلى منصة SchoolPla وابدأ في إدارة مدرستك بسهولة</p>
                </div>

                <!-- Steps -->
                <div class="flex items-center justify-center gap-4 mb-8">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-indigo-600 text-white rounded-full flex items-center justify-center text-sm font-bold">1</div>
                        <span class="text-indigo-600 font-medium">معلومات المدرسة</span>
                    </div>
                    <div class="w-16 h-0.5 bg-gray-300"></div>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center text-sm font-bold">2</div>
                        <span class="text-gray-400 font-medium">مراجعة الإدارة</span>
                    </div>
                    <div class="w-16 h-0.5 bg-gray-300"></div>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center text-sm font-bold">3</div>
                        <span class="text-gray-400 font-medium">البدء</span>
                    </div>
                </div>

                <!-- Form -->
                <form action="{{ route('school.register.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Error Message -->
                    @if($errors->has('error'))
                        <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-red-700">
                            <i class="fas fa-exclamation-circle ml-2"></i>
                            {{ $errors->first('error') }}
                        </div>
                    @endif

                    <!-- معلومات المدرسة -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
                            <i class="fas fa-school text-indigo-600"></i>
                            معلومات المدرسة
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    اسم المدرسة <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="school_name" value="{{ old('school_name') }}" 
                                       class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none @error('school_name') border-red-500 @enderror"
                                       placeholder="مثال: مدرسة النور الأهلية"
                                       required>
                                @error('school_name')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    النطاق الفرعي <span class="text-red-500">*</span>
                                </label>
                                <div class="flex items-center border border-gray-200 rounded-xl overflow-hidden @error('subdomain') border-red-500 @enderror">
                                    <input type="text" name="subdomain" value="{{ old('subdomain') }}" 
                                           class="flex-1 px-4 py-3 border-0 focus:ring-0 outline-none font-mono text-left"
                                           dir="ltr"
                                           placeholder="alnoor"
                                           pattern="[a-z0-9-]+"
                                           required>
                                    <span class="px-4 py-3 bg-gray-50 text-gray-500 border-r border-gray-200">.schoolpla.com</span>
                                </div>
                                <p class="text-gray-500 text-sm mt-1">أحرف إنجليزية صغيرة وأرقام فقط</p>
                                @error('subdomain')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    نوع المدرسة <span class="text-red-500">*</span>
                                </label>
                                <select name="type" 
                                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none @error('type') border-red-500 @enderror"
                                        required>
                                    <option value="">اختر النوع</option>
                                    <option value="public" {{ old('type') == 'public' ? 'selected' : '' }}>حكومية</option>
                                    <option value="private" {{ old('type') == 'private' ? 'selected' : '' }}>أهلية</option>
                                    <option value="international" {{ old('type') == 'international' ? 'selected' : '' }}>عالمية</option>
                                </select>
                                @error('type')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    المرحلة التعليمية <span class="text-red-500">*</span>
                                </label>
                                <select name="level" 
                                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none @error('level') border-red-500 @enderror"
                                        required>
                                    <option value="">اختر المرحلة</option>
                                    <option value="elementary" {{ old('level') == 'elementary' ? 'selected' : '' }}>ابتدائي</option>
                                    <option value="middle" {{ old('level') == 'middle' ? 'selected' : '' }}>متوسط</option>
                                    <option value="high" {{ old('level') == 'high' ? 'selected' : '' }}>ثانوي</option>
                                    <option value="all" {{ old('level') == 'all' ? 'selected' : '' }}>جميع المراحل</option>
                                </select>
                                @error('level')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    رقم هاتف المدرسة <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="phone" value="{{ old('phone') }}" 
                                       class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none @error('phone') border-red-500 @enderror"
                                       dir="ltr"
                                       placeholder="05xxxxxxxx"
                                       required>
                                @error('phone')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    البريد الإلكتروني (اختياري)
                                </label>
                                <input type="email" name="email" value="{{ old('email') }}" 
                                       class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none @error('email') border-red-500 @enderror"
                                       dir="ltr"
                                       placeholder="info@school.edu.sa">
                                @error('email')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    عنوان المدرسة (اختياري)
                                </label>
                                <textarea name="address" rows="2"
                                          class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none resize-none @error('address') border-red-500 @enderror"
                                          placeholder="المدينة، الحي، الشارع">{{ old('address') }}</textarea>
                                @error('address')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- حساب المدير -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
                            <i class="fas fa-user-shield text-indigo-600"></i>
                            حساب مدير المدرسة
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    اسم المدير <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="admin_name" value="{{ old('admin_name') }}" 
                                       class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none @error('admin_name') border-red-500 @enderror"
                                       placeholder="الاسم الكامل"
                                       required>
                                @error('admin_name')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    رقم جوال المدير <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="admin_phone" value="{{ old('admin_phone') }}" 
                                       class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none @error('admin_phone') border-red-500 @enderror"
                                       dir="ltr"
                                       placeholder="05xxxxxxxx"
                                       required>
                                <p class="text-gray-500 text-sm mt-1">سيتم استخدامه لتسجيل الدخول</p>
                                @error('admin_phone')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    كلمة المرور <span class="text-red-500">*</span>
                                </label>
                                <input type="password" name="admin_password" 
                                       class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none @error('admin_password') border-red-500 @enderror"
                                       placeholder="••••••••"
                                       minlength="8"
                                       required>
                                @error('admin_password')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    تأكيد كلمة المرور <span class="text-red-500">*</span>
                                </label>
                                <input type="password" name="admin_password_confirmation" 
                                       class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none"
                                       placeholder="••••••••"
                                       required>
                            </div>
                        </div>
                    </div>

                    <!-- Features -->
                    <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-2xl p-6">
                        <h3 class="font-bold text-gray-800 mb-4">ماذا ستحصل عليه؟</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="flex items-center gap-2 text-gray-700">
                                <i class="fas fa-check-circle text-green-500"></i>
                                <span>إدارة الطلاب</span>
                            </div>
                            <div class="flex items-center gap-2 text-gray-700">
                                <i class="fas fa-check-circle text-green-500"></i>
                                <span>إدارة المعلمين</span>
                            </div>
                            <div class="flex items-center gap-2 text-gray-700">
                                <i class="fas fa-check-circle text-green-500"></i>
                                <span>نظام الدرجات</span>
                            </div>
                            <div class="flex items-center gap-2 text-gray-700">
                                <i class="fas fa-check-circle text-green-500"></i>
                                <span>الجدول الدراسي</span>
                            </div>
                            <div class="flex items-center gap-2 text-gray-700">
                                <i class="fas fa-check-circle text-green-500"></i>
                                <span>تسجيل الحضور</span>
                            </div>
                            <div class="flex items-center gap-2 text-gray-700">
                                <i class="fas fa-check-circle text-green-500"></i>
                                <span>الرسائل</span>
                            </div>
                            <div class="flex items-center gap-2 text-gray-700">
                                <i class="fas fa-check-circle text-green-500"></i>
                                <span>التقارير</span>
                            </div>
                            <div class="flex items-center gap-2 text-gray-700">
                                <i class="fas fa-check-circle text-green-500"></i>
                                <span>الإشعارات</span>
                            </div>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <p class="text-gray-500 text-sm">
                            بالتسجيل، أنت توافق على 
                            <a href="#" class="text-indigo-600 hover:underline">شروط الاستخدام</a>
                            و
                            <a href="#" class="text-indigo-600 hover:underline">سياسة الخصوصية</a>
                        </p>
                        <button type="submit" 
                                class="w-full sm:w-auto px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl font-medium hover:opacity-90 transition">
                            <i class="fas fa-paper-plane ml-2"></i>
                            إرسال طلب التسجيل
                        </button>
                    </div>
                </form>

                <!-- Already have account -->
                <div class="text-center mt-8">
                    <p class="text-gray-600">
                        لديك حساب بالفعل؟
                        <a href="{{ route('login') }}" class="text-indigo-600 hover:underline font-medium">تسجيل الدخول</a>
                    </p>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 py-4">
            <div class="max-w-6xl mx-auto px-4 text-center text-gray-500 text-sm">
                © {{ date('Y') }} SchoolPla - جميع الحقوق محفوظة
            </div>
        </footer>
    </div>
</body>
</html>
