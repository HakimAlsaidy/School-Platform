@extends('layouts.superadmin')

@section('title', 'إدارة المدارس')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">إدارة المدارس</h1>
            <p class="text-gray-600 mt-1">إدارة جميع المدارس المسجلة في المنصة</p>
        </div>
        <a href="{{ route('superadmin.schools.create') }}" 
           class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-2.5 rounded-xl font-medium hover:opacity-90 transition">
            <i class="fas fa-plus"></i>
            إضافة مدرسة جديدة
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
        <form method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="البحث بالاسم أو النطاق الفرعي..."
                       class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none">
            </div>
            
            <select name="status" class="px-4 py-2 border border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none">
                <option value="">جميع الحالات</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>مفعّل</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>معطّل</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>بانتظار التفعيل</option>
            </select>
            
            <select name="type" class="px-4 py-2 border border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none">
                <option value="">جميع الأنواع</option>
                <option value="public" {{ request('type') == 'public' ? 'selected' : '' }}>حكومية</option>
                <option value="private" {{ request('type') == 'private' ? 'selected' : '' }}>أهلية</option>
                <option value="international" {{ request('type') == 'international' ? 'selected' : '' }}>عالمية</option>
            </select>
            
            <button type="submit" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition">
                <i class="fas fa-search ml-2"></i>
                بحث
            </button>
        </form>
    </div>

    <!-- Schools Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700">المدرسة</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700">النطاق الفرعي</th>
                        <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700">النوع</th>
                        <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700">الطلاب</th>
                        <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700">الحالة</th>
                        <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700">تاريخ التسجيل</th>
                        <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($schools as $school)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if($school->logo)
                                        <img src="{{ $school->logo_url }}" alt="" class="w-10 h-10 rounded-xl object-cover">
                                    @else
                                        <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-xl flex items-center justify-center">
                                            <i class="fas fa-school text-white"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-semibold text-gray-800">{{ $school->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $school->phone }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <a href="http://{{ $school->subdomain }}.{{ config('app.domain', 'edulink.test') }}" 
                                   target="_blank" class="text-indigo-600 hover:text-indigo-700 font-mono text-sm">
                                    {{ $school->subdomain }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 rounded-full text-sm font-medium
                                    @if($school->type == 'public') bg-blue-100 text-blue-700
                                    @elseif($school->type == 'private') bg-green-100 text-green-700
                                    @else bg-purple-100 text-purple-700
                                    @endif">
                                    {{ $school->type_name }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="font-semibold text-gray-800">{{ $school->students_count ?? $school->students()->count() }}</span>
                                @if($school->max_students)
                                    <span class="text-gray-400">/ {{ $school->max_students }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if(!$school->is_verified)
                                    <span class="px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-700">
                                        بانتظار التفعيل
                                    </span>
                                @elseif($school->is_active)
                                    <span class="px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-700">
                                        مفعّل
                                    </span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-700">
                                        معطّل
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center text-gray-600">
                                {{ $school->created_at->format('Y/m/d') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('superadmin.schools.show', $school) }}" 
                                       class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center hover:bg-blue-200 transition"
                                       title="عرض">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>
                                    <a href="{{ route('superadmin.schools.edit', $school) }}" 
                                       class="w-8 h-8 bg-amber-100 text-amber-600 rounded-lg flex items-center justify-center hover:bg-amber-200 transition"
                                       title="تعديل">
                                        <i class="fas fa-edit text-sm"></i>
                                    </a>
                                    
                                    @if(!$school->is_verified)
                                        <form action="{{ route('superadmin.schools.approve', $school) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" 
                                                    class="w-8 h-8 bg-green-100 text-green-600 rounded-lg flex items-center justify-center hover:bg-green-200 transition"
                                                    title="موافقة">
                                                <i class="fas fa-check text-sm"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('superadmin.schools.reject', $school) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="w-8 h-8 bg-red-100 text-red-600 rounded-lg flex items-center justify-center hover:bg-red-200 transition"
                                                    title="رفض"
                                                    onclick="return confirm('هل أنت متأكد من رفض هذه المدرسة؟')">
                                                <i class="fas fa-times text-sm"></i>
                                            </button>
                                        </form>
                                    @else
                                        @if($school->is_active)
                                            <form action="{{ route('superadmin.schools.suspend', $school) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" 
                                                        class="w-8 h-8 bg-red-100 text-red-600 rounded-lg flex items-center justify-center hover:bg-red-200 transition"
                                                        title="تعليق"
                                                        onclick="return confirm('هل أنت متأكد من تعليق هذه المدرسة؟')">
                                                    <i class="fas fa-ban text-sm"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('superadmin.schools.approve', $school) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" 
                                                        class="w-8 h-8 bg-green-100 text-green-600 rounded-lg flex items-center justify-center hover:bg-green-200 transition"
                                                        title="إعادة تفعيل">
                                                    <i class="fas fa-redo text-sm"></i>
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-school text-2xl text-gray-400"></i>
                                    </div>
                                    <p class="text-gray-500">لا توجد مدارس مسجلة</p>
                                    <a href="{{ route('superadmin.schools.create') }}" class="text-indigo-600 hover:text-indigo-700">
                                        إضافة أول مدرسة
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($schools->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $schools->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
