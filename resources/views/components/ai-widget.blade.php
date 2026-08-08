{{-- AI Assistant Floating Widget - يمكن تضمينه في أي صفحة --}}

<!-- Floating Button -->
<button id="aiWidgetBtn" onclick="toggleAIWidget()" class="fixed bottom-20 lg:bottom-6 left-6 z-50 w-14 h-14 gradient-bg rounded-full flex items-center justify-center text-white shadow-2xl hover:scale-110 transition-transform" aria-label="المساعد الذكي">
    <i class="fas fa-robot text-xl"></i>
    <span class="absolute -top-1 -right-1 w-3 h-3 bg-green-400 rounded-full animate-pulse"></span>
</button>

<!-- Chat Panel -->
<div id="aiWidgetPanel" class="fixed bottom-36 lg:bottom-24 left-4 lg:left-6 z-50 w-[calc(100%-2rem)] max-w-sm bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden transition-all duration-300 transform scale-0 opacity-0 origin-bottom-left" style="transform-origin: bottom left;">
    {{-- Header --}}
    <div class="gradient-bg px-4 py-3 flex items-center gap-3">
        <div class="w-9 h-9 bg-white/20 rounded-full flex items-center justify-center">
            <i class="fas fa-robot text-white"></i>
        </div>
        <div class="flex-1">
            <p class="text-white font-bold text-sm">المساعد الذكي</p>
            <p class="text-indigo-200 text-xs flex items-center gap-1">
                <span class="w-1.5 h-1.5 bg-green-400 rounded-full animate-pulse"></span>
                متصل
            </p>
        </div>
        <button onclick="toggleAIWidget()" class="text-indigo-200 hover:text-white transition">
            <i class="fas fa-times"></i>
        </button>
    </div>

    {{-- Messages --}}
    <div id="aiWidgetMessages" class="h-72 overflow-y-auto p-4 space-y-3 bg-gray-50">
        <div class="flex items-start gap-2">
            <div class="w-7 h-7 gradient-bg rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-robot text-white text-xs"></i>
            </div>
            <div class="bg-white rounded-xl rounded-tr-sm px-3 py-2 shadow-sm max-w-[85%]">
                <p class="text-gray-700 text-sm">مرحباً! 👋 كيف يمكنني مساعدتك؟</p>
            </div>
        </div>
    </div>

    {{-- Input --}}
    <div class="p-3 border-t border-gray-100 bg-white">
        <div class="flex gap-2">
            <input type="text" id="aiWidgetInput" placeholder="اسألني أي شيء..."
                   class="flex-1 px-3 py-2 text-sm rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                   onkeydown="if(event.key === 'Enter') sendAIWidget()">
            <button onclick="sendAIWidget()" class="w-10 h-10 gradient-bg rounded-xl flex items-center justify-center text-white hover:opacity-90 transition">
                <i class="fas fa-paper-plane text-sm"></i>
            </button>
        </div>
    </div>
</div>

<script>
function toggleAIWidget() {
    const panel = document.getElementById('aiWidgetPanel');
    const isOpen = !panel.classList.contains('scale-0');
    
    if (isOpen) {
        panel.classList.add('scale-0', 'opacity-0');
    } else {
        panel.classList.remove('scale-0', 'opacity-0');
    }
}

function addWidgetMessage(text, isUser = false) {
    const container = document.getElementById('aiWidgetMessages');
    const wrapper = document.createElement('div');
    wrapper.className = `flex items-start gap-2 ${isUser ? 'flex-row-reverse' : ''}`;
    
    const avatar = document.createElement('div');
    avatar.className = `w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 ${isUser ? 'bg-indigo-100' : 'gradient-bg'}`;
    avatar.innerHTML = `<i class="fas ${isUser ? 'fa-user text-indigo-600' : 'fa-robot text-white'} text-xs"></i>`;
    
    const bubble = document.createElement('div');
    bubble.className = `${isUser ? 'bg-indigo-600 text-white rounded-xl rounded-tl-sm' : 'bg-white rounded-xl rounded-tr-sm shadow-sm'} px-3 py-2 max-w-[85%]`;
    bubble.innerHTML = `<p class="text-sm ${isUser ? 'text-white' : 'text-gray-700'}">${text}</p>`;
    
    wrapper.appendChild(avatar);
    wrapper.appendChild(bubble);
    container.appendChild(wrapper);
    container.scrollTop = container.scrollHeight;
}

async function sendAIWidget() {
    const input = document.getElementById('aiWidgetInput');
    const question = input.value.trim();
    if (!question) return;
    
    addWidgetMessage(question, true);
    input.value = '';
    
    try {
        const response = await fetch('{{ route('ai.ask') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ question: question })
        });
        
        const data = await response.json();
        addWidgetMessage(data.answer || 'عذراً، لم أتمكن من معالجة سؤالك.');
    } catch (error) {
        addWidgetMessage('عذراً، حدث خطأ في الاتصال.');
    }
}
</script>
