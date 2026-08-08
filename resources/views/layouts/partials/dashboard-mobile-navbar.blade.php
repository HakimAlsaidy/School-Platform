{{-- Mobile Navbar for Dashboard Pages (Admin/Teacher/Guardian) --}}
{{-- Shows only on mobile (lg:hidden), replaces sidebar on small screens --}}

<nav id="dashboardMobileNav" class="lg:hidden fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-lg border-b border-gray-100 shadow-sm">
    <div class="px-4">
        <div class="flex items-center justify-between h-14">
            <!-- Logo & Title -->
            <a href="{{ url('/') }}" class="flex items-center gap-2">
                <div class="w-9 h-9 gradient-bg rounded-lg flex items-center justify-center">
                    <i class="fas fa-graduation-cap text-white text-sm"></i>
                </div>
                <div class="flex flex-col">
                    <span class="font-bold text-gray-800 text-sm">منصة المدرسة</span>
                    <span class="text-xs text-gray-500">
                        @if(auth()->user()->isAdmin())
                            لوحة الإدارة
                        @elseif(auth()->user()->isTeacher())
                            لوحة المعلم
                        @else
                            لوحة ولي الأمر
                        @endif
                    </span>
                </div>
            </a>
            
            <!-- Right Side: Notifications + Menu Button -->
            <div class="flex items-center gap-2">
                <!-- Notifications -->
                <a href="{{ auth()->user()->isAdmin() ? route('admin.pending-users') : route('messages.inbox') }}" class="relative w-9 h-9 flex items-center justify-center rounded-lg text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 transition">
                    <i class="fas fa-bell text-lg"></i>
                    @if(isset($totalNotifications) && $totalNotifications > 0)
                        <span class="absolute -top-0.5 -right-0.5 min-w-[16px] h-[16px] bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center px-0.5 animate-pulse">
                            {{ $totalNotifications > 9 ? '9+' : $totalNotifications }}
                        </span>
                    @endif
                </a>
                
                <!-- User Avatar -->
                <div class="w-8 h-8 rounded-full overflow-hidden border-2 border-indigo-100">
                    <img src="{{ auth()->user()->avatar_url }}" alt="Avatar" class="w-full h-full object-cover">
                </div>
                
                <!-- Mobile Menu Button (Hamburger) -->
                <button type="button" id="dashboardMobileMenuBtn" class="w-10 h-10 flex items-center justify-center rounded-lg hover:bg-gray-100 active:bg-gray-200 transition" onclick="toggleDashboardMobileNav()" aria-label="فتح القائمة">
                    <div class="hamburger-icon w-5 h-4 relative flex flex-col justify-between" id="dashboardHamburger">
                        <span class="block w-full h-0.5 bg-gray-600 rounded-full transition-all duration-300 origin-center"></span>
                        <span class="block w-full h-0.5 bg-gray-600 rounded-full transition-all duration-300"></span>
                        <span class="block w-full h-0.5 bg-gray-600 rounded-full transition-all duration-300 origin-center"></span>
                    </div>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Mobile Menu Dropdown -->
    <div id="dashboardMobileMenu" class="hidden bg-white border-t border-gray-100 shadow-lg max-h-[80vh] overflow-y-auto">
        <!-- User Info -->
        <div class="px-4 py-3 bg-gradient-to-l from-indigo-50 to-purple-50 border-b">
            <div class="flex items-center gap-3">
                <img src="{{ auth()->user()->avatar_url }}" alt="Avatar" class="w-12 h-12 rounded-full object-cover border-2 border-white shadow">
                <div class="flex-1 min-w-0">
                    <p class="font-bold text-gray-800 truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-indigo-600 font-medium">{{ auth()->user()->role->display_name ?? 'مستخدم' }}</p>
                </div>
            </div>
        </div>
        
        <!-- Navigation Links -->
        <div class="px-4 py-3 space-y-1">
            @if(auth()->user()->isAdmin())
                {{-- Admin Navigation --}}
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-indigo-50 hover:text-indigo-600' }} transition" onclick="closeDashboardMobileNav()">
                    <i class="fas fa-home w-5 text-center"></i>
                    <span>الرئيسية</span>
                </a>
                <a href="{{ route('admin.students.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.students.*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-indigo-50 hover:text-indigo-600' }} transition relative" onclick="closeDashboardMobileNav()">
                    <i class="fas fa-user-graduate w-5 text-center"></i>
                    <span>الطلاب</span>
                    @if(isset($newStudentsCount) && $newStudentsCount > 0)
                        <span class="mr-auto bg-blue-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">+{{ $newStudentsCount }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.teachers.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.teachers.*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-indigo-50 hover:text-indigo-600' }} transition relative" onclick="closeDashboardMobileNav()">
                    <i class="fas fa-chalkboard-teacher w-5 text-center"></i>
                    <span>المعلمين</span>
                    @if(isset($teachersWithoutClassrooms) && $teachersWithoutClassrooms > 0)
                        <span class="mr-auto bg-amber-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $teachersWithoutClassrooms }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.guardians.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.guardians.*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-indigo-50 hover:text-indigo-600' }} transition relative" onclick="closeDashboardMobileNav()">
                    <i class="fas fa-users w-5 text-center"></i>
                    <span>أولياء الأمور</span>
                    @if(isset($guardiansWithoutStudents) && $guardiansWithoutStudents > 0)
                        <span class="mr-auto bg-amber-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $guardiansWithoutStudents }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.classrooms.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.classrooms.*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-indigo-50 hover:text-indigo-600' }} transition" onclick="closeDashboardMobileNav()">
                    <i class="fas fa-school w-5 text-center"></i>
                    <span>الفصول</span>
                </a>
                <a href="{{ route('admin.subjects.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.subjects.*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-indigo-50 hover:text-indigo-600' }} transition" onclick="closeDashboardMobileNav()">
                    <i class="fas fa-book w-5 text-center"></i>
                    <span>المواد</span>
                </a>
                <a href="{{ route('admin.grades.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.grades.*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-indigo-50 hover:text-indigo-600' }} transition" onclick="closeDashboardMobileNav()">
                    <i class="fas fa-layer-group w-5 text-center"></i>
                    <span>الصفوف</span>
                </a>
<a href="{{ route('admin.announcements.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.announcements.*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-indigo-50 hover:text-indigo-600' }} transition" onclick="closeDashboardMobileNav()">
                    <i class="fas fa-bullhorn w-5 text-center"></i>
                    <span>الإعلانات</span>
                </a>
                <a href="{{ route('admin.finance.fees') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.finance.*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-indigo-50 hover:text-indigo-600' }} transition" onclick="closeDashboardMobileNav()">
                    <i class="fas fa-money-bill-wave w-5 text-center"></i>
                    <span>الرسوم والمالية</span>
                </a>
                <a href="{{ route('admin.library.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.library.*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-indigo-50 hover:text-indigo-600' }} transition" onclick="closeDashboardMobileNav()">
                    <i class="fas fa-book-reader w-5 text-center"></i>
                    <span>المكتبة</span>
                </a>
                <a href="{{ route('admin.transport.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.transport.*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-indigo-50 hover:text-indigo-600' }} transition" onclick="closeDashboardMobileNav()">
                    <i class="fas fa-bus w-5 text-center"></i>
                    <span>النقل المدرسي</span>
                </a>
                <a href="{{ route('admin.reports.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.reports.*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-indigo-50 hover:text-indigo-600' }} transition" onclick="closeDashboardMobileNav()">
                    <i class="fas fa-chart-bar w-5 text-center"></i>
                    <span>التقارير</span>
                </a>
<a href="{{ route('admin.pending-users') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.pending-users') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-indigo-50 hover:text-indigo-600' }} transition relative" onclick="closeDashboardMobileNav()">
                    <i class="fas fa-user-clock w-5 text-center"></i>
                    <span>طلبات التسجيل</span>
                    @if(isset($pendingUsersCount) && $pendingUsersCount > 0)
                        <span class="mr-auto bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full animate-pulse">
                            {{ $pendingUsersCount }}
                        </span>
                    @endif
                </a>
                <a href="{{ route('ai.analytics') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('ai.analytics') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-indigo-50 hover:text-indigo-600' }} transition" onclick="closeDashboardMobileNav()">
                    <i class="fas fa-brain w-5 text-center"></i>
                    <span>التحليلات الذكية</span>
                </a>
                <a href="{{ route('ai.assistant') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('ai.assistant') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-indigo-50 hover:text-indigo-600' }} transition" onclick="closeDashboardMobileNav()">
                    <i class="fas fa-robot w-5 text-center"></i>
                    <span>المساعد الذكي</span>
                </a>
                
            @elseif(auth()->user()->isTeacher())
                {{-- Teacher Navigation --}}
                <a href="{{ route('teacher.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('teacher.dashboard') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-indigo-50 hover:text-indigo-600' }} transition" onclick="closeDashboardMobileNav()">
                    <i class="fas fa-home w-5 text-center"></i>
                    <span>الرئيسية</span>
                </a>
                <a href="{{ route('teacher.attendance.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('teacher.attendance.*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-indigo-50 hover:text-indigo-600' }} transition" onclick="closeDashboardMobileNav()">
                    <i class="fas fa-clipboard-check w-5 text-center"></i>
                    <span>الحضور</span>
                </a>
                <a href="{{ route('teacher.scores.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('teacher.scores.*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-indigo-50 hover:text-indigo-600' }} transition" onclick="closeDashboardMobileNav()">
                    <i class="fas fa-star w-5 text-center"></i>
                    <span>الدرجات</span>
                </a>
                <a href="{{ route('teacher.assignments.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('teacher.assignments.*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-indigo-50 hover:text-indigo-600' }} transition" onclick="closeDashboardMobileNav()">
                    <i class="fas fa-tasks w-5 text-center"></i>
                    <span>الواجبات</span>
                </a>
<a href="{{ route('teacher.behaviors.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('teacher.behaviors.*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-indigo-50 hover:text-indigo-600' }} transition" onclick="closeDashboardMobileNav()">
                    <i class="fas fa-user-check w-5 text-center"></i>
                    <span>السلوك</span>
                </a>
                <a href="{{ route('teacher.question-bank.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('teacher.question-bank.*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-indigo-50 hover:text-indigo-600' }} transition" onclick="closeDashboardMobileNav()">
                    <i class="fas fa-question-circle w-5 text-center"></i>
                    <span>بنك الأسئلة</span>
                </a>
                <a href="{{ route('teacher.quizzes.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('teacher.quizzes.*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-indigo-50 hover:text-indigo-600' }} transition" onclick="closeDashboardMobileNav()">
                    <i class="fas fa-clipboard-list w-5 text-center"></i>
                    <span>الاختبارات الإلكترونية</span>
                </a>
                <a href="{{ route('teacher.materials.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('teacher.materials.*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-indigo-50 hover:text-indigo-600' }} transition" onclick="closeDashboardMobileNav()">
                    <i class="fas fa-folder-open w-5 text-center"></i>
                    <span>المواد الدراسية</span>
                </a>
                <a href="{{ route('ai.analytics') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('ai.analytics') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-indigo-50 hover:text-indigo-600' }} transition" onclick="closeDashboardMobileNav()">
                    <i class="fas fa-brain w-5 text-center"></i>
                    <span>التحليلات الذكية</span>
                </a>
                <a href="{{ route('ai.assistant') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('ai.assistant') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-indigo-50 hover:text-indigo-600' }} transition" onclick="closeDashboardMobileNav()">
                    <i class="fas fa-robot w-5 text-center"></i>
                    <span>المساعد الذكي</span>
                </a>
                
            @elseif(auth()->user()->isParent())
                {{-- Parent/Guardian Navigation --}}
                <a href="{{ route('parent.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('parent.dashboard') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-indigo-50 hover:text-indigo-600' }} transition" onclick="closeDashboardMobileNav()">
                    <i class="fas fa-home w-5 text-center"></i>
                    <span>الرئيسية</span>
                </a>
<a href="{{ route('parent.students.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('parent.students.*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-indigo-50 hover:text-indigo-600' }} transition" onclick="closeDashboardMobileNav()">
                    <i class="fas fa-child w-5 text-center"></i>
                    <span>أبنائي</span>
                </a>
                <a href="{{ route('parent.finance.fees') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('parent.finance.*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-indigo-50 hover:text-indigo-600' }} transition" onclick="closeDashboardMobileNav()">
                    <i class="fas fa-money-bill-wave w-5 text-center"></i>
                    <span>الرسوم والمدفوعات</span>
                </a>
                <a href="{{ route('parent.transport.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('parent.transport.*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-indigo-50 hover:text-indigo-600' }} transition" onclick="closeDashboardMobileNav()">
                    <i class="fas fa-bus w-5 text-center"></i>
                    <span>النقل المدرسي</span>
                </a>
                <a href="{{ route('parent.quizzes.index', Auth::user()->guardian->students()->first()->id ?? 0) }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('parent.quizzes.*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-indigo-50 hover:text-indigo-600' }} transition" onclick="closeDashboardMobileNav()">
                    <i class="fas fa-clipboard-list w-5 text-center"></i>
                    <span>الاختبارات</span>
                </a>
                <a href="{{ route('ai.analytics') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('ai.analytics') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-indigo-50 hover:text-indigo-600' }} transition" onclick="closeDashboardMobileNav()">
                    <i class="fas fa-brain w-5 text-center"></i>
                    <span>تحليل الأداء</span>
                </a>
                <a href="{{ route('ai.assistant') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('ai.assistant') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-indigo-50 hover:text-indigo-600' }} transition" onclick="closeDashboardMobileNav()">
                    <i class="fas fa-robot w-5 text-center"></i>
                    <span>المساعد الذكي</span>
                </a>
            @endif
            
            <!-- Shared Links -->
            <div class="pt-3 mt-3 border-t border-gray-100">
                <a href="{{ route('messages.inbox') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('messages.*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-indigo-50 hover:text-indigo-600' }} transition" onclick="closeDashboardMobileNav()">
                    <i class="fas fa-envelope w-5 text-center"></i>
                    <span>الرسائل</span>
                    @if(auth()->user()->unread_messages_count > 0)
                        <span class="mr-auto bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">
                            {{ auth()->user()->unread_messages_count }}
                        </span>
                    @endif
                </a>
            </div>
            
            <!-- Logout -->
            <div class="pt-3 mt-3 border-t border-gray-100">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="flex items-center justify-center gap-2 w-full px-4 py-3 bg-red-50 text-red-600 rounded-xl font-semibold hover:bg-red-100 transition">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>تسجيل الخروج</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

{{-- Mobile Nav Spacer - to prevent content from hiding behind fixed navbar --}}
<div class="lg:hidden h-14"></div>

<style>
    /* Hamburger Animation */
    #dashboardHamburger.active span:nth-child(1) {
        transform: rotate(45deg) translateY(7px);
    }
    #dashboardHamburger.active span:nth-child(2) {
        opacity: 0;
        transform: scaleX(0);
    }
    #dashboardHamburger.active span:nth-child(3) {
        transform: rotate(-45deg) translateY(-7px);
    }
    
    /* Menu slide animation */
    #dashboardMobileMenu {
        animation: slideDown 0.3s ease-out;
    }
    
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<script>
function toggleDashboardMobileNav() {
    const menu = document.getElementById('dashboardMobileMenu');
    const hamburger = document.getElementById('dashboardHamburger');
    const body = document.body;
    
    menu.classList.toggle('hidden');
    hamburger.classList.toggle('active');
    
    // Prevent body scroll when menu is open
    if (!menu.classList.contains('hidden')) {
        body.style.overflow = 'hidden';
    } else {
        body.style.overflow = '';
    }
}

function closeDashboardMobileNav() {
    const menu = document.getElementById('dashboardMobileMenu');
    const hamburger = document.getElementById('dashboardHamburger');
    const body = document.body;
    
    menu.classList.add('hidden');
    hamburger.classList.remove('active');
    body.style.overflow = '';
}

// Close on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDashboardMobileNav();
    }
});

// Close when clicking outside
document.addEventListener('click', function(e) {
    const nav = document.getElementById('dashboardMobileNav');
    const menu = document.getElementById('dashboardMobileMenu');
    
    if (nav && !nav.contains(e.target) && !menu.classList.contains('hidden')) {
        closeDashboardMobileNav();
    }
});
</script>
