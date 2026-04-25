@extends('layouts.dashboard')

@section('page-title', 'إدارة المواد')
@section('page-description', 'قائمة المواد الدراسية')

@section('dashboard-content')
<div class="flex items-center justify-between mb-6">
    <div></div>
    <a href="{{ route('admin.subjects.create') }}" class="px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition flex items-center gap-2">
        <i class="fas fa-plus"></i>
        إضافة مادة جديدة
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    @forelse($subjects as $subject)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden card-hover">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center"
                        style="background-color: {{ $subject->color ?? '#6366f1' }}20;">
                        <i class="fas fa-book text-xl" style="color: {{ $subject->color ?? '#6366f1' }};"></i>
                    </div>
                    <span class="text-xs text-gray-500">{{ $subject->code }}</span>
                </div>
                
                <h4 class="font-bold text-gray-800 text-lg mb-2">{{ $subject->name }}</h4>
                
                @if($subject->description)
                    <p class="text-sm text-gray-500 mb-4 line-clamp-2">{{ $subject->description }}</p>
                @endif
                
                <div class="flex items-center gap-4 text-sm text-gray-500 mb-4">
                    <span>
                        <i class="fas fa-chalkboard-teacher ml-1"></i>
                        {{ $subject->teachers_count ?? 0 }} معلم
                    </span>
                </div>
                
                <div class="flex items-center justify-between pt-4 border-t">
                    <a href="{{ route('admin.subjects.edit', $subject) }}" 
                       class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('admin.subjects.destroy', $subject) }}" method="POST" class="inline"
                          onsubmit="return confirm('هل أنت متأكد من حذف هذه المادة؟')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
            <i class="fas fa-book text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-bold text-gray-600 mb-2">لا توجد مواد</h3>
            <p class="text-gray-500 mb-4">لم يتم إضافة أي مواد دراسية بعد</p>
            <a href="{{ route('admin.subjects.create') }}" 
               class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition">
                <i class="fas fa-plus ml-2"></i>إضافة مادة
            </a>
        </div>
    @endforelse
</div>

@if($subjects->hasPages())
    <div class="mt-6">
        {{ $subjects->links() }}
    </div>
@endif
@endsection
