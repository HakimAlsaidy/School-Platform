@extends(auth()->user()->isAdmin() ? 'layouts.dashboard' : (auth()->user()->isTeacher() ? 'layouts.dashboard' : 'layouts.dashboard'))

@section('page-title', 'المساعد الذكي')
@section('page-description', 'اسألني عن أي شيء في المنصة')

@section('dashboard-content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6 text-center">
        <div class="w-20 h-20 mx-auto gradient-bg rounded-3xl flex items-center justify-center mb-4 animate-float">
            <i class="fas fa-robot text-white text-4xl"></i>
        </div>
        <h2 class="text-2xl font-bold text-gray-800">المساعد الذكي</h2>
        <p class="text-gray-500 mt-1">مساعدك الشخصي المدعوم بالذكاء الاصطناعي لفهم واستخدام المنصة</p>
    </div>

    {{-- Context Student Selector (for Parents) --}}
    @if($children->isNotEmpty())
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-4">
        <label class="block text-sm font-semibold text-gray-700 mb-2">
            <i class="fas fa-child ml-1 text-indigo-500"></i>
            سياق المحادثة (اختر الطالب)
        </label>
        <select id="assistantStudentId" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500">
            <option value="">-- عام --</option>
            @foreach($children as $child)
                <option value="{{ $child->id }}">{{ $child->name }}</option>
            @endforeach
        </select>
    </div>
    @endif

    {{-- Chat Container --}}
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        {{-- Chat Header --}}
        <div class="gradient-bg px-6 py-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                <i class="fas fa-robot text-white"></i>
            </div>
            <div class="flex-1">
<p class="text-white font-bold">مساعد إيدو لينك</p>
                <p class="text-indigo-200 text-xs flex items-center gap-1">
                    <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                    متصل الآن
                </p>
            </div>
            <button onclick="clearChat()" class="text-indigo-200 hover:text-white transition" title="مسح المحادثة">
                <i class="fas fa-trash"></i>
            </button>
        </div>

        {{-- Messages Area --}}
        <div id="chatMessages" class="h-[450px] overflow-y-auto p-6 space-y-4 bg-gray-50">
            {{-- Welcome Message --}}
            <div class="flex items-start gap-3">
                <div class="w-9 h-9 gradient-bg rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-robot text-white text-sm"></i>
                </div>
                <div class="bg-white rounded-2xl rounded-tr-sm px-4 py-3 shadow-sm max-w-[80%]">
<p class="text-gray-700">مرحباً بك! 👋 أنا المساعد الذكي لمنصة إيدو لينك. يمكنني مساعدتك في فهم الميزات، متابعة الأداء، أو تقديم إرشادات. كيف يمكنني مساعدتك اليوم؟</p>
                </div>
            </div>
        </div>

        {{-- Suggestions --}}
        <div id="suggestions" class="px-6 py-3 border-t border-gray-100 bg-white">
            <p class="text-xs text-gray-400 mb-2">أسئلة مقترحة:</p>
            <div class="flex flex-wrap gap-2" id="suggestionChips">
                @foreach(['مرحباً', 'كيف أستخدم المنصة؟', 'عرض التحليلات'] as $suggestion)
                    <button onclick="askSuggestion('{{ $suggestion }}')" class="text-sm px-3 py-1.5 bg-indigo-50 text-indigo-600 rounded-full hover:bg-indigo-100 transition">
                        {{ $suggestion }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Input Area --}}
        <div class="p-4 border-t border-gray-100 bg-white">
            <div class="flex items-center gap-3">
                <div class="flex-1 relative">
                    <input type="text" id="chatInput" 
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                           placeholder="اكتب سؤالك هنا..."
                           onkeydown="if(event.key === 'Enter') sendMessage()">
                </div>
                <button onclick="sendMessage()" class="w-12 h-12 gradient-bg rounded-xl flex items-center justify-center text-white hover:opacity-90 transition shadow-lg">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let currentStudentId = document.getElementById('assistantStudentId')?.value || '';

if (document.getElementById('assistantStudentId')) {
    document.getElementById('assistantStudentId').addEventListener('change', function() {
        currentStudentId = this.value;
    });
}

function addMessage(text, isUser = false) {
    const container = document.getElementById('chatMessages');
    const wrapper = document.createElement('div');
    wrapper.className = `flex items-start gap-3 ${isUser ? 'flex-row-reverse' : ''}`;

    const avatar = document.createElement('div');
    avatar.className = `w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 ${isUser ? 'bg-indigo-100' : 'gradient-bg'}`;
    avatar.innerHTML = `<i class="fas ${isUser ? 'fa-user text-indigo-600' : 'fa-robot text-white'} text-sm"></i>`;

    const bubble = document.createElement('div');
    bubble.className = `${isUser ? 'bg-indigo-600 text-white rounded-2xl rounded-tl-sm' : 'bg-white rounded-2xl rounded-tr-sm shadow-sm'} px-4 py-3 max-w-[80%]`;
    bubble.innerHTML = `<p class="${isUser ? 'text-white' : 'text-gray-700'}">${text}</p>`;

    wrapper.appendChild(avatar);
    wrapper.appendChild(bubble);
    container.appendChild(wrapper);
    container.scrollTop = container.scrollHeight;
    return bubble;
}

function showTyping() {
    const bubble = addMessage('<span class="typing-dots"><span></span><span></span><span></span></span>', false);
    return bubble;
}

function removeTyping(bubble) {
    if (bubble && bubble.parentElement) {
        bubble.parentElement.remove();
    }
}

async function sendMessage() {
    const input = document.getElementById('chatInput');
    const question = input.value.trim();
    if (!question) return;

    // Add user message
    addMessage(question, true);
    input.value = '';

    // Show typing indicator
    const typingBubble = showTyping();

    try {
        const response = await fetch('{{ route('ai.ask') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                question: question,
                student_id: currentStudentId || null,
            })
        });

        const data = await response.json();
        removeTyping(typingBubble);

        if (data.answer) {
            addMessage(data.answer, false);
        }

        // Update suggestions
        if (data.suggestions && data.suggestions.length) {
            const chips = document.getElementById('suggestionChips');
            chips.innerHTML = '';
            data.suggestions.forEach(s => {
                const btn = document.createElement('button');
                btn.onclick = () => askSuggestion(s);
                btn.className = 'text-sm px-3 py-1.5 bg-indigo-50 text-indigo-600 rounded-full hover:bg-indigo-100 transition';
                btn.textContent = s;
                chips.appendChild(btn);
            });
        }
    } catch (error) {
        removeTyping(typingBubble);
        addMessage('عذراً، حدث خطأ في الاتصال. حاول مرة أخرى.', false);
    }
}

function askSuggestion(text) {
    document.getElementById('chatInput').value = text;
    sendMessage();
}

function clearChat() {
    const container = document.getElementById('chatMessages');
    container.innerHTML = `
        <div class="flex items-start gap-3">
            <div class="w-9 h-9 gradient-bg rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-robot text-white text-sm"></i>
            </div>
            <div class="bg-white rounded-2xl rounded-tr-sm px-4 py-3 shadow-sm max-w-[80%]">
                <p class="text-gray-700">تم مسح المحادثة. كيف يمكنني مساعدتك؟</p>
            </div>
        </div>
    `;
}
</script>

<style>
.typing-dots {
    display: inline-flex;
    gap: 4px;
    align-items: center;
}
.typing-dots span {
    width: 6px;
    height: 6px;
    background: #6366f1;
    border-radius: 50%;
    animation: typingBounce 1.4s infinite;
}
.typing-dots span:nth-child(2) { animation-delay: 0.2s; }
.typing-dots span:nth-child(3) { animation-delay: 0.4s; }
@keyframes typingBounce {
    0%, 60%, 100% { transform: translateY(0); opacity: 0.4; }
    30% { transform: translateY(-6px); opacity: 1; }
}
</style>
@endpush
