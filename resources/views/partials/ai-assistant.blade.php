{{-- Assistant IA VintApp — widget flottant (invités et connectés) --}}
{{-- shortcut: position bas-droite partagée avec support/widget.blade.php, décaler si les deux sont activés --}}
<div id="aiAssistant" class="fixed bottom-6 right-6 z-50">
    {{-- Bouton flottant --}}
    <button id="aiAssistantToggle"
            type="button"
            aria-label="Ouvrir l'assistant VintApp"
            class="w-14 h-14 rounded-full bg-vinted-primary-600 text-white shadow-lg hover:shadow-xl hover:bg-vinted-primary-700 hover:scale-105 transition-all duration-200">
        <svg class="w-6 h-6 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 3.104a2.25 2.25 0 00-1.5 0c-.478.182-.92.45-1.303.792L3.53 7.02a2.25 2.25 0 00-.64 1.474v7.012c0 .553.228 1.08.64 1.474l3.417 3.124c.383.342.825.61 1.303.792a2.25 2.25 0 001.5 0c.478-.182.92-.45 1.303-.792l1.217-1.112a2.25 2.25 0 011.54-.612h4.512A2.25 2.25 0 0020.25 16.5V7.5A2.25 2.25 0 0018 5.25h-4.512a2.25 2.25 0 01-1.54-.612L10.73 3.896a2.25 2.25 0 00-1.303-.792z"/>
        </svg>
    </button>

    {{-- Panneau de conversation --}}
    <div id="aiAssistantPanel"
         class="hidden absolute bottom-16 right-0 w-96 max-w-[calc(100vw-2rem)] bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="bg-vinted-primary-600 text-white px-5 py-4 flex justify-between items-start">
            <div>
                <h3 class="text-lg font-semibold mb-0.5">Assistant VintApp</h3>
                <p class="text-vinted-primary-100 text-sm">Posez votre question, je vous réponds</p>
            </div>
            <button id="aiAssistantClose" type="button" aria-label="Fermer"
                    class="text-white hover:bg-white/10 rounded-md p-1 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div id="aiAssistantMessages" class="h-80 overflow-y-auto p-4 space-y-3 bg-gray-50 dark:bg-gray-900/40"></div>

        <form id="aiAssistantForm" class="border-t border-gray-200 dark:border-gray-700 p-3 flex items-end gap-2">
            <textarea id="aiAssistantInput"
                      rows="1"
                      maxlength="2000"
                      placeholder="Votre message..."
                      class="flex-1 resize-none rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-white outline-none transition-colors focus:border-vinted-primary-400 focus:ring-2 focus:ring-vinted-primary-100 dark:focus:ring-vinted-primary-500/20"></textarea>
            <button type="submit" id="aiAssistantSend"
                    class="h-10 w-10 shrink-0 inline-flex items-center justify-center rounded-md bg-vinted-primary-600 text-white hover:bg-vinted-primary-700 transition-colors disabled:opacity-50">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
            </button>
        </form>
    </div>
</div>

<style>
    #aiAssistantMessages .ai-msg {
        max-width: 85%;
        padding: .5rem .75rem;
        border-radius: .75rem;
        font-size: .875rem;
        line-height: 1.35;
        white-space: pre-wrap;
        word-break: break-word;
    }
    #aiAssistantMessages .ai-msg-user {
        margin-left: auto;
        background: var(--color-primary-600, #7c3aed);
        color: #fff;
        border-bottom-right-radius: .25rem;
    }
    #aiAssistantMessages .ai-msg-bot {
        background: #fff;
        color: #111827;
        border: 1px solid #e5e7eb;
        border-bottom-left-radius: .25rem;
    }
    .dark #aiAssistantMessages .ai-msg-bot {
        background: #1f2937;
        color: #f3f4f6;
        border-color: #374151;
    }
</style>

<script>
(function () {
    const panel = document.getElementById('aiAssistantPanel');
    const toggle = document.getElementById('aiAssistantToggle');
    const close = document.getElementById('aiAssistantClose');
    const form = document.getElementById('aiAssistantForm');
    const input = document.getElementById('aiAssistantInput');
    const sendBtn = document.getElementById('aiAssistantSend');
    const messages = document.getElementById('aiAssistantMessages');

    if (!panel || !toggle) return;

    // Historique de conversation (mémoire de session uniquement).
    const history = [];
    const greeting = "Bonjour ! Je suis l'assistant VintApp. Comment puis-je vous aider ?";

    function appendMessage(text, kind) {
        const el = document.createElement('div');
        el.className = 'ai-msg ' + (kind === 'user' ? 'ai-msg-user' : 'ai-msg-bot');
        el.textContent = text;
        messages.appendChild(el);
        messages.scrollTop = messages.scrollHeight;
        return el;
    }

    appendMessage(greeting, 'bot');

    toggle.addEventListener('click', function () {
        panel.classList.toggle('hidden');
        if (!panel.classList.contains('hidden')) input.focus();
    });

    close.addEventListener('click', function () {
        panel.classList.add('hidden');
    });

    // Auto-resize de la zone de saisie + Entrée pour envoyer.
    input.addEventListener('input', function () {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 120) + 'px';
    });
    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            form.requestSubmit();
        }
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const message = input.value.trim();
        if (!message) return;

        appendMessage(message, 'user');
        history.push({ role: 'user', content: message });
        input.value = '';
        input.style.height = 'auto';

        const thinking = appendMessage('…', 'bot');
        sendBtn.disabled = true;

        fetch(@json(route('assistant.ask')), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ message: message, history: history.slice(0, -1) })
        })
            .then(function (response) {
                return response.json().catch(function () { return {}; }).then(function (data) {
                    return { ok: response.ok, data: data };
                });
            })
            .then(function (result) {
                if (result.ok && result.data.success) {
                    thinking.textContent = result.data.answer;
                    history.push({ role: 'assistant', content: result.data.answer });
                } else {
                    thinking.textContent = (result.data.error || 'Une erreur est survenue. Réessayez plus tard.');
                }
                messages.scrollTop = messages.scrollHeight;
            })
            .catch(function () {
                thinking.textContent = 'Connexion impossible. Vérifiez votre réseau et réessayez.';
            })
            .finally(function () {
                sendBtn.disabled = false;
                input.focus();
            });
    });
})();
</script>
