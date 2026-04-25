@extends('layouts.dashboard')

@section('page-title', 'التقارير')
@section('page-description', 'عرض تقارير شاملة عن النظام المدرسي')

@section('dashboard-content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <!-- Attendance Report -->
    <a href="{{ route('admin.reports.attendance') }}" class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 card-hover group">
        <div class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition">
            <i class="fas fa-clipboard-check text-3xl text-green-600"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-800 mb-2">تقرير الحضور والغياب</h3>
        <p class="text-gray-500 text-sm">عرض إحصائيات الحضور والغياب للطلاب مع إمكانية التصفية حسب الفصل والتاريخ</p>
    </a>
    
    <!-- Scores Report -->
    <a href="{{ route('admin.reports.scores') }}" class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 card-hover group">
        <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition">
            <i class="fas fa-chart-line text-3xl text-blue-600"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-800 mb-2">تقرير الدرجات</h3>
        <p class="text-gray-500 text-sm">تحليل درجات الطلاب حسب المادة والفصل مع المتوسطات والإحصائيات</p>
    </a>
    
    <!-- Students Report -->
    <a href="{{ route('admin.reports.students') }}" class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 card-hover group">
        <div class="w-16 h-16 bg-purple-100 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition">
            <i class="fas fa-user-graduate text-3xl text-purple-600"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-800 mb-2">تقرير الطلاب</h3>
        <p class="text-gray-500 text-sm">تقرير شامل عن أداء الطلاب يشمل الحضور والدرجات والسلوك</p>
    </a>
    
    <!-- Teachers Report -->
    <a href="{{ route('admin.reports.teachers') }}" class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 card-hover group">
        <div class="w-16 h-16 bg-amber-100 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition">
            <i class="fas fa-chalkboard-teacher text-3xl text-amber-600"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-800 mb-2">تقرير المعلمين</h3>
        <p class="text-gray-500 text-sm">معلومات تفصيلية عن المعلمين والفصول والمواد المسندة إليهم</p>
    </a>
</div>
@endsection
