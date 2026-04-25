<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Super Admin') - منصة إدارة المدارس</title>
    
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
    <!-- Navbar -->
    <nav class="bg-white border-b border-gray-200 fixed top-0 left-0 right-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center gap-4">
                    <!-- Logo -->
                    <a href="{{ route('superadmin.dashboard') }}" class="flex items-center gap-3">
                        <div class="w-10 h-10 gradient-bg rounded-xl flex items-center justify-center">
                            <i class="fas fa-crown text-white"></i>
                        </div>
                        <span class="font-bold text-xl text-gray-800">Super Admin</span>
                    </a>
                </div>

                <div class="flex items-center gap-4">
                    <!-- روابط سريعة -->
                    <a href="{{ route('superadmin.dashboard') }}" class="text-gray-600 hover:text-indigo-600 transition {{ request()->routeIs('superadmin.dashboard') ? 'text-indigo-600 font-medium' : '' }}">
                        <i class="fas fa-home ml-1"></i>
                        الرئيسية
                    </a>
                    <a href="{{ route('superadmin.schools.index') }}" class="text-gray-600 hover:text-indigo-600 transition {{ request()->routeIs('superadmin.schools.*') ? 'text-indigo-600 font-medium' : '' }}">
                        <i class="fas fa-school ml-1"></i>
                        المدارس
                    </a>
                    <a href="{{ route('superadmin.subscriptions.index') }}" class="text-gray-600 hover:text-indigo-600 transition {{ request()->routeIs('superadmin.subscriptions.*') ? 'text-indigo-600 font-medium' : '' }}">
                        <i class="fas fa-credit-card ml-1"></i>
                        الاشتراكات
                    </a>
                    <a href="{{ route('superadmin.reports.index') }}" class="text-gray-600 hover:text-indigo-600 transition {{ request()->routeIs('superadmin.reports.*') ? 'text-indigo-600 font-medium' : '' }}">
                        <i class="fas fa-chart-bar ml-1"></i>
                        التقارير
                    </a>
                    <a href="{{ route('superadmin.settings.index') }}" class="text-gray-600 hover:text-indigo-600 transition {{ request()->routeIs('superadmin.settings.*') ? 'text-indigo-600 font-medium' : '' }}">
                        <i class="fas fa-cog ml-1"></i>
                        الإعدادات
                    </a>
                    
                    <!-- المستخدم -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-2 text-gray-600 hover:text-gray-800">
                            <img src="{{ auth()->user()->avatar_url }}" alt="" class="w-8 h-8 rounded-full">
                            <span class="hidden sm:inline">{{ auth()->user()->name }}</span>
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        
                        <div x-show="open" @click.away="open = false" x-cloak
                             class="absolute left-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-2">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full text-right px-4 py-2 text-red-600 hover:bg-red-50 transition">
                                    <i class="fas fa-sign-out-alt ml-2"></i>
                                    تسجيل الخروج
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="pt-20 pb-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700">
                    <i class="fas fa-check-circle ml-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700">
                    <i class="fas fa-exclamation-circle ml-2"></i>
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    @stack('scripts')
</body>
</html>
