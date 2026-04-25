@extends('layouts.dashboard')

@section('page-title', $message->subject)
@section('page-description', 'عرض الرسالة')

@section('dashboard-content')
<div class="mb-6">
    <a href="{{ url()->previous() }}" class="text-indigo-600 hover:text-indigo-800">
        <i class="fas fa-arrow-right ml-2"></i>العودة
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100">
        <h3 class="text-xl font-bold text-gray-800 mb-4">{{ $message->subject }}</h3>
        
        <div class="flex items-center gap-4">
            @if($message->sender_id == auth()->id())
                <img src="{{ $message->receiver->avatar_url }}" alt="" class="w-12 h-12 rounded-full">
                <div class="flex-1">
                    <p class="text-sm text-gray-500">إلى:</p>
                    <p class="font-semibold text-gray-800">{{ $message->receiver->name }}</p>
                </div>
            @else
                <img src="{{ $message->sender->avatar_url }}" alt="" class="w-12 h-12 rounded-full">
                <div class="flex-1">
                    <p class="text-sm text-gray-500">من:</p>
                    <p class="font-semibold text-gray-800">{{ $message->sender->name }}</p>
                </div>
            @endif
            <div class="text-left">
                <p class="text-sm text-gray-500">{{ $message->created_at->format('Y/m/d H:i') }}</p>
                <p class="text-xs text-gray-400">{{ $message->created_at->diffForHumans() }}</p>
            </div>
        </div>
    </div>
    
    <div class="p-6">
        <div class="prose max-w-none text-gray-700 whitespace-pre-wrap">{{ $message->content }}</div>
    </div>
    
    @if($message->sender_id != auth()->id())
        <div class="p-6 border-t border-gray-100 bg-gray-50">
            <h4 class="font-bold text-gray-800 mb-4">الرد على الرسالة</h4>
            <form action="{{ route('messages.reply', $message) }}" method="POST">
                @csrf
                <textarea name="content" rows="4" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 mb-4"
                    placeholder="اكتب ردك هنا..."></textarea>
                <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition">
                    <i class="fas fa-reply ml-2"></i>إرسال الرد
                </button>
            </form>
        </div>
    @endif
</div>

<!-- Related Messages / Thread -->
@if(isset($thread) && $thread && $thread->count() > 1)
    <div class="mt-6 bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="p-4 border-b border-gray-100">
            <h4 class="font-bold text-gray-800">المحادثة</h4>
        </div>
        <div class="divide-y divide-gray-100">
            @foreach($thread as $threadMessage)
                <div class="p-4 {{ $threadMessage->id == $message->id ? 'bg-indigo-50' : '' }}">
                    <div class="flex items-center gap-3 mb-2">
                        <img src="{{ $threadMessage->sender->avatar_url }}" alt="" class="w-8 h-8 rounded-full">
                        <span class="font-semibold text-gray-800">{{ $threadMessage->sender->name }}</span>
                        <span class="text-xs text-gray-400">{{ $threadMessage->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-sm text-gray-600 pr-11">{{ Str::limit($threadMessage->content, 150) }}</p>
                    @if($threadMessage->id != $message->id)
                        <a href="{{ route('messages.show', $threadMessage) }}" class="text-sm text-indigo-600 pr-11 mt-1 inline-block">
                            عرض الرسالة
                        </a>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
@endif
@endsection
