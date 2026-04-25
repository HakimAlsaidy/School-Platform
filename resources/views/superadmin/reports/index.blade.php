@extends('layouts.superadmin')

@section('title', 'التقارير والإحصائيات')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800">📈 التقارير والإحصائيات</h1>
    <p class="text-gray-600 mt-2">نظرة شاملة على أداء المنصة</p>
</div>

<!-- معدل النمو -->
<div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl p-6 text-white mb-8">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-white/80">معدل النمو الشهري</p>
            <p class="text-4xl font-bold mt-2">
                {{ number_format($growthRate, 1) }}%
                @if($growthRate > 0)
                    <i class="fas fa-arrow-up text-green-300 text-2xl"></i>
                @elseif($growthRate < 0)
                    <i class="fas fa-arrow-down text-red-300 text-2xl"></i>
                @endif
            </p>
        </div>
        <div class="w-20 h-20 bg-white/20 rounded-2xl flex items-center justify-center">
            <i class="fas fa-chart-line text-4xl"></i>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- النمو الشهري -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-6">
            <i class="fas fa-chart-bar text-indigo-500 ml-2"></i>
            المدارس والطلاب - آخر 12 شهر
        </h2>
        <div class="h-64" id="monthly-chart"></div>
    </div>

    <!-- توزيع المدارس حسب النوع -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-6">
            <i class="fas fa-pie-chart text-purple-500 ml-2"></i>
            توزيع المدارس حسب النوع
        </h2>
        <div class="grid grid-cols-3 gap-4">
            @php
                $typeNames = ['public' => 'حكومية', 'private' => 'أهلية', 'international' => 'عالمية'];
                $typeColors = ['public' => 'blue', 'private' => 'green', 'international' => 'purple'];
            @endphp
            @foreach($schoolsByType as $type => $count)
                <div class="text-center p-4 bg-{{ $typeColors[$type] ?? 'gray' }}-50 rounded-xl">
                    <p class="text-3xl font-bold text-{{ $typeColors[$type] ?? 'gray' }}-600">{{ $count }}</p>
                    <p class="text-sm text-gray-600 mt-1">{{ $typeNames[$type] ?? $type }}</p>
                </div>
            @endforeach
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- توزيع المدارس حسب المرحلة -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-6">
            <i class="fas fa-graduation-cap text-green-500 ml-2"></i>
            توزيع المدارس حسب المرحلة
        </h2>
        <div class="space-y-4">
            @php
                $levelNames = ['elementary' => 'ابتدائي', 'middle' => 'متوسط', 'high' => 'ثانوي', 'all' => 'جميع المراحل'];
                $total = array_sum($schoolsByLevel->toArray());
            @endphp
            @foreach($schoolsByLevel as $level => $count)
                @php $percentage = $total > 0 ? ($count / $total) * 100 : 0; @endphp
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-gray-600">{{ $levelNames[$level] ?? $level }}</span>
                        <span class="font-semibold text-gray-800">{{ $count }}</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-3">
                        <div class="bg-green-500 h-3 rounded-full transition-all" style="width: {{ $percentage }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- توزيع الاشتراكات -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-6">
            <i class="fas fa-crown text-yellow-500 ml-2"></i>
            توزيع الاشتراكات
        </h2>
        <div class="grid grid-cols-2 gap-4">
            @php
                $planNames = ['free' => 'مجاني', 'basic' => 'أساسي', 'pro' => 'متقدم', 'enterprise' => 'مؤسسي'];
                $planColors = ['free' => 'gray', 'basic' => 'blue', 'pro' => 'purple', 'enterprise' => 'yellow'];
            @endphp
            @foreach($subscriptionStats as $plan => $count)
                <div class="p-4 bg-{{ $planColors[$plan] }}-50 rounded-xl text-center">
                    <p class="text-2xl font-bold text-{{ $planColors[$plan] }}-600">{{ $count }}</p>
                    <p class="text-sm text-gray-600 mt-1">{{ $planNames[$plan] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- أكبر 10 مدارس -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
    <h2 class="text-lg font-bold text-gray-800 mb-6">
        <i class="fas fa-trophy text-yellow-500 ml-2"></i>
        أكبر 10 مدارس
    </h2>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-right px-4 py-3 text-sm font-semibold text-gray-600">#</th>
                    <th class="text-right px-4 py-3 text-sm font-semibold text-gray-600">المدرسة</th>
                    <th class="text-right px-4 py-3 text-sm font-semibold text-gray-600">النوع</th>
                    <th class="text-right px-4 py-3 text-sm font-semibold text-gray-600">الطلاب</th>
                    <th class="text-right px-4 py-3 text-sm font-semibold text-gray-600">المعلمين</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topSchools as $index => $school)
                    <tr class="border-b border-gray-50 hover:bg-gray-50">
                        <td class="px-4 py-3">
                            @if($index < 3)
                                <span class="w-8 h-8 inline-flex items-center justify-center rounded-full 
                                    {{ $index == 0 ? 'bg-yellow-100 text-yellow-700' : ($index == 1 ? 'bg-gray-100 text-gray-700' : 'bg-orange-100 text-orange-700') }}">
                                    <i class="fas fa-medal"></i>
                                </span>
                            @else
                                {{ $index + 1 }}
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <img src="{{ $school->logo_url ?? 'https://ui-avatars.com/api/?name='.$school->name }}" 
                                     class="w-8 h-8 rounded-lg object-cover">
                                <span class="font-medium text-gray-800">{{ $school->name }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $typeNames[$school->type] ?? $school->type }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-sm">{{ $school->students_count }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-sm">{{ $school->teachers_count }}</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
// الرسم البياني للنمو الشهري
var monthlyData = @json($monthlyStats);
var months = Object.values(monthlyData).map(d => d.month);
var schoolsData = Object.values(monthlyData).map(d => d.schools);
var studentsData = Object.values(monthlyData).map(d => d.students);

var options = {
    series: [
        { name: 'المدارس', data: schoolsData },
        { name: 'الطلاب', data: studentsData }
    ],
    chart: {
        type: 'bar',
        height: 250,
        fontFamily: 'Tajawal, sans-serif',
        toolbar: { show: false }
    },
    plotOptions: {
        bar: {
            horizontal: false,
            columnWidth: '55%',
            borderRadius: 6
        }
    },
    dataLabels: { enabled: false },
    xaxis: { categories: months },
    yaxis: { title: { text: 'العدد' } },
    fill: { opacity: 1 },
    colors: ['#6366f1', '#10b981']
};

var chart = new ApexCharts(document.getElementById('monthly-chart'), options);
chart.render();
</script>
@endpush
@endsection
