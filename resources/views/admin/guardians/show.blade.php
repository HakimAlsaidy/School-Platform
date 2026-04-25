@extends('layouts.dashboard')

@section('title', 'تفاصيل ولي الأمر')

@section('content')
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">تفاصيل ولي الأمر</h1>
            <p class="text-gray-600 mt-1">عرض معلومات: {{ $guardian->user->name }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.guardians.edit', $guardian) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-edit ml-2"></i>
                تعديل
            </a>
            <a href="{{ route('admin.guardians.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-arrow-right ml-2"></i>
                رجوع
            </a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- بطاقة المعلومات الشخصية -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="text-center">
                <div class="w-24 h-24 bg-gradient-to-br from-teal-500 to-cyan-500 rounded-full mx-auto flex items-center justify-center text-white text-3xl font-bold">
                    {{ mb_substr($guardian->user->name, 0, 1) }}
                </div>
                <h2 class="mt-4 text-xl font-bold text-gray-800">{{ $guardian->user->name }}</h2>
                <p class="text-gray-500">
                    @php
                        $relationships = [
                            'father' => 'أب',
                            'mother' => 'أم',
                            'brother' => 'أخ',
                            'sister' => 'أخت',
                            'uncle' => 'عم/خال',
                            'aunt' => 'عمة/خالة',
                            'grandfather' => 'جد',
                            'grandmother' => 'جدة',
                            'other' => 'أخرى'
                        ];
                    @endphp
                    {{ $relationships[$guardian->relationship] ?? 'ولي أمر' }}
                </p>
                
                <div class="mt-4">
                    @if($guardian->user->is_active)
                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">
                            <i class="fas fa-check-circle ml-1"></i>
                            نشط
                        </span>
                    @else
                        <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm">
                            <i class="fas fa-times-circle ml-1"></i>
                            غير نشط
                        </span>
                    @endif
                </div>
            </div>

            <div class="mt-6 border-t pt-4 space-y-3">
                <div class="flex items-center text-gray-600">
                    <i class="fas fa-envelope w-8 text-teal-500"></i>
                    <span class="text-sm">{{ $guardian->user->email }}</span>
                </div>
                @if($guardian->phone)
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-phone w-8 text-teal-500"></i>
                        <span class="text-sm">{{ $guardian->phone }}</span>
                    </div>
                @endif
                @if($guardian->emergency_phone)
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-phone-alt w-8 text-red-500"></i>
                        <span class="text-sm">طوارئ: {{ $guardian->emergency_phone }}</span>
                    </div>
                @endif
                @if($guardian->occupation)
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-briefcase w-8 text-teal-500"></i>
                        <span class="text-sm">{{ $guardian->occupation }}</span>
                    </div>
                @endif
                @if($guardian->address)
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-map-marker-alt w-8 text-teal-500"></i>
                        <span class="text-sm">{{ $guardian->address }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- الأبناء -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-users text-teal-500 ml-2"></i>
                الأبناء المسجلون ({{ $guardian->students->count() }})
            </h3>

            @if($guardian->students->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($guardian->students as $student)
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 bg-gradient-to-br {{ $student->gender == 'male' ? 'from-blue-500 to-blue-600' : 'from-pink-500 to-pink-600' }} rounded-full flex items-center justify-center text-white font-bold">
                                    {{ mb_substr($student->name, 0, 1) }}
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-bold text-gray-800">{{ $student->name }}</h4>
                                    <p class="text-sm text-gray-500">{{ $student->student_id }}</p>
                                    <p class="text-sm text-gray-600 mt-1">
                                        <i class="fas fa-school text-gray-400 ml-1"></i>
                                        {{ $student->classroom->grade->name }} - {{ $student->classroom->name }}
                                    </p>
                                </div>
                                <a href="{{ route('admin.students.show', $student) }}" class="text-indigo-600 hover:text-indigo-800">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-user-graduate text-6xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500">لا يوجد أبناء مسجلين</p>
                    <a href="{{ route('admin.students.create') }}?guardian_id={{ $guardian->id }}" class="inline-block mt-4 text-indigo-600 hover:text-indigo-800">
                        <i class="fas fa-plus ml-1"></i>
                        إضافة طالب
                    </a>
                </div>
            @endif
        </div>

        <!-- إحصائيات الأبناء -->
        @if($guardian->students->count() > 0)
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
                @php
                    $totalAttendance = 0;
                    $totalPresent = 0;
                    $totalScores = 0;
                    $scoresCount = 0;
                    
                    foreach($guardian->students as $student) {
                        $totalAttendance += $student->attendances->count();
                        $totalPresent += $student->attendances->where('status', 'present')->count();
                        foreach($student->scores as $score) {
                            $totalScores += ($score->score / $score->max_score) * 100;
                            $scoresCount++;
                        }
                    }
                    
                    $attendanceRate = $totalAttendance > 0 ? round(($totalPresent / $totalAttendance) * 100) : 0;
                    $avgScore = $scoresCount > 0 ? round($totalScores / $scoresCount) : 0;
                @endphp
                
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-4 text-white text-center">
                    <div class="text-3xl font-bold">{{ $guardian->students->count() }}</div>
                    <div class="text-sm opacity-90">عدد الأبناء</div>
                </div>
                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-4 text-white text-center">
                    <div class="text-3xl font-bold">{{ $attendanceRate }}%</div>
                    <div class="text-sm opacity-90">نسبة الحضور</div>
                </div>
                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-4 text-white text-center">
                    <div class="text-3xl font-bold">{{ $avgScore }}%</div>
                    <div class="text-sm opacity-90">متوسط الدرجات</div>
                </div>
                <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-4 text-white text-center">
                    <div class="text-3xl font-bold">{{ $guardian->students->sum(fn($s) => $s->behaviors->sum('points')) }}</div>
                    <div class="text-sm opacity-90">نقاط السلوك</div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
