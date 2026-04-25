@extends('layouts.app')

@section('content')
{{-- Mobile Navbar (shows only on mobile) --}}
@include('layouts.partials.dashboard-mobile-navbar')

<div class="flex min-h-screen">
    <!-- Sidebar (Desktop only) -->
    <aside id="sidebar" class="hidden lg:block w-72 bg-white shadow-xl fixed h-full z-40">
        
        <!-- Logo -->
        <div class="gradient-bg p-6">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center">
                    <i class="fas fa-graduation-cap text-2xl text-indigo-600"></i>
                </div>
                <div>
                    <h1 class="text-white font-bold text-xl">منصة المدرسة</h1>
                    <p class="text-indigo-200 text-sm">لوحة التحكم</p>
                </div>
            </div>
        </div>
        
        <!-- User Info -->
        <div class="p-4 border-b">
            <div class="flex items-center gap-3">
                <img src="{{ auth()->user()->avatar_url }}" alt="Avatar" class="w-10 h-10 rounded-full object-cover">
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-gray-800 truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ auth()->user()->role->display_name ?? 'مستخدم' }}</p>
                </div>
            </div>
        </div>
        
        <!-- Navigation -->
        <nav class="p-4 space-y-1 overflow-y-auto" style="max-height: calc(100vh - 280px);">
            @if(auth()->user()->isAdmin())
                @include('layouts.partials.admin-sidebar')
            @elseif(auth()->user()->isTeacher())
                @include('layouts.partials.teacher-sidebar')
            @elseif(auth()->user()->isParent())
                @include('layouts.partials.parent-sidebar')
            @endif
            
            <!-- Shared Links -->
            <div class="pt-4 mt-4 border-t">
                <a href="{{ route('messages.inbox') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-gray-600 hover:text-indigo-600 {{ request()->routeIs('messages.*') ? 'active bg-indigo-100 text-indigo-700' : '' }}">
                    <i class="fas fa-envelope w-5"></i>
                    <span>الرسائل</span>
                    @if(auth()->user()->unread_messages_count > 0)
                        <span class="mr-auto bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">
                            {{ auth()->user()->unread_messages_count }}
                        </span>
                    @endif
                </a>
            </div>
        </nav>
        
        <!-- Logout -->
        <div class="absolute bottom-0 left-0 right-0 p-4 border-t bg-white">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>تسجيل الخروج</span>
                </button>
            </form>
        </div>
    </aside>
    
    <!-- Main Content -->
    <main class="flex-1 lg:mr-72 min-h-screen">
        <!-- Top Bar (Desktop only) -->
        <header class="hidden lg:block sticky top-0 z-30 {{ request()->routeIs('admin.*') ? 'bg-white/80 backdrop-blur-lg border-b border-gray-100' : 'bg-white shadow-sm' }}">
            @if(request()->routeIs('admin.*'))
                <div class="flex items-center justify-between px-6 py-3">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                            <div class="w-9 h-9 gradient-bg rounded-lg flex items-center justify-center">
                                <i class="fas fa-graduation-cap text-white text-sm"></i>
                            </div>
                            <span class="font-bold text-gray-800">SchoolPla</span>
                        </a>
                    </div>

                    <nav class="hidden xl:flex items-center gap-6">
                        <a href="{{ route('admin.dashboard') }}" class="text-sm {{ request()->routeIs('admin.dashboard') ? 'text-indigo-600 font-semibold' : 'text-gray-600 hover:text-indigo-600' }} transition">الرئيسية</a>
                        <a href="{{ route('admin.students.index') }}" class="text-sm {{ request()->routeIs('admin.students.*') ? 'text-indigo-600 font-semibold' : 'text-gray-600 hover:text-indigo-600' }} transition">الطلاب</a>
                        <a href="{{ route('admin.teachers.index') }}" class="text-sm {{ request()->routeIs('admin.teachers.*') ? 'text-indigo-600 font-semibold' : 'text-gray-600 hover:text-indigo-600' }} transition">المعلمين</a>
                        <a href="{{ route('admin.schedules.index') }}" class="text-sm {{ request()->routeIs('admin.schedules.*') ? 'text-indigo-600 font-semibold' : 'text-gray-600 hover:text-indigo-600' }} transition">الجدول</a>
                        <a href="{{ route('admin.reports.index') }}" class="text-sm {{ request()->routeIs('admin.reports.*') ? 'text-indigo-600 font-semibold' : 'text-gray-600 hover:text-indigo-600' }} transition">التقارير</a>
                    </nav>

                    <div class="flex items-center gap-3">
                        <!-- Notification Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="relative p-2 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition">
                                <i class="fas fa-bell text-lg"></i>
                                @if(isset($totalNotifications) && $totalNotifications > 0)
                                    <span class="absolute -top-1 -right-1 min-w-[18px] h-[18px] bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center px-1 animate-pulse">
                                        {{ $totalNotifications > 99 ? '99+' : $totalNotifications }}
                                    </span>
                                @endif
                            </button>
                            
                            <!-- Dropdown Menu -->
                            <div x-show="open" @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 transform scale-95"
                                 x-transition:enter-end="opacity-100 transform scale-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 transform scale-100"
                                 x-transition:leave-end="opacity-0 transform scale-95"
                                 class="absolute left-0 mt-2 w-80 bg-white rounded-xl shadow-xl border border-gray-100 z-50 overflow-hidden"
                                 style="display: none;">
                                
                                <div class="p-4 border-b border-gray-100 bg-gradient-to-l from-indigo-50 to-purple-50">
                                    <div class="flex items-center justify-between">
                                        <h4 class="font-bold text-gray-800">الإشعارات</h4>
                                        @if(isset($totalNotifications) && $totalNotifications > 0)
                                            <span class="text-xs text-indigo-600 font-medium">{{ $totalNotifications }} جديد</span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="max-h-80 overflow-y-auto">
                                    @if(isset($pendingUsersCount) && $pendingUsersCount > 0)
                                        <a href="{{ route('admin.pending-users') }}" class="block p-4 hover:bg-gray-50 transition border-b border-gray-50">
                                            <div class="flex items-start gap-3">
                                                <div class="w-10 h-10 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center flex-shrink-0">
                                                    <i class="fas fa-user-clock"></i>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-semibold text-gray-800">طلبات تسجيل جديدة</p>
                                                    <p class="text-xs text-gray-500 mt-0.5">{{ $pendingUsersCount }} طلب بانتظار الموافقة</p>
                                                </div>
                                                <span class="bg-orange-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">
                                                    {{ $pendingUsersCount }}
                                                </span>
                                            </div>
                                        </a>
                                    @endif
                                    
                                    @if(isset($unreadMessagesCount) && $unreadMessagesCount > 0)
                                        <a href="{{ route('messages.inbox') }}" class="block p-4 hover:bg-gray-50 transition border-b border-gray-50">
                                            <div class="flex items-start gap-3">
                                                <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                                                    <i class="fas fa-envelope"></i>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-semibold text-gray-800">رسائل غير مقروءة</p>
                                                    <p class="text-xs text-gray-500 mt-0.5">{{ $unreadMessagesCount }} رسالة جديدة</p>
                                                </div>
                                                <span class="bg-blue-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">
                                                    {{ $unreadMessagesCount }}
                                                </span>
                                            </div>
                                        </a>
                                    @endif
                                    
                                    @if(isset($recentPendingUsers) && $recentPendingUsers->count() > 0)
                                        <div class="px-4 py-2 bg-gray-50">
                                            <p class="text-xs font-semibold text-gray-500">آخر طلبات التسجيل</p>
                                        </div>
                                        @foreach($recentPendingUsers as $pendingUser)
                                            <div class="p-3 hover:bg-gray-50 transition border-b border-gray-50">
                                                <div class="flex items-center gap-3">
                                                    <img src="{{ $pendingUser->avatar_url }}" alt="" class="w-8 h-8 rounded-full object-cover">
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-sm font-medium text-gray-800 truncate">{{ $pendingUser->name }}</p>
                                                        <p class="text-xs text-gray-500">{{ $pendingUser->role?->display_name ?? 'مستخدم' }} • {{ $pendingUser->created_at->diffForHumans() }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                    
                                    @if((!isset($totalNotifications) || $totalNotifications == 0))
                                        <div class="p-8 text-center">
                                            <i class="fas fa-bell-slash text-3xl text-gray-300 mb-2"></i>
                                            <p class="text-sm text-gray-500">لا توجد إشعارات جديدة</p>
                                        </div>
                                    @endif
                                </div>
                                
                                @if(isset($totalNotifications) && $totalNotifications > 0)
                                    <a href="{{ route('admin.pending-users') }}" class="block p-3 text-center text-sm text-indigo-600 hover:bg-indigo-50 transition border-t">
                                        عرض جميع الإشعارات
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="text-sm text-gray-500">
                            <i class="fas fa-calendar-alt ml-1"></i>
                            {{ now()->locale('ar')->translatedFormat('l، d F Y') }}
                        </div>
                        <img src="{{ auth()->user()->avatar_url }}" alt="Avatar" class="w-8 h-8 rounded-full object-cover">
                    </div>
                </div>
            @else
                <div class="flex items-center justify-between px-6 py-4">
                    <div class="flex-1">
                        <h2 class="text-xl font-bold text-gray-800 truncate">@yield('page-title', 'لوحة التحكم')</h2>
                        <p class="text-sm text-gray-500">@yield('page-description', '')</p>
                    </div>
                    
                    <div class="flex items-center gap-4">
                        <!-- Notifications -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="relative p-2 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition">
                                <i class="fas fa-bell text-xl"></i>
                                @if(isset($unreadNotificationsCount) && $unreadNotificationsCount > 0)
                                    <span class="absolute -top-1 -right-1 min-w-[18px] h-[18px] bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center px-1 animate-pulse">
                                        {{ $unreadNotificationsCount > 99 ? '99+' : $unreadNotificationsCount }}
                                    </span>
                                @endif
                            </button>
                            
                            <!-- Dropdown Menu -->
                            <div x-show="open" @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 transform scale-95"
                                 x-transition:enter-end="opacity-100 transform scale-100"
                                 class="absolute left-0 mt-2 w-80 bg-white rounded-xl shadow-xl border border-gray-100 z-50 overflow-hidden"
                                 style="display: none;">
                                <div class="p-4 border-b border-gray-100 bg-gradient-to-l from-indigo-50 to-purple-50">
                                    <div class="flex items-center justify-between">
                                        <h4 class="font-bold text-gray-800">الإشعارات</h4>
                                        @if(isset($unreadNotificationsCount) && $unreadNotificationsCount > 0)
                                            <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-xs text-indigo-600 hover:underline">
                                                    تحديد الكل كمقروء
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                                <div class="max-h-80 overflow-y-auto">
                                    @if(isset($recentNotifications) && $recentNotifications->count() > 0)
                                        @foreach($recentNotifications as $notification)
                                            <a href="{{ route('notifications.read', $notification) }}" 
                                               class="block p-4 hover:bg-gray-50 transition border-b border-gray-50 {{ !$notification->isRead() ? 'bg-indigo-50/50' : '' }}">
                                                <div class="flex items-start gap-3">
                                                    <div class="w-10 h-10 bg-{{ $notification->color }}-100 text-{{ $notification->color }}-600 rounded-full flex items-center justify-center flex-shrink-0">
                                                        <i class="{{ $notification->icon }}"></i>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-sm font-semibold text-gray-800 {{ !$notification->isRead() ? 'font-bold' : '' }}">{{ $notification->title }}</p>
                                                        <p class="text-xs text-gray-500 mt-0.5 truncate">{{ $notification->message }}</p>
                                                        <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                                    </div>
                                                    @if(!$notification->isRead())
                                                        <span class="w-2 h-2 bg-indigo-500 rounded-full"></span>
                                                    @endif
                                                </div>
                                            </a>
                                        @endforeach
                                    @else
                                        <div class="p-8 text-center">
                                            <i class="fas fa-bell-slash text-3xl text-gray-300 mb-2"></i>
                                            <p class="text-sm text-gray-500">لا توجد إشعارات</p>
                                        </div>
                                    @endif
                                </div>
                                @if(isset($recentNotifications) && $recentNotifications->count() > 0)
                                    <a href="{{ route('notifications.index') }}" class="block p-3 text-center text-sm text-indigo-600 hover:bg-indigo-50 transition border-t">
                                        عرض جميع الإشعارات
                                    </a>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Date -->
                        <div class="text-sm text-gray-500">
                            <i class="fas fa-calendar-alt ml-1"></i>
                            {{ now()->locale('ar')->translatedFormat('l، d F Y') }}
                        </div>
                    </div>
                </div>
            @endif
        </header>
        
        <!-- Page Content -->
        <div class="p-4 sm:p-6 animate-fade-in pb-20 lg:pb-6">
            <!-- Alerts -->
            @if(session('success'))
                <div class="mb-4 sm:mb-6 p-4 bg-green-50 border border-green-200 rounded-xl flex items-center gap-3">
                    <i class="fas fa-check-circle text-green-500 text-xl flex-shrink-0"></i>
                    <p class="text-green-700 text-sm sm:text-base">{{ session('success') }}</p>
                </div>
            @endif
            
            @if(session('info'))
                <div class="mb-4 sm:mb-6 p-4 bg-blue-50 border border-blue-200 rounded-xl flex items-center gap-3">
                    <i class="fas fa-info-circle text-blue-500 text-xl flex-shrink-0"></i>
                    <p class="text-blue-700 text-sm sm:text-base">{{ session('info') }}</p>
                </div>
            @endif
            
            @if(session('error'))
                <div class="mb-4 sm:mb-6 p-4 bg-red-50 border border-red-200 rounded-xl flex items-center gap-3">
                    <i class="fas fa-exclamation-circle text-red-500 text-xl flex-shrink-0"></i>
                    <p class="text-red-700 text-sm sm:text-base">{{ session('error') }}</p>
                </div>
            @endif
            
            @if($errors->any())
                <div class="mb-4 sm:mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
                    <div class="flex items-center gap-3 mb-2">
                        <i class="fas fa-exclamation-triangle text-red-500 text-xl flex-shrink-0"></i>
                        <p class="text-red-700 font-semibold text-sm sm:text-base">يرجى تصحيح الأخطاء التالية:</p>
                    </div>
                    <ul class="list-disc list-inside text-red-600 text-xs sm:text-sm mr-6 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            @yield('dashboard-content')
        </div>
    </main>
</div>

<!-- Mobile Bottom Navigation -->
<nav class="bottom-nav lg:hidden">
    <div class="bottom-nav-items">
        @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.dashboard') }}" class="bottom-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-home"></i>
                <span>الرئيسية</span>
            </a>
            <a href="{{ route('admin.students.index') }}" class="bottom-nav-item {{ request()->routeIs('admin.students.*') ? 'active' : '' }}">
                <i class="fas fa-user-graduate"></i>
                <span>الطلاب</span>
            </a>
            <a href="{{ route('admin.schedules.index') }}" class="bottom-nav-item {{ request()->routeIs('admin.schedules.*') ? 'active' : '' }}">
                <i class="fas fa-calendar-alt"></i>
                <span>الجدول</span>
            </a>
            <a href="{{ route('admin.reports.index') }}" class="bottom-nav-item {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                <i class="fas fa-chart-bar"></i>
                <span>التقارير</span>
            </a>
        @elseif(auth()->user()->isTeacher())
            <a href="{{ route('teacher.dashboard') }}" class="bottom-nav-item {{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}">
                <i class="fas fa-home"></i>
                <span>الرئيسية</span>
            </a>
            <a href="{{ route('teacher.attendance.index') }}" class="bottom-nav-item {{ request()->routeIs('teacher.attendance.*') ? 'active' : '' }}">
                <i class="fas fa-clipboard-check"></i>
                <span>الحضور</span>
            </a>
            <a href="{{ route('teacher.scores.index') }}" class="bottom-nav-item {{ request()->routeIs('teacher.scores.*') ? 'active' : '' }}">
                <i class="fas fa-star"></i>
                <span>الدرجات</span>
            </a>
            <a href="{{ route('teacher.assignments.index') }}" class="bottom-nav-item {{ request()->routeIs('teacher.assignments.*') ? 'active' : '' }}">
                <i class="fas fa-tasks"></i>
                <span>الواجبات</span>
            </a>
        @elseif(auth()->user()->isParent())
            <a href="{{ route('parent.dashboard') }}" class="bottom-nav-item {{ request()->routeIs('parent.dashboard') ? 'active' : '' }}">
                <i class="fas fa-home"></i>
                <span>الرئيسية</span>
            </a>
            <a href="{{ route('parent.students.index') }}" class="bottom-nav-item {{ request()->routeIs('parent.students.index') ? 'active' : '' }}">
                <i class="fas fa-child"></i>
                <span>أبنائي</span>
            </a>
        @endif
        <a href="{{ route('messages.inbox') }}" class="bottom-nav-item {{ request()->routeIs('messages.*') ? 'active' : '' }}">
            <i class="fas fa-envelope"></i>
            <span>الرسائل</span>
            @if(auth()->user()->unread_messages_count > 0)
                <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">
                    {{ min(auth()->user()->unread_messages_count, 9) }}{{ auth()->user()->unread_messages_count > 9 ? '+' : '' }}
                </span>
            @endif
        </a>
    </div>
</nav>

{{-- No sidebar toggle script needed - mobile navbar handles navigation --}}
@endsection
