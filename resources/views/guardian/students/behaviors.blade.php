@extends('layouts.dashboard')

@section('page-title', 'سجل سلوك ' . $student->name)
@section('page-description', 'متابعة السلوكيات الإيجابية والسلبية')

@section('dashboard-content')
<div class="mb-6">
    <a href="{{ route('parent.students.show', $student) }}" class="text-indigo-600 hover:text-indigo-800">
        <i class="fas fa-arrow-right ml-2"></i>العودة لصفحة الطالب
    </a>
</div>

<!-- Stats -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-xl p-4 border border-gray-100 text-center">
        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-2">
            <i class="fas fa-thumbs-up text-green-600"></i>
        </div>
        <p class="text-2xl font-bold text-green-600">{{ $stats['positive'] }}</p>
        <p class="text-sm text-gray-500">سلوكيات إيجابية</p>
    </div>
    <div class="bg-white rounded-xl p-4 border border-gray-100 text-center">
        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-2">
            <i class="fas fa-thumbs-down text-red-600"></i>
        </div>
        <p class="text-2xl font-bold text-red-600">{{ $stats['negative'] }}</p>
        <p class="text-sm text-gray-500">سلوكيات سلبية</p>
    </div>
    <div class="bg-white rounded-xl p-4 border border-gray-100 text-center">
        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-2">
            <i class="fas fa-star text-blue-600"></i>
        </div>
        <p class="text-2xl font-bold text-blue-600">{{ $stats['total_points'] >= 0 ? '+' : '' }}{{ $stats['total_points'] }}</p>
        <p class="text-sm text-gray-500">إجمالي النقاط</p>
    </div>
    <div class="bg-white rounded-xl p-4 border border-gray-100 text-center">
        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-2">
            <i class="fas fa-calendar text-purple-600"></i>
        </div>
        <p class="text-2xl font-bold text-purple-600">{{ $stats['this_month'] }}</p>
        <p class="text-sm text-gray-500">هذا الشهر</p>
    </div>
</div>

<!-- Points Progress -->
@if($stats['total_points'] != 0)
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
    <div class="flex items-center justify-between mb-4">
        <h4 class="font-bold text-gray-800">ميزان النقاط</h4>
        <span class="text-lg font-bold {{ $stats['total_points'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
            {{ $stats['total_points'] >= 0 ? '+' : '' }}{{ $stats['total_points'] }} نقطة
        </span>
    </div>
    <div class="flex items-center gap-4">
        <span class="text-green-600 font-medium">+{{ $stats['positive_points'] }}</span>
        <div class="flex-1 bg-gray-100 rounded-full h-4 overflow-hidden flex">
            @php
                $total = abs($stats['positive_points']) + abs($stats['negative_points']);
                $positiveWidth = $total > 0 ? (abs($stats['positive_points']) / $total) * 100 : 50;
            @endphp
            <div class="bg-green-500 h-full" style="width: {{ $positiveWidth }}%"></div>
            <div class="bg-red-500 h-full" style="width: {{ 100 - $positiveWidth }}%"></div>
        </div>
        <span class="text-red-600 font-medium">{{ $stats['negative_points'] }}</span>
    </div>
</div>
@endif

<!-- Filter -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
    <form action="{{ route('parent.students.behaviors', $student) }}" method="GET" class="flex flex-wrap items-end gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">النوع</label>
            <select name="type" class="px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
                <option value="">الكل</option>
                <option value="positive" {{ request('type') == 'positive' ? 'selected' : '' }}>إيجابي</option>
                <option value="negative" {{ request('type') == 'negative' ? 'selected' : '' }}>سلبي</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">من تاريخ</label>
            <input type="date" name="from" value="{{ request('from') }}"
                class="px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">إلى تاريخ</label>
            <input type="date" name="to" value="{{ request('to') }}"
                class="px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
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
                            <span class="px-3 py-1 rounded-full text-sm font-bold
                                {{ $behavior->type == 'positive' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $behavior->points > 0 ? '+' : '' }}{{ $behavior->points }} نقطة
                            </span>
                        </div>
                        @if($behavior->description)
                            <p class="text-gray-600 mb-3">{{ $behavior->description }}</p>
                        @endif
                        <div class="flex items-center gap-4 text-sm text-gray-500">
                            <span>
                                <i class="fas fa-user-tie ml-1"></i>
                                {{ $behavior->teacher->user->name ?? 'المعلم' }}
                            </span>
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
                <p>لم يتم تسجيل أي سلوكيات حتى الآن</p>
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
