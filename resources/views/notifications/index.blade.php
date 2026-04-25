@extends('layouts.dashboard')

@section('page-title', 'الإشعارات')
@section('page-description', 'جميع الإشعارات')

@section('dashboard-content')
<div class="space-y-6">
    {{-- رأس الصفحة --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800">
                <i class="fas fa-bell text-indigo-600 ml-2"></i>
                جميع الإشعارات
            </h2>
            <p class="text-sm text-gray-500 mt-1">{{ $notifications->total() }} إشعار</p>
        </div>
        
        <div class="flex gap-2">
            @if($notifications->where('read_at', null)->count() > 0)
                <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-secondary text-sm">
                        <i class="fas fa-check-double ml-1"></i>
                        تحديد الكل كمقروء
                    </button>
                </form>
            @endif
            
            @if($notifications->count() > 0)
                <form action="{{ route('notifications.destroy-all') }}" method="POST" 
                      onsubmit="return confirm('هل تريد حذف جميع الإشعارات؟')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger text-sm">
                        <i class="fas fa-trash ml-1"></i>
                        حذف الكل
                    </button>
                </form>
            @endif
        </div>
    </div>
    
    {{-- قائمة الإشعارات --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        @forelse($notifications as $notification)
            <div class="p-4 border-b border-gray-100 {{ !$notification->isRead() ? 'bg-indigo-50/50' : '' }} hover:bg-gray-50 transition">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-{{ $notification->color }}-100 text-{{ $notification->color }}-600 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="{{ $notification->icon }} text-xl"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <h4 class="font-semibold text-gray-800 {{ !$notification->isRead() ? 'font-bold' : '' }}">
                                    {{ $notification->title }}
                                    @if(!$notification->isRead())
                                        <span class="inline-block w-2 h-2 bg-indigo-500 rounded-full mr-2"></span>
                                    @endif
                                </h4>
                                <p class="text-sm text-gray-600 mt-1">{{ $notification->message }}</p>
                                <p class="text-xs text-gray-400 mt-2">
                                    <i class="fas fa-clock ml-1"></i>
                                    {{ $notification->created_at->diffForHumans() }}
                                </p>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                @if($notification->action_url)
                                    <a href="{{ route('notifications.read', $notification) }}" 
                                       class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                                        {{ $notification->action_text ?? 'عرض' }}
                                        <i class="fas fa-arrow-left mr-1"></i>
                                    </a>
                                @endif
                                <form action="{{ route('notifications.destroy', $notification) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-red-500 transition">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="p-12 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-bell-slash text-3xl text-gray-300"></i>
                </div>
                <h3 class="font-semibold text-gray-600 mb-1">لا توجد إشعارات</h3>
                <p class="text-sm text-gray-400">ستظهر الإشعارات الجديدة هنا</p>
            </div>
        @endforelse
    </div>
    
    {{-- Pagination --}}
    @if($notifications->hasPages())
        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection
