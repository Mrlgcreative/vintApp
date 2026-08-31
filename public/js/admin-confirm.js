/**
 * VintConfirm — Modale de confirmation globale (plugin unique pour tester le back-office).
 *
 * - Expose une API Promise : VintConfirm({ title, message, confirmText, cancelText, danger, icon })
 * - Intercepte les clics sur les éléments portant data-confirm :
 *     - <a href data-confirm="msg"> -> vérifie puis navigue
 *     - <form data-confirm="msg">  -> vérifie puis soumet (vraie soumission)
 *     - <button type="submit" data-confirm="msg"> au sein d'un form -> vérifie puis soumet
 *     - <button data-confirm data-href="..."> -> vérifie puis navigue
 *     - <a|button data-method="DELETE|POST|PUT|PATCH" data-href="/url" data-confirm="msg">
 *         -> envoie une requête fetch avec le bon verbe et le CSRF (méthode Laravel-ish)
 * - Remplit aussi data-confirm-text (message long) et data-confirm-title (titre personnalisé).
 */
(function () {
    'use strict';

    var CONFIRM_IDS = { overlay: 'vc-overlay', modal: 'vc-modal', title: 'vc-title', msg: 'vc-msg', confirm: 'vc-confirm-btn', cancel: 'vc-cancel-btn', close: 'vc-close', icon: 'vc-icon', confirmLabel: 'vc-button-label' };

    var currentResolve = null;
    var currentOnConfirm = null;

    function ensureModal() {
        if (document.getElementById(CONFIRM_IDS.overlay)) return;
        var css = document.createElement('style');
        css.id = 'vc-style';
        css.textContent = [
            '#vc-overlay{position:fixed;inset:0;z-index:99999;display:none;align-items:center;justify-content:center;background:rgba(15,23,42,.55);backdrop-filter:blur(4px);padding:1rem;animation:vc-fade .18s ease}',
            '#vc-overlay.vc-open{display:flex}',
            '#vc-modal{width:100%;max-width:420px;background:#fff;border-radius:1rem;box-shadow:0 20px 50px rgba(2,6,23,.35);overflow:hidden;animation:vc-pop .22s cubic-bezier(.16,1,.3,1)}',
            '.dark #vc-modal{background:#0f172a;border:1px solid rgba(255,255,255,.08)}',
            '@keyframes vc-fade{from{opacity:0}to{opacity:1}}',
            '@keyframes vc-pop{from{opacity:0;transform:scale(.96) translateY(6px)}to{opacity:1;transform:scale(1) translateY(0)}}',
            '#vc-close{position:absolute;top:.75rem;right:.75rem;width:1.75rem;height:1.75rem;display:flex;align-items:center;justify-content:center;border-radius:.5rem;color:#94a3b8;background:transparent;cursor:pointer;border:none;font-size:.85rem}',
            '#vc-close:hover{background:#f1f5f9}.dark #vc-close:hover{background:rgba(255,255,255,.08)}',
            '#vc-title{font-size:1.05rem;font-weight:700;color:#0f172a;margin:0}.dark #vc-title{color:#f8fafc}',
            '#vc-msg{margin:.35rem 0 0;font-size:.9rem;line-height:1.55;color:#64748b}.dark #vc-msg{color:#94a3b8}',
            '#vc-action{margin-top:.35rem;font-size:.78rem;font-weight:600;color:#f59e0b}',
            '#vc-footer{display:flex;gap:.6rem;padding:1rem 1.25rem;border-top:1px solid #e2e8f0;margin-top:1rem}.dark #vc-footer{border-color:rgba(255,255,255,.08)}',
            '#vc-btn{display:inline-flex;align-items:center;justify-content:center;gap:.4rem;flex:1;padding:.6rem 1rem;border-radius:.6rem;font-size:.9rem;font-weight:600;border:none;cursor:pointer;color:#fff;transition:filter .15s,transform .1s}',
            '#vc-btn:hover{filter:brightness(1.08)}#vc-btn:active{transform:scale(.98)}',
            '#vc-cancelbtn{flex:1;padding:.6rem 1rem;border-radius:.6rem;font-size:.9rem;font-weight:600;cursor:pointer;background:#f1f5f9;color:#334155;border:1px solid #e2e8f0;transition:background .15s}',
            '.dark #vc-cancelbtn{background:rgba(255,255,255,.06);color:#cbd5e1;border-color:rgba(255,255,255,.1)}',
            '#vc-cancelbtn:hover{background:#e2e8f0}.dark #vc-cancelbtn:hover{background:rgba(255,255,255,.12)}'
        ].join('\n');
        document.head.appendChild(css);

        var overlay = document.createElement('div');
        overlay.id = CONFIRM_IDS.overlay;
        overlay.setAttribute('role', 'dialog');
        overlay.setAttribute('aria-modal', 'true');
        overlay.innerHTML =
            '<div id="' + CONFIRM_IDS.modal + '">' +
                '<div style="padding:1.25rem 1.25rem 0;position:relative">' +
                    '<button id="' + CONFIRM_IDS.close + '" type="button" aria-label="Fermer" style="position:absolute;top:.75rem;right:.75rem;width:1.75rem;height:1.75rem;display:flex;align-items:center;justify-content:center;border-radius:.5rem;color:#94a3b8;background:transparent;cursor:pointer;border:none;font-size:.85rem">✕</button>' +
                    '<div style="display:flex;align-items:flex-start;gap:.85rem">' +
                        '<div id="' + CONFIRM_IDS.icon + '" style="width:2.6rem;height:2.6rem;border-radius:.8rem;display:flex;align-items:center;justify-content:center;background:#fef3c7;color:#d97706;flex-shrink:0"><i class="fas fa-triangle-exclamation"></i></div>' +
                        '<div style="flex:1;min-width:0"><h3 id="' + CONFIRM_IDS.title + '" style="font-size:1.05rem;font-weight:700;color:#0f172a;margin:0">Confirmer l\'action</h3><p id="' + CONFIRM_IDS.msg + '" style="margin:.35rem 0 0;font-size:.9rem;line-height:1.55;color:#64748b"></p><p id="' + CONFIRM_IDS.action + '" style="margin:.35rem 0 0;font-size:.78rem;font-weight:600;color:#f59e0b;display:none"></p></div>' +
                    '</div>' +
                '</div>' +
                '<div id="vc-footer" style="display:flex;gap:.6rem;padding:1rem 1.25rem;border-top:1px solid #e2e8f0;margin-top:1rem">' +
                    '<button id="' + CONFIRM_IDS.cancel + '" type="button" style="flex:1;padding:.6rem 1rem;border-radius:.6rem;font-size:.9rem;font-weight:600;cursor:pointer;background:#f1f5f9;color:#334155;border:1px solid #e2e8f0">Annuler</button>' +
                    '<button id="' + CONFIRM_IDS.confirm + '" type="button" style="flex:1;display:inline-flex;align-items:center;justify-content:center;gap:.4rem;padding:.6rem 1rem;border-radius:.6rem;font-size:.9rem;font-weight:600;border:none;cursor:pointer;color:#fff;background:#d97706"><i class="fas fa-check"></i><span id="' + CONFIRM_IDS.confirmLabel + '">Confirmer</span></button>' +
                '</div>' +
            '</div>';

        var confirmBtn = overlay.querySelector('#' + CONFIRM_IDS.confirm);
        var cancelBtn = overlay.querySelector('#' + CONFIRM_IDS.cancel);
        var closeBtn = overlay.querySelector('#' + CONFIRM_IDS.close);

        function close() {
            overlay.classList.remove('vc-open');
            document.body.style.overflow = '';
        }
        function settle(value) {
            close();
            var done = currentOnConfirm;
            var resolve = currentResolve;
            currentOnConfirm = null;
            currentResolve = null;
            if (resolve) resolve(value);
            if (value && done) done();
        }

        confirmBtn.addEventListener('click', function () { settle(true); });
        cancelBtn.addEventListener('click', function () { settle(false); });
        closeBtn.addEventListener('click', function () { settle(false); });
        overlay.addEventListener('click', function (e) { if (e.target === overlay) settle(false); });
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && overlay.classList.contains('vc-open')) settle(false); });

        document.body.appendChild(overlay);
    }

    /**
     * Ouvrir la modale. Retourne une Promise (true si confirmé).
     */
    function show(opts) {
        ensureModal();
        opts = opts || {};
        var overlay = document.getElementById(CONFIRM_IDS.overlay);
        var modal = document.getElementById(CONFIRM_IDS.modal);
        var titleEl = document.getElementById(CONFIRM_IDS.title);
        var msgEl = document.getElementById(CONFIRM_IDS.msg);
        var actionEl = document.getElementById(CONFIRM_IDS.action);
        var iconEl = document.getElementById(CONFIRM_IDS.icon);
        var confirmBtn = document.getElementById(CONFIRM_IDS.confirm);
        var labelSpan = document.getElementById(CONFIRM_IDS.confirmLabel);
        var cancelBtn = document.getElementById(CONFIRM_IDS.cancel);

        titleEl.textContent = opts.title || 'Confirmer l\'action';
        msgEl.textContent = opts.message || '';
        actionEl.style.display = opts.action ? 'block' : 'none';
        actionEl.textContent = opts.action || '';
        labelSpan.textContent = opts.confirmText || 'Confirmer';
        cancelBtn.textContent = opts.cancelText || 'Annuler';

        var danger = !!opts.danger;
        iconEl.style.background = danger ? '#fee2e2' : '#fef3c7';
        iconEl.style.color = danger ? '#dc2626' : '#d97706';
        var ic = document.createElement('i');
        ic.className = 'fas ' + (danger ? 'fa-triangle-exclamation' : 'fa-circle-question');
        iconEl.innerHTML = '';
        iconEl.appendChild(ic);

        confirmBtn.style.background = danger ? '#dc2626' : '#d97706';
        var okIcon = document.createElement('i');
        okIcon.className = 'fas fa-check';
        confirmBtn.innerHTML = '';
        confirmBtn.appendChild(okIcon);
        confirmBtn.appendChild(labelSpan);

        overlay.classList.add('vc-open');
        document.body.style.overflow = 'hidden';

        return new Promise(function (resolve) {
            currentResolve = resolve;
            currentOnConfirm = opts.onConfirm || null;
        });
    }

    function isSafeMethod(method) {
        return !method || method === 'GET' || method === 'HEAD';
    }

    function csrfToken() {
        var m = document.querySelector('meta[name="csrf-token"]');
        if (m) return m.getAttribute('content');
        var input = document.querySelector('input[name="_token"]');
        return input ? input.value : '';
    }

    // Demande de confirmation puis exécution selon le type d'élément.
    function confirmAndRun(el, fallbackMsg) {
        var title = el.getAttribute('data-confirm-title') || null;
        var msg = el.getAttribute('data-confirm') || el.getAttribute('data-confirm-text') || fallbackMsg || 'Voulez-vous continuer cette action ?';
        var danger = el.hasAttribute('data-confirm-danger') || el.classList.contains('text-red-600') || el.classList.contains('text-red-500') || null;
        var actionLabel = el.getAttribute('data-confirm-action') || el.getAttribute('data-confirm-label') || null;
        var method = (el.getAttribute('data-method') || '').toUpperCase();

        show({
            title: title,
            message: msg,
            danger: !!danger,
            action: actionLabel,
            confirmText: el.getAttribute('data-confirm-confirm') || undefined,
            cancelText: el.getAttribute('data-confirm-cancel') || undefined,
            onConfirm: function () { runAction(el, method); }
        });
    }

    function runAction(el, method) {
        var tag = el.tagName.toLowerCase();
        // Boutons / liens avec data-href -> naviguer ou appeler méthode
        var href = el.getAttribute('data-href');

        if (method && method !== 'GET' && method !== 'HEAD') {
            // Envoi fetch pour DELETE/PUT/PATCH/POST
            var url = href || el.getAttribute('href');
            if (url && !isSafeMethod(method)) {
                fetch(url, {
                    method: method,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken(),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }).then(function (resp) {
                    if (resp.redirected) { window.location.href = resp.url; return; }
                    if (resp.ok) {
                        if (typeof showToast === 'function') showToast('Action effectuée', 'success');
                        if (el.getAttribute('data-reload') !== 'false') window.location.reload();
                    } else {
                        if (typeof showToast === 'function') showToast('Erreur lors de l\'action', 'error');
                        else window.location.reload();
                    }
                }).catch(function () {
                    if (typeof showToast === 'function') showToast('Erreur réseau', 'error');
                });
                return;
            }
        }

        if (href) { window.location.href = href; return; }
        if (el.getAttribute('href')) { window.location.href = el.getAttribute('href'); return; }

        // Formulaire : on soumet réellement le formulaire englobant.
        var form = el.closest('form');
        if (form) { form.submit(); return; }

        // Dernier recours : clic natif rejoué.
        if (el.click) el.click();
    }

    // --- Interception par délégation d'événements (couvre toutes les pages) ---
    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (!form || form.tagName.toLowerCase() !== 'form') return;
        // Form avec data-confirm, ou bouton de soumission avec data-confirm
        var submitterBtn = form.querySelector('[type="submit"][data-confirm]');
        var formAttr = form.getAttribute('data-confirm');
        if (!formAttr && !submitterBtn) return;

        e.preventDefault();
        var payload = submitterBtn && submitterBtn.getAttribute('data-confirm') ? submitterBtn : form;
        confirmAndRun(payload, formAttr || (submitterBtn ? submitterBtn.getAttribute('data-confirm') : null));
    }, true);

    document.addEventListener('click', function (e) {
        var el = e.target.closest ? e.target.closest('[data-confirm], [data-method]') : null;
        if (!el) return;
        var hasConfirm = el.hasAttribute('data-confirm') || el.hasAttribute('data-confirm-text');
        var hasMethod = el.hasAttribute('data-method');
        var type = (el.getAttribute && el.getAttribute('type') || '').toLowerCase();
        var tag = el.tagName ? el.tagName.toLowerCase() : '';
        // Les boutons submit sont gérés par l'événement 'submit' (les soumettre après confirmation)
        if (tag === 'button' && type === 'submit') return;
        var method = (el.getAttribute('data-method') || '').toUpperCase();
        var isDelete = method === 'DELETE';

        // Cas 1 : data-confirm présent (opt-in explicite) sur un lien ou un bouton non submit
        if (hasConfirm && (el.tagName === 'A' || tag === 'button')) {
            e.preventDefault();
            e.stopPropagation();
            confirmAndRun(el);
            return;
        }

        // Cas 2 : data-method="DELETE" sans data-confirm (action destructive, on confirme par défaut)
        if (hasMethod && isDelete && (el.tagName === 'A' || tag === 'button')) {
            e.preventDefault();
            e.stopPropagation();
            show({
                title: el.getAttribute('data-confirm-title') || 'Confirmer la suppression',
                message: el.getAttribute('data-confirm') || 'Voulez-vous vraiment supprimer cet élément ? Cette action est irréversible.',
                danger: true,
                confirmText: el.getAttribute('data-confirm-confirm') || 'Supprimer',
                onConfirm: function () { runAction(el, method); }
            });
        }
    }, true);

    // --- API globale ---
    window.VintConfirm = show;
    window.showConfirm = show;
    window.confirmAction = function (message, callback) {
        show({ message: message, danger: true, confirmText: 'Confirmer', onConfirm: callback });
    };
})();
