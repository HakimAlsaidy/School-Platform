@extends('layouts.dashboard')

@section('page-title', 'الإعلانات')
@section('page-description', 'إدارة إعلانات المدرسة')

@section('dashboard-content')
<div class="flex items-center justify-between mb-6">
    <div></div>
    <a href="{{ route('admin.announcements.create') }}" class="px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition flex items-center gap-2">
        <i class="fas fa-plus"></i>
        إضافة إعلان جديد
    </a>
</div>

<div class="space-y-4">
    @forelse($announcements as $announcement)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden card-hover">
            <div class="p-6">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0
                        @if($announcement->is_pinned) bg-amber-100 @else bg-indigo-100 @endif">
                        <i class="fas fa-{{ $announcement->is_pinned ? 'thumbtack' : 'bullhorn' }} text-xl
                            @if($announcement->is_pinned) text-amber-600 @else text-indigo-600 @endif"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <h4 class="font-bold text-gray-800 text-lg">{{ $announcement->title }}</h4>
                            @if($announcement->is_pinned)
                                <span class="px-2 py-1 bg-amber-100 text-amber-700 rounded-lg text-xs">مثبت</span>
                            @endif
                            <span class="px-2 py-1 rounded-lg text-xs
                                @if($announcement->target == 'all') bg-green-100 text-green-700
                                @elseif($announcement->target == 'teachers') bg-blue-100 text-blue-700
                                @elseif($announcement->target == 'parents') bg-purple-100 text-purple-700
                                @else bg-gray-100 text-gray-700 @endif">
                                @if($announcement->target == 'all') للجميع
                                @elseif($announcement->target == 'teachers') للمعلمين
                                @elseif($announcement->target == 'parents') لأولياء الأمور
                                @else {{ $announcement->target }} @endif
                            </span>
                        </div>
                        <p class="text-gray-600 mb-3">{{ Str::limit($announcement->content, 200) }}</p>
                        <div class="flex items-center gap-4 text-sm text-gray-500">
                            <span>
                                <i class="fas fa-user ml-1"></i>
                                {{ $announcement->author->name ?? 'غير معروف' }}
                            </span>
                            <span>
                                <i class="fas fa-calendar ml-1"></i>
                                {{ $announcement->created_at->format('Y/m/d') }}
                            </span>
                            @if($announcement->expires_at)
                                <span class="{{ $announcement->expires_at->isPast() ? 'text-red-500' : '' }}">
                                    <i class="fas fa-clock ml-1"></i>
                                    ينتهي: {{ $announcement->expires_at->format('Y/m/d') }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.announcements.edit', $announcement) }}" 
                           class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.announcements.destroy', $announcement) }}" method="POST" class="inline"
                              onsubmit="return confirm('هل أنت متأكد من حذف هذا الإعلان؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
            <i class="fas fa-bullhorn text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-bold text-gray-600 mb-2">لا توجد إعلانات</h3>
            <p class="text-gray-500 mb-4">لم يتم إضافة أي إعلانات بعد</p>
            <a href="{{ route('admin.announcements.create') }}" 
               class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition">
                <i class="fas fa-plus ml-2"></i>إضافة إعلان
            </a>
        </div>
    @endforelse
</div>

@if($announcements->hasPages())
    <div class="mt-6">
        {{ $announcements->links() }}
    </div>
@endif
@endsection
