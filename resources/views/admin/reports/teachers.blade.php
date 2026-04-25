@extends('layouts.dashboard')

@section('page-title', 'تقرير المعلمين')
@section('page-description', 'تحليل أداء المعلمين')

@section('dashboard-content')
<!-- Summary Stats -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-full bg-indigo-50 flex items-center justify-center">
                <i class="fas fa-chalkboard-teacher text-indigo-600 text-xl"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm">إجمالي المعلمين</p>
                <p class="text-2xl font-bold text-gray-800">{{ $teachers->total() }}</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-full bg-green-50 flex items-center justify-center">
                <i class="fas fa-clipboard-check text-green-600 text-xl"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm">إجمالي الدرجات المسجلة</p>
                <p class="text-2xl font-bold text-gray-800">{{ $teachers->sum('scores_count') }}</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-full bg-yellow-50 flex items-center justify-center">
                <i class="fas fa-user-check text-yellow-600 text-xl"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm">إجمالي تقييمات السلوك</p>
                <p class="text-2xl font-bold text-gray-800">{{ $teachers->sum('behaviors_count') }}</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-full bg-purple-50 flex items-center justify-center">
                <i class="fas fa-book text-purple-600 text-xl"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm">متوسط المواد لكل معلم</p>
                <p class="text-2xl font-bold text-gray-800">
                    {{ number_format($teachers->avg(fn($t) => $t->subjects->count()) ?? 0, 1) }}
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Teachers Table -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-4 border-b border-gray-100 flex justify-between items-center">
        <h3 class="font-bold text-gray-800">
            <i class="fas fa-table ml-2 text-indigo-600"></i>
            قائمة المعلمين
        </h3>
        <span class="text-sm text-gray-500">{{ $teachers->total() }} معلم</span>
    </div>
    
    @if($teachers->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">المعلم</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">التخصص</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">المواد</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">الفصول</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">الدرجات المسجلة</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">تقييمات السلوك</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">النشاط</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($teachers as $teacher)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white font-bold">
                                        {{ mb_substr($teacher->user->name ?? 'م', 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $teacher->user->name ?? '-' }}</p>
                                        <p class="text-xs text-gray-400">{{ $teacher->user->email ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center text-sm text-gray-600">
                                {{ $teacher->specialization ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($teacher->subjects->count() > 0)
                                    <div class="flex flex-wrap justify-center gap-1">
                                        @foreach($teacher->subjects->take(2) as $subject)
                                            <span class="px-2 py-1 bg-indigo-50 text-indigo-700 rounded-full text-xs">
                                                {{ $subject->name }}
                                            </span>
                                        @endforeach
                                        @if($teacher->subjects->count() > 2)
                                            <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded-full text-xs">
                                                +{{ $teacher->subjects->count() - 2 }}
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($teacher->classrooms->count() > 0)
                                    <div class="flex flex-wrap justify-center gap-1">
                                        @foreach($teacher->classrooms->take(2) as $classroom)
                                            <span class="px-2 py-1 bg-green-50 text-green-700 rounded-full text-xs">
                                                {{ $classroom->name }}
                                            </span>
                                        @endforeach
                                        @if($teacher->classrooms->count() > 2)
                                            <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded-full text-xs">
                                                +{{ $teacher->classrooms->count() - 2 }}
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="font-semibold {{ $teacher->scores_count > 0 ? 'text-green-600' : 'text-gray-400' }}">
                                    {{ $teacher->scores_count }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="font-semibold {{ $teacher->behaviors_count > 0 ? 'text-blue-600' : 'text-gray-400' }}">
                                    {{ $teacher->behaviors_count }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @php
                                    $activity = $teacher->scores_count + $teacher->behaviors_count;
                                @endphp
                                @if($activity >= 20)
                                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs">
                                        <i class="fas fa-fire ml-1"></i>نشط جداً
                                    </span>
                                @elseif($activity >= 10)
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs">
                                        <i class="fas fa-check ml-1"></i>نشط
                                    </span>
                                @elseif($activity > 0)
                                    <span class="px-2 py-1 bg-orange-100 text-orange-700 rounded-full text-xs">
                                        <i class="fas fa-clock ml-1"></i>متوسط
                                    </span>
                                @else
                                    <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded-full text-xs">
                                        <i class="fas fa-minus ml-1"></i>غير محدد
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="p-4 border-t border-gray-100">
            {{ $teachers->links() }}
        </div>
    @else
        <div class="p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-chalkboard-teacher text-gray-400 text-2xl"></i>
            </div>
            <p class="text-gray-500">لا يوجد معلمين</p>
        </div>
    @endif
</div>
@endsection
