<!--
    VintApp Toast — composant shadcn-style global.
    Usage (JS) :
      toast.add({ description: "..." })                     // défaut
      toast.add({ type: "success", description: "..." })
      toast.add({ type: "info", description: "..." })
      toast.add({ type: "warning", description: "..." })
      toast.add({ type: "error", description: "...", priority: "high" })
      toast.add({ title: "Titre", description: "..." })
    Alias simple : window.showToast(message, type)  (type: success|info|warning|error|default)
-->
<div id="toast-container" class="fixed top-4 right-4 z-[100] space-y-2 w-[calc(100vw-2rem)] max-w-sm pointer-events-none" aria-live="polite" role="status"></div>

<script>
(function () {
    if (window.__vintToastInjected) return;
    window.__vintToastInjected = true;

    var TOAST_STYLES = {
        default: {
            container: 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700',
            iconColor: 'text-gray-500 dark:text-gray-400',
            icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
        },
        success: {
            container: 'bg-white dark:bg-gray-800 border-green-200 dark:border-green-800',
            iconColor: 'text-emerald-500 dark:text-emerald-400',
            icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
        },
        info: {
            container: 'bg-white dark:bg-gray-800 border-primary-200 dark:border-primary-800',
            iconColor: 'text-primary',
            icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
        },
        warning: {
            container: 'bg-white dark:bg-gray-800 border-amber-200 dark:border-amber-700',
            iconColor: 'text-amber-500 dark:text-amber-400',
            icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>'
        },
        error: {
            container: 'bg-white dark:bg-gray-800 border-red-200 dark:border-red-800',
            iconColor: 'text-red-500 dark:text-red-400',
            icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>'
        }
    };

    function getContainer() {
        var el = document.getElementById('toast-container');
        if (el) return el;
        el = document.createElement('div');
        el.id = 'toast-container';
        el.className = 'fixed top-4 right-4 z-[100] space-y-2 w-[calc(100vw-2rem)] max-w-sm pointer-events-none';
        el.setAttribute('aria-live', 'polite');
        el.setAttribute('role', 'status');
        document.body.appendChild(el);
        return el;
    }

    function add(options) {
        options = options || {};
        var type = options.type || 'default';
        if (!TOAST_STYLES[type]) type = 'default';
        var style = TOAST_STYLES[type];
        var container = getContainer();
        var id = 'toast-' + Date.now() + '-' + Math.random().toString(36).slice(2, 6);
        var borderColor = type === 'default' ? ' border-transparent' : ' border-l-4 border-l-transparent';
        var hideAfter = (options.type === 'error' && options.priority === 'high') ? 8000 : 4500;

        var toastEl = document.createElement('div');
        toastEl.id = id;
        toastEl.className = 'pointer-events-auto flex items-start gap-3 rounded-xl border border-l-4 border-gray-200 dark:border-gray-700 shadow-lg shadow-black/5 dark:shadow-black/20 p-3.5 pr-10 transform translate-x-full opacity-0 transition-all duration-300 ease-out ' + style.container;

        var titleHtml = options.title
            ? '<p class="text-sm font-semibold text-gray-900 dark:text-white">' + escapeHtml(options.title) + '</p>'
            : '';

        toastEl.innerHTML =
            '<div class="flex-shrink-0 ' + style.iconColor + '">' + style.icon + '</div>' +
            '<div class="flex-1 min-w-0">' +
                titleHtml +
                '<p class="' + (options.title ? 'text-sm text-gray-500 dark:text-gray-400 mt-0.5' : 'text-sm font-medium text-gray-700 dark:text-gray-200') + '">' + escapeHtml(options.description || '') + '</p>' +
            '</div>' +
            '<button type="button" onclick="document.getElementById(\'' + id + '\').remove()" class="absolute top-2.5 right-2.5 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition-colors" aria-label="Fermer">' +
                '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>' +
            '</button>';

        container.appendChild(toastEl);

        requestAnimationFrame(function () {
            requestAnimationFrame(function () {
                toastEl.classList.remove('translate-x-full', 'opacity-0');
            });
        });

        setTimeout(function () {
            dismiss(id);
        }, hideAfter);
    }

    function dismiss(id) {
        var el = document.getElementById(id);
        if (!el) return;
        el.classList.add('translate-x-full', 'opacity-0');
        setTimeout(function () {
            var e = document.getElementById(id);
            if (e && e.parentNode) e.parentNode.removeChild(e);
        }, 300);
    }

    function escapeHtml(str) {
        return String(str == null ? '' : str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    window.toast = {
        add: add,
        dismiss: dismiss
    };

    // Alias compatible : showToast(message, type) — ne remplace pas une fonction existante
    if (typeof window.showToast !== 'function') {
        window.showToast = function (message, type) {
            add({ type: type || 'default', description: message });
        };
    }
})();
</script>
