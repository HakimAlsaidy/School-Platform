@extends('layouts.dashboard')

@section('page-title', 'سلة المحذوفات - الطلاب')
@section('page-description', 'الطلاب المحذوفين - يمكن استعادتهم')

@section('dashboard-content')
<div class="space-y-6">
    {{-- رابط العودة --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.students.index') }}" class="text-indigo-600 hover:text-indigo-800">
            <i class="fas fa-arrow-right ml-1"></i>
            العودة للطلاب
        </a>
        <span class="px-4 py-2 bg-red-100 text-red-700 rounded-lg text-sm">
            <i class="fas fa-trash ml-2"></i>
            {{ $students->total() }} طالب محذوف
        </span>
    </div>
    
    {{-- قائمة الطلاب المحذوفين --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-4 bg-red-50 border-b border-red-100">
            <h3 class="font-bold text-red-800">
                <i class="fas fa-trash-restore ml-2"></i>
                سلة المحذوفات
            </h3>
            <p class="text-sm text-red-600 mt-1">الطلاب في هذه القائمة محذوفين مؤقتاً ويمكن استعادتهم</p>
        </div>
        
        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-600">الطالب</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-600">رقم الطالب</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-600">الصف</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-600">تاريخ الحذف</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-600">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($students as $student)
                        <tr class="hover:bg-gray-50 opacity-75">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($student->name) }}&background=ef4444&color=fff" 
                                         alt="{{ $student->name }}" class="w-10 h-10 rounded-full grayscale">
                                    <div>
                                        <p class="font-semibold text-gray-800">{{ $student->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $student->gender == 'male' ? 'ذكر' : 'أنثى' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-600 font-mono text-sm">{{ $student->student_id }}</td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-sm">
                                    {{ $student->grade->name ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-500 text-sm">
                                {{ $student->deleted_at->diffForHumans() }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <form action="{{ route('admin.students.restore', $student->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition" title="استعادة">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.students.force-delete', $student->id) }}" method="POST" class="inline" 
                                          onsubmit="return confirm('هل أنت متأكد من الحذف النهائي؟ لا يمكن التراجع عن هذا الإجراء!')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" title="حذف نهائي">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-check-circle text-4xl mb-3 text-green-300"></i>
                                <p>سلة المحذوفات فارغة</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Mobile Cards View -->
        <div class="md:hidden p-4 space-y-4">
            @forelse($students as $student)
                <div class="bg-white border border-red-100 rounded-xl p-4 shadow-sm opacity-75">
                    <div class="flex items-center gap-3 mb-3">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($student->name) }}&background=ef4444&color=fff" 
                             alt="{{ $student->name }}" class="w-12 h-12 rounded-full grayscale">
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-gray-800 truncate">{{ $student->name }}</p>
                            <p class="text-sm text-gray-500">{{ $student->student_id }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between gap-2 pt-3 border-t border-gray-100">
                        <span class="text-sm text-gray-500">حُذف {{ $student->deleted_at->diffForHumans() }}</span>
                        <div class="flex gap-2">
                            <form action="{{ route('admin.students.restore', $student->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="p-2 text-green-600 bg-green-50 rounded-lg text-sm">
                                    <i class="fas fa-undo"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.students.force-delete', $student->id) }}" method="POST" class="inline"
                                  onsubmit="return confirm('حذف نهائي؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-red-600 bg-red-50 rounded-lg text-sm">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 text-gray-500">
                    <i class="fas fa-check-circle text-4xl mb-3 text-green-300"></i>
                    <p>سلة المحذوفات فارغة</p>
                </div>
            @endforelse
        </div>
        
        <!-- Pagination -->
        @if($students->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $students->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
