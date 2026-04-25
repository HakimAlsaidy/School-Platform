@extends('layouts.dashboard')

@section('page-title', 'جداول الفصول')
@section('page-description', 'إدارة جداول الفصول الدراسية')

@section('dashboard-content')
<div class="space-y-6">
    {{-- شريط الأدوات --}}
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('admin.schedules.index') }}" 
               class="px-4 py-2.5 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition">
                <i class="fas fa-arrow-right ml-2"></i>
                رجوع
            </a>
            
            <form method="GET" action="{{ route('admin.schedules.classrooms') }}" class="flex items-center gap-3">
                <select name="classroom_id" onchange="this.form.submit()" 
                        class="px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 bg-white">
                    <option value="">-- اختر الفصل --</option>
                    @foreach($classrooms as $classroom)
                        <option value="{{ $classroom->id }}" {{ request('classroom_id') == $classroom->id ? 'selected' : '' }}>
                            {{ $classroom->grade->name }} - {{ $classroom->name }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
        
        @if($selectedClassroom)
        <div class="flex items-center gap-2">
            <button onclick="openCopyModal()" 
                    class="px-4 py-2.5 bg-blue-100 text-blue-700 rounded-xl hover:bg-blue-200 transition">
                <i class="fas fa-copy ml-2"></i>
                نسخ الجدول
            </button>
            <button onclick="confirmClear()" 
                    class="px-4 py-2.5 bg-red-100 text-red-700 rounded-xl hover:bg-red-200 transition">
                <i class="fas fa-trash ml-2"></i>
                مسح الجدول
            </button>
        </div>
        @endif
    </div>
    
    @if(session('success'))
    <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center gap-3">
        <i class="fas fa-check-circle text-xl"></i>
        {{ session('success') }}
    </div>
    @endif
    
    @if(session('error'))
    <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-center gap-3">
        <i class="fas fa-exclamation-circle text-xl"></i>
        {{ session('error') }}
    </div>
    @endif
    
    @if($selectedClassroom)
    {{-- عنوان الفصل --}}
    <div class="bg-gradient-to-l from-indigo-600 to-indigo-700 rounded-2xl p-6 text-white">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center">
                <i class="fas fa-calendar-alt text-2xl"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold">{{ $selectedClassroom->grade->name }} - {{ $selectedClassroom->name }}</h2>
                <p class="text-indigo-200">الجدول الأسبوعي للفصل</p>
            </div>
        </div>
    </div>
    
    {{-- جدول الحصص --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[800px]">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600 w-24">الحصة</th>
                        @foreach($days as $dayKey => $dayName)
                        <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600">{{ $dayName }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($periods as $periodNum => $times)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-center">
                            <div class="font-bold text-indigo-600 text-lg">{{ $periodNum }}</div>
                        </td>
                        @foreach($days as $dayKey => $dayName)
                        @php
                            $schedule = isset($schedules[$dayKey]) 
                                ? $schedules[$dayKey]->firstWhere('period_number', $periodNum) 
                                : null;
                        @endphp
                        <td class="px-2 py-2">
                            @if($schedule)
                            <div class="bg-indigo-50 rounded-xl p-2 relative group cursor-pointer hover:bg-indigo-100 transition flex items-center gap-2"
                                 onclick="openEditPeriodModal({{ json_encode($schedule) }})">
                                @if($schedule->start_time && $schedule->end_time)
                                <div class="bg-indigo-600 text-white rounded-lg px-2 py-1 text-center min-w-[50px]">
                                    <div class="text-xs font-bold">{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}</div>
                                    <div class="text-[10px] opacity-80">{{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</div>
                                </div>
                                @endif
                                <div class="flex-1">
                                    <div class="font-semibold text-indigo-700 text-sm">{{ $schedule->subject->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $schedule->teacher->user->name }}</div>
                                </div>
                                <button onclick="event.stopPropagation(); deletePeriod({{ $schedule->id }})" 
                                        class="absolute top-1 left-1 w-5 h-5 bg-red-500 text-white rounded-full opacity-0 group-hover:opacity-100 transition text-[10px] flex items-center justify-center">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            @else
                            <button onclick="openAddPeriodModal('{{ $dayKey }}', {{ $periodNum }}, '{{ $times['start'] }}', '{{ $times['end'] }}')" 
                                    class="w-full h-14 border-2 border-dashed border-gray-200 rounded-xl hover:border-indigo-300 hover:bg-indigo-50 transition flex items-center justify-center text-gray-400 hover:text-indigo-500">
                                <i class="fas fa-plus"></i>
                            </button>
                            @endif
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
    {{-- إحصائيات --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl p-4 border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-800">{{ $schedules->flatten()->count() }}</div>
                    <div class="text-sm text-gray-500">حصة مسجلة</div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-amber-600"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-800">{{ (count($days) * count($periods)) - $schedules->flatten()->count() }}</div>
                    <div class="text-sm text-gray-500">خانة فارغة</div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-book text-indigo-600"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-800">{{ $schedules->flatten()->unique('subject_id')->count() }}</div>
                    <div class="text-sm text-gray-500">مادة</div>
                </div>
            </div>
        </div>
    </div>
    @else
    {{-- رسالة اختيار الفصل --}}
    <div class="bg-white rounded-2xl p-12 text-center border border-gray-100">
        <div class="w-20 h-20 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-calendar-alt text-4xl text-indigo-600"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-800 mb-2">اختر فصلاً لعرض الجدول</h3>
        <p class="text-gray-500 mb-6">قم باختيار الفصل من القائمة أعلاه لعرض وتعديل الجدول الدراسي</p>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 max-w-2xl mx-auto">
            @foreach($classrooms->take(8) as $classroom)
            <a href="{{ route('admin.schedules.classrooms', ['classroom_id' => $classroom->id]) }}" 
               class="p-4 bg-gray-50 rounded-xl hover:bg-indigo-50 hover:border-indigo-200 border border-gray-100 transition">
                <div class="font-semibold text-gray-800">{{ $classroom->name }}</div>
                <div class="text-sm text-gray-500">{{ $classroom->grade->name }}</div>
            </a>
            @endforeach
        </div>
    </div>
    @endif
</div>

{{-- Modal إضافة حصة --}}
<div id="addPeriodModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl w-full max-w-md">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-800">إضافة حصة</h3>
        </div>
        <form action="{{ route('admin.schedules.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="classroom_id" value="{{ $selectedClassroom?->id }}">
            <input type="hidden" name="day" id="addDay">
            <input type="hidden" name="period_number" id="addPeriodNumber">
            
            <div id="addPeriodInfo" class="p-3 bg-gray-50 rounded-xl text-sm text-gray-600"></div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">المادة</label>
                <select name="subject_id" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
                    <option value="">اختر المادة</option>
                    @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">المعلم</label>
                <select name="teacher_id" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
                    <option value="">اختر المعلم</option>
                    @foreach($teachers as $teacher)
                    <option value="{{ $teacher->id }}">{{ $teacher->user->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">وقت البدء</label>
                    <input type="time" name="start_time" id="addStartTime" required 
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">وقت الانتهاء</label>
                    <input type="time" name="end_time" id="addEndTime" required 
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
            
            <div class="flex gap-3 pt-4">
                <button type="submit" class="flex-1 px-4 py-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition">
                    إضافة
                </button>
                <button type="button" onclick="closeAddPeriodModal()" class="px-4 py-2.5 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition">
                    إلغاء
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal تعديل حصة --}}
<div id="editPeriodModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl w-full max-w-md">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-800">تعديل حصة</h3>
        </div>
        <form id="editPeriodForm" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">المادة</label>
                <select name="subject_id" id="editSubjectId" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
                    @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">المعلم</label>
                <select name="teacher_id" id="editTeacherId" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
                    @foreach($teachers as $teacher)
                    <option value="{{ $teacher->id }}">{{ $teacher->user->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">وقت البدء</label>
                    <input type="time" name="start_time" id="editStartTime" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">وقت الانتهاء</label>
                    <input type="time" name="end_time" id="editEndTime" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
            
            <div class="flex gap-3 pt-4">
                <button type="submit" class="flex-1 px-4 py-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition">
                    حفظ التعديلات
                </button>
                <button type="button" onclick="closeEditPeriodModal()" class="px-4 py-2.5 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition">
                    إلغاء
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal نسخ الجدول --}}
<div id="copyModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl w-full max-w-md">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-800">نسخ الجدول إلى فصل آخر</h3>
        </div>
        <form action="{{ route('admin.schedules.copy') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="from_classroom_id" value="{{ $selectedClassroom?->id }}">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">نسخ إلى</label>
                <select name="to_classroom_id" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
                    <option value="">اختر الفصل المستهدف</option>
                    @foreach($classrooms as $classroom)
                        @if($classroom->id != $selectedClassroom?->id)
                        <option value="{{ $classroom->id }}">{{ $classroom->grade->name }} - {{ $classroom->name }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            
            <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-sm text-amber-700">
                <i class="fas fa-exclamation-triangle ml-2"></i>
                سيتم نسخ جميع الحصص من الفصل الحالي إلى الفصل المستهدف
            </div>
            
            <div class="flex gap-3 pt-4">
                <button type="submit" class="flex-1 px-4 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition">
                    نسخ الجدول
                </button>
                <button type="button" onclick="closeCopyModal()" class="px-4 py-2.5 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition">
                    إلغاء
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Form حذف الجدول --}}
<form id="clearForm" action="{{ route('admin.schedules.clear') }}" method="POST" class="hidden">
    @csrf
    <input type="hidden" name="classroom_id" value="{{ $selectedClassroom?->id }}">
</form>

{{-- Form حذف حصة --}}
<form id="deleteForm" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

@endsection

@push('scripts')
<script>
    const days = @json($days);
    const PERIOD_DURATION = 40;
    const BREAK_DURATION = 5;
    
    const existingSchedules = @json($schedules->flatten()->values());
    let firstPeriodTime = null;
    
    const period1Schedule = existingSchedules.find(s => s.period_number === 1);
    if (period1Schedule && period1Schedule.start_time) {
        firstPeriodTime = period1Schedule.start_time.substring(0, 5);
    }
    
    function calculatePeriodTime(periodNumber) {
        if (!firstPeriodTime) {
            return { start: '07:30', end: '08:10' };
        }
        
        const [hours, minutes] = firstPeriodTime.split(':').map(Number);
        const firstStartMinutes = hours * 60 + minutes;
        const startMinutes = firstStartMinutes + (periodNumber - 1) * (PERIOD_DURATION + BREAK_DURATION);
        const endMinutes = startMinutes + PERIOD_DURATION;
        
        return {
            start: formatTime(startMinutes),
            end: formatTime(endMinutes)
        };
    }
    
    function formatTime(totalMinutes) {
        const hours = Math.floor(totalMinutes / 60) % 24;
        const minutes = totalMinutes % 60;
        return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;
    }
    
    function openAddPeriodModal(day, periodNumber, startTime, endTime) {
        document.getElementById('addDay').value = day;
        document.getElementById('addPeriodNumber').value = periodNumber;
        
        const calculatedTime = calculatePeriodTime(periodNumber);
        document.getElementById('addStartTime').value = calculatedTime.start;
        document.getElementById('addEndTime').value = calculatedTime.end;
        
        document.getElementById('addPeriodInfo').innerHTML = `
            <i class="fas fa-info-circle ml-2"></i>
            ${days[day]} - الحصة ${periodNumber}
        `;
        document.getElementById('addPeriodModal').classList.remove('hidden');
        document.getElementById('addPeriodModal').classList.add('flex');
    }
    
    document.getElementById('addStartTime')?.addEventListener('change', function() {
        const periodNumber = parseInt(document.getElementById('addPeriodNumber').value);
        if (periodNumber === 1) {
            firstPeriodTime = this.value;
        }
    });
    
    function closeAddPeriodModal() {
        document.getElementById('addPeriodModal').classList.add('hidden');
        document.getElementById('addPeriodModal').classList.remove('flex');
    }
    
    function openEditPeriodModal(schedule) {
        document.getElementById('editPeriodForm').action = `/admin/schedules/${schedule.id}`;
        document.getElementById('editSubjectId').value = schedule.subject_id;
        document.getElementById('editTeacherId').value = schedule.teacher_id;
        document.getElementById('editStartTime').value = schedule.start_time?.substring(0, 5) || '';
        document.getElementById('editEndTime').value = schedule.end_time?.substring(0, 5) || '';
        document.getElementById('editPeriodModal').classList.remove('hidden');
        document.getElementById('editPeriodModal').classList.add('flex');
    }
    
    function closeEditPeriodModal() {
        document.getElementById('editPeriodModal').classList.add('hidden');
        document.getElementById('editPeriodModal').classList.remove('flex');
    }
    
    function openCopyModal() {
        document.getElementById('copyModal').classList.remove('hidden');
        document.getElementById('copyModal').classList.add('flex');
    }
    
    function closeCopyModal() {
        document.getElementById('copyModal').classList.add('hidden');
        document.getElementById('copyModal').classList.remove('flex');
    }
    
    function deletePeriod(scheduleId) {
        if (confirm('هل أنت متأكد من حذف هذه الحصة؟')) {
            const form = document.getElementById('deleteForm');
            form.action = `/admin/schedules/${scheduleId}`;
            form.submit();
        }
    }
    
    function confirmClear() {
        if (confirm('هل أنت متأكد من حذف جميع حصص هذا الفصل؟')) {
            document.getElementById('clearForm').submit();
        }
    }
    
    document.querySelectorAll('.fixed.inset-0').forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        });
    });
</script>
@endpush
