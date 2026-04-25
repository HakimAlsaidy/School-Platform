@extends('layouts.dashboard')

@section('page-title', 'تسجيل السلوك')
@section('page-description', 'إدارة سلوكيات الطلاب')

@section('dashboard-content')
<div class="flex items-center justify-between mb-6">
    <div></div>
    <a href="{{ route('teacher.behaviors.create') }}" class="px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition flex items-center gap-2">
        <i class="fas fa-plus"></i>
        تسجيل سلوك جديد
    </a>
</div>

<!-- Filter -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
    <form action="{{ route('teacher.behaviors.index') }}" method="GET" class="flex flex-wrap items-end gap-4">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-sm font-medium text-gray-700 mb-2">الفصل</label>
            <select name="classroom_id" class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
                <option value="">جميع الفصول</option>
                @foreach($classrooms as $classroom)
                    <option value="{{ $classroom->id }}" {{ request('classroom_id') == $classroom->id ? 'selected' : '' }}>
                        {{ $classroom->full_name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">النوع</label>
            <select name="type" class="px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
                <option value="">الكل</option>
                <option value="positive" {{ request('type') == 'positive' ? 'selected' : '' }}>إيجابي</option>
                <option value="negative" {{ request('type') == 'negative' ? 'selected' : '' }}>سلبي</option>
            </select>
        </div>
        <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition">
            <i class="fas fa-filter ml-2"></i>تصفية
        </button>
    </form>
</div>

<!-- Behaviors List -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="divide-y divide-gray-100">
        @forelse($behaviors as $behavior)
            <div class="p-6 hover:bg-gray-50">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0
                        {{ $behavior->type == 'positive' ? 'bg-green-100' : 'bg-red-100' }}">
                        <i class="fas fa-{{ $behavior->type == 'positive' ? 'thumbs-up' : 'thumbs-down' }} 
                            {{ $behavior->type == 'positive' ? 'text-green-600' : 'text-red-600' }}"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-bold text-gray-800">{{ $behavior->title }}</h4>
                            <div class="flex items-center gap-3">
                                <span class="px-3 py-1 rounded-full text-sm font-bold
                                    {{ $behavior->type == 'positive' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $behavior->points > 0 ? '+' : '' }}{{ $behavior->points }} نقطة
                                </span>
                                <form action="{{ route('teacher.behaviors.destroy', $behavior) }}" method="POST" class="inline"
                                      onsubmit="return confirm('هل أنت متأكد من حذف هذا السلوك؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-3 mb-3">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($behavior->student->name) }}&background=6366f1&color=fff" 
                                 alt="" class="w-8 h-8 rounded-full">
                            <div>
                                <p class="font-medium text-gray-800">{{ $behavior->student->name }}</p>
                                <p class="text-sm text-gray-500">{{ $behavior->student->classroom->full_name ?? '' }}</p>
                            </div>
                        </div>
                        
                        @if($behavior->description)
                            <p class="text-gray-600 mb-3">{{ $behavior->description }}</p>
                        @endif
                        
                        <div class="flex items-center gap-4 text-sm text-gray-500">
                            <span>
                                <i class="fas fa-calendar ml-1"></i>
                                {{ $behavior->date->format('Y/m/d') }}
                            </span>
                            @if($behavior->subject)
                                <span>
                                    <i class="fas fa-book ml-1"></i>
                                    {{ $behavior->subject->name }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="p-12 text-center text-gray-500">
                <i class="fas fa-award text-6xl mb-4 text-gray-300"></i>
                <h3 class="text-xl font-bold text-gray-600 mb-2">لا يوجد سجل سلوك</h3>
                <p class="text-gray-500 mb-4">لم تقم بتسجيل أي سلوكيات بعد</p>
                <a href="{{ route('teacher.behaviors.create') }}" 
                   class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition">
                    <i class="fas fa-plus ml-2"></i>تسجيل سلوك
                </a>
            </div>
        @endforelse
    </div>
    
    @if($behaviors->hasPages())
        <div class="p-4 border-t">
            {{ $behaviors->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
