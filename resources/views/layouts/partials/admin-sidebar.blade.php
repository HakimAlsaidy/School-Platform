<!-- Admin Sidebar Navigation -->
<a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 rounded-lg text-gray-600 hover:text-indigo-600">
    <i class="fas fa-home w-5"></i>
    <span>الرئيسية</span>
</a>

<a href="{{ route('admin.students.index') }}" class="sidebar-link {{ request()->routeIs('admin.students.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 rounded-lg text-gray-600 hover:text-indigo-600 relative">
    <i class="fas fa-user-graduate w-5"></i>
    <span>الطلاب</span>
    @if(isset($newStudentsCount) && $newStudentsCount > 0)
        <span class="mr-auto bg-blue-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">
            +{{ $newStudentsCount }}
        </span>
    @endif
</a>

<a href="{{ route('admin.teachers.index') }}" class="sidebar-link {{ request()->routeIs('admin.teachers.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 rounded-lg text-gray-600 hover:text-indigo-600 relative">
    <i class="fas fa-chalkboard-teacher w-5"></i>
    <span>المعلمين</span>
    @if(isset($teachersWithoutClassrooms) && $teachersWithoutClassrooms > 0)
        <span class="mr-auto bg-amber-500 text-white text-xs font-bold px-2 py-0.5 rounded-full" title="{{ $teachersWithoutClassrooms }} معلم بدون فصول">
            {{ $teachersWithoutClassrooms }}
        </span>
    @elseif(isset($newTeachersCount) && $newTeachersCount > 0)
        <span class="mr-auto bg-green-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">
            +{{ $newTeachersCount }}
        </span>
    @endif
</a>

<a href="{{ route('admin.guardians.index') }}" class="sidebar-link {{ request()->routeIs('admin.guardians.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 rounded-lg text-gray-600 hover:text-indigo-600 relative">
    <i class="fas fa-users w-5"></i>
    <span>أولياء الأمور</span>
    @if(isset($guardiansWithoutStudents) && $guardiansWithoutStudents > 0)
        <span class="mr-auto bg-amber-500 text-white text-xs font-bold px-2 py-0.5 rounded-full" title="{{ $guardiansWithoutStudents }} بدون أبناء">
            {{ $guardiansWithoutStudents }}
        </span>
    @elseif(isset($newGuardiansCount) && $newGuardiansCount > 0)
        <span class="mr-auto bg-green-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">
            +{{ $newGuardiansCount }}
        </span>
    @endif
</a>

<a href="{{ route('admin.classrooms.index') }}" class="sidebar-link {{ request()->routeIs('admin.classrooms.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 rounded-lg text-gray-600 hover:text-indigo-600">
    <i class="fas fa-door-open w-5"></i>
    <span>الفصول</span>
</a>

<a href="{{ route('admin.grades.index') }}" class="sidebar-link {{ request()->routeIs('admin.grades.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 rounded-lg text-gray-600 hover:text-indigo-600">
    <i class="fas fa-layer-group w-5"></i>
    <span>الصفوف الدراسية</span>
</a>

<a href="{{ route('admin.subjects.index') }}" class="sidebar-link {{ request()->routeIs('admin.subjects.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 rounded-lg text-gray-600 hover:text-indigo-600">
    <i class="fas fa-book w-5"></i>
    <span>المواد الدراسية</span>
</a>

<a href="{{ route('admin.schedules.index') }}" class="sidebar-link {{ request()->routeIs('admin.schedules.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 rounded-lg text-gray-600 hover:text-indigo-600">
    <i class="fas fa-calendar-alt w-5"></i>
    <span>الجدول الدراسي</span>
</a>

<a href="{{ route('admin.announcements.index') }}" class="sidebar-link {{ request()->routeIs('admin.announcements.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 rounded-lg text-gray-600 hover:text-indigo-600">
    <i class="fas fa-bullhorn w-5"></i>
    <span>الإعلانات</span>
</a>

<div class="pt-2">
    <p class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase">الإدارة المدرسية</p>
    
    <a href="{{ route('admin.finance.fees') }}" class="sidebar-link {{ request()->routeIs('admin.finance.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 rounded-lg text-gray-600 hover:text-indigo-600">
        <i class="fas fa-money-bill-wave w-5"></i>
        <span>الرسوم والمالية</span>
    </a>
    
    <a href="{{ route('admin.library.index') }}" class="sidebar-link {{ request()->routeIs('admin.library.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 rounded-lg text-gray-600 hover:text-indigo-600">
        <i class="fas fa-book-reader w-5"></i>
        <span>المكتبة</span>
    </a>
    
    <a href="{{ route('admin.transport.index') }}" class="sidebar-link {{ request()->routeIs('admin.transport.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 rounded-lg text-gray-600 hover:text-indigo-600">
        <i class="fas fa-bus w-5"></i>
        <span>النقل المدرسي</span>
    </a>
</div>

<a href="{{ route('admin.pending-users') }}" class="sidebar-link {{ request()->routeIs('admin.pending-users') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 rounded-lg text-gray-600 hover:text-indigo-600 relative">
    <i class="fas fa-user-clock w-5"></i>
    <span>طلبات التسجيل</span>
    @if(isset($pendingUsersCount) && $pendingUsersCount > 0)
        <span class="mr-auto bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full animate-pulse">
            {{ $pendingUsersCount }}
        </span>
    @endif
</a>

<div class="pt-2">
    <p class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase">الذكاء الاصطناعي</p>
    
    <a href="{{ route('ai.analytics') }}" class="sidebar-link {{ request()->routeIs('ai.analytics') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 rounded-lg text-gray-600 hover:text-indigo-600">
        <i class="fas fa-brain w-5"></i>
        <span>التحليلات الذكية</span>
    </a>
    
    <a href="{{ route('ai.assistant') }}" class="sidebar-link {{ request()->routeIs('ai.assistant') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 rounded-lg text-gray-600 hover:text-indigo-600">
        <i class="fas fa-robot w-5"></i>
        <span>المساعد الذكي</span>
    </a>
</div>

<div class="pt-2">
    <p class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase">التقارير</p>
    
    <a href="{{ route('admin.reports.index') }}" class="sidebar-link {{ request()->routeIs('admin.reports.index') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 rounded-lg text-gray-600 hover:text-indigo-600">
        <i class="fas fa-chart-bar w-5"></i>
        <span>جميع التقارير</span>
    </a>
    
    <a href="{{ route('admin.reports.attendance') }}" class="sidebar-link {{ request()->routeIs('admin.reports.attendance') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 rounded-lg text-gray-600 hover:text-indigo-600">
        <i class="fas fa-clipboard-check w-5"></i>
        <span>تقرير الحضور</span>
    </a>
    
    <a href="{{ route('admin.reports.scores') }}" class="sidebar-link {{ request()->routeIs('admin.reports.scores') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 rounded-lg text-gray-600 hover:text-indigo-600">
        <i class="fas fa-chart-line w-5"></i>
        <span>تقرير الدرجات</span>
    </a>
</div>
