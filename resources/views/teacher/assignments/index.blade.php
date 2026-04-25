@extends('layouts.dashboard')

@section('page-title', 'إدارة الواجبات')
@section('page-description', 'إنشاء ومتابعة الواجبات')

@section('dashboard-content')
<div class="flex items-center justify-between mb-6">
    <div></div>
    <a href="{{ route('teacher.assignments.create') }}" class="px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition flex items-center gap-2">
        <i class="fas fa-plus"></i>
        إنشاء واجب جديد
    </a>
</div>

<!-- Filter -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
    <form action="{{ route('teacher.assignments.index') }}" method="GET" class="flex flex-wrap items-end gap-4">
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
        <div class="flex-1 min-w-[200px]">
            <label class="block text-sm font-medium text-gray-700 mb-2">المادة</label>
            <select name="subject_id" class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
                <option value="">جميع المواد</option>
                @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                        {{ $subject->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition">
            <i class="fas fa-filter ml-2"></i>تصفية
        </button>
    </form>
</div>

<!-- Assignments List -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($assignments as $assignment)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden card-hover">
            <div class="p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-file-alt text-indigo-600"></i>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-medium
                        @if($assignment->due_date->isPast()) bg-red-100 text-red-700
                        @elseif($assignment->due_date->isToday()) bg-amber-100 text-amber-700
                        @else bg-green-100 text-green-700 @endif">
                        @if($assignment->due_date->isPast())
                            منتهي
                        @elseif($assignment->due_date->isToday())
                            ينتهي اليوم
                        @else
                            {{ $assignment->due_date->diffForHumans() }}
                        @endif
                    </span>
                </div>
                
                <h4 class="font-bold text-gray-800 mb-2">{{ $assignment->title }}</h4>
                <p class="text-sm text-gray-500 mb-4 line-clamp-2">{{ Str::limit($assignment->description, 100) }}</p>
                
                <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
                    <span class="px-2 py-1 bg-gray-100 rounded">{{ $assignment->subject->name }}</span>
                    <span class="px-2 py-1 bg-gray-100 rounded">{{ $assignment->classroom->full_name }}</span>
                </div>
                
                <div class="flex items-center justify-between pt-4 border-t">
                    <div class="text-sm">
                        <span class="text-gray-500">التسليمات:</span>
                        <span class="font-bold text-indigo-600">{{ $assignment->submissions_count ?? 0 }}</span>
                        <span class="text-gray-400">/ {{ $assignment->classroom->students_count ?? 0 }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('teacher.assignments.show', $assignment) }}" 
                           class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('teacher.assignments.edit', $assignment) }}" 
                           class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('teacher.assignments.destroy', $assignment) }}" method="POST" class="inline"
                              onsubmit="return confirm('هل أنت متأكد من حذف هذا الواجب؟')">
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
        <div class="col-span-full bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
            <i class="fas fa-tasks text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-bold text-gray-600 mb-2">لا توجد واجبات</h3>
            <p class="text-gray-500 mb-4">لم تقم بإنشاء أي واجبات بعد</p>
            <a href="{{ route('teacher.assignments.create') }}" 
               class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition">
                <i class="fas fa-plus ml-2"></i>إنشاء واجب
            </a>
        </div>
    @endforelse
</div>

@if($assignments->hasPages())
    <div class="mt-6">
        {{ $assignments->withQueryString()->links() }}
    </div>
@endif
@endsection
