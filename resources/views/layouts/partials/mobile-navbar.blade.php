{{-- Mobile Navbar Component - Appears on all pages --}}
<nav id="globalNav" class="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-lg border-b border-gray-100 transform transition-transform duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="flex items-center justify-between h-14 sm:h-16">
            <!-- Logo -->
            <a href="{{ url('/') }}" class="flex items-center gap-2 sm:gap-3">
                <div class="w-8 h-8 sm:w-10 sm:h-10 gradient-bg rounded-lg sm:rounded-xl flex items-center justify-center">
                    <i class="fas fa-graduation-cap text-white text-sm sm:text-lg"></i>
                </div>
                <span class="text-lg sm:text-xl font-bold text-gray-800">إيدو لينك</span>
            </a>
            
            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center gap-4 lg:gap-6">
                <a href="{{ url('/') }}#about" class="text-gray-600 hover:text-indigo-600 transition text-sm">من نحن</a>
                <a href="{{ url('/') }}#services" class="text-gray-600 hover:text-indigo-600 transition text-sm">خدماتنا</a>
                <a href="{{ url('/') }}#features" class="text-gray-600 hover:text-indigo-600 transition text-sm">المميزات</a>
                <a href="{{ url('/') }}#contact" class="text-gray-600 hover:text-indigo-600 transition text-sm">تواصل معنا</a>
            </div>
            
            <!-- Auth Buttons (Desktop) -->
            <div class="hidden md:flex items-center gap-2 sm:gap-3">
                @auth
                    @php
                        $dashboardRoute = auth()->user()->isAdmin() ? 'admin.dashboard' : 
                                         (auth()->user()->isTeacher() ? 'teacher.dashboard' : 'parent.dashboard');
                    @endphp
                    <a href="{{ route($dashboardRoute) }}" class="px-4 py-2 gradient-bg text-white text-sm rounded-lg hover:shadow-lg transition">
                        <i class="fas fa-columns ml-1"></i> لوحة التحكم
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 text-gray-700 hover:text-indigo-600 transition text-sm font-medium">
                        تسجيل الدخول
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="px-4 py-2 gradient-bg text-white text-sm rounded-lg hover:shadow-lg transition">
                            إنشاء حساب
                        </a>
                    @endif
                @endauth
            </div>
            
            <!-- Mobile Menu Button (Hamburger) -->
            <button type="button" id="globalNavBtn" class="md:hidden w-10 h-10 flex items-center justify-center rounded-lg hover:bg-gray-100 active:bg-gray-200 transition" onclick="toggleGlobalNav()" aria-label="فتح القائمة">
                <div class="hamburger-icon w-5 h-4 relative flex flex-col justify-between" id="globalHamburger">
                    <span class="block w-full h-0.5 bg-gray-600 rounded-full transition-all duration-300"></span>
                    <span class="block w-full h-0.5 bg-gray-600 rounded-full transition-all duration-300"></span>
                    <span class="block w-full h-0.5 bg-gray-600 rounded-full transition-all duration-300"></span>
                </div>
            </button>
        </div>
    </div>
    
    <!-- Mobile Menu Dropdown -->
    <div id="globalNavMenu" class="md:hidden hidden bg-white border-t border-gray-100 shadow-lg max-h-[80vh] overflow-y-auto">
        <div class="px-4 py-3 space-y-1">
            <!-- Navigation Links -->
            <a href="{{ url('/') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition" onclick="closeGlobalNav()">
                <i class="fas fa-home w-5 text-center"></i>
                <span>الرئيسية</span>
            </a>
            <a href="{{ url('/') }}#about" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition" onclick="closeGlobalNav()">
                <i class="fas fa-info-circle w-5 text-center"></i>
                <span>من نحن</span>
            </a>
            <a href="{{ url('/') }}#services" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition" onclick="closeGlobalNav()">
                <i class="fas fa-concierge-bell w-5 text-center"></i>
                <span>خدماتنا</span>
            </a>
            <a href="{{ url('/') }}#features" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition" onclick="closeGlobalNav()">
                <i class="fas fa-star w-5 text-center"></i>
                <span>المميزات</span>
            </a>
            <a href="{{ url('/') }}#contact" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition" onclick="closeGlobalNav()">
                <i class="fas fa-envelope w-5 text-center"></i>
                <span>تواصل معنا</span>
            </a>
            
            <!-- Auth Section -->
            <div class="pt-3 mt-3 border-t border-gray-100 space-y-2">
                @auth
                    @php
                        $dashboardRoute = auth()->user()->isAdmin() ? 'admin.dashboard' : 
                                         (auth()->user()->isTeacher() ? 'teacher.dashboard' : 'parent.dashboard');
                    @endphp
                    <a href="{{ route($dashboardRoute) }}" class="flex items-center justify-center gap-2 w-full px-4 py-3 gradient-bg text-white rounded-xl font-semibold">
                        <i class="fas fa-columns"></i>
                        <span>لوحة التحكم</span>
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="w-full">
                        @csrf
                        <button type="submit" class="flex items-center justify-center gap-2 w-full px-4 py-3 bg-red-50 text-red-600 rounded-xl font-semibold hover:bg-red-100 transition">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>تسجيل الخروج</span>
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="flex items-center justify-center gap-2 w-full px-4 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 transition">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>تسجيل الدخول</span>
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="flex items-center justify-center gap-2 w-full px-4 py-3 gradient-bg text-white rounded-xl font-semibold">
                            <i class="fas fa-user-plus"></i>
                            <span>إنشاء حساب</span>
                        </a>
                    @endif
                @endauth
            </div>
        </div>
    </div>
</nav>

{{-- Spacer for fixed navbar --}}
<div class="h-14 sm:h-16 md:h-16"></div>

<style>
    /* Hamburger Animation */
    #globalHamburger.active span:nth-child(1) {
        transform: translateY(7px) rotate(45deg);
    }
    #globalHamburger.active span:nth-child(2) {
        opacity: 0;
        transform: translateX(-10px);
    }
    #globalHamburger.active span:nth-child(3) {
        transform: translateY(-7px) rotate(-45deg);
    }
    
    /* Hide on scroll down */
    #globalNav.nav-hidden {
        transform: translateY(-100%);
    }
</style>

<script>
// Global Navigation Toggle
function toggleGlobalNav() {
    const menu = document.getElementById('globalNavMenu');
    const hamburger = document.getElementById('globalHamburger');
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

function closeGlobalNav() {
    const menu = document.getElementById('globalNavMenu');
    const hamburger = document.getElementById('globalHamburger');
    const body = document.body;
    
    menu.classList.add('hidden');
    hamburger.classList.remove('active');
    body.style.overflow = '';
}

// Close menu on window resize
window.addEventListener('resize', function() {
    if (window.innerWidth >= 768) {
        closeGlobalNav();
    }
});

// Close menu on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeGlobalNav();
    }
});

// Hide navbar on scroll down, show on scroll up (mobile only)
let lastScrollY = window.scrollY;
const globalNav = document.getElementById('globalNav');

window.addEventListener('scroll', function() {
    const currentScrollY = window.scrollY;
    
    if (window.innerWidth < 768) {
        if (currentScrollY > lastScrollY && currentScrollY > 60) {
            globalNav.classList.add('nav-hidden');
            closeGlobalNav(); // Close menu if scrolling
        } else {
            globalNav.classList.remove('nav-hidden');
        }
    } else {
        globalNav.classList.remove('nav-hidden');
    }
    
    lastScrollY = currentScrollY;
});
</script>
