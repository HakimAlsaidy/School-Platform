@extends('layouts.dashboard')

@section('page-title', 'إضافة طالب جديد')
@section('page-description', 'أدخل بيانات الطالب الجديد')

@section('dashboard-content')
<div class="max-w-4xl">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('admin.students.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">اسم الطالب <span class="text-red-500">*</span></label>
                    <div class="voice-input-wrapper">
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('name') border-red-500 @enderror"
                            placeholder="أدخل اسم الطالب">
                        <x-voice-input target="name" />
                    </div>
                    @error('name')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Gender -->
                <div>
                    <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">الجنس <span class="text-red-500">*</span></label>
                    <select id="gender" name="gender" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 @error('gender') border-red-500 @enderror">
                        <option value="">اختر الجنس</option>
                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>ذكر</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>أنثى</option>
                    </select>
                    @error('gender')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Birth Date with Voice Input -->
                <div>
                    <label for="birth_date" class="block text-sm font-medium text-gray-700 mb-2">تاريخ الميلاد <span class="text-red-500">*</span></label>
                    <div class="relative flex items-center gap-2">
                        <input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date') }}" required
                            class="flex-1 px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 @error('birth_date') border-red-500 @enderror">
                        <button type="button" onclick="startVoiceDateInput()" 
                            class="p-3 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-xl transition" title="أدخل التاريخ بالصوت">
                            <i class="fas fa-microphone" id="voiceDateIcon"></i>
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">يمكنك قول: "15 مارس 2015" أو "2015-03-15"</p>
                    @error('birth_date')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Grade (Required) -->
                <div>
                    <label for="grade_id" class="block text-sm font-medium text-gray-700 mb-2">الصف <span class="text-red-500">*</span></label>
                    <select id="grade_id" name="grade_id" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 @error('grade_id') border-red-500 @enderror"
                        onchange="loadClassrooms(this.value)">
                        <option value="">اختر الصف</option>
                        @foreach($grades as $grade)
                            <option value="{{ $grade->id }}" {{ old('grade_id', request('grade_id')) == $grade->id ? 'selected' : '' }}>
                                {{ $grade->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('grade_id')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Classroom (Optional) -->
                <div>
                    <label for="classroom_id" class="block text-sm font-medium text-gray-700 mb-2">الفصل <span class="text-gray-400 text-xs">(اختياري)</span></label>
                    <select id="classroom_id" name="classroom_id"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 @error('classroom_id') border-red-500 @enderror">
                        <option value="">-- بدون فصل (يُحدد لاحقاً) --</option>
                        @foreach($grades as $grade)
                            @if(old('grade_id', request('grade_id')) == $grade->id)
                                @foreach($grade->classrooms as $classroom)
                                    <option value="{{ $classroom->id }}" {{ old('classroom_id') == $classroom->id ? 'selected' : '' }}>
                                        {{ $classroom->name }}
                                    </option>
                                @endforeach
                            @endif
                        @endforeach
                    </select>
                    @error('classroom_id')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Guardian with Search and Add New -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        ولي الأمر <span class="text-red-500">*</span>
                    </label>
                    
                    <div class="flex items-center gap-2 mb-3">
                        <div class="flex-1 relative">
                            <input type="text" id="guardianSearch" placeholder="ابحث عن ولي الأمر بالاسم أو رقم الهاتف..."
                                class="w-full px-4 py-3 pr-10 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500"
                                autocomplete="off" onkeyup="searchGuardians(this.value)">
                            <i class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        </div>
                        <button type="button" onclick="openNewGuardianModal()" 
                            class="px-4 py-3 bg-green-50 hover:bg-green-100 text-green-600 rounded-xl transition flex items-center gap-2 whitespace-nowrap">
                            <i class="fas fa-plus"></i>
                            <span class="hidden sm:inline">إضافة ولي أمر جديد</span>
                        </button>
                    </div>
                    
                    <!-- Guardian Search Results -->
                    <div id="guardianSearchResults" class="hidden bg-white border border-gray-200 rounded-xl shadow-lg max-h-48 overflow-y-auto mb-3">
                    </div>
                    
                    <!-- Selected Guardian Display -->
                    <div id="selectedGuardianDisplay" class="hidden p-4 bg-indigo-50 border border-indigo-200 rounded-xl">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-indigo-200 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-indigo-600"></i>
                                </div>
                                <div>
                                    <p id="selectedGuardianName" class="font-semibold text-gray-800"></p>
                                    <p id="selectedGuardianPhone" class="text-sm text-gray-500"></p>
                                </div>
                            </div>
                            <button type="button" onclick="clearSelectedGuardian()" class="text-red-500 hover:text-red-700">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    
                    <input type="hidden" id="guardian_id" name="guardian_id" value="{{ old('guardian_id') }}" required>
                    
                    @error('guardian_id')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Address -->
                <div class="md:col-span-2">
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-2">العنوان</label>
                    <div class="voice-input-wrapper items-start">
                        <textarea id="address" name="address" rows="2"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 @error('address') border-red-500 @enderror"
                            placeholder="أدخل عنوان الطالب">{{ old('address') }}</textarea>
                        <x-voice-input target="address" />
                    </div>
                    @error('address')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Medical Notes -->
                <div class="md:col-span-2">
                    <label for="medical_notes" class="block text-sm font-medium text-gray-700 mb-2">ملاحظات طبية</label>
                    <div class="voice-input-wrapper items-start">
                        <textarea id="medical_notes" name="medical_notes" rows="3"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 @error('medical_notes') border-red-500 @enderror"
                            placeholder="أي ملاحظات طبية مهمة">{{ old('medical_notes') }}</textarea>
                        <x-voice-input target="medical_notes" />
                    </div>
                    @error('medical_notes')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Photo -->
                <div class="md:col-span-2">
                    <label for="photo" class="block text-sm font-medium text-gray-700 mb-2">صورة الطالب</label>
                    <input type="file" id="photo" name="photo" accept="image/*"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 @error('photo') border-red-500 @enderror">
                    @error('photo')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <!-- Actions -->
            <div class="flex items-center justify-end gap-4 pt-6 border-t">
                <a href="{{ route('admin.students.index') }}" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition">
                    إلغاء
                </a>
                <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition flex items-center gap-2">
                    <i class="fas fa-save"></i>
                    حفظ الطالب
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal لإضافة ولي أمر جديد -->
<div id="newGuardianModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeNewGuardianModal()"></div>
    
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto animate-modal">
            <div class="sticky top-0 bg-white flex items-center justify-between p-6 border-b border-gray-100 z-10">
                <h3 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-user-plus text-green-500 ml-2"></i>
                    إضافة ولي أمر جديد
                </h3>
                <button onclick="closeNewGuardianModal()" class="w-10 h-10 flex items-center justify-center rounded-lg hover:bg-gray-100">
                    <i class="fas fa-times text-gray-500"></i>
                </button>
            </div>
            
            <form id="newGuardianForm" class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">الاسم <span class="text-red-500">*</span></label>
                        <input type="text" id="newGuardianName" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500"
                            placeholder="اسم ولي الأمر">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">البريد الإلكتروني <span class="text-red-500">*</span></label>
                        <input type="email" id="newGuardianEmail" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500"
                            placeholder="example@email.com">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">رقم الهاتف <span class="text-red-500">*</span></label>
                        <input type="tel" id="newGuardianPhone" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500"
                            placeholder="05xxxxxxxx" dir="ltr">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">صلة القرابة <span class="text-red-500">*</span></label>
                        <select id="newGuardianRelation" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
                            <option value="">اختر صلة القرابة</option>
                            <option value="father">أب</option>
                            <option value="mother">أم</option>
                            <option value="grandfather">جد</option>
                            <option value="grandmother">جدة</option>
                            <option value="uncle">عم/خال</option>
                            <option value="aunt">عمة/خالة</option>
                            <option value="brother">أخ</option>
                            <option value="sister">أخت</option>
                            <option value="other">أخرى</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">كلمة المرور <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="password" id="newGuardianPassword" required minlength="8"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500"
                                placeholder="8 أحرف على الأقل">
                            <button type="button" onclick="togglePasswordVisibility('newGuardianPassword', this)" 
                                class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">تأكيد كلمة المرور <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="password" id="newGuardianPasswordConfirm" required minlength="8"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500"
                                placeholder="أعد كتابة كلمة المرور">
                            <button type="button" onclick="togglePasswordVisibility('newGuardianPasswordConfirm', this)" 
                                class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">العنوان <span class="text-gray-400 text-xs">(اختياري)</span></label>
                    <textarea id="newGuardianAddress" rows="2"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500"
                        placeholder="عنوان السكن"></textarea>
                </div>
                
                <div id="newGuardianError" class="hidden p-3 bg-red-50 border border-red-200 rounded-xl text-red-600 text-sm"></div>
                
                <div class="flex items-center gap-3 pt-4 border-t">
                    <button type="submit" class="flex-1 px-6 py-3 bg-green-600 text-white rounded-xl hover:bg-green-700 transition font-semibold">
                        <i class="fas fa-plus ml-2"></i>
                        إضافة ولي الأمر
                    </button>
                    <button type="button" onclick="closeNewGuardianModal()" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition">
                        إلغاء
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    @keyframes modalIn {
        from { opacity: 0; transform: scale(0.95) translateY(-10px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }
    .animate-modal { animation: modalIn 0.2s ease-out; }
</style>

@push('scripts')
<script>
    // بيانات أولياء الأمور
    const guardiansData = @json($guardians->map(fn($g) => ['id' => $g->id, 'name' => $g->user->name, 'phone' => $g->phone ?? $g->user->phone ?? '']));
    
    // البحث في أولياء الأمور
    function searchGuardians(query) {
        const resultsDiv = document.getElementById('guardianSearchResults');
        
        if (query.length < 2) {
            resultsDiv.classList.add('hidden');
            return;
        }
        
        const filtered = guardiansData.filter(g => 
            g.name.includes(query) || (g.phone && g.phone.includes(query))
        );
        
        if (filtered.length === 0) {
            resultsDiv.innerHTML = '<div class="p-4 text-center text-gray-500">لا توجد نتائج</div>';
        } else {
            resultsDiv.innerHTML = filtered.map(g => `
                <div class="p-3 hover:bg-indigo-50 cursor-pointer transition flex items-center gap-3" onclick="selectGuardian(${g.id}, '${g.name}', '${g.phone || ''}')">
                    <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-indigo-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800">${g.name}</p>
                        <p class="text-xs text-gray-500">${g.phone || 'بدون رقم'}</p>
                    </div>
                </div>
            `).join('');
        }
        
        resultsDiv.classList.remove('hidden');
    }
    
    // اختيار ولي أمر
    function selectGuardian(id, name, phone) {
        document.getElementById('guardian_id').value = id;
        document.getElementById('selectedGuardianName').textContent = name;
        document.getElementById('selectedGuardianPhone').textContent = phone || 'بدون رقم';
        document.getElementById('selectedGuardianDisplay').classList.remove('hidden');
        document.getElementById('guardianSearchResults').classList.add('hidden');
        document.getElementById('guardianSearch').value = '';
    }
    
    // مسح ولي الأمر المحدد
    function clearSelectedGuardian() {
        document.getElementById('guardian_id').value = '';
        document.getElementById('selectedGuardianDisplay').classList.add('hidden');
    }
    
    // فتح modal إضافة ولي أمر جديد
    function openNewGuardianModal() {
        document.getElementById('newGuardianModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    
    // إغلاق modal 
    function closeNewGuardianModal() {
        document.getElementById('newGuardianModal').classList.add('hidden');
        document.body.style.overflow = '';
        // مسح الحقول
        document.getElementById('newGuardianForm').reset();
        document.getElementById('newGuardianError').classList.add('hidden');
    }
    
    // إظهار/إخفاء كلمة المرور
    function togglePasswordVisibility(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon = btn.querySelector('i');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
    
    // إرسال نموذج ولي الأمر الجديد
    document.getElementById('newGuardianForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const errorDiv = document.getElementById('newGuardianError');
        errorDiv.classList.add('hidden');
        
        // التحقق من تطابق كلمتي المرور
        const password = document.getElementById('newGuardianPassword').value;
        const passwordConfirm = document.getElementById('newGuardianPasswordConfirm').value;
        
        if (password !== passwordConfirm) {
            errorDiv.textContent = 'كلمتا المرور غير متطابقتين';
            errorDiv.classList.remove('hidden');
            return;
        }
        
        if (password.length < 8) {
            errorDiv.textContent = 'كلمة المرور يجب أن تكون 8 أحرف على الأقل';
            errorDiv.classList.remove('hidden');
            return;
        }
        
        const data = {
            name: document.getElementById('newGuardianName').value,
            email: document.getElementById('newGuardianEmail').value,
            phone: document.getElementById('newGuardianPhone').value,
            relationship: document.getElementById('newGuardianRelation').value,
            password: password,
            password_confirmation: passwordConfirm,
            address: document.getElementById('newGuardianAddress').value,
            _token: '{{ csrf_token() }}'
        };
        
        try {
            const response = await fetch('{{ route("admin.guardians.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            if (response.ok) {
                // إضافة للقائمة واختياره
                guardiansData.push({id: result.id, name: data.name, phone: data.phone});
                selectGuardian(result.id, data.name, data.phone);
                closeNewGuardianModal();
                
                // مسح النموذج
                document.getElementById('newGuardianForm').reset();
            } else {
                errorDiv.textContent = result.message || 'حدث خطأ أثناء الإضافة';
                errorDiv.classList.remove('hidden');
            }
        } catch (error) {
            errorDiv.textContent = 'حدث خطأ في الاتصال';
            errorDiv.classList.remove('hidden');
        }
    });
    
    // الإدخال الصوتي للتاريخ
    let voiceRecognition = null;
    
    function startVoiceDateInput() {
        if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
            alert('متصفحك لا يدعم الإدخال الصوتي');
            return;
        }
        
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        voiceRecognition = new SpeechRecognition();
        voiceRecognition.lang = 'ar-SA';
        voiceRecognition.continuous = false;
        
        const icon = document.getElementById('voiceDateIcon');
        icon.classList.remove('fa-microphone');
        icon.classList.add('fa-spinner', 'fa-spin');
        
        voiceRecognition.onresult = function(event) {
            const transcript = event.results[0][0].transcript;
            const date = parseArabicDate(transcript);
            if (date) {
                document.getElementById('birth_date').value = date;
            } else {
                alert('لم أفهم التاريخ. حاول مرة أخرى بصيغة: "15 مارس 2015"');
            }
            icon.classList.remove('fa-spinner', 'fa-spin');
            icon.classList.add('fa-microphone');
        };
        
        voiceRecognition.onerror = function() {
            icon.classList.remove('fa-spinner', 'fa-spin');
            icon.classList.add('fa-microphone');
        };
        
        voiceRecognition.start();
    }
    
    // تحويل التاريخ العربي
    function parseArabicDate(text) {
        const months = {
            'يناير': '01', 'فبراير': '02', 'مارس': '03', 'أبريل': '04', 'ابريل': '04',
            'مايو': '05', 'يونيو': '06', 'يوليو': '07', 'أغسطس': '08', 'اغسطس': '08',
            'سبتمبر': '09', 'أكتوبر': '10', 'اكتوبر': '10', 'نوفمبر': '11', 'ديسمبر': '12'
        };
        
        // محاولة تحليل الصيغة: "15 مارس 2015"
        for (const [monthName, monthNum] of Object.entries(months)) {
            if (text.includes(monthName)) {
                const numbers = text.match(/\d+/g);
                if (numbers && numbers.length >= 2) {
                    const day = numbers[0].padStart(2, '0');
                    const year = numbers[1].length === 4 ? numbers[1] : '20' + numbers[1];
                    return `${year}-${monthNum}-${day}`;
                }
            }
        }
        
        // محاولة تحليل الصيغة: "2015-03-15" أو "2015/03/15"
        const match = text.match(/(\d{4})[-\/](\d{1,2})[-\/](\d{1,2})/);
        if (match) {
            return `${match[1]}-${match[2].padStart(2, '0')}-${match[3].padStart(2, '0')}`;
        }
        
        return null;
    }
    
    // إغلاق بـ Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeNewGuardianModal();
    });
    
    // إخفاء نتائج البحث عند النقر خارجها
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#guardianSearch') && !e.target.closest('#guardianSearchResults')) {
            document.getElementById('guardianSearchResults').classList.add('hidden');
        }
    });

    // بيانات الفصول لكل صف
    const gradeClassrooms = @json($grades->mapWithKeys(function($grade) {
        return [$grade->id => $grade->classrooms->map(function($c) {
            return ['id' => $c->id, 'name' => $c->name];
        })];
    }));

    // تحميل الفصول عند اختيار الصف
    function loadClassrooms(gradeId) {
        const classroomSelect = document.getElementById('classroom_id');
        classroomSelect.innerHTML = '<option value="">-- بدون فصل (يُحدد لاحقاً) --</option>';
        
        if (gradeId && gradeClassrooms[gradeId]) {
            gradeClassrooms[gradeId].forEach(function(classroom) {
                const option = document.createElement('option');
                option.value = classroom.id;
                option.textContent = classroom.name;
                classroomSelect.appendChild(option);
            });
        }
    }

    // تحميل الفصول عند تحميل الصفحة إذا كان هناك صف محدد
    document.addEventListener('DOMContentLoaded', function() {
        const gradeSelect = document.getElementById('grade_id');
        if (gradeSelect.value) {
            loadClassrooms(gradeSelect.value);
        }
    });
</script>
@endpush
@endsection
