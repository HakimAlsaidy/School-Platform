@extends('layouts.dashboard')

@section('page-title', 'المواد الدراسية والفصلية')
@section('page-description', 'إدارة ومشاركة المواد التعليمية')

@section('dashboard-content')
<button onclick="openMaterialModal()" class="mb-6 px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition flex items-center gap-2">
    <i class="fas fa-plus"></i> إضافة مادة
</button>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($materials as $material)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden card-hover">
            <div class="gradient-bg p-5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-file-alt text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-white">{{ $material->title }}</h3>
                            <p class="text-xs text-white/80">{{ $material->subject->name }}</p>
                        </div>
                    </div>
                    <a href="{{ $material->external_url ?: ($material->file_path ? Storage::url($material->file_path) : '#') }}" target="_blank" class="text-white/70 hover:text-white">
                        <i class="fas fa-download"></i>
                    </a>
                </div>
            </div>
            <div class="p-4">
                <div class="flex items-center justify-between text-sm mb-3">
                    <span class="px-2 py-1 bg-indigo-50 text-indigo-600 rounded-full text-xs">{{ $material->type_label }}</span>
                    <span class="text-gray-500 text-xs">{{ $material->classroom->full_name ?? '-' }}</span>
                </div>
                @if($material->description)
                    <p class="text-sm text-gray-600 mb-3">{{ Str::limit($material->description, 80) }}</p>
                @endif
                <div class="flex items-center justify-between text-xs text-gray-500 pt-3 border-t">
                    <span>{{ $material->created_at->format('Y/m/d') }}</span>
                    <form action="{{ route('teacher.materials.destroy', $material) }}" method="POST" onsubmit="return confirm('حذف المادة؟')">
                        @csrf @method('DELETE')
                        <button class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full bg-white rounded-2xl border border-gray-100 p-12 text-center">
            <i class="fas fa-file-alt text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500">لا توجد مواد دراسية</p>
        </div>
    @endforelse
</div>

<!-- Modal إضافة مادة -->
<div id="materialModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black/50" onclick="closeMaterialModal()"></div>
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6">
            <h3 class="text-xl font-bold mb-4">إضافة مادة دراسية</h3>
            <form action="{{ route('teacher.materials.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <input type="text" name="title" required class="w-full px-4 py-3 border border-gray-200 rounded-xl" placeholder="عنوان المادة *">
                <div class="grid grid-cols-2 gap-4">
                    <select name="classroom_id" required class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                        <option value="">الفصل *</option>
                        @foreach($classrooms as $classroom)
                            <option value="{{ $classroom->id }}">{{ $classroom->full_name }}</option>
                        @endforeach
                    </select>
                    <select name="subject_id" required class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                        <option value="">المادة *</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                        @endforeach
                    </select>
                </div>
                <select name="type" id="materialType" class="w-full px-4 py-3 border border-gray-200 rounded-xl" onchange="toggleMaterialType()">
                    <option value="file">ملف</option>
                    <option value="link">رابط</option>
                    <option value="text">نص</option>
                    <option value="video">فيديو</option>
                </select>
                <input type="file" name="file" id="materialFileInput" class="w-full px-4 py-3 border border-gray-200 rounded-xl" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,image/*,video/*">
                <input type="url" name="external_url" id="materialUrlInput" class="w-full px-4 py-3 border border-gray-200 rounded-xl hidden" placeholder="رابط الملف">
                <textarea name="content" id="materialContentInput" rows="3" class="w-full px-4 py-3 border border-gray-200 rounded-xl hidden" placeholder="محتوى النص..."></textarea>
                <textarea name="description" rows="2" class="w-full px-4 py-3 border border-gray-200 rounded-xl" placeholder="الوصف"></textarea>
                <button type="submit" class="w-full px-6 py-3 bg-indigo-600 text-white rounded-xl">حفظ المادة</button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openMaterialModal(){document.getElementById('materialModal').classList.remove('hidden');document.body.style.overflow='hidden';}
function closeMaterialModal(){document.getElementById('materialModal').classList.add('hidden');document.body.style.overflow='';}
function toggleMaterialType(){
    const t=document.getElementById('materialType').value;
    document.getElementById('materialFileInput').classList.toggle('hidden',t!=='file'&&t!=='video');
    document.getElementById('materialUrlInput').classList.toggle('hidden',t!=='link');
    document.getElementById('materialContentInput').classList.toggle('hidden',t!=='text');
}
document.addEventListener('keydown',function(e){if(e.key==='Escape')closeMaterialModal();});
</script>
@endpush
@endsection
