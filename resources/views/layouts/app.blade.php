<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'منصة المدرسة التعليمية')</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.1/css/all.min.css">
    
    <!-- Google Fonts - Arabic -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">
    
    <style>
        /* Alpine.js cloak - hide elements until Alpine loads */
        [x-cloak] { display: none !important; }
        
        * {
            font-family: 'Tajawal', sans-serif;
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .auth-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
        
        .input-focus:focus {
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .animate-float {
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-fade-in {
            animation: fadeIn 0.6s ease-out;
        }
        
        /* Voice Input Styles */
        .voice-input-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid #e5e7eb;
            background: white;
            color: #6b7280;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        .voice-input-btn:hover {
            border-color: #6366f1;
            color: #6366f1;
            background: #eef2ff;
        }
        
        .voice-input-btn.listening {
            border-color: #ef4444;
            background: #fef2f2;
            color: #ef4444;
            animation: pulse-recording 1.5s infinite;
        }
        
        @keyframes pulse-recording {
            0%, 100% { 
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4);
            }
            50% { 
                box-shadow: 0 0 0 10px rgba(239, 68, 68, 0);
            }
        }
        
        .voice-input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .voice-input-wrapper input,
        .voice-input-wrapper textarea {
            flex: 1;
        }
        
        /* Voice Input Modal */
        .voice-modal {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        
        .voice-modal.active {
            opacity: 1;
            visibility: visible;
        }
        
        .voice-modal-content {
            background: white;
            border-radius: 24px;
            padding: 32px;
            text-align: center;
            max-width: 400px;
            width: 90%;
            transform: scale(0.9);
            transition: transform 0.3s ease;
        }
        
        .voice-modal.active .voice-modal-content {
            transform: scale(1);
        }
        
        .voice-wave {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            height: 60px;
            margin: 20px 0;
        }
        
        .voice-wave span {
            width: 6px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 3px;
            animation: wave 1s ease-in-out infinite;
        }
        
        .voice-wave span:nth-child(1) { animation-delay: 0s; height: 20px; }
        .voice-wave span:nth-child(2) { animation-delay: 0.1s; height: 35px; }
        .voice-wave span:nth-child(3) { animation-delay: 0.2s; height: 50px; }
        .voice-wave span:nth-child(4) { animation-delay: 0.3s; height: 35px; }
        .voice-wave span:nth-child(5) { animation-delay: 0.4s; height: 20px; }
        
        @keyframes wave {
            0%, 100% { transform: scaleY(1); }
            50% { transform: scaleY(0.5); }
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-50 min-h-screen">
    {{-- Include Mobile Navbar for non-dashboard pages --}}
    @if(!request()->routeIs('admin.*') && !request()->routeIs('teacher.*') && !request()->routeIs('parent.*') && !request()->routeIs('messages.*'))
        @include('layouts.partials.mobile-navbar')
    @endif
    
    @yield('content')
    
    {{-- Voice Input Modal --}}
    <div id="voiceModal" class="voice-modal">
        <div class="voice-modal-content">
            <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-microphone text-red-500 text-3xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">جاري الاستماع...</h3>
            <p class="text-gray-500 mb-4">تحدث الآن باللغة العربية</p>
            <div class="voice-wave">
                <span></span>
                <span></span>
                <span></span>
                <span></span>
                <span></span>
            </div>
            <p id="voiceTranscript" class="text-gray-700 font-medium min-h-[24px]"></p>
            <button type="button" onclick="VoiceInput.stop()" class="mt-6 px-6 py-2 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition">
                إيقاف
            </button>
        </div>
    </div>
    
    {{-- Voice Input Script --}}
    <script>
    const VoiceInput = {
        recognition: null,
        currentTarget: null,
        isListening: false,
        
        init() {
            // Check browser support
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            
            if (!SpeechRecognition) {
                console.warn('Speech Recognition not supported');
                // Hide all voice buttons
                document.querySelectorAll('.voice-input-btn').forEach(btn => {
                    btn.style.display = 'none';
                });
                return;
            }
            
            this.recognition = new SpeechRecognition();
            this.recognition.lang = 'ar-SA'; // Arabic - Saudi Arabia
            this.recognition.continuous = true;
            this.recognition.interimResults = true;
            
            this.recognition.onresult = (event) => {
                let finalTranscript = '';
                let interimTranscript = '';
                
                for (let i = event.resultIndex; i < event.results.length; i++) {
                    const transcript = event.results[i][0].transcript;
                    if (event.results[i].isFinal) {
                        finalTranscript += transcript;
                    } else {
                        interimTranscript += transcript;
                    }
                }
                
                // Show transcript in modal
                const transcriptEl = document.getElementById('voiceTranscript');
                if (transcriptEl) {
                    transcriptEl.textContent = interimTranscript || finalTranscript;
                }
                
                // Update target input
                if (this.currentTarget && finalTranscript) {
                    const target = document.getElementById(this.currentTarget);
                    if (target) {
                        if (target.tagName === 'TEXTAREA' || target.type === 'text' || target.type === 'search') {
                            target.value += (target.value ? ' ' : '') + finalTranscript;
                        } else {
                            target.value = finalTranscript;
                        }
                        // Trigger input event
                        target.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                }
            };
            
            this.recognition.onerror = (event) => {
                console.error('Speech recognition error:', event.error);
                this.stop();
                
                if (event.error === 'not-allowed') {
                    alert('يرجى السماح بالوصول إلى الميكروفون للاستخدام الإدخال الصوتي');
                }
            };
            
            this.recognition.onend = () => {
                if (this.isListening) {
                    // Restart if still listening
                    try {
                        this.recognition.start();
                    } catch (e) {
                        this.stop();
                    }
                }
            };
            
            // Bind click events to voice buttons
            document.addEventListener('click', (e) => {
                const btn = e.target.closest('.voice-input-btn');
                if (btn) {
                    e.preventDefault();
                    const targetId = btn.dataset.target;
                    if (this.isListening && this.currentTarget === targetId) {
                        this.stop();
                    } else {
                        this.start(targetId, btn);
                    }
                }
            });
        },
        
        start(targetId, btn) {
            if (!this.recognition) {
                alert('متصفحك لا يدعم الإدخال الصوتي');
                return;
            }
            
            // Stop any existing session
            this.stop();
            
            this.currentTarget = targetId;
            this.isListening = true;
            
            // Update button state
            if (btn) {
                btn.classList.add('listening');
            }
            
            // Show modal
            const modal = document.getElementById('voiceModal');
            if (modal) {
                modal.classList.add('active');
                document.getElementById('voiceTranscript').textContent = '';
            }
            
            // Start recognition
            try {
                this.recognition.start();
            } catch (e) {
                console.error('Failed to start recognition:', e);
                this.stop();
            }
        },
        
        stop() {
            this.isListening = false;
            
            if (this.recognition) {
                try {
                    this.recognition.stop();
                } catch (e) {}
            }
            
            // Update all buttons
            document.querySelectorAll('.voice-input-btn').forEach(btn => {
                btn.classList.remove('listening');
            });
            
            // Hide modal
            const modal = document.getElementById('voiceModal');
            if (modal) {
                modal.classList.remove('active');
            }
            
            this.currentTarget = null;
        }
    };
    
    // Initialize on DOM ready
    document.addEventListener('DOMContentLoaded', () => {
        VoiceInput.init();
    });
    </script>
    
    @stack('scripts')
</body>
</html>
