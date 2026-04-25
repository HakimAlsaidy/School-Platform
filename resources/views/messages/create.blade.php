@extends('layouts.dashboard')

@section('page-title', 'إرسال رسالة جديدة')
@section('page-description', 'إرسال رسالة')

@section('dashboard-content')
<div class="mb-6">
    <a href="{{ route('messages.inbox') }}" class="text-indigo-600 hover:text-indigo-800">
        <i class="fas fa-arrow-right ml-2"></i>العودة لصندوق الوارد
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100">
    <div class="p-6 border-b border-gray-100">
        <h3 class="text-lg font-bold text-gray-800">رسالة جديدة</h3>
    </div>
    
    <form action="{{ route('messages.store') }}" method="POST" class="p-6">
        @csrf
        
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">إلى <span class="text-red-500">*</span></label>
            <select name="receiver_id" required
                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 @error('receiver_id') border-red-500 @enderror">
                <option value="">-- اختر المستلم --</option>
                
                @if(auth()->user()->isAdmin())
                    <optgroup label="المعلمون">
                        @foreach($teachers ?? [] as $teacher)
                            <option value="{{ $teacher->user_id }}" {{ old('receiver_id') == $teacher->user_id ? 'selected' : '' }}>
                                {{ $teacher->user->name }} (معلم)
                            </option>
                        @endforeach
                    </optgroup>
                    <optgroup label="أولياء الأمور">
                        @foreach($guardians ?? [] as $guardian)
                            <option value="{{ $guardian->user_id }}" {{ old('receiver_id') == $guardian->user_id ? 'selected' : '' }}>
                                {{ $guardian->user->name }} (ولي أمر)
                            </option>
                        @endforeach
                    </optgroup>
                @elseif(auth()->user()->isTeacher())
                    <optgroup label="الإدارة">
                        @foreach($admins ?? [] as $admin)
                            <option value="{{ $admin->id }}" {{ old('receiver_id') == $admin->id ? 'selected' : '' }}>
                                {{ $admin->name }} (إدارة)
                            </option>
                        @endforeach
                    </optgroup>
                    <optgroup label="أولياء الأمور">
                        @foreach($guardians ?? [] as $guardian)
                            <option value="{{ $guardian->user_id }}" {{ old('receiver_id') == $guardian->user_id ? 'selected' : '' }}>
                                {{ $guardian->user->name }} (ولي أمر)
                            </option>
                        @endforeach
                    </optgroup>
                @elseif(auth()->user()->isParent())
                    <optgroup label="الإدارة">
                        @foreach($admins ?? [] as $admin)
                            <option value="{{ $admin->id }}" {{ old('receiver_id') == $admin->id ? 'selected' : '' }}>
                                {{ $admin->name }} (إدارة)
                            </option>
                        @endforeach
                    </optgroup>
                    <optgroup label="المعلمون">
                        @foreach($teachers ?? [] as $teacher)
                            <option value="{{ $teacher->user_id }}" {{ old('receiver_id') == $teacher->user_id ? 'selected' : '' }}>
                                {{ $teacher->user->name }} (معلم)
                            </option>
                        @endforeach
                    </optgroup>
                @endif
            </select>
            @error('receiver_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">الموضوع <span class="text-red-500">*</span></label>
            <div class="voice-input-wrapper">
                <input type="text" id="subject" name="subject" value="{{ old('subject') }}" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 @error('subject') border-red-500 @enderror"
                    placeholder="موضوع الرسالة">
                <x-voice-input target="subject" />
            </div>
            @error('subject')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">الرسالة <span class="text-red-500">*</span></label>
            <div class="voice-input-wrapper items-start">
                <textarea id="content" name="content" rows="8" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 @error('content') border-red-500 @enderror"
                    placeholder="اكتب رسالتك هنا...">{{ old('content') }}</textarea>
                <x-voice-input target="content" />
            </div>
            @error('content')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <div class="flex items-center gap-4 pt-6 border-t">
            <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition">
                <i class="fas fa-paper-plane ml-2"></i>إرسال الرسالة
            </button>
            <a href="{{ route('messages.inbox') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition">
                إلغاء
            </a>
        </div>
    </form>
</div>
@endsection
