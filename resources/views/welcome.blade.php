<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="منصة المدرسة التعليمية - نظام متكامل لإدارة العملية التعليمية">
    <meta name="keywords" content="مدرسة, تعليم, إدارة, طلاب, معلمين, أولياء أمور">
    <meta name="author" content="SchoolPla">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>منصة المدرسة التعليمية | SchoolPla</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🎓</text></svg>">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        * { font-family: 'Tajawal', sans-serif; }
        
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .hero-pattern {
            background-color: #667eea;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        
        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .card-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes fade-in-up {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-fade-in-up {
            animation: fade-in-up 0.8s ease-out forwards;
        }
        
        .delay-100 { animation-delay: 0.1s; opacity: 0; }
        .delay-200 { animation-delay: 0.2s; opacity: 0; }
        .delay-300 { animation-delay: 0.3s; opacity: 0; }
        .delay-400 { animation-delay: 0.4s; opacity: 0; }
        .delay-500 { animation-delay: 0.5s; opacity: 0; }
        
        .blob {
            border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
            animation: blob 8s ease-in-out infinite;
        }
        
        @keyframes blob {
            0%, 100% { border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%; }
            25% { border-radius: 58% 42% 75% 25% / 76% 46% 54% 24%; }
            50% { border-radius: 50% 50% 33% 67% / 55% 27% 73% 45%; }
            75% { border-radius: 33% 67% 58% 42% / 63% 68% 32% 37%; }
        }
        
        /* Navbar transition for scroll */
        nav {
            transition: transform 0.3s ease-in-out;
        }
        
        /* Mobile Responsive Improvements */
        @media (max-width: 640px) {
            .hero-stats {
                grid-template-columns: repeat(3, 1fr);
                gap: 0.5rem;
            }
            
            .hero-stats p.text-3xl {
                font-size: 1.25rem;
            }
            
            .hero-stats p.text-sm {
                font-size: 0.625rem;
            }
            
            .services-grid > div {
                padding: 1.5rem;
            }
            
            .footer-grid {
                grid-template-columns: 1fr;
                text-align: center;
            }
            
            .footer-social {
                justify-content: center;
            }
        }
        
        /* Touch-friendly */
        @media (pointer: coarse) {
            a, button {
                min-height: 44px;
                min-width: 44px;
            }
        }
        
        /* Reduced motion preference */
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white/80 backdrop-blur-lg border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 gradient-bg rounded-xl flex items-center justify-center">
                        <i class="fas fa-graduation-cap text-white text-lg"></i>
                    </div>
                    <span class="text-xl font-bold text-gray-800">SchoolPla</span>
                </div>
                
                <!-- Nav Links (Desktop) -->
                <div class="hidden md:flex items-center gap-4 lg:gap-8">
                    <a href="#about" class="text-gray-600 hover:text-indigo-600 transition text-sm lg:text-base">من نحن</a>
                    <a href="#vision" class="text-gray-600 hover:text-indigo-600 transition text-sm lg:text-base">رؤيتنا</a>
                    <a href="#services" class="text-gray-600 hover:text-indigo-600 transition text-sm lg:text-base">خدماتنا</a>
                    <a href="#features" class="text-gray-600 hover:text-indigo-600 transition text-sm lg:text-base">المميزات</a>
                    <a href="#contact" class="text-gray-600 hover:text-indigo-600 transition text-sm lg:text-base">تواصل معنا</a>
                </div>
                
                <!-- Auth Buttons (Desktop) -->
                <div class="hidden md:flex items-center gap-3">
                    @auth
                        @php
                            $dashboardRoute = auth()->user()->isAdmin() ? 'admin.dashboard' : 
                                             (auth()->user()->isTeacher() ? 'teacher.dashboard' : 'parent.dashboard');
                        @endphp
                        <a href="{{ route($dashboardRoute) }}" class="px-5 py-2 gradient-bg text-white rounded-xl hover:shadow-lg transition">
                            لوحة التحكم
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 text-gray-700 hover:text-indigo-600 transition">
                            تسجيل الدخول
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-5 py-2 gradient-bg text-white rounded-xl hover:shadow-lg transition">
                                إنشاء حساب
                            </a>
                        @endif
                    @endauth
                </div>
                
                <!-- Mobile Menu Button -->
                <button type="button" id="mobileNavBtn" class="md:hidden w-10 h-10 flex items-center justify-center rounded-lg hover:bg-gray-100 transition" onclick="toggleMobileNav()">
                    <i class="fas fa-bars text-xl text-gray-600" id="mobileNavIcon"></i>
                </button>
            </div>
        </div>
        
        <!-- Mobile Navigation Menu -->
        <div id="mobileNavMenu" class="md:hidden hidden bg-white border-t border-gray-100 shadow-lg">
            <div class="px-4 py-4 space-y-2">
                <a href="#about" class="block px-4 py-3 rounded-xl text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition" onclick="closeMobileNav()">
                    <i class="fas fa-info-circle ml-2 w-5"></i>من نحن
                </a>
                <a href="#vision" class="block px-4 py-3 rounded-xl text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition" onclick="closeMobileNav()">
                    <i class="fas fa-eye ml-2 w-5"></i>رؤيتنا
                </a>
                <a href="#services" class="block px-4 py-3 rounded-xl text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition" onclick="closeMobileNav()">
                    <i class="fas fa-concierge-bell ml-2 w-5"></i>خدماتنا
                </a>
                <a href="#features" class="block px-4 py-3 rounded-xl text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition" onclick="closeMobileNav()">
                    <i class="fas fa-star ml-2 w-5"></i>المميزات
                </a>
                <a href="#contact" class="block px-4 py-3 rounded-xl text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition" onclick="closeMobileNav()">
                    <i class="fas fa-envelope ml-2 w-5"></i>تواصل معنا
                </a>
                
                <div class="pt-4 mt-4 border-t border-gray-100 space-y-2">
                    @auth
                        @php
                            $dashboardRoute = auth()->user()->isAdmin() ? 'admin.dashboard' : 
                                             (auth()->user()->isTeacher() ? 'teacher.dashboard' : 'parent.dashboard');
                        @endphp
                        <a href="{{ route($dashboardRoute) }}" class="block w-full px-4 py-3 gradient-bg text-white text-center rounded-xl font-semibold">
                            <i class="fas fa-columns ml-2"></i>لوحة التحكم
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="block w-full px-4 py-3 bg-gray-100 text-gray-700 text-center rounded-xl font-semibold hover:bg-gray-200 transition">
                            <i class="fas fa-sign-in-alt ml-2"></i>تسجيل الدخول
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="block w-full px-4 py-3 gradient-bg text-white text-center rounded-xl font-semibold">
                                <i class="fas fa-user-plus ml-2"></i>إنشاء حساب
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative min-h-screen flex items-center hero-pattern gradient-bg overflow-hidden">
        <!-- Decorative Blobs -->
        <div class="absolute top-20 right-20 w-72 h-72 bg-purple-400/30 blob blur-3xl"></div>
        <div class="absolute bottom-20 left-20 w-96 h-96 bg-indigo-400/30 blob blur-3xl" style="animation-delay: -4s;"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-32 relative z-10">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <!-- Text Content -->
                <div class="text-white">
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold leading-tight animate-fade-in-up">
                        منصة المدرسة
                        <span class="block text-yellow-300">التعليمية الذكية</span>
                    </h1>
                    <p class="mt-6 text-xl text-indigo-100 leading-relaxed animate-fade-in-up delay-100">
                        نظام متكامل لإدارة العملية التعليمية يربط بين الإدارة والمعلمين وأولياء الأمور في منصة واحدة سهلة الاستخدام
                    </p>
                    <div class="mt-10 flex flex-wrap gap-4 animate-fade-in-up delay-200">
                        <a href="{{ route('register') }}" class="px-8 py-4 bg-white text-indigo-600 font-bold rounded-xl hover:shadow-2xl transform hover:-translate-y-1 transition-all">
                            <i class="fas fa-rocket ml-2"></i>
                            ابدأ الآن مجاناً
                        </a>
                        <a href="#about" class="px-8 py-4 border-2 border-white/50 text-white font-bold rounded-xl hover:bg-white/10 transition">
                            <i class="fas fa-play-circle ml-2"></i>
                            اكتشف المزيد
                        </a>
                    </div>
                    
                    <!-- Stats -->
                    <div class="mt-12 grid grid-cols-3 gap-6 animate-fade-in-up delay-300">
                        <div class="text-center">
                            <p class="text-3xl font-bold text-white">+1000</p>
                            <p class="text-indigo-200 text-sm">مدرسة</p>
                        </div>
                        <div class="text-center">
                            <p class="text-3xl font-bold text-white">+50K</p>
                            <p class="text-indigo-200 text-sm">طالب</p>
                        </div>
                        <div class="text-center">
                            <p class="text-3xl font-bold text-white">+5K</p>
                            <p class="text-indigo-200 text-sm">معلم</p>
                        </div>
                    </div>
                </div>
                
                <!-- Hero Image/Illustration -->
                <div class="hidden lg:block animate-fade-in-up delay-400">
                    <div class="relative">
                        <div class="w-full h-96 bg-white/10 backdrop-blur rounded-3xl p-8 animate-float">
                            <div class="grid grid-cols-2 gap-4 h-full">
                                <div class="bg-white/20 rounded-2xl p-4 flex flex-col items-center justify-center">
                                    <i class="fas fa-user-graduate text-5xl text-white mb-3"></i>
                                    <p class="text-white font-semibold">متابعة الطلاب</p>
                                </div>
                                <div class="bg-white/20 rounded-2xl p-4 flex flex-col items-center justify-center">
                                    <i class="fas fa-chart-line text-5xl text-white mb-3"></i>
                                    <p class="text-white font-semibold">تقارير ذكية</p>
                                </div>
                                <div class="bg-white/20 rounded-2xl p-4 flex flex-col items-center justify-center">
                                    <i class="fas fa-comments text-5xl text-white mb-3"></i>
                                    <p class="text-white font-semibold">تواصل فعّال</p>
                                </div>
                                <div class="bg-white/20 rounded-2xl p-4 flex flex-col items-center justify-center">
                                    <i class="fas fa-calendar-check text-5xl text-white mb-3"></i>
                                    <p class="text-white font-semibold">جدول منظم</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Wave Separator -->
        <div class="absolute bottom-0 left-0 right-0">
            <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0 120L60 105C120 90 240 60 360 45C480 30 600 30 720 37.5C840 45 960 60 1080 67.5C1200 75 1320 75 1380 75L1440 75V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0Z" fill="#f9fafb"/>
            </svg>
        </div>
    </section>

    <!-- About Us Section -->
    <section id="about" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <!-- Image/Illustration Side -->
                <div class="relative">
                    <div class="absolute -top-4 -right-4 w-72 h-72 bg-indigo-100 rounded-full blur-3xl opacity-60"></div>
                    <div class="relative bg-white rounded-3xl p-8 shadow-xl">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl p-6 text-white text-center">
                                <i class="fas fa-school text-4xl mb-3"></i>
                                <p class="text-3xl font-bold">15+</p>
                                <p class="text-sm opacity-80">سنة خبرة</p>
                            </div>
                            <div class="bg-gradient-to-br from-green-500 to-teal-600 rounded-2xl p-6 text-white text-center">
                                <i class="fas fa-award text-4xl mb-3"></i>
                                <p class="text-3xl font-bold">50+</p>
                                <p class="text-sm opacity-80">جائزة تميز</p>
                            </div>
                            <div class="bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl p-6 text-white text-center">
                                <i class="fas fa-users text-4xl mb-3"></i>
                                <p class="text-3xl font-bold">100K+</p>
                                <p class="text-sm opacity-80">مستخدم نشط</p>
                            </div>
                            <div class="bg-gradient-to-br from-rose-500 to-pink-600 rounded-2xl p-6 text-white text-center">
                                <i class="fas fa-star text-4xl mb-3"></i>
                                <p class="text-3xl font-bold">4.9</p>
                                <p class="text-sm opacity-80">تقييم المستخدمين</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Text Content -->
                <div>
                    <span class="text-indigo-600 font-semibold text-lg">من نحن</span>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mt-2 mb-6">
                        نبني مستقبل التعليم الرقمي
                    </h2>
                    <p class="text-gray-600 leading-relaxed mb-6">
                        منصة SchoolPla هي نتاج سنوات من الخبرة في مجال التعليم والتقنية. نسعى لتقديم حلول مبتكرة تسهّل العملية التعليمية وتعزز التواصل بين جميع أطراف المنظومة التعليمية.
                    </p>
                    <p class="text-gray-600 leading-relaxed mb-8">
                        فريقنا يضم نخبة من المتخصصين في التعليم والبرمجة وتجربة المستخدم، نعمل معاً لتحقيق هدف واحد: جعل التعليم أسهل وأكثر فعالية للجميع.
                    </p>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-check text-green-600"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800">فريق متخصص</h4>
                                <p class="text-sm text-gray-500">خبراء في التعليم والتقنية</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-headset text-blue-600"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800">دعم متواصل</h4>
                                <p class="text-sm text-gray-500">على مدار الساعة</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Vision & Mission Section -->
    <section id="vision" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="text-indigo-600 font-semibold text-lg">رؤيتنا ورسالتنا</span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mt-2">نحو تعليم أفضل للجميع</h2>
            </div>
            
            <div class="grid md:grid-cols-2 gap-8">
                <!-- Vision Card -->
                <div class="relative group">
                    <div class="absolute inset-0 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-3xl transform -rotate-2 group-hover:rotate-0 transition-transform"></div>
                    <div class="relative bg-white rounded-3xl p-10 shadow-lg border border-gray-100">
                        <div class="w-16 h-16 bg-indigo-100 rounded-2xl flex items-center justify-center mb-6">
                            <i class="fas fa-eye text-3xl text-indigo-600"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-4">رؤيتنا</h3>
                        <p class="text-gray-600 leading-relaxed text-lg">
                            أن نكون المنصة التعليمية الرائدة في المنطقة العربية، ونساهم في بناء جيل متعلم ومبدع قادر على مواجهة تحديات المستقبل، من خلال توفير أدوات تقنية متطورة تخدم العملية التعليمية.
                        </p>
                        <div class="mt-6 pt-6 border-t border-gray-100">
                            <div class="flex items-center gap-4 text-gray-500">
                                <span class="flex items-center gap-2">
                                    <i class="fas fa-globe"></i>
                                    انتشار عالمي
                                </span>
                                <span class="flex items-center gap-2">
                                    <i class="fas fa-rocket"></i>
                                    ابتكار مستمر
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Mission Card -->
                <div class="relative group">
                    <div class="absolute inset-0 bg-gradient-to-r from-green-600 to-teal-600 rounded-3xl transform rotate-2 group-hover:rotate-0 transition-transform"></div>
                    <div class="relative bg-white rounded-3xl p-10 shadow-lg border border-gray-100">
                        <div class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center mb-6">
                            <i class="fas fa-bullseye text-3xl text-green-600"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-4">رسالتنا</h3>
                        <p class="text-gray-600 leading-relaxed text-lg">
                            تمكين المؤسسات التعليمية من تحقيق أهدافها من خلال توفير منصة شاملة وسهلة الاستخدام تربط بين الإدارة والمعلمين وأولياء الأمور، وتوفر البيانات والتحليلات اللازمة لاتخاذ قرارات مدروسة.
                        </p>
                        <div class="mt-6 pt-6 border-t border-gray-100">
                            <div class="flex items-center gap-4 text-gray-500">
                                <span class="flex items-center gap-2">
                                    <i class="fas fa-heart"></i>
                                    خدمة متميزة
                                </span>
                                <span class="flex items-center gap-2">
                                    <i class="fas fa-shield-alt"></i>
                                    أمان وموثوقية
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Values -->
            <div class="mt-16">
                <h3 class="text-2xl font-bold text-gray-800 text-center mb-10">قيمنا الأساسية</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <div class="text-center p-6 bg-gray-50 rounded-2xl hover:bg-indigo-50 transition">
                        <div class="w-14 h-14 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-lightbulb text-2xl text-indigo-600"></i>
                        </div>
                        <h4 class="font-bold text-gray-800">الابتكار</h4>
                        <p class="text-sm text-gray-500 mt-2">نبحث دائماً عن حلول جديدة</p>
                    </div>
                    <div class="text-center p-6 bg-gray-50 rounded-2xl hover:bg-green-50 transition">
                        <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-handshake text-2xl text-green-600"></i>
                        </div>
                        <h4 class="font-bold text-gray-800">الشراكة</h4>
                        <p class="text-sm text-gray-500 mt-2">نعمل يداً بيد مع عملائنا</p>
                    </div>
                    <div class="text-center p-6 bg-gray-50 rounded-2xl hover:bg-amber-50 transition">
                        <div class="w-14 h-14 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-gem text-2xl text-amber-600"></i>
                        </div>
                        <h4 class="font-bold text-gray-800">الجودة</h4>
                        <p class="text-sm text-gray-500 mt-2">نلتزم بأعلى المعايير</p>
                    </div>
                    <div class="text-center p-6 bg-gray-50 rounded-2xl hover:bg-rose-50 transition">
                        <div class="w-14 h-14 bg-rose-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-users text-2xl text-rose-600"></i>
                        </div>
                        <h4 class="font-bold text-gray-800">المجتمع</h4>
                        <p class="text-sm text-gray-500 mt-2">نبني مجتمعاً تعليمياً متكاملاً</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="text-indigo-600 font-semibold text-lg">خدماتنا</span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mt-2">حلول شاملة لاحتياجاتك</h2>
                <p class="mt-4 text-gray-600 max-w-2xl mx-auto">نقدم مجموعة متكاملة من الخدمات التي تغطي جميع جوانب العملية التعليمية</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Service 1 -->
                <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 hover:shadow-xl transition-shadow">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-graduation-cap text-2xl text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">نظام إدارة المدرسة</h3>
                    <p class="text-gray-600 mb-6">نظام شامل لإدارة جميع جوانب المدرسة من الطلاب والمعلمين إلى الفصول والمواد الدراسية.</p>
                    <ul class="space-y-2 text-gray-500 text-sm">
                        <li class="flex items-center gap-2"><i class="fas fa-check text-green-500"></i> إدارة بيانات الطلاب</li>
                        <li class="flex items-center gap-2"><i class="fas fa-check text-green-500"></i> إدارة المعلمين والموظفين</li>
                        <li class="flex items-center gap-2"><i class="fas fa-check text-green-500"></i> تنظيم الفصول والجداول</li>
                    </ul>
                </div>
                
                <!-- Service 2 -->
                <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 hover:shadow-xl transition-shadow">
                    <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-teal-600 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-chart-pie text-2xl text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">التقارير والتحليلات</h3>
                    <p class="text-gray-600 mb-6">تقارير تفصيلية وتحليلات ذكية تساعدك على فهم أداء المدرسة واتخاذ قرارات مدروسة.</p>
                    <ul class="space-y-2 text-gray-500 text-sm">
                        <li class="flex items-center gap-2"><i class="fas fa-check text-green-500"></i> تقارير الأداء الأكاديمي</li>
                        <li class="flex items-center gap-2"><i class="fas fa-check text-green-500"></i> إحصائيات الحضور</li>
                        <li class="flex items-center gap-2"><i class="fas fa-check text-green-500"></i> تحليل البيانات المتقدم</li>
                    </ul>
                </div>
                
                <!-- Service 3 -->
                <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 hover:shadow-xl transition-shadow">
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-comments text-2xl text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">منصة التواصل</h3>
                    <p class="text-gray-600 mb-6">نظام متكامل للتواصل بين المدرسة وأولياء الأمور يضمن متابعة فعّالة لمستوى الطلاب.</p>
                    <ul class="space-y-2 text-gray-500 text-sm">
                        <li class="flex items-center gap-2"><i class="fas fa-check text-green-500"></i> رسائل مباشرة</li>
                        <li class="flex items-center gap-2"><i class="fas fa-check text-green-500"></i> إشعارات فورية</li>
                        <li class="flex items-center gap-2"><i class="fas fa-check text-green-500"></i> تقارير دورية لأولياء الأمور</li>
                    </ul>
                </div>
            </div>
            
            <!-- Additional Services -->
            <div class="mt-12 grid md:grid-cols-4 gap-6">
                <div class="bg-white rounded-2xl p-6 text-center border border-gray-100">
                    <i class="fas fa-calendar-alt text-3xl text-indigo-600 mb-3"></i>
                    <h4 class="font-semibold text-gray-800">إدارة الجداول</h4>
                </div>
                <div class="bg-white rounded-2xl p-6 text-center border border-gray-100">
                    <i class="fas fa-clipboard-check text-3xl text-green-600 mb-3"></i>
                    <h4 class="font-semibold text-gray-800">تتبع الحضور</h4>
                </div>
                <div class="bg-white rounded-2xl p-6 text-center border border-gray-100">
                    <i class="fas fa-tasks text-3xl text-amber-600 mb-3"></i>
                    <h4 class="font-semibold text-gray-800">إدارة الواجبات</h4>
                </div>
                <div class="bg-white rounded-2xl p-6 text-center border border-gray-100">
                    <i class="fas fa-file-invoice text-3xl text-rose-600 mb-3"></i>
                    <h4 class="font-semibold text-gray-800">الشهادات والتقارير</h4>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800">مميزات المنصة</h2>
                <p class="mt-4 text-xl text-gray-600">كل ما تحتاجه لإدارة مدرستك بكفاءة عالية</p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-white rounded-2xl p-8 shadow-sm card-hover border border-gray-100">
                    <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-user-graduate text-2xl text-blue-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">إدارة الطلاب</h3>
                    <p class="text-gray-600">متابعة شاملة لبيانات الطلاب، الحضور، الدرجات، والسلوك في مكان واحد</p>
                </div>
                
                <!-- Feature 2 -->
                <div class="bg-white rounded-2xl p-8 shadow-sm card-hover border border-gray-100">
                    <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-chalkboard-teacher text-2xl text-green-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">أدوات المعلم</h3>
                    <p class="text-gray-600">تسجيل الحضور، إدخال الدرجات، إنشاء الواجبات، وتتبع أداء الطلاب بسهولة</p>
                </div>
                
                <!-- Feature 3 -->
                <div class="bg-white rounded-2xl p-8 shadow-sm card-hover border border-gray-100">
                    <div class="w-14 h-14 bg-purple-100 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-users text-2xl text-purple-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">بوابة أولياء الأمور</h3>
                    <p class="text-gray-600">متابعة مستوى الأبناء، التواصل مع المعلمين، واستلام الإشعارات الفورية</p>
                </div>
                
                <!-- Feature 4 -->
                <div class="bg-white rounded-2xl p-8 shadow-sm card-hover border border-gray-100">
                    <div class="w-14 h-14 bg-amber-100 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-chart-bar text-2xl text-amber-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">تقارير وإحصائيات</h3>
                    <p class="text-gray-600">تقارير تفصيلية وإحصائيات شاملة لاتخاذ قرارات مبنية على البيانات</p>
                </div>
                
                <!-- Feature 5 -->
                <div class="bg-white rounded-2xl p-8 shadow-sm card-hover border border-gray-100">
                    <div class="w-14 h-14 bg-rose-100 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-bell text-2xl text-rose-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">إشعارات ذكية</h3>
                    <p class="text-gray-600">نظام إشعارات متقدم لإبقاء الجميع على اطلاع بآخر المستجدات</p>
                </div>
                
                <!-- Feature 6 -->
                <div class="bg-white rounded-2xl p-8 shadow-sm card-hover border border-gray-100">
                    <div class="w-14 h-14 bg-indigo-100 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-shield-alt text-2xl text-indigo-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">أمان وخصوصية</h3>
                    <p class="text-gray-600">حماية متقدمة للبيانات مع صلاحيات مخصصة لكل مستخدم</p>
                </div>
            </div>
        </div>
    </section>

    <!-- User Types Section -->
    <section id="users" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800">لكل مستخدم تجربة مخصصة</h2>
                <p class="mt-4 text-xl text-gray-600">واجهات مصممة خصيصاً لتلبية احتياجات كل فئة</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Admin -->
                <div class="relative group">
                    <div class="absolute inset-0 gradient-bg rounded-3xl transform rotate-3 group-hover:rotate-6 transition-transform"></div>
                    <div class="relative bg-white rounded-3xl p-8 shadow-xl">
                        <div class="w-20 h-20 gradient-bg rounded-2xl flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-user-shield text-3xl text-white"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 text-center mb-4">الإدارة</h3>
                        <ul class="space-y-3 text-gray-600">
                            <li class="flex items-center gap-3">
                                <i class="fas fa-check text-green-500"></i>
                                إدارة المعلمين والطلاب
                            </li>
                            <li class="flex items-center gap-3">
                                <i class="fas fa-check text-green-500"></i>
                                إنشاء الفصول والمواد
                            </li>
                            <li class="flex items-center gap-3">
                                <i class="fas fa-check text-green-500"></i>
                                تقارير شاملة
                            </li>
                            <li class="flex items-center gap-3">
                                <i class="fas fa-check text-green-500"></i>
                                إعدادات النظام
                            </li>
                        </ul>
                    </div>
                </div>
                
                <!-- Teacher -->
                <div class="relative group">
                    <div class="absolute inset-0 bg-green-500 rounded-3xl transform rotate-3 group-hover:rotate-6 transition-transform"></div>
                    <div class="relative bg-white rounded-3xl p-8 shadow-xl">
                        <div class="w-20 h-20 bg-green-500 rounded-2xl flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-chalkboard-teacher text-3xl text-white"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 text-center mb-4">المعلم</h3>
                        <ul class="space-y-3 text-gray-600">
                            <li class="flex items-center gap-3">
                                <i class="fas fa-check text-green-500"></i>
                                تسجيل الحضور
                            </li>
                            <li class="flex items-center gap-3">
                                <i class="fas fa-check text-green-500"></i>
                                إدخال الدرجات
                            </li>
                            <li class="flex items-center gap-3">
                                <i class="fas fa-check text-green-500"></i>
                                إنشاء الواجبات
                            </li>
                            <li class="flex items-center gap-3">
                                <i class="fas fa-check text-green-500"></i>
                                التواصل مع أولياء الأمور
                            </li>
                        </ul>
                    </div>
                </div>
                
                <!-- Parent -->
                <div class="relative group">
                    <div class="absolute inset-0 bg-blue-500 rounded-3xl transform rotate-3 group-hover:rotate-6 transition-transform"></div>
                    <div class="relative bg-white rounded-3xl p-8 shadow-xl">
                        <div class="w-20 h-20 bg-blue-500 rounded-2xl flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-user-friends text-3xl text-white"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 text-center mb-4">ولي الأمر</h3>
                        <ul class="space-y-3 text-gray-600">
                            <li class="flex items-center gap-3">
                                <i class="fas fa-check text-green-500"></i>
                                متابعة الأبناء
                            </li>
                            <li class="flex items-center gap-3">
                                <i class="fas fa-check text-green-500"></i>
                                عرض الدرجات والحضور
                            </li>
                            <li class="flex items-center gap-3">
                                <i class="fas fa-check text-green-500"></i>
                                الجدول الدراسي
                            </li>
                            <li class="flex items-center gap-3">
                                <i class="fas fa-check text-green-500"></i>
                                التواصل مع المعلمين
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 gradient-bg relative overflow-hidden">
        <div class="absolute inset-0 hero-pattern opacity-50"></div>
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">
                جاهز لتحويل مدرستك رقمياً؟
            </h2>
            <p class="text-xl text-indigo-100 mb-10">
                انضم إلى آلاف المدارس التي تستخدم منصتنا لتحسين العملية التعليمية
            </p>
            <a href="{{ route('register') }}" class="inline-block px-10 py-4 bg-white text-indigo-600 font-bold text-lg rounded-xl hover:shadow-2xl transform hover:-translate-y-1 transition-all">
                <i class="fas fa-rocket ml-2"></i>
                ابدأ تجربتك المجانية
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contact" class="bg-gray-900 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-12">
                <!-- Logo & Description -->
                <div class="md:col-span-1">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 gradient-bg rounded-xl flex items-center justify-center">
                            <i class="fas fa-graduation-cap text-white text-xl"></i>
                        </div>
                        <span class="text-2xl font-bold">SchoolPla</span>
                    </div>
                    <p class="text-gray-400 leading-relaxed">
                        منصة تعليمية متكاملة تهدف إلى تسهيل العملية التعليمية وتعزيز التواصل بين جميع الأطراف
                    </p>
                    <div class="flex gap-4 mt-6">
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-indigo-600 transition">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-indigo-600 transition">
                            <i class="fab fa-facebook"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-indigo-600 transition">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-indigo-600 transition">
                            <i class="fab fa-linkedin"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div>
                    <h4 class="font-bold text-lg mb-6">روابط سريعة</h4>
                    <ul class="space-y-3 text-gray-400">
                        <li><a href="#about" class="hover:text-white transition">من نحن</a></li>
                        <li><a href="#vision" class="hover:text-white transition">رؤيتنا</a></li>
                        <li><a href="#services" class="hover:text-white transition">خدماتنا</a></li>
                        <li><a href="#features" class="hover:text-white transition">المميزات</a></li>
                    </ul>
                </div>
                
                <!-- Account Links -->
                <div>
                    <h4 class="font-bold text-lg mb-6">حسابك</h4>
                    <ul class="space-y-3 text-gray-400">
                        <li><a href="{{ route('login') }}" class="hover:text-white transition">تسجيل الدخول</a></li>
                        <li><a href="{{ route('register') }}" class="hover:text-white transition">إنشاء حساب</a></li>
                        <li><a href="#" class="hover:text-white transition">استعادة كلمة المرور</a></li>
                        <li><a href="#" class="hover:text-white transition">الدعم الفني</a></li>
                    </ul>
                </div>
                
                <!-- Contact -->
                <div>
                    <h4 class="font-bold text-lg mb-6">تواصل معنا</h4>
                    <ul class="space-y-4 text-gray-400">
                        <li class="flex items-start gap-3">
                            <i class="fas fa-map-marker-alt mt-1 text-indigo-400"></i>
                            <span>المملكة العربية السعودية<br>الرياض - حي العليا</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fas fa-envelope text-indigo-400"></i>
                            info@schoolpla.com
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fas fa-phone text-indigo-400"></i>
                            <span dir="ltr">+966 XX XXX XXXX</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fas fa-clock text-indigo-400"></i>
                            الأحد - الخميس: 8ص - 4م
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Newsletter -->
            <div class="mt-12 pt-8 border-t border-gray-800">
                <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                    <div>
                        <h4 class="font-bold text-lg">اشترك في نشرتنا البريدية</h4>
                        <p class="text-gray-400 text-sm mt-1">احصل على آخر الأخبار والتحديثات</p>
                    </div>
                    <form class="flex gap-2 w-full md:w-auto">
                        <input type="email" placeholder="بريدك الإلكتروني" class="px-4 py-3 bg-gray-800 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 w-full md:w-64">
                        <button type="submit" class="px-6 py-3 gradient-bg rounded-lg font-semibold hover:shadow-lg transition whitespace-nowrap">
                            اشترك الآن
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="border-t border-gray-800 mt-8 pt-8 flex flex-col md:flex-row items-center justify-between gap-4 text-gray-500 text-sm">
                <p>© {{ date('Y') }} SchoolPla. جميع الحقوق محفوظة.</p>
                <div class="flex gap-6">
                    <a href="#" class="hover:text-white transition">سياسة الخصوصية</a>
                    <a href="#" class="hover:text-white transition">شروط الاستخدام</a>
                    <a href="#" class="hover:text-white transition">سياسة ملفات تعريف الارتباط</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Mobile Navigation Toggle
        function toggleMobileNav() {
            const menu = document.getElementById('mobileNavMenu');
            const icon = document.getElementById('mobileNavIcon');
            
            menu.classList.toggle('hidden');
            
            if (menu.classList.contains('hidden')) {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            } else {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            }
        }
        
        function closeMobileNav() {
            const menu = document.getElementById('mobileNavMenu');
            const icon = document.getElementById('mobileNavIcon');
            
            menu.classList.add('hidden');
            icon.classList.remove('fa-times');
            icon.classList.add('fa-bars');
        }
        
        // Close mobile nav on window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 768) {
                closeMobileNav();
            }
        });
        
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    // Close mobile nav first
                    closeMobileNav();
                    
                    // Scroll to target with offset for fixed navbar
                    const offset = 80;
                    const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - offset;
                    
                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                }
            });
        });
        
        // Hide navbar on scroll down, show on scroll up (mobile only)
        let lastScroll = 0;
        const navbar = document.querySelector('nav');
        
        window.addEventListener('scroll', function() {
            if (window.innerWidth < 768) {
                const currentScroll = window.pageYOffset;
                
                if (currentScroll > lastScroll && currentScroll > 100) {
                    navbar.style.transform = 'translateY(-100%)';
                } else {
                    navbar.style.transform = 'translateY(0)';
                }
                
                lastScroll = currentScroll;
            } else {
                navbar.style.transform = 'translateY(0)';
            }
        });
    </script>
</body>
</html>
