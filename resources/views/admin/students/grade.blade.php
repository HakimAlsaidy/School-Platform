@extends('layouts.dashboard')

@section('page-title', 'طلاب ' . $grade->name)
@section('page-description', 'عرض وإدارة طلاب الصف')

@section('dashboard-content')
<div x-data="{ open: false }" class="space-y-6">
    {{-- رابط العودة --}}
    <div class="flex items-center gap-2 text-sm">
        <a href="{{ route('admin.students.index') }}" class="text-indigo-600 hover:text-indigo-800">
            <i class="fas fa-arrow-right ml-1"></i>
            الطلاب
        </a>
        <span class="text-gray-400">/</span>
        <span class="text-gray-600">{{ $grade->name }}</span>
    </div>
    
    {{-- رأس الصفحة --}}
    <div class="bg-gradient-to-l from-indigo-500 to-indigo-600 rounded-2xl p-6 text-white">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-graduation-cap text-3xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold">{{ $grade->name }}</h2>
                    <p class="text-indigo-100">{{ $students->total() }} طالب في {{ $classrooms->count() }} فصل</p>
                </div>
            </div>
            <div class="flex gap-2">
                <button @click="open = true" class="px-6 py-3 bg-white/20 hover:bg-white/30 text-white font-semibold rounded-xl transition-all duration-300 flex items-center">
                    <i class="fas fa-plus ml-2"></i>
                    إضافة طالب
                </button>
            </div>
        </div>
    </div>
    
    {{-- فلترة وبحث --}}
    <div class="bg-white rounded-xl p-4 border border-gray-100">
        <form action="{{ route('admin.students.grade', $grade) }}" method="GET" class="flex flex-col sm:flex-row gap-4">
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="بحث بالاسم أو رقم الطالب..."
                       class="w-full pr-10 pl-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                <i class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            </div>
            <select name="classroom_id" class="px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500" onchange="this.form.submit()">
                <option value="">كل الفصول</option>
                @foreach($classrooms as $classroom)
                    <option value="{{ $classroom->id }}" {{ request('classroom_id') == $classroom->id ? 'selected' : '' }}>
                        {{ $classroom->name }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn-primary">
                <i class="fas fa-search ml-2"></i>
                بحث
            </button>
        </form>
    </div>

    {{-- جدول الطلاب --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-600">الطالب</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-600">رقم الطالب</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-600">الفصل</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-600">ولي الأمر</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-600">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($students as $student)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($student->name) }}&background=6366f1&color=fff" 
                                         alt="{{ $student->name }}" class="w-10 h-10 rounded-full">
                                    <div>
                                        <p class="font-semibold text-gray-800">{{ $student->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $student->gender == 'male' ? 'ذكر' : 'أنثى' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-600 font-mono text-sm">{{ $student->student_id }}</td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-sm">
                                    {{ $student->classroom->name ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-600">{{ $student->guardian->user->name ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.students.show', $student) }}" 
                                       class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.students.edit', $student) }}" 
                                       class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition" title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.students.destroy', $student) }}" method="POST" class="inline" 
                                          onsubmit="return confirm('هل أنت متأكد من حذف هذا الطالب؟ يمكنك استعادته لاحقاً من سلة المحذوفات.')">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="redirect_to_grade" value="1">
                                        <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-user-graduate text-4xl mb-3 text-gray-300"></i>
                                <p>لا يوجد طلاب في هذا الصف</p>
                                <a href="{{ route('admin.students.create') }}?grade_id={{ $grade->id }}" 
                                   class="text-indigo-600 hover:underline mt-2 inline-block">
                                    إضافة طالب جديد
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Mobile Cards View -->
        <div class="md:hidden p-4 space-y-4">
            @forelse($students as $student)
                <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
                    <div class="flex items-center gap-3 mb-3">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($student->name) }}&background=6366f1&color=fff" 
                             alt="{{ $student->name }}" class="w-12 h-12 rounded-full">
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-gray-800 truncate">{{ $student->name }}</p>
                            <p class="text-sm text-gray-500">{{ $student->student_id }}</p>
                        </div>
                        <span class="px-2 py-1 bg-indigo-100 text-indigo-700 rounded-full text-xs">
                            {{ $student->classroom->name ?? '-' }}
                        </span>
                    </div>
                    
                    <div class="flex items-center justify-between gap-2 pt-3 border-t border-gray-100">
                        <span class="text-sm text-gray-500">{{ $student->guardian->user->name ?? '-' }}</span>
                        <div class="flex gap-2">
                            <a href="{{ route('admin.students.show', $student) }}" 
                               class="p-2 text-blue-600 bg-blue-50 rounded-lg text-sm">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.students.edit', $student) }}" 
                               class="p-2 text-amber-600 bg-amber-50 rounded-lg text-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 text-gray-500">
                    <i class="fas fa-user-graduate text-4xl mb-3 text-gray-300"></i>
                    <p>لا يوجد طلاب</p>
                </div>
            @endforelse
        </div>
        
        <!-- Pagination -->
        @if($students->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $students->withQueryString()->links() }}
            </div>
        @endif
    </div>

    {{-- مودال إضافة طالب --}}
    <div x-show="open" x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto" 
         @keydown.escape.window="open = false">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" 
                 x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="open = false"></div>
            
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            
            <div x-show="open" x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-data="studentForm()"
                 class="inline-block w-full max-w-lg overflow-hidden text-right align-bottom bg-white rounded-2xl shadow-xl transform transition-all sm:my-8 sm:align-middle">
                
                <form action="{{ route('admin.students.store-in-grade', $grade) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">
                            <i class="fas fa-user-plus text-indigo-600 ml-2"></i>
                            تسجيل طالب جديد في {{ $grade->name }}
                        </h3>
                        
                        <div class="space-y-4">
                            {{-- صورة الطالب --}}
                            <div class="flex justify-center">
                                <label class="cursor-pointer group">
                                    <div class="w-24 h-24 rounded-full bg-gray-100 border-2 border-dashed border-gray-300 flex items-center justify-center overflow-hidden group-hover:border-indigo-400 transition">
                                        <template x-if="!photoPreview">
                                            <div class="text-center">
                                                <i class="fas fa-camera text-2xl text-gray-400 group-hover:text-indigo-500"></i>
                                                <p class="text-xs text-gray-400 mt-1">صورة</p>
                                            </div>
                                        </template>
                                        <template x-if="photoPreview">
                                            <img :src="photoPreview" class="w-full h-full object-cover">
                                        </template>
                                    </div>
                                    <input type="file" name="photo" accept="image/*" class="hidden" @change="previewPhoto($event)">
                                </label>
                            </div>

                            {{-- اسم الطالب --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">اسم الطالب <span class="text-red-500">*</span></label>
                                <input type="text" name="name" required 
                                       class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500"
                                       placeholder="الاسم الكامل">
                            </div>
                            
                            {{-- الجنس وتاريخ الميلاد --}}
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">الجنس <span class="text-red-500">*</span></label>
                                    <select name="gender" required 
                                            class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
                                        <option value="male">ذكر</option>
                                        <option value="female">أنثى</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">تاريخ الميلاد <span class="text-red-500">*</span></label>
                                    <input type="date" name="birth_date" required 
                                           class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
                                </div>
                            </div>
                            
                            {{-- ولي الأمر --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">ولي الأمر <span class="text-red-500">*</span></label>
                                <div class="flex gap-2">
                                    <select name="guardian_id" x-model="selectedGuardian" required 
                                            class="flex-1 px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
                                        <option value="">-- اختر ولي الأمر --</option>
                                        @foreach($guardians as $guardian)
                                            <option value="{{ $guardian->id }}">{{ $guardian->user->name }}</option>
                                        @endforeach
                                        <template x-for="g in newGuardians" :key="g.id">
                                            <option :value="g.id" x-text="g.name"></option>
                                        </template>
                                    </select>
                                    <button type="button" @click="showGuardianForm = !showGuardianForm" 
                                            class="px-3 py-2 bg-emerald-100 text-emerald-700 rounded-xl hover:bg-emerald-200 transition"
                                            title="إضافة ولي أمر جديد">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            
                            {{-- نموذج إضافة ولي أمر جديد --}}
                            <div x-show="showGuardianForm" x-transition class="p-4 bg-emerald-50 rounded-xl border border-emerald-200">
                                <h4 class="font-medium text-emerald-800 mb-3">
                                    <i class="fas fa-user-tie ml-2"></i>
                                    إضافة ولي أمر جديد
                                </h4>
                                <div class="space-y-3">
                                    {{-- الاسم والجوال --}}
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs text-gray-600 mb-1">الاسم الكامل <span class="text-red-500">*</span></label>
                                            <input type="text" x-model="guardianName" 
                                                   class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-600 mb-1">رقم الجوال <span class="text-red-500">*</span></label>
                                            <input type="text" x-model="guardianPhone" placeholder="05xxxxxxxx"
                                                   class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500">
                                        </div>
                                    </div>
                                    
                                    {{-- كلمة المرور وصلة القرابة --}}
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs text-gray-600 mb-1">كلمة المرور <span class="text-red-500">*</span></label>
                                            <input type="password" x-model="guardianPassword" 
                                                   class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-600 mb-1">صلة القرابة</label>
                                            <select x-model="guardianRelation" 
                                                    class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500">
                                                <option value="father">الأب</option>
                                                <option value="mother">الأم</option>
                                                <option value="other">آخر</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    {{-- المهنة وهاتف الطوارئ --}}
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs text-gray-600 mb-1">المهنة</label>
                                            <input type="text" x-model="guardianOccupation" placeholder="مثال: معلم، مهندس..."
                                                   class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-600 mb-1">هاتف الطوارئ</label>
                                            <input type="text" x-model="guardianEmergencyPhone" 
                                                   class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500">
                                        </div>
                                    </div>
                                    
                                    {{-- العنوان --}}
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">العنوان</label>
                                        <textarea x-model="guardianAddress" rows="2"
                                                  class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500"></textarea>
                                    </div>
                                    
                                    <div class="flex gap-2 pt-2">
                                        <button type="button" @click="addGuardian()" 
                                                :disabled="guardianLoading"
                                                class="flex-1 px-3 py-2 bg-emerald-600 text-white rounded-lg text-sm hover:bg-emerald-700 transition disabled:opacity-50">
                                            <span x-show="!guardianLoading">
                                                <i class="fas fa-check ml-1"></i> حفظ ولي الأمر
                                            </span>
                                            <span x-show="guardianLoading">
                                                <i class="fas fa-spinner fa-spin ml-1"></i> جاري الحفظ...
                                            </span>
                                        </button>
                                        <button type="button" @click="showGuardianForm = false" 
                                                class="px-3 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm hover:bg-gray-300 transition">
                                            إلغاء
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                        <button type="button" @click="$dispatch('close-modal'); open = false" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition">إلغاء</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition flex items-center gap-2">
                            <i class="fas fa-check ml-2"></i>
                            تسجيل الطالب
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function studentForm() {
    return {
        photoPreview: null,
        showGuardianForm: false,
        selectedGuardian: '',
        newGuardians: [],
        guardianName: '',
        guardianPhone: '',
        guardianPassword: '',
        guardianRelation: 'father',
        guardianOccupation: '',
        guardianEmergencyPhone: '',
        guardianAddress: '',
        guardianLoading: false,
        
        previewPhoto(event) {
            const file = event.target.files[0];
            if (file) {
                this.photoPreview = URL.createObjectURL(file);
            }
        },
        
        async addGuardian() {
            if (!this.guardianName || !this.guardianPhone || !this.guardianPassword) {
                alert('يرجى إدخال اسم ورقم جوال وكلمة مرور ولي الأمر');
                return;
            }
            
            if (this.guardianPassword.length < 6) {
                alert('كلمة المرور يجب أن تكون 6 أحرف على الأقل');
                return;
            }
            
            this.guardianLoading = true;
            
            try {
                const response = await fetch('{{ route("admin.students.guardian-quick") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        name: this.guardianName,
                        phone: this.guardianPhone,
                        password: this.guardianPassword,
                        relation: this.guardianRelation,
                        occupation: this.guardianOccupation,
                        emergency_phone: this.guardianEmergencyPhone,
                        address: this.guardianAddress
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.newGuardians.push(data.guardian);
                    this.selectedGuardian = data.guardian.id;
                    this.showGuardianForm = false;
                    // Reset form
                    this.guardianName = '';
                    this.guardianPhone = '';
                    this.guardianPassword = '';
                    this.guardianOccupation = '';
                    this.guardianEmergencyPhone = '';
                    this.guardianAddress = '';
                }
            } catch (error) {
                alert('حدث خطأ أثناء إضافة ولي الأمر');
            }
            
            this.guardianLoading = false;
        }
    }
}
</script>
@endpush
@endsection
