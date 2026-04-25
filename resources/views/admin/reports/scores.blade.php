@extends('layouts.dashboard')

@section('page-title', 'تقرير الدرجات')
@section('page-description', 'تحليل درجات الطلاب')

@section('dashboard-content')
<!-- Filters -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
    <form action="{{ route('admin.reports.scores') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">الفصل</label>
            <select name="classroom_id" class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
                <option value="">كل الفصول</option>
                @foreach($grades as $grade)
                    <optgroup label="{{ $grade->name }}">
                        @foreach($grade->classrooms as $classroom)
                            <option value="{{ $classroom->id }}" {{ request('classroom_id') == $classroom->id ? 'selected' : '' }}>
                                {{ $classroom->name }}
                            </option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">المادة</label>
            <select name="subject_id" class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
                <option value="">كل المواد</option>
                @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                        {{ $subject->name }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">نوع الاختبار</label>
            <select name="exam_type" class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
                <option value="">الكل</option>
                <option value="quiz" {{ request('exam_type') == 'quiz' ? 'selected' : '' }}>اختبار قصير</option>
                <option value="midterm" {{ request('exam_type') == 'midterm' ? 'selected' : '' }}>اختبار نصفي</option>
                <option value="final" {{ request('exam_type') == 'final' ? 'selected' : '' }}>اختبار نهائي</option>
                <option value="homework" {{ request('exam_type') == 'homework' ? 'selected' : '' }}>واجب</option>
                <option value="participation" {{ request('exam_type') == 'participation' ? 'selected' : '' }}>مشاركة</option>
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">الفصل الدراسي</label>
            <input type="text" name="semester" value="{{ request('semester') }}" placeholder="مثال: الأول 2024"
                class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
        </div>
        
        <div class="flex items-end">
            <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition">
                <i class="fas fa-filter ml-2"></i>تصفية
            </button>
        </div>
    </form>
</div>

<!-- Average Score -->
@if($averageScore)
    <div class="bg-indigo-50 rounded-2xl p-6 mb-6 border border-indigo-100 text-center">
        <p class="text-sm text-indigo-600 mb-1">المتوسط العام</p>
        <p class="text-4xl font-bold text-indigo-700">{{ round($averageScore, 2) }}</p>
    </div>
@endif

<!-- Table -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-600">الطالب</th>
                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-600">الفصل</th>
                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-600">المادة</th>
                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-600">نوع الاختبار</th>
                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-600">الدرجة</th>
                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-600">التقدير</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($scores as $score)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-semibold text-gray-800">{{ $score->student->name }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $score->student->classroom->full_name ?? '-' }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $score->subject->name }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $score->exam_type_label }}</td>
                        <td class="px-6 py-4">
                            <span class="font-bold text-{{ $score->percentage >= 60 ? 'green' : 'red' }}-600">
                                {{ $score->score }}/{{ $score->max_score }}
                            </span>
                            <span class="text-gray-400 text-sm">({{ $score->percentage }}%)</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-sm 
                                @if($score->percentage >= 90) bg-green-100 text-green-700
                                @elseif($score->percentage >= 80) bg-blue-100 text-blue-700
                                @elseif($score->percentage >= 70) bg-indigo-100 text-indigo-700
                                @elseif($score->percentage >= 60) bg-amber-100 text-amber-700
                                @else bg-red-100 text-red-700 @endif">
                                {{ $score->grade }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-chart-bar text-4xl mb-3 text-gray-300"></i>
                            <p>لا توجد درجات مسجلة</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($scores->hasPages())
        <div class="p-4 border-t border-gray-100">
            {{ $scores->links() }}
        </div>
    @endif
</div>
@endsection
