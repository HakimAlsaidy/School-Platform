<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تم التسجيل بنجاح - SchoolPla</title>
    
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
        
        @keyframes checkmark {
            0% { transform: scale(0); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }
        
        .animate-checkmark {
            animation: checkmark 0.5s ease-out;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="max-w-lg mx-auto px-4 py-12">
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8 text-center">
            <!-- Success Icon -->
            <div class="w-24 h-24 bg-green-100 rounded-full mx-auto mb-6 flex items-center justify-center animate-checkmark">
                <i class="fas fa-check text-4xl text-green-500"></i>
            </div>

            <!-- Title -->
            <h1 class="text-2xl font-bold text-gray-800 mb-3">تم إرسال طلبك بنجاح!</h1>
            <p class="text-gray-600 mb-6">
                شكراً لتسجيل مدرسة <strong>{{ session('school_name') }}</strong> في منصة SchoolPla
            </p>

            <!-- Steps -->
            <div class="bg-gray-50 rounded-xl p-6 mb-6 text-right">
                <h3 class="font-bold text-gray-800 mb-4">الخطوات التالية:</h3>
                <div class="space-y-3">
                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center text-sm font-bold shrink-0 mt-0.5">1</div>
                        <p class="text-gray-700">سيتم مراجعة طلبك من قبل فريق الإدارة</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center text-sm font-bold shrink-0 mt-0.5">2</div>
                        <p class="text-gray-700">ستصلك رسالة عند الموافقة على طلبك</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center text-sm font-bold shrink-0 mt-0.5">3</div>
                        <p class="text-gray-700">بعد التفعيل، يمكنك الدخول من خلال الرابط:</p>
                    </div>
                </div>
            </div>

            <!-- School URL -->
            <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl p-4 mb-6">
                <p class="text-sm text-gray-600 mb-2">رابط مدرستك سيكون:</p>
                <p class="font-mono text-indigo-600 font-bold text-lg" dir="ltr">
                    {{ session('subdomain') }}.schoolpla.com
                </p>
            </div>

            <!-- Info Box -->
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6 text-right">
                <div class="flex items-start gap-3">
                    <i class="fas fa-info-circle text-amber-500 mt-1"></i>
                    <div>
                        <p class="text-amber-800 font-medium">ملاحظة مهمة</p>
                        <p class="text-amber-700 text-sm">
                            عادة ما تتم مراجعة الطلبات خلال 24-48 ساعة في أيام العمل.
                            احتفظ ببيانات تسجيل الدخول الخاصة بك.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('home') }}" 
                   class="flex-1 px-6 py-3 border border-gray-200 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition text-center">
                    <i class="fas fa-home ml-2"></i>
                    الصفحة الرئيسية
                </a>
                <a href="{{ route('login') }}" 
                   class="flex-1 px-6 py-3 gradient-bg text-white rounded-xl font-medium hover:opacity-90 transition text-center">
                    <i class="fas fa-sign-in-alt ml-2"></i>
                    تسجيل الدخول
                </a>
            </div>
        </div>

        <!-- Footer -->
        <p class="text-center text-gray-500 text-sm mt-6">
            هل لديك سؤال؟ 
            <a href="#" class="text-indigo-600 hover:underline">تواصل معنا</a>
        </p>
    </div>
</body>
</html>
