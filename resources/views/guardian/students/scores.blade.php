@extends('layouts.dashboard')

@section('page-title', 'درجات ' . $student->name)
@section('page-description', 'سجل الدرجات والاختبارات')

@section('dashboard-content')
<div class="mb-6">
    <a href="{{ route('parent.students.show', $student) }}" class="text-indigo-600 hover:text-indigo-800">
        <i class="fas fa-arrow-right ml-2"></i>العودة لصفحة الطالب
    </a>
</div>

<!-- Stats -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-xl p-4 border border-gray-100 text-center">
        <p class="text-3xl font-bold text-indigo-600">{{ round($averageScore) }}</p>
        <p class="text-sm text-gray-500">المعدل العام</p>
    </div>
    <div class="bg-white rounded-xl p-4 border border-gray-100 text-center">
        <p class="text-3xl font-bold text-green-600">{{ $highestScore }}</p>
        <p class="text-sm text-gray-500">أعلى درجة</p>
    </div>
    <div class="bg-white rounded-xl p-4 border border-gray-100 text-center">
        <p class="text-3xl font-bold text-red-600">{{ $lowestScore }}</p>
        <p class="text-sm text-gray-500">أدنى درجة</p>
    </div>
    <div class="bg-white rounded-xl p-4 border border-gray-100 text-center">
        <p class="text-3xl font-bold text-purple-600">{{ $totalExams }}</p>
        <p class="text-sm text-gray-500">عدد الاختبارات</p>
    </div>
</div>

<!-- Filter -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
    <form action="{{ route('parent.students.scores', $student) }}" method="GET" class="flex flex-wrap items-end gap-4">
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
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">نوع الاختبار</label>
            <select name="exam_type" class="px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
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
            <select name="semester" class="px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
                <option value="">الكل</option>
                <option value="1" {{ request('semester') == '1' ? 'selected' : '' }}>الفصل الأول</option>
                <option value="2" {{ request('semester') == '2' ? 'selected' : '' }}>الفصل الثاني</option>
            </select>
        </div>
        <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition">
            <i class="fas fa-filter ml-2"></i>تصفية
        </button>
    </form>
</div>

<!-- Scores by Subject Chart -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
    <h4 class="font-bold text-gray-800 mb-4">
        <i class="fas fa-chart-bar text-indigo-500 ml-2"></i>
        الدرجات حسب المادة
    </h4>
    <div class="space-y-4">
        @foreach($scoresBySubject as $subjectData)
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="font-semibold text-gray-800">{{ $subjectData['subject'] }}</span>
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-500">{{ $subjectData['count'] }} اختبارات</span>
                        <span class="text-sm font-bold px-2 py-1 rounded-lg
                            @if($subjectData['average'] >= 90) bg-green-100 text-green-700
                            @elseif($subjectData['average'] >= 70) bg-blue-100 text-blue-700
                            @elseif($subjectData['average'] >= 60) bg-amber-100 text-amber-700
                            @else bg-red-100 text-red-700 @endif">
                            {{ $subjectData['average'] }}
                        </span>
                    </div>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-3">
                    <div class="h-3 rounded-full transition-all duration-500
                        @if($subjectData['average'] >= 90) bg-green-500
                        @elseif($subjectData['average'] >= 70) bg-blue-500
                        @elseif($subjectData['average'] >= 60) bg-amber-500
                        @else bg-red-500 @endif"
                        style="width: {{ min($subjectData['average'], 100) }}%"></div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Scores Table -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-4 border-b border-gray-100">
        <h4 class="font-bold text-gray-800">سجل الدرجات</h4>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-600">التاريخ</th>
                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-600">المادة</th>
                    <th class="px-6 py-4 text-center text-sm font-semibold text-gray-600">نوع الاختبار</th>
                    <th class="px-6 py-4 text-center text-sm font-semibold text-gray-600">الدرجة</th>
                    <th class="px-6 py-4 text-center text-sm font-semibold text-gray-600">من</th>
                    <th class="px-6 py-4 text-center text-sm font-semibold text-gray-600">النسبة</th>
                    <th class="px-6 py-4 text-center text-sm font-semibold text-gray-600">التقدير</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($scores as $score)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-gray-500">{{ $score->exam_date->format('Y/m/d') }}</td>
                        <td class="px-6 py-4 font-medium">{{ $score->subject->name }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-1 bg-gray-100 rounded-lg text-sm">{{ $score->exam_type_label }}</span>
                        </td>
                        <td class="px-6 py-4 text-center font-bold
                            {{ $score->percentage >= 60 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $score->score }}
                        </td>
                        <td class="px-6 py-4 text-center text-gray-500">{{ $score->max_score }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-1 rounded-lg text-sm font-medium
                                @if($score->percentage >= 90) bg-green-100 text-green-700
                                @elseif($score->percentage >= 70) bg-blue-100 text-blue-700
                                @elseif($score->percentage >= 60) bg-amber-100 text-amber-700
                                @else bg-red-100 text-red-700 @endif">
                                {{ round($score->percentage) }}%
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-3 py-1 rounded-full text-sm font-medium
                                @if($score->grade == 'A' || $score->grade == 'A+') bg-green-100 text-green-700
                                @elseif($score->grade == 'B' || $score->grade == 'B+') bg-blue-100 text-blue-700
                                @elseif($score->grade == 'C' || $score->grade == 'C+') bg-amber-100 text-amber-700
                                @elseif($score->grade == 'D' || $score->grade == 'D+') bg-orange-100 text-orange-700
                                @else bg-red-100 text-red-700 @endif">
                                {{ $score->grade }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-chart-line text-4xl mb-3 text-gray-300"></i>
                            <p>لا توجد درجات مسجلة</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($scores->hasPages())
        <div class="p-4 border-t">
            {{ $scores->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
