{{-- Voice Input Component --}}
{{-- Usage: <x-voice-input target="input_id" /> --}}

@props(['target', 'class' => ''])

<button type="button" 
    class="voice-input-btn {{ $class }}" 
    data-target="{{ $target }}"
    title="الإدخال الصوتي">
    <i class="fas fa-microphone"></i>
</button>
