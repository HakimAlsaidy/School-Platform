<!-- Parent Sidebar Navigation -->
<a href="{{ route('parent.dashboard') }}" class="sidebar-link {{ request()->routeIs('parent.dashboard') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 rounded-lg text-gray-600 hover:text-indigo-600">
    <i class="fas fa-home w-5"></i>
    <span>الرئيسية</span>
</a>

<a href="{{ route('parent.students.index') }}" class="sidebar-link {{ request()->routeIs('parent.students.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 rounded-lg text-gray-600 hover:text-indigo-600">
    <i class="fas fa-child w-5"></i>
    <span>أبنائي</span>
</a>

<div class="pt-2">
    <p class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase">متابعة</p>
</div>
