@extends('layouts.dashboard')

@section('page-title', 'إدارة المعلمين')
@section('page-description', 'قائمة المعلمين وإدارتهم')

@section('dashboard-content')
<!-- Header - Responsive -->
<div class="page-header-responsive">
    <div class="relative w-full sm:w-64">
        <input type="text" placeholder="بحث عن معلم..." 
            class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
    </div>
    <a href="{{ route('admin.teachers.create') }}" class="w-full sm:w-auto px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition flex items-center justify-center gap-2">
        <i class="fas fa-plus"></i>
        <span>إضافة معلم جديد</span>
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <!-- Desktop Table View -->
    <div class="hidden md:block overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-600">المعلم</th>
                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-600">التخصص</th>
                    <th class="px-6 py-4 text-center text-sm font-semibold text-gray-600">الفصول</th>
                    <th class="px-6 py-4 text-center text-sm font-semibold text-gray-600">المواد</th>
                    <th class="px-6 py-4 text-center text-sm font-semibold text-gray-600">الحالة</th>
                    <th class="px-6 py-4 text-center text-sm font-semibold text-gray-600">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($teachers as $teacher)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img src="{{ $teacher->user->avatar_url }}" alt="" class="w-10 h-10 rounded-full">
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $teacher->user->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $teacher->user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">{{ $teacher->specialization ?? '-' }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-sm">
                                {{ $teacher->classrooms_count ?? 0 }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm">
                                {{ $teacher->subjects_count ?? 0 }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($teacher->user->is_active)
                                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm">نشط</span>
                            @else
                                <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm">غير نشط</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.teachers.show', $teacher) }}" 
                                   class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.teachers.edit', $teacher) }}" 
                                   class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.teachers.destroy', $teacher) }}" method="POST" class="inline"
                                      onsubmit="return confirm('هل أنت متأكد من حذف هذا المعلم؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-chalkboard-teacher text-4xl mb-3 text-gray-300"></i>
                            <p>لا يوجد معلمين</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Mobile Cards View -->
    <div class="md:hidden p-4 space-y-4">
        @forelse($teachers as $teacher)
            <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
                <!-- Teacher Header -->
                <div class="flex items-center gap-3 mb-3">
                    <img src="{{ $teacher->user->avatar_url }}" alt="" class="w-12 h-12 rounded-full">
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-gray-800 truncate">{{ $teacher->user->name }}</p>
                        <p class="text-sm text-gray-500 truncate">{{ $teacher->user->email }}</p>
                    </div>
                    @if($teacher->user->is_active)
                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs">نشط</span>
                    @else
                        <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs">غير نشط</span>
                    @endif
                </div>
                
                <!-- Teacher Info -->
                <div class="grid grid-cols-3 gap-2 text-center mb-3">
                    <div class="bg-gray-50 rounded-lg p-2">
                        <p class="text-gray-500 text-xs">التخصص</p>
                        <p class="font-medium text-gray-700 text-sm truncate">{{ $teacher->specialization ?? '-' }}</p>
                    </div>
                    <div class="bg-indigo-50 rounded-lg p-2">
                        <p class="text-indigo-500 text-xs">الفصول</p>
                        <p class="font-bold text-indigo-700">{{ $teacher->classrooms_count ?? 0 }}</p>
                    </div>
                    <div class="bg-green-50 rounded-lg p-2">
                        <p class="text-green-500 text-xs">المواد</p>
                        <p class="font-bold text-green-700">{{ $teacher->subjects_count ?? 0 }}</p>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                    <a href="{{ route('admin.teachers.show', $teacher) }}" class="flex-1 p-2 text-center text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition text-sm">
                        <i class="fas fa-eye ml-1"></i>عرض
                    </a>
                    <a href="{{ route('admin.teachers.edit', $teacher) }}" class="flex-1 p-2 text-center text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg transition text-sm">
                        <i class="fas fa-edit ml-1"></i>تعديل
                    </a>
                    <form action="{{ route('admin.teachers.destroy', $teacher) }}" method="POST" class="flex-1" onsubmit="return confirm('هل أنت متأكد؟')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full p-2 text-center text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition text-sm">
                            <i class="fas fa-trash ml-1"></i>حذف
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="text-center py-12 text-gray-500">
                <i class="fas fa-chalkboard-teacher text-4xl mb-3 text-gray-300"></i>
                <p>لا يوجد معلمين</p>
            </div>
        @endforelse
    </div>
    
    @if($teachers->hasPages())
        <div class="p-4 border-t">
            {{ $teachers->links() }}
        </div>
    @endif
</div>
@endsection
