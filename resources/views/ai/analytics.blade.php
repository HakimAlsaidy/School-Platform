@extends(auth()->user()->isAdmin() ? 'layouts.dashboard' : (auth()->user()->isTeacher() ? 'layouts.dashboard' : 'layouts.dashboard'))

@section('page-title', 'التحليلات الذكية')
@section('page-description', 'رؤى ذكية مدعومة بتحليل البيانات')

@section('dashboard-content')
<div class="mb-6">
    <div class="flex items-center justify-between flex-wrap gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
                <span class="w-12 h-12 gradient-bg rounded-xl flex items-center justify-center">
                    <i class="fas fa-brain text-white text-xl"></i>
                </span>
                التحليلات الذكية
            </h2>
            <p class="text-gray-500 mt-2">نظام ذكاء اصطناعي يحلل بيانات المنصة ويقدم رؤى وتوصيات ذكية</p>
        </div>
        <a href="{{ route('ai.assistant') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition shadow-lg shadow-indigo-200">
            <i class="fas fa-robot"></i>
            المساعد الذكي
        </a>
    </div>
</div>

{{-- Student Selector for Parents --}}
@if($children->isNotEmpty())
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
    <div class="flex items-center gap-3 mb-4">
        <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
            <i class="fas fa-child text-indigo-600"></i>
        </div>
        <div>
            <h3 class="font-bold text-gray-800">تحليل أداء الطالب</h3>
            <p class="text-sm text-gray-500">اختر أحد أبنائك لعرض تحليل ذكي مفصل</p>
        </div>
    </div>
    <form method="GET" action="{{ route('ai.analytics') }}" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[200px]">
            <select name="student_id" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                <option value="">-- اختر الطالب --</option>
                @foreach($children as $child)
                    <option value="{{ $child->id }}" {{ $selectedStudentId == $child->id ? 'selected' : '' }}>
                        {{ $child->name }} - {{ $child->classroom->full_name ?? 'بدون فصل' }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition font-semibold">
            <i class="fas fa-microchip ml-2"></i>تحليل
        </button>
    </form>
</div>
@endif

{{-- Student Insights --}}
@if($studentInsights)
<div class="mb-8">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
            <i class="fas fa-user-graduate text-indigo-500"></i>
            تحليل أداء الطالب
        </h3>
    </div>

    {{-- Summary + Risk --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h4 class="font-bold text-gray-800 mb-3"><i class="fas fa-align-left text-indigo-500 ml-2"></i>الملخص الذكي</h4>
            <p class="text-gray-600 leading-relaxed">{{ $studentInsights['summary'] }}</p>

            @if($studentInsights['performance_trend']['direction'] !== 'neutral')
                <div class="mt-4 p-4 rounded-xl {{ $studentInsights['performance_trend']['direction'] === 'up' ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' }}">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fas {{ $studentInsights['performance_trend']['direction'] === 'up' ? 'fa-arrow-trend-up text-green-600' : 'fa-arrow-trend-down text-red-600' }}"></i>
                        <span class="font-semibold {{ $studentInsights['performance_trend']['direction'] === 'up' ? 'text-green-700' : 'text-red-700' }}">
                            {{ $studentInsights['performance_trend']['trend'] }}
                        </span>
                    </div>
                </div>
            @endif
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h4 class="font-bold text-gray-800 mb-3"><i class="fas fa-shield-halved text-indigo-500 ml-2"></i>تقييم المخاطر</h4>
            @php
                $riskColors = ['high' => 'red', 'medium' => 'amber', 'low' => 'yellow', 'none' => 'green'];
                $riskColor = $riskColors[$studentInsights['risk_assessment']['level']] ?? 'gray';
            @endphp
            <div class="p-4 rounded-xl bg-{{ $riskColor }}-50 border border-{{ $riskColor }}-200">
                <p class="text-{{ $riskColor }}-700 font-semibold">{{ $studentInsights['risk_assessment']['label'] }}</p>
                <p class="text-sm text-{{ $riskColor }}-600 mt-1">مستوى الخطر: {{ $studentInsights['risk_assessment']['score'] }}/100</p>
            </div>
            @if($studentInsights['risk_assessment']['factors'])
                <ul class="mt-3 space-y-1">
                    @foreach($studentInsights['risk_assessment']['factors'] as $factor)
                        <li class="text-sm text-gray-600 flex items-start gap-2">
                            <i class="fas fa-exclamation-circle text-amber-500 mt-1 text-xs"></i>
                            {{ $factor }}
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100">
            <p class="text-sm text-gray-500">متوسط الدرجات</p>
            <p class="text-2xl font-bold text-indigo-600">{{ round($studentInsights['attendance_analysis']['rate'] ?? $schoolAnalytics['avgScore'], 2) <= 100 ? ($studentInsights['performance_trend']['data'] ? '—' : '—') : '' }}</p>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100">
            <p class="text-sm text-gray-500">نسبة الحضور</p>
            <p class="text-2xl font-bold text-green-600">{{ $studentInsights['attendance_analysis']['rate'] ?? 100 }}%</p>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100">
            <p class="text-sm text-gray-500">سلوك إيجابي</p>
            <p class="text-2xl font-bold text-purple-600">{{ $studentInsights['behavior_analysis']['positive'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100">
            <p class="text-sm text-gray-500">سلوك سلبي</p>
            <p class="text-2xl font-bold text-red-600">{{ $studentInsights['behavior_analysis']['negative'] ?? 0 }}</p>
        </div>
    </div>

    {{-- Strengths & Weaknesses --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h4 class="font-bold text-gray-800 mb-4"><i class="fas fa-star text-green-500 ml-2"></i>نقاط القوة</h4>
            @forelse($studentInsights['strengths'] as $strength)
                <div class="flex items-center justify-between p-3 bg-green-50 rounded-xl mb-2">
                    <span class="font-medium text-green-800">{{ $strength['name'] }}</span>
                    <span class="px-3 py-1 bg-green-600 text-white text-sm rounded-lg font-bold">{{ $strength['average'] }}</span>
                </div>
            @empty
                <p class="text-gray-500 text-sm">لا توجد نقاط قوة ملحوظة بعد</p>
            @endforelse
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h4 class="font-bold text-gray-800 mb-4"><i class="fas fa-exclamation-triangle text-red-500 ml-2"></i>نقاط الضعف</h4>
            @forelse($studentInsights['weaknesses'] as $weakness)
                <div class="flex items-center justify-between p-3 bg-red-50 rounded-xl mb-2">
                    <span class="font-medium text-red-800">{{ $weakness['name'] }}</span>
                    <span class="px-3 py-1 bg-red-600 text-white text-sm rounded-lg font-bold">{{ $weakness['average'] }}</span>
                </div>
            @empty
                <p class="text-gray-500 text-sm">لا توجد نقاط ضعف ملحوظة</p>
            @endforelse
        </div>
    </div>

    {{-- Recommendations --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h4 class="font-bold text-gray-800 mb-4"><i class="fas fa-lightbulb text-amber-500 ml-2"></i>التوصيات الذكية</h4>
        <div class="space-y-3">
            @foreach($studentInsights['recommendations'] as $rec)
                <div class="flex items-start gap-3 p-4 rounded-xl border border-gray-100 hover:bg-gray-50 transition">
                    <div class="w-10 h-10 bg-{{ $rec['color'] }}-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas {{ $rec['icon'] }} text-{{ $rec['color'] }}-600"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between flex-wrap gap-2">
                            <p class="font-semibold text-gray-800">{{ $rec['title'] }}</p>
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $rec['priority'] === 'high' ? 'bg-red-100 text-red-700' : ($rec['priority'] === 'medium' ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700') }}">
                                {{ $rec['priority'] === 'high' ? 'أولوية عالية' : ($rec['priority'] === 'medium' ? 'أولوية متوسطة' : 'أولوية منخفضة') }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 mt-1">{{ $rec['text'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- School Analytics (for Admin) --}}
@if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
<div class="mb-8">
    <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2 mb-4">
        <i class="fas fa-chart-line text-indigo-500"></i>
        تحليلات المدرسة العامة
    </h3>

    {{-- Totals --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <p class="text-sm text-gray-500">إجمالي الطلاب</p>
            <p class="text-2xl font-bold text-indigo-600">{{ $schoolAnalytics['totals']['totalStudents'] }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <p class="text-sm text-gray-500">المعلمون</p>
            <p class="text-2xl font-bold text-blue-600">{{ $schoolAnalytics['totals']['totalTeachers'] }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <p class="text-sm text-gray-500">الفصول</p>
            <p class="text-2xl font-bold text-green-600">{{ $schoolAnalytics['totals']['totalClassrooms'] }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <p class="text-sm text-gray-500">الصفوف</p>
            <p class="text-2xl font-bold text-purple-600">{{ $schoolAnalytics['totals']['totalGrades'] }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <p class="text-sm text-gray-500">متوسط الدرجات</p>
            <p class="text-2xl font-bold text-amber-600">{{ $schoolAnalytics['avgScore'] }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Grade Performance Chart --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h4 class="font-bold text-gray-800 mb-4"><i class="fas fa-chart-bar text-indigo-500 ml-2"></i>أداء الصفوف</h4>
            <canvas id="gradeChart" height="250"></canvas>
        </div>

        {{-- Risk Distribution Chart --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h4 class="font-bold text-gray-800 mb-4"><i class="fas fa-chart-pie text-indigo-500 ml-2"></i>توزيع مخاطر التعثر</h4>
            <canvas id="riskChart" height="250"></canvas>
        </div>
    </div>

    {{-- Attendance Overview --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mt-6">
        <h4 class="font-bold text-gray-800 mb-4"><i class="fas fa-clipboard-check text-indigo-500 ml-2"></i>نظرة عامة على الحضور</h4>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="p-4 bg-green-50 rounded-xl text-center">
                <p class="text-2xl font-bold text-green-600">{{ $schoolAnalytics['attendance']['present'] }}</p>
                <p class="text-sm text-green-700">حاضر</p>
            </div>
            <div class="p-4 bg-red-50 rounded-xl text-center">
                <p class="text-2xl font-bold text-red-600">{{ $schoolAnalytics['attendance']['absent'] }}</p>
                <p class="text-sm text-red-700">غائب</p>
            </div>
            <div class="p-4 bg-amber-50 rounded-xl text-center">
                <p class="text-2xl font-bold text-amber-600">{{ $schoolAnalytics['attendance']['late'] }}</p>
                <p class="text-sm text-amber-700">متأخر</p>
            </div>
            <div class="p-4 bg-blue-50 rounded-xl text-center">
                <p class="text-2xl font-bold text-blue-600">{{ $schoolAnalytics['attendance']['excused'] }}</p>
                <p class="text-sm text-blue-700">معذور</p>
            </div>
        </div>
    </div>

    {{-- Top Students --}}
    @if($schoolAnalytics['top_students']->isNotEmpty())
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mt-6">
        <h4 class="font-bold text-gray-800 mb-4"><i class="fas fa-trophy text-amber-500 ml-2"></i>أفضل 10 طلاب</h4>
        <div class="space-y-2">
            @foreach($schoolAnalytics['top_students'] as $index => $student)
                <div class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition">
                    <span class="w-8 h-8 rounded-full flex items-center justify-center font-bold {{ $index < 3 ? 'bg-amber-100 text-amber-600' : 'bg-gray-100 text-gray-600' }}">
                        {{ $index + 1 }}
                    </span>
                    <div class="flex-1">
                        <p class="font-medium text-gray-800">{{ $student['name'] }}</p>
                    </div>
                    <span class="px-3 py-1 bg-indigo-100 text-indigo-700 text-sm rounded-lg font-bold">{{ $student['average'] }}</span>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endif

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Grade Performance Chart
    const gradeCanvas = document.getElementById('gradeChart');
    if (gradeCanvas) {
        const gradeData = @json($schoolAnalytics['grade_performance']);
        new Chart(gradeCanvas, {
            type: 'bar',
            data: {
                labels: gradeData.map(g => g.name),
                datasets: [{
                    label: 'متوسط الدرجات',
                    data: gradeData.map(g => g.average),
                    backgroundColor: 'rgba(99, 102, 241, 0.7)',
                    borderColor: '#6366f1',
                    borderWidth: 1,
                    borderRadius: 8,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    }

    // Risk Distribution Chart
    const riskCanvas = document.getElementById('riskChart');
    if (riskCanvas) {
        const riskData = @json($schoolAnalytics['risk_distribution']);
        new Chart(riskCanvas, {
            type: 'doughnut',
            data: {
                labels: ['خطر مرتفع', 'خطر متوسط', 'خطر منخفض', 'لا يوجد خطر'],
                datasets: [{
                    data: [riskData.high, riskData.medium, riskData.low, riskData.none],
                    backgroundColor: ['#ef4444', '#f59e0b', '#eab308', '#22c55e'],
                    borderWidth: 2,
                    borderColor: '#fff',
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { usePointStyle: true, padding: 15 }
                    }
                }
            }
        });
    }
});
</script>
@endpush
