@extends('layouts.dashboard')

@section('page-title', 'تسجيل الحضور والغياب')
@section('page-description', 'سجل حضور وغياب الطلاب')

@section('dashboard-content')
@if(!$selectedClassroom)
    <!-- عرض الفصول كقوالب/بطاقات -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">📋 فصولي الدراسية</h2>
                <p class="text-gray-500 mt-1">اختر الفصل لتسجيل الحضور والغياب</p>
            </div>
            <div class="flex items-center gap-3 bg-white rounded-xl px-4 py-2 shadow-sm border border-gray-100">
                <label class="text-sm text-gray-600">التاريخ:</label>
                <input type="date" id="attendanceDate" value="{{ $selectedDate }}"
                    class="px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
            </div>
        </div>
        
        @php
            $classroomsByGrade = $classrooms->groupBy(fn($c) => $c->grade->name ?? 'غير محدد');
        @endphp
        
        @forelse($classroomsByGrade as $gradeName => $gradeClassrooms)
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center text-white text-sm">
                        {{ $loop->iteration }}
                    </span>
                    {{ $gradeName }}
                </h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    @foreach($gradeClassrooms as $classroom)
                        @php
                            $colors = [
                                'from-blue-500 to-blue-600',
                                'from-emerald-500 to-emerald-600',
                                'from-amber-500 to-amber-600',
                                'from-rose-500 to-rose-600',
                                'from-violet-500 to-violet-600',
                                'from-cyan-500 to-cyan-600',
                            ];
                            $colorClass = $colors[$loop->index % count($colors)];
                            $studentCount = $classroom->students->count() ?? 0;
                        @endphp
                        
                        <a href="{{ route('teacher.attendance.index', ['classroom_id' => $classroom->id, 'date' => $selectedDate]) }}" 
                           class="classroom-card group relative bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg hover:border-indigo-200 transition-all duration-300 hover:-translate-y-1">
                            
                            <!-- الشريط العلوي الملون -->
                            <div class="h-2 bg-gradient-to-r {{ $colorClass }}"></div>
                            
                            <div class="p-5">
                                <!-- رأس البطاقة -->
                                <div class="flex items-start justify-between mb-4">
                                    <div class="w-14 h-14 bg-gradient-to-br {{ $colorClass }} rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                                        <i class="fas fa-users text-white text-xl"></i>
                                    </div>
                                    <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-medium">
                                        {{ $studentCount }} طالب
                                    </span>
                                </div>
                                
                                <!-- معلومات الفصل -->
                                <h4 class="text-lg font-bold text-gray-800 mb-1 group-hover:text-indigo-600 transition-colors">
                                    {{ $classroom->name }}
                                </h4>
                                <p class="text-sm text-gray-500 mb-4">{{ $gradeName }}</p>
                                
                                <!-- زر الدخول -->
                                <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                    <span class="text-sm text-gray-500">
                                        <i class="fas fa-clipboard-check ml-1"></i>
                                        تسجيل الحضور
                                    </span>
                                    <span class="w-8 h-8 bg-indigo-50 rounded-full flex items-center justify-center text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-all">
                                        <i class="fas fa-arrow-left text-sm"></i>
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-school text-4xl text-gray-400"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-600 mb-2">لا توجد فصول</h3>
                <p class="text-gray-500">لم يتم تعيين أي فصول دراسية لك بعد</p>
            </div>
        @endforelse
    </div>
    
    @push('scripts')
    <script>
        document.getElementById('attendanceDate')?.addEventListener('change', function() {
            const links = document.querySelectorAll('.classroom-card');
            const newDate = this.value;
            links.forEach(link => {
                const url = new URL(link.href);
                url.searchParams.set('date', newDate);
                link.href = url.toString();
            });
        });
    </script>
    @endpush

@else
    <!-- نموذج الحضور مع زر العودة -->
    <div class="mb-4">
        <a href="{{ route('teacher.attendance.index') }}" 
           class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 transition">
            <i class="fas fa-arrow-right"></i>
            <span>العودة للفصول</span>
        </a>
    </div>
    
    <!-- Attendance Form -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-indigo-500 to-purple-600">
            <div class="flex items-center justify-between text-white">
                <div>
                    <h3 class="text-xl font-bold">{{ $selectedClassroom->name }}</h3>
                    <p class="text-indigo-100 text-sm mt-1">
                        <i class="fas fa-graduation-cap ml-1"></i>
                        {{ $selectedClassroom->grade->name ?? '' }}
                    </p>
                </div>
                <div class="text-left">
                    <p class="text-indigo-100 text-sm">التاريخ</p>
                    <p class="text-lg font-bold">{{ $selectedDate }}</p>
                </div>
            </div>
        </div>
        
        <div class="p-4 bg-indigo-50 border-b border-indigo-100">
            <div class="flex items-center justify-between">
                <span class="text-indigo-700 font-medium">
                    <i class="fas fa-users ml-2"></i>
                    {{ $selectedClassroom->students->count() }} طالب
                </span>
                <div class="flex gap-3">
                    <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm">
                        <i class="fas fa-check-circle ml-1"></i>
                        حاضر
                    </span>
                    <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm">
                        <i class="fas fa-times-circle ml-1"></i>
                        غائب
                    </span>
                    <span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-sm">
                        <i class="fas fa-clock ml-1"></i>
                        متأخر
                    </span>
                </div>
            </div>
        </div>
        
        <form action="{{ route('teacher.attendance.store') }}" method="POST">
            @csrf
            <input type="hidden" name="classroom_id" value="{{ $selectedClassroom->id }}">
            <input type="hidden" name="date" value="{{ $selectedDate }}">
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-gray-600">الطالب</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-gray-600">حاضر</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-gray-600">غائب</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-gray-600">متأخر</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-gray-600">معذور</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-gray-600">ملاحظات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($selectedClassroom->students as $index => $student)
                            @php
                                $currentAttendance = $attendances[$student->id] ?? null;
                                $currentStatus = $currentAttendance?->status ?? 'present';
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <input type="hidden" name="attendance[{{ $index }}][student_id]" value="{{ $student->id }}">
                                    <div class="flex items-center gap-3">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($student->name) }}&background=6366f1&color=fff" 
                                             alt="" class="w-10 h-10 rounded-full">
                                        <div>
                                            <p class="font-semibold text-gray-800">{{ $student->name }}</p>
                                            <p class="text-sm text-gray-500">{{ $student->student_id }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <input type="radio" name="attendance[{{ $index }}][status]" value="present"
                                        {{ $currentStatus == 'present' ? 'checked' : '' }}
                                        class="w-5 h-5 text-green-600 focus:ring-green-500">
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <input type="radio" name="attendance[{{ $index }}][status]" value="absent"
                                        {{ $currentStatus == 'absent' ? 'checked' : '' }}
                                        class="w-5 h-5 text-red-600 focus:ring-red-500">
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <input type="radio" name="attendance[{{ $index }}][status]" value="late"
                                        {{ $currentStatus == 'late' ? 'checked' : '' }}
                                        class="w-5 h-5 text-amber-600 focus:ring-amber-500">
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <input type="radio" name="attendance[{{ $index }}][status]" value="excused"
                                        {{ $currentStatus == 'excused' ? 'checked' : '' }}
                                        class="w-5 h-5 text-blue-600 focus:ring-blue-500">
                                </td>
                                <td class="px-6 py-4">
                                    <input type="text" name="attendance[{{ $index }}][notes]" 
                                        value="{{ $currentAttendance?->notes }}"
                                        placeholder="ملاحظة..."
                                        class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="p-6 border-t border-gray-100 bg-gray-50">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="flex gap-4 text-sm">
                        <button type="button" onclick="setAllStatus('present')" class="flex items-center gap-1 px-3 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition">
                            <i class="fas fa-check-double"></i>الكل حاضر
                        </button>
                        <button type="button" onclick="setAllStatus('absent')" class="flex items-center gap-1 px-3 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition">
                            <i class="fas fa-times-circle"></i>الكل غائب
                        </button>
                    </div>
                    <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition flex items-center gap-2 shadow-lg">
                        <i class="fas fa-save"></i>
                        حفظ الحضور
                    </button>
                </div>
            </div>
        </form>
    </div>
    
    @push('scripts')
    <script>
        function setAllStatus(status) {
            document.querySelectorAll(`input[type="radio"][value="${status}"]`).forEach(radio => {
                radio.checked = true;
            });
        }
    </script>
    @endpush
@endif
@endsection
