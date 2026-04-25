<!-- Teacher Sidebar Navigation -->
<a href="{{ route('teacher.dashboard') }}" class="sidebar-link {{ request()->routeIs('teacher.dashboard') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 rounded-lg text-gray-600 hover:text-indigo-600">
    <i class="fas fa-home w-5"></i>
    <span>الرئيسية</span>
</a>

<a href="{{ route('teacher.schedule.index') }}" class="sidebar-link {{ request()->routeIs('teacher.schedule.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 rounded-lg text-gray-600 hover:text-indigo-600">
    <i class="fas fa-calendar-alt w-5"></i>
    <span>جدولي الدراسي</span>
</a>

<a href="{{ route('teacher.attendance.index') }}" class="sidebar-link {{ request()->routeIs('teacher.attendance.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 rounded-lg text-gray-600 hover:text-indigo-600">
    <i class="fas fa-clipboard-check w-5"></i>
    <span>الحضور والغياب</span>
</a>

<a href="{{ route('teacher.scores.index') }}" class="sidebar-link {{ request()->routeIs('teacher.scores.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 rounded-lg text-gray-600 hover:text-indigo-600">
    <i class="fas fa-star w-5"></i>
    <span>الدرجات</span>
</a>

<a href="{{ route('teacher.assignments.index') }}" class="sidebar-link {{ request()->routeIs('teacher.assignments.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 rounded-lg text-gray-600 hover:text-indigo-600">
    <i class="fas fa-tasks w-5"></i>
    <span>الواجبات</span>
</a>

<a href="{{ route('teacher.behaviors.index') }}" class="sidebar-link {{ request()->routeIs('teacher.behaviors.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 rounded-lg text-gray-600 hover:text-indigo-600">
    <i class="fas fa-award w-5"></i>
    <span>السلوك</span>
</a>

<div class="pt-2">
    <p class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase">التقارير</p>
    
    <a href="{{ route('teacher.attendance.report') }}" class="sidebar-link {{ request()->routeIs('teacher.attendance.report') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 rounded-lg text-gray-600 hover:text-indigo-600">
        <i class="fas fa-chart-pie w-5"></i>
        <span>تقرير الحضور</span>
    </a>
    
    <a href="{{ route('teacher.scores.report') }}" class="sidebar-link {{ request()->routeIs('teacher.scores.report') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 rounded-lg text-gray-600 hover:text-indigo-600">
        <i class="fas fa-chart-line w-5"></i>
        <span>تقرير الدرجات</span>
    </a>
</div>
