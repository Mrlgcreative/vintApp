@extends('layouts.admin')

@section('page-title', 'Envoyer Newsletter')

@section('content')
<!-- En-tête -->
<div class="mb-6 flex items-center justify-between">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
        <i class="fas fa-paper-plane mr-3 text-primary-600"></i>
        Envoyer une Newsletter
    </h2>
    <a href="{{ route('admin.settings.newsletter.subscribers') }}" 
       class="inline-flex items-center rounded-lg bg-gray-200 dark:bg-gray-700 px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-200 shadow-sm transition-all hover:bg-gray-300">
        <i class="fas fa-arrow-left mr-2"></i>
        Retour
    </a>
</div>

<div class="grid gap-6 lg:grid-cols-3">
    <!-- Formulaire principal (2/3) -->
    <div class="lg:col-span-2">
        <div class="overflow-hidden rounded-xl bg-white dark:bg-gray-800 shadow-md">
            <div class="bg-gradient-to-r from-primary-600 to-primary-700 px-6 py-4">
                <h3 class="text-lg font-semibold text-white">
                    <i class="fas fa-edit mr-2"></i>
                    Composer la newsletter
                </h3>
            </div>
            
            <div class="p-6">
                <form method="POST" action="{{ route('admin.settings.newsletter.process') }}" id="newsletterForm">
                    @csrf

                    <!-- Destinataires -->
                    <div class="mb-6">
                        <label class="mb-3 block text-sm font-semibold text-gray-700 dark:text-gray-200">
                            <i class="fas fa-users mr-2 text-gray-400"></i>
                            Destinataires
                        </label>
                        <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
                            <label class="relative flex cursor-pointer items-center rounded-lg border-2 border-gray-200 dark:border-gray-700 p-4 transition-all hover:border-primary-300 has-[:checked]:border-primary-600 has-[:checked]:bg-primary-50">
                                <input type="radio" name="recipient_type" value="all" checked class="peer sr-only">
                                <div class="flex w-full items-center">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 peer-checked:bg-blue-600">
                                        <i class="fas fa-users text-blue-600 peer-checked:text-white"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">Tous</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Tous les abonnés</p>
                                    </div>
                                </div>
                            </label>

                            <label class="relative flex cursor-pointer items-center rounded-lg border-2 border-gray-200 dark:border-gray-700 p-4 transition-all hover:border-green-300 has-[:checked]:border-green-600 has-[:checked]:bg-green-50">
                                <input type="radio" name="recipient_type" value="active" class="peer sr-only">
                                <div class="flex w-full items-center">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-green-100 peer-checked:bg-green-600">
                                        <i class="fas fa-user-check text-green-600 peer-checked:text-white"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">Actifs</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Actifs uniquement</p>
                                    </div>
                                </div>
                            </label>

                            <label class="relative flex cursor-pointer items-center rounded-lg border-2 border-gray-200 dark:border-gray-700 p-4 transition-all hover:border-cyan-300 has-[:checked]:border-cyan-600 has-[:checked]:bg-cyan-50">
                                <input type="radio" name="recipient_type" value="verified" class="peer sr-only">
                                <div class="flex w-full items-center">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-cyan-100 peer-checked:bg-cyan-600">
                                        <i class="fas fa-envelope-circle-check text-cyan-600 peer-checked:text-white"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">Vérifiés</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Vérifiés uniquement</p>
                                    </div>
                                </div>
                            </label>
                        </div>
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            <i class="fas fa-info-circle mr-1"></i>
                            Sélectionnez le groupe d'abonnés à qui envoyer cette newsletter
                        </p>
                    </div>

                    <!-- Sujet -->
                    <div class="mb-6">
                        <label for="subject" class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-200">
                            <i class="fas fa-heading mr-2 text-gray-400"></i>
                            Sujet de l'email *
                        </label>
                        <input type="text" 
                               class="block w-full rounded-lg border border-gray-300 px-4 py-3 text-gray-900 dark:text-white placeholder-gray-400 transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200 @error('subject') border-red-500 @enderror" 
                               id="subject" 
                               name="subject" 
                               required
                               placeholder="Ex: 🎁 Offre spéciale : -20% sur tous les articles !"
                               value="{{ old('subject') }}">
                        @error('subject')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Contenu -->
                    <div class="mb-6">
                        <label for="content" class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-200">
                            <i class="fas fa-align-left mr-2 text-gray-400"></i>
                            Contenu de l'email *
                        </label>
                        <textarea class="block w-full rounded-lg border border-gray-300 px-4 py-3 text-gray-900 dark:text-white placeholder-gray-400 transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200 @error('content') border-red-500 @enderror" 
                                  id="content" 
                                  name="content" 
                                  rows="12" 
                                  required
                                  placeholder="Écrivez votre message ici... Vous pouvez utiliser du HTML.">{{ old('content') }}</textarea>
                        @error('content')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            <i class="fas fa-code mr-1"></i>
                            Vous pouvez utiliser du HTML pour formater votre contenu (gras, liens, images, etc.)
                        </p>
                    </div>

                    <!-- Boutons d'action -->
                    <div class="flex flex-wrap gap-3">
                        <button type="button" 
                                class="inline-flex items-center rounded-lg border-2 border-gray-300 bg-white dark:bg-gray-800 px-5 py-2.5 text-sm font-medium text-gray-700 transition-all hover:border-gray-400 hover:bg-gray-50 dark:bg-gray-900" 
                                id="previewBtn">
                            <i class="fas fa-eye mr-2"></i>
                            Aperçu de l'email
                        </button>
                        
                        <button type="submit" 
                                class="inline-flex items-center rounded-lg bg-gradient-to-r from-primary-600 to-primary-700 px-5 py-2.5 text-sm font-medium text-white shadow-md transition-all hover:from-primary-700 hover:to-primary-800 hover:shadow-lg" 
                                id="sendBtn">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Envoyer la newsletter
                        </button>
                        
                        <a href="{{ route('admin.settings.newsletter.subscribers') }}" 
                           class="inline-flex items-center rounded-lg border-2 border-red-300 bg-white dark:bg-gray-800 px-5 py-2.5 text-sm font-medium text-red-600 transition-all hover:border-red-400 hover:bg-red-50">
                            <i class="fas fa-times mr-2"></i>
                            Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar (1/3) -->
    <div class="space-y-6">
        <!-- Statistiques -->
        <div class="overflow-hidden rounded-xl bg-white dark:bg-gray-800 shadow-md">
            <div class="bg-gradient-to-r from-orange-500 to-orange-600 px-6 py-4">
                <h3 class="text-lg font-semibold text-white">
                    <i class="fas fa-chart-bar mr-2"></i>
                    Statistiques
                </h3>
            </div>
            <div class="p-6">
                <div class="rounded-xl bg-gradient-to-br from-gray-50 to-gray-100 p-6 text-center">
                    <h3 class="text-4xl font-bold text-primary-600" id="recipientCount">-</h3>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">abonné(s) recevront cette newsletter</p>
                </div>
            </div>
        </div>

        <!-- Conseils -->
        <div class="overflow-hidden rounded-xl bg-white dark:bg-gray-800 shadow-md">
            <div class="bg-gradient-to-r from-cyan-500 to-cyan-600 px-6 py-4">
                <h3 class="text-lg font-semibold text-white">
                    <i class="fas fa-lightbulb mr-2"></i>
                    Conseils
                </h3>
            </div>
            <div class="p-6">
                <div class="mb-4">
                    <h4 class="mb-3 flex items-center text-sm font-bold text-gray-900 dark:text-white">
                        <i class="fas fa-star mr-2 text-yellow-500"></i>
                        Bonnes pratiques
                    </h4>
                    <ul class="space-y-2 text-xs text-gray-700 dark:text-gray-200">
                        <li class="flex items-start">
                            <i class="fas fa-check mr-2 mt-0.5 text-green-500"></i>
                            <span>Utilisez un sujet accrocheur et clair</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check mr-2 mt-0.5 text-green-500"></i>
                            <span>Ajoutez des emojis pour attirer l'attention 🎯</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check mr-2 mt-0.5 text-green-500"></i>
                            <span>Personnalisez votre message</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check mr-2 mt-0.5 text-green-500"></i>
                            <span>Incluez des appels à l'action clairs</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check mr-2 mt-0.5 text-green-500"></i>
                            <span>Testez votre email avant l'envoi</span>
                        </li>
                    </ul>
                </div>

                <div class="mb-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                    <h4 class="mb-3 flex items-center text-sm font-bold text-gray-900 dark:text-white">
                        <i class="fas fa-code mr-2 text-primary-600"></i>
                        Balises HTML utiles
                    </h4>
                    <ul class="space-y-1.5 text-xs text-gray-700 dark:text-gray-200">
                        <li><code class="rounded bg-gray-100 dark:bg-gray-800 px-1.5 py-0.5 font-mono text-primary-600">&lt;strong&gt;</code> pour du texte en gras</li>
                        <li><code class="rounded bg-gray-100 dark:bg-gray-800 px-1.5 py-0.5 font-mono text-primary-600">&lt;em&gt;</code> pour du texte en italique</li>
                        <li><code class="rounded bg-gray-100 dark:bg-gray-800 px-1.5 py-0.5 font-mono text-primary-600">&lt;a href=""&gt;</code> pour des liens</li>
                        <li><code class="rounded bg-gray-100 dark:bg-gray-800 px-1.5 py-0.5 font-mono text-primary-600">&lt;br&gt;</code> pour un saut de ligne</li>
                        <li><code class="rounded bg-gray-100 dark:bg-gray-800 px-1.5 py-0.5 font-mono text-primary-600">&lt;p&gt;</code> pour des paragraphes</li>
                    </ul>
                </div>

                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                    <h4 class="mb-3 flex items-center text-sm font-bold text-gray-900 dark:text-white">
                        <i class="fas fa-file-code mr-2 text-green-600"></i>
                        Exemple de contenu
                    </h4>
                    <div class="rounded-lg border-l-4 border-primary-600 bg-gray-50 dark:bg-gray-900 p-3 text-xs text-gray-700 dark:text-gray-200">
                        <p class="mb-2"><strong>Bonjour,</strong></p>
                        <p class="mb-2">Profitez de notre <strong>offre exceptionnelle</strong> !</p>
                        <p class="mb-2">-20% sur tous les articles jusqu'à dimanche.</p>
                        <p><a href="#" class="text-primary-600 hover:underline">Découvrir les offres →</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Aperçu -->
<div id="previewModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4 backdrop-blur-sm">
    <div class="w-full max-w-4xl overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-2xl">
        <div class="flex items-center justify-between bg-gradient-to-r from-primary-600 to-primary-700 px-6 py-4">
            <h3 class="text-lg font-semibold text-white">
                <i class="fas fa-eye mr-2"></i>
                Aperçu de la newsletter
            </h3>
            <button type="button" class="text-white transition-colors hover:text-gray-200" id="closeModalBtn">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <div class="max-h-[70vh] overflow-y-auto p-6">
            <div class="mx-auto max-w-2xl">
                <div class="overflow-hidden rounded-xl bg-gray-100 dark:bg-gray-800 p-6">
                    <div class="overflow-hidden rounded-t-xl bg-gradient-to-r from-primary-600 to-primary-700 px-8 py-10 text-center">
                        <h2 id="preview-subject" class="text-2xl font-bold text-white"></h2>
                    </div>
                    <div class="rounded-b-xl bg-white dark:bg-gray-800 p-8 shadow-lg">
                        <div id="preview-content" class="prose max-w-none"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="border-t border-gray-200 bg-gray-50 dark:bg-gray-900 px-6 py-4">
            <button type="button" class="rounded-lg bg-gray-200 dark:bg-gray-700 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 transition-colors hover:bg-gray-300" id="closeModalBtn2">
                <i class="fas fa-times mr-2"></i>
                Fermer
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('newsletterForm');
    const previewBtn = document.getElementById('previewBtn');
    const sendBtn = document.getElementById('sendBtn');
    const previewModal = document.getElementById('previewModal');
    const closeModalBtns = [document.getElementById('closeModalBtn'), document.getElementById('closeModalBtn2')];

    // Mise à jour du compteur de destinataires (simulation)
    const recipientRadios = document.querySelectorAll('input[name="recipient_type"]');
    recipientRadios.forEach(radio => {
        radio.addEventListener('change', updateRecipientCount);
    });

    function updateRecipientCount() {
        // Simulation - vous pouvez faire un appel AJAX réel ici
        const counts = { all: '{{ $stats['total'] ?? 0 }}', active: '{{ $stats['active'] ?? 0 }}', verified: '{{ $stats['verified'] ?? 0 }}' };
        const selected = document.querySelector('input[name="recipient_type"]:checked').value;
        document.getElementById('recipientCount').textContent = counts[selected] || '-';
    }

    updateRecipientCount();

    // Aperçu
    previewBtn.addEventListener('click', function() {
        const subject = document.getElementById('subject').value;
        const content = document.getElementById('content').value;

        document.getElementById('preview-subject').textContent = subject || 'Sujet de l\'email';
        document.getElementById('preview-content').innerHTML = content || '<p class="text-gray-500 dark:text-gray-400">Aucun contenu</p>';

        previewModal.classList.remove('hidden');
        previewModal.classList.add('flex');
    });

    // Fermer la modal
    closeModalBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            previewModal.classList.add('hidden');
            previewModal.classList.remove('flex');
        });
    });

    // Fermer en cliquant en dehors
    previewModal.addEventListener('click', function(e) {
        if (e.target === previewModal) {
            previewModal.classList.add('hidden');
            previewModal.classList.remove('flex');
        }
    });

    // Confirmation avant envoi
    form.addEventListener('submit', function(e) {
        if (!confirm('Êtes-vous sûr de vouloir envoyer cette newsletter ? Cette action est irréversible.')) {
            e.preventDefault();
        } else {
            sendBtn.disabled = true;
            sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Envoi en cours...';
        }
    });
});
</script>
@endpush
@endsection
