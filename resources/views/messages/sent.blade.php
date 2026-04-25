@extends('layouts.dashboard')

@section('page-title', 'الرسائل المرسلة')
@section('page-description', 'رسائلك المرسلة')

@section('dashboard-content')
<div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('messages.inbox') }}" class="px-4 py-2 rounded-xl {{ request()->routeIs('messages.inbox') ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700' }}">
            <i class="fas fa-inbox ml-2"></i>الوارد
        </a>
        <a href="{{ route('messages.sent') }}" class="px-4 py-2 rounded-xl {{ request()->routeIs('messages.sent') ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700' }}">
            <i class="fas fa-paper-plane ml-2"></i>المرسل
        </a>
    </div>
    <a href="{{ route('messages.create') }}" class="px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition flex items-center gap-2">
        <i class="fas fa-plus"></i>
        رسالة جديدة
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="divide-y divide-gray-100">
        @forelse($messages as $message)
            <a href="{{ route('messages.show', $message) }}" class="block p-4 hover:bg-gray-50 transition">
                <div class="flex items-center gap-4">
                    <img src="{{ $message->receiver->avatar_url }}" alt="" class="w-12 h-12 rounded-full">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-sm text-gray-500">إلى:</span>
                            <p class="font-semibold text-gray-800">{{ $message->receiver->name }}</p>
                            <span class="text-xs text-gray-400">{{ $message->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="font-medium text-gray-700 truncate">{{ $message->subject }}</p>
                        <p class="text-sm text-gray-500 truncate">{{ Str::limit($message->content, 80) }}</p>
                    </div>
                    <div class="flex-shrink-0 text-center">
                        @if($message->read_at)
                            <span class="text-xs text-green-600">
                                <i class="fas fa-check-double"></i> مقروءة
                            </span>
                        @else
                            <span class="text-xs text-gray-400">
                                <i class="fas fa-check"></i> مرسلة
                            </span>
                        @endif
                    </div>
                </div>
            </a>
        @empty
            <div class="p-12 text-center text-gray-500">
                <i class="fas fa-paper-plane text-6xl mb-4 text-gray-300"></i>
                <h3 class="text-xl font-bold text-gray-600 mb-2">لا توجد رسائل مرسلة</h3>
                <p>لم ترسل أي رسائل بعد</p>
            </div>
        @endforelse
    </div>
    
    @if($messages->hasPages())
        <div class="p-4 border-t">
            {{ $messages->links() }}
        </div>
    @endif
</div>
@endsection
