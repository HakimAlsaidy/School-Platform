@extends('layouts.dashboard')

@section('page-title', $assignment->title)
@section('page-description', 'تفاصيل الواجب والتسليمات')

@section('dashboard-content')
<div class="mb-6">
    <a href="{{ route('teacher.assignments.index') }}" class="text-indigo-600 hover:text-indigo-800">
        <i class="fas fa-arrow-right ml-2"></i>العودة لقائمة الواجبات
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Assignment Details -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 sticky top-24">
            <div class="p-6 border-b border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-file-alt text-indigo-600"></i>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-medium
                        @if($assignment->due_date->isPast()) bg-red-100 text-red-700
                        @elseif($assignment->due_date->isToday()) bg-amber-100 text-amber-700
                        @else bg-green-100 text-green-700 @endif">
                        @if($assignment->due_date->isPast())
                            منتهي
                        @elseif($assignment->due_date->isToday())
                            ينتهي اليوم
                        @else
                            {{ $assignment->due_date->diffForHumans() }}
                        @endif
                    </span>
                </div>
                <h3 class="text-lg font-bold text-gray-800 mb-2">{{ $assignment->title }}</h3>
                <p class="text-sm text-gray-500">{{ $assignment->description }}</p>
            </div>
            
            <div class="p-6 space-y-4">
                <div class="flex items-center gap-3">
                    <i class="fas fa-book text-gray-400 w-5"></i>
                    <span class="text-gray-600">{{ $assignment->subject->name }}</span>
                </div>
                <div class="flex items-center gap-3">
                    <i class="fas fa-door-open text-gray-400 w-5"></i>
                    <span class="text-gray-600">{{ $assignment->classroom->full_name }}</span>
                </div>
                <div class="flex items-center gap-3">
                    <i class="fas fa-calendar text-gray-400 w-5"></i>
                    <span class="text-gray-600">{{ $assignment->due_date->format('Y/m/d H:i') }}</span>
                </div>
                <div class="flex items-center gap-3">
                    <i class="fas fa-star text-gray-400 w-5"></i>
                    <span class="text-gray-600">{{ $assignment->max_score }} درجة</span>
                </div>
                
                @if($assignment->attachment)
                    <div class="pt-4 border-t">
                        <a href="{{ Storage::url($assignment->attachment) }}" target="_blank"
                           class="flex items-center gap-2 text-indigo-600 hover:text-indigo-700">
                            <i class="fas fa-paperclip"></i>
                            <span>تحميل المرفق</span>
                        </a>
                    </div>
                @endif
            </div>
            
            <div class="p-4 border-t space-y-2">
                <a href="{{ route('teacher.assignments.edit', $assignment) }}" 
                   class="block w-full py-2 px-4 bg-blue-50 text-blue-700 rounded-xl hover:bg-blue-100 transition text-center">
                    <i class="fas fa-edit ml-1"></i> تعديل
                </a>
            </div>
        </div>
    </div>
    
    <!-- Submissions -->
    <div class="lg:col-span-2">
        <!-- Stats -->
        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-xl p-4 border border-gray-100 text-center">
                <p class="text-2xl font-bold text-indigo-600">{{ $assignment->submissions->count() }}</p>
                <p class="text-sm text-gray-500">تسليمات</p>
            </div>
            <div class="bg-white rounded-xl p-4 border border-gray-100 text-center">
                <p class="text-2xl font-bold text-green-600">{{ $assignment->submissions->whereNotNull('score')->count() }}</p>
                <p class="text-sm text-gray-500">مُقيّم</p>
            </div>
            <div class="bg-white rounded-xl p-4 border border-gray-100 text-center">
                <p class="text-2xl font-bold text-amber-600">{{ $assignment->submissions->whereNull('score')->count() }}</p>
                <p class="text-sm text-gray-500">قيد الانتظار</p>
            </div>
        </div>
        
        <!-- Submissions List -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="p-4 border-b border-gray-100">
                <h4 class="font-bold text-gray-800">تسليمات الطلاب</h4>
            </div>
            
            <div class="divide-y divide-gray-100">
                @forelse($assignment->submissions as $submission)
                    <div class="p-4 hover:bg-gray-50">
                        <div class="flex items-center gap-4">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($submission->student->name) }}&background=6366f1&color=fff" 
                                 alt="" class="w-12 h-12 rounded-full">
                            <div class="flex-1">
                                <p class="font-semibold text-gray-800">{{ $submission->student->name }}</p>
                                <p class="text-sm text-gray-500">
                                    سُلّم {{ $submission->submitted_at->diffForHumans() }}
                                    @if($submission->submitted_at->gt($assignment->due_date))
                                        <span class="text-red-500">(متأخر)</span>
                                    @endif
                                </p>
                            </div>
                            
                            @if($submission->score !== null)
                                <div class="text-center">
                                    <p class="text-lg font-bold {{ $submission->score >= ($assignment->max_score * 0.6) ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $submission->score }}
                                    </p>
                                    <p class="text-xs text-gray-500">من {{ $assignment->max_score }}</p>
                                </div>
                            @else
                                <button onclick="openGradeModal({{ $submission->id }}, '{{ $submission->student->name }}')"
                                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition text-sm">
                                    <i class="fas fa-check ml-1"></i> تقييم
                                </button>
                            @endif
                            
                            @if($submission->file)
                                <a href="{{ Storage::url($submission->file) }}" target="_blank"
                                   class="p-2 text-gray-500 hover:text-indigo-600 transition">
                                    <i class="fas fa-download"></i>
                                </a>
                            @endif
                        </div>
                        
                        @if($submission->content)
                            <div class="mt-3 p-3 bg-gray-50 rounded-lg text-sm text-gray-600">
                                {{ Str::limit($submission->content, 200) }}
                            </div>
                        @endif
                        
                        @if($submission->feedback)
                            <div class="mt-3 p-3 bg-indigo-50 rounded-lg text-sm text-indigo-700">
                                <strong>ملاحظات:</strong> {{ $submission->feedback }}
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-3 text-gray-300"></i>
                        <p>لم يتم تسليم أي واجبات بعد</p>
                    </div>
                @endforelse
            </div>
        </div>
        
        <!-- Students who haven't submitted -->
        @php
            $submittedStudentIds = $assignment->submissions->pluck('student_id')->toArray();
            $notSubmitted = $assignment->classroom->students->whereNotIn('id', $submittedStudentIds);
        @endphp
        
        @if($notSubmitted->count() > 0)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 mt-6">
                <div class="p-4 border-b border-gray-100">
                    <h4 class="font-bold text-gray-800">لم يسلموا بعد ({{ $notSubmitted->count() }})</h4>
                </div>
                <div class="p-4">
                    <div class="flex flex-wrap gap-2">
                        @foreach($notSubmitted as $student)
                            <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm">
                                {{ $student->name }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Grade Modal -->
<div id="gradeModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md mx-4">
        <h3 class="text-lg font-bold text-gray-800 mb-4">تقييم الواجب</h3>
        <p id="gradeStudentName" class="text-gray-600 mb-4"></p>
        
        <form id="gradeForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">الدرجة</label>
                <input type="number" name="score" min="0" max="{{ $assignment->max_score }}" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500"
                    placeholder="من {{ $assignment->max_score }}">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">ملاحظات (اختياري)</label>
                <textarea name="feedback" rows="3"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500"
                    placeholder="اكتب ملاحظاتك هنا..."></textarea>
            </div>
            
            <div class="flex gap-3">
                <button type="submit" class="flex-1 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition">
                    حفظ التقييم
                </button>
                <button type="button" onclick="closeGradeModal()" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition">
                    إلغاء
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openGradeModal(submissionId, studentName) {
        document.getElementById('gradeStudentName').textContent = 'الطالب: ' + studentName;
        document.getElementById('gradeForm').action = '{{ url("teacher/assignments") }}/{{ $assignment->id }}/submissions/' + submissionId + '/grade';
        document.getElementById('gradeModal').classList.remove('hidden');
        document.getElementById('gradeModal').classList.add('flex');
    }
    
    function closeGradeModal() {
        document.getElementById('gradeModal').classList.add('hidden');
        document.getElementById('gradeModal').classList.remove('flex');
    }
</script>
@endpush
@endsection
