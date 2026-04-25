@extends('layouts.dashboard')

@section('page-title', 'إدارة أولياء الأمور')
@section('page-description', 'قائمة أولياء الأمور وإدارتهم')

@section('dashboard-content')
<div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-4">
        <div class="relative">
            <input type="text" placeholder="بحث عن ولي أمر..." 
                class="w-64 pl-10 pr-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
        </div>
    </div>
    <a href="{{ route('admin.guardians.create') }}" class="px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition flex items-center gap-2">
        <i class="fas fa-plus"></i>
        إضافة ولي أمر جديد
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-600">ولي الأمر</th>
                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-600">الهاتف</th>
                    <th class="px-6 py-4 text-center text-sm font-semibold text-gray-600">عدد الأبناء</th>
                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-600">الأبناء</th>
                    <th class="px-6 py-4 text-center text-sm font-semibold text-gray-600">الحالة</th>
                    <th class="px-6 py-4 text-center text-sm font-semibold text-gray-600">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($guardians as $guardian)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img src="{{ $guardian->user->avatar_url }}" alt="" class="w-10 h-10 rounded-full">
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $guardian->user->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $guardian->user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">{{ $guardian->phone ?? '-' }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-sm">
                                {{ $guardian->students_count ?? 0 }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1">
                                @foreach($guardian->students->take(3) as $student)
                                    <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs">
                                        {{ $student->name }}
                                    </span>
                                @endforeach
                                @if($guardian->students->count() > 3)
                                    <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs">
                                        +{{ $guardian->students->count() - 3 }}
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($guardian->user->is_active)
                                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm">نشط</span>
                            @else
                                <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm">غير نشط</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.guardians.show', $guardian) }}" 
                                   class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.guardians.edit', $guardian) }}" 
                                   class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.guardians.destroy', $guardian) }}" method="POST" class="inline"
                                      onsubmit="return confirm('هل أنت متأكد من حذف ولي الأمر هذا؟')">
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
                            <i class="fas fa-users text-4xl mb-3 text-gray-300"></i>
                            <p>لا يوجد أولياء أمور</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($guardians->hasPages())
        <div class="p-4 border-t">
            {{ $guardians->links() }}
        </div>
    @endif
</div>
@endsection
