@extends('layouts.dashboard')

@section('title', 'تفاصيل الفصل')

@section('content')
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">تفاصيل الفصل</h1>
            <p class="text-gray-600 mt-1">{{ $classroom->grade->name }} - {{ $classroom->name }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.classrooms.edit', $classroom) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-edit ml-2"></i>
                تعديل
            </a>
            <a href="{{ route('admin.classrooms.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-arrow-right ml-2"></i>
                رجوع
            </a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
    <!-- إحصائيات سريعة -->
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-5 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-blue-100 text-sm">عدد الطلاب</p>
                <p class="text-3xl font-bold mt-1">{{ $classroom->students->count() }}</p>
            </div>
            <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                <i class="fas fa-users text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-5 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-100 text-sm">السعة القصوى</p>
                <p class="text-3xl font-bold mt-1">{{ $classroom->capacity }}</p>
            </div>
            <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                <i class="fas fa-chair text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-5 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-purple-100 text-sm">المقاعد المتاحة</p>
                <p class="text-3xl font-bold mt-1">{{ $classroom->capacity - $classroom->students->count() }}</p>
            </div>
            <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                <i class="fas fa-plus text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-5 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-orange-100 text-sm">نسبة الامتلاء</p>
                <p class="text-3xl font-bold mt-1">{{ $classroom->capacity > 0 ? round(($classroom->students->count() / $classroom->capacity) * 100) : 0 }}%</p>
            </div>
            <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                <i class="fas fa-chart-pie text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- قائمة الطلاب -->
<div class="mt-6 bg-white rounded-xl shadow-sm">
    <div class="p-6 border-b flex items-center justify-between">
        <h3 class="text-lg font-semibold text-gray-800">
            <i class="fas fa-user-graduate text-indigo-500 ml-2"></i>
            قائمة الطلاب
        </h3>
        <a href="{{ route('admin.students.create') }}?classroom_id={{ $classroom->id }}" class="text-indigo-600 hover:text-indigo-800 text-sm">
            <i class="fas fa-plus ml-1"></i>
            إضافة طالب
        </a>
    </div>

    @if($classroom->students->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الطالب</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">رقم الطالب</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الجنس</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">ولي الأمر</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($classroom->students as $index => $student)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-br {{ $student->gender == 'male' ? 'from-blue-500 to-blue-600' : 'from-pink-500 to-pink-600' }} rounded-full flex items-center justify-center text-white font-bold">
                                        {{ mb_substr($student->name, 0, 1) }}
                                    </div>
                                    <span class="font-medium text-gray-900">{{ $student->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->student_id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 rounded-full text-xs {{ $student->gender == 'male' ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800' }}">
                                    {{ $student->gender == 'male' ? 'ذكر' : 'أنثى' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $student->guardian->user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($student->is_active)
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">نشط</span>
                                @else
                                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">غير نشط</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="{{ route('admin.students.show', $student) }}" class="text-indigo-600 hover:text-indigo-900 ml-3">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.students.edit', $student) }}" class="text-yellow-600 hover:text-yellow-900">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="p-12 text-center">
            <i class="fas fa-users text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900">لا يوجد طلاب</h3>
            <p class="text-gray-500 mt-1">لم يتم تسجيل أي طالب في هذا الفصل بعد</p>
            <a href="{{ route('admin.students.create') }}?classroom_id={{ $classroom->id }}" class="inline-block mt-4 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-plus ml-2"></i>
                إضافة طالب
            </a>
        </div>
    @endif
</div>
@endsection
