@extends('layouts.superadmin')

@section('title', 'إعدادات المنصة')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800">⚙️ إعدادات المنصة</h1>
    <p class="text-gray-600 mt-2">تخصيص إعدادات المنصة العامة</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- الإعدادات العامة -->
    <div class="lg:col-span-2">
        <form action="{{ route('superadmin.settings.update') }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                <h2 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
                    <i class="fas fa-globe text-indigo-500"></i>
                    الإعدادات العامة
                </h2>

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">اسم المنصة</label>
                        <input type="text" name="platform_name" value="{{ $settings['platform_name'] }}"
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-indigo-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">وصف المنصة</label>
                        <textarea name="platform_description" rows="3"
                                  class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-indigo-500 outline-none resize-none">{{ $settings['platform_description'] }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">البريد الإلكتروني</label>
                            <input type="email" name="contact_email" value="{{ $settings['contact_email'] }}"
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-indigo-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">رقم الهاتف</label>
                            <input type="text" name="contact_phone" value="{{ $settings['contact_phone'] }}"
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-indigo-500 outline-none">
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                <h2 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
                    <i class="fas fa-school text-green-500"></i>
                    إعدادات المدارس
                </h2>

                <div class="space-y-6">
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                        <div>
                            <p class="font-medium text-gray-800">الموافقة التلقائية على المدارس</p>
                            <p class="text-sm text-gray-500">تفعيل المدارس مباشرة بدون مراجعة</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="auto_approve_schools" value="1" 
                                   class="sr-only peer" {{ $settings['auto_approve_schools'] ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:right-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                        </label>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">فترة التجربة (يوم)</label>
                            <input type="number" name="trial_days" value="{{ $settings['trial_days'] }}" min="7" max="90"
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-indigo-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">الحد الأقصى للطلاب (مجاني)</label>
                            <input type="number" name="max_students_free" value="{{ $settings['max_students_free'] }}" min="10" max="100"
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-indigo-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">الحد الأقصى للمعلمين (مجاني)</label>
                            <input type="number" name="max_teachers_free" value="{{ $settings['max_teachers_free'] }}" min="5" max="50"
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-indigo-500 outline-none">
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-8 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition">
                    <i class="fas fa-save ml-2"></i>
                    حفظ الإعدادات
                </button>
            </div>
        </form>
    </div>

    <!-- خطط الأسعار -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-24">
            <h2 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
                <i class="fas fa-tags text-purple-500"></i>
                خطط الأسعار
            </h2>

            <div class="space-y-4">
                @foreach($plans as $key => $plan)
                    <div class="p-4 border border-gray-200 rounded-xl">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-semibold text-gray-800">{{ $plan['name'] }}</span>
                            <span class="text-indigo-600 font-bold">{{ $plan['price'] }} ريال</span>
                        </div>
                        <div class="text-sm text-gray-500">
                            <p>الطلاب: {{ $plan['max_students'] == 0 ? 'غير محدود' : $plan['max_students'] }}</p>
                            <p>المعلمين: {{ $plan['max_teachers'] == 0 ? 'غير محدود' : $plan['max_teachers'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 p-4 bg-yellow-50 rounded-xl">
                <p class="text-sm text-yellow-700">
                    <i class="fas fa-info-circle ml-1"></i>
                    لتعديل الخطط، تواصل مع فريق التطوير
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
