@extends('layouts.admin')

@section('title', 'Paramètres de pré-inscription')
@section('page-title', 'Paramètres de pré-inscription')

@section('page-actions')
<div class="flex flex-wrap gap-2">
    <a href="{{ route('preregistration.index') }}" target="_blank"
       class="inline-flex items-center gap-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
        <i class="fas fa-external-link-alt"></i>Voir la page
    </a>
    <a href="{{ route('admin.waiting-users.index') }}"
       class="inline-flex items-center gap-2 rounded-xl bg-primary-600 hover:bg-primary-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
        <i class="fas fa-users"></i>Gérer les inscriptions
    </a>
</div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Messages -->
    @if(session('success'))
        <div class="flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 dark:border-emerald-800 dark:bg-emerald-900/20 px-4 py-3 animate-fade-in">
            <i class="fas fa-check-circle text-emerald-600 dark:text-emerald-400"></i>
            <p class="flex-1 text-sm font-medium text-emerald-800 dark:text-emerald-200">{{ session('success') }}</p>
            <button type="button" class="text-emerald-600 hover:text-emerald-800 dark:text-emerald-400 dark:hover:text-emerald-200" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-2xl border border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-900/20 px-4 py-3 animate-fade-in">
            <div class="flex items-center gap-3">
                <i class="fas fa-exclamation-circle text-red-600 dark:text-red-400"></i>
                <p class="flex-1 text-sm font-medium text-red-800 dark:text-red-200">Veuillez corriger les erreurs ci-dessous :</p>
                <button type="button" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-200" onclick="this.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <ul class="mt-2 ml-9 list-disc space-y-1 text-sm text-red-700 dark:text-red-300">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.settings.preregistration.update') }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Statut de la pré-inscription -->
        @php
            $isEnabled = Setting::get('preregistration_enabled', false);
        @endphp

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800 overflow-hidden">
            <div class="px-5 py-4 bg-gradient-to-r from-primary-600 to-primary-700">
                <h5 class="text-white font-semibold">
                    <i class="fas fa-toggle-on mr-2"></i>Statut de la pré-inscription
                </h5>
            </div>
            <div class="p-5 sm:p-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h6 class="font-semibold text-slate-900 dark:text-white mb-1">Activer la pré-inscription</h6>
                        <p class="text-sm text-slate-500 dark:text-slate-400">
                            Les utilisateurs {{ $isEnabled ? 'peuvent' : 'ne peuvent pas' }} actuellement s'inscrire
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <label class="relative inline-flex cursor-pointer items-center">
                            <input type="checkbox" id="preregistration_enabled" name="preregistration_enabled" value="1"
                                   class="peer sr-only" {{ $isEnabled ? 'checked' : '' }}>
                            <div class="h-6 w-11 rounded-full bg-slate-300 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-slate-300 after:bg-white after:transition-all peer-checked:bg-emerald-500 peer-checked:after:translate-x-5 peer-checked:after:border-white dark:bg-slate-600"></div>
                        </label>
                        <span id="status-badge" class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {{ $isEnabled ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-300' : 'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-900/30 dark:text-red-300' }}">
                            {{ $isEnabled ? 'Activée' : 'Désactivée' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
            <!-- Contenu de la page -->
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800 overflow-hidden">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700 px-5 py-4">
                    <h5 class="text-base font-semibold text-slate-900 dark:text-white">
                        <i class="fas fa-edit mr-2 text-primary-600"></i>Contenu de la page
                    </h5>
                </div>
                <div class="p-5 sm:p-6 space-y-4">
                    <div>
                        <label for="preregistration_title" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                            <i class="fas fa-heading mr-1"></i>Titre principal
                        </label>
                        <input type="text" id="preregistration_title" name="preregistration_title"
                               value="{{ Setting::get('preregistration_title', 'Rejoignez-nous en avant-première !') }}"
                               placeholder="Rejoignez-nous en avant-première !"
                               class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors">
                    </div>

                    <div>
                        <label for="preregistration_subtitle" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                            <i class="fas fa-text-height mr-1"></i>Sous-titre
                        </label>
                        <input type="text" id="preregistration_subtitle" name="preregistration_subtitle"
                               value="{{ Setting::get('preregistration_subtitle', 'Inscrivez-vous maintenant...') }}"
                               placeholder="Inscrivez-vous maintenant..."
                               class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors">
                    </div>

                    <div>
                        <label for="preregistration_message" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                            <i class="fas fa-align-left mr-1"></i>Message d'accueil
                        </label>
                        <textarea id="preregistration_message" name="preregistration_message" rows="4"
                                  placeholder="Nous préparons quelque chose de spécial..."
                                  class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors">{{ Setting::get('preregistration_message', 'Nous préparons quelque chose de spécial...') }}</textarea>
                    </div>

                    <div>
                        <label for="preregistration_closed_message" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                            <i class="fas fa-ban mr-1"></i>Message de fermeture
                        </label>
                        <textarea id="preregistration_closed_message" name="preregistration_closed_message" rows="3"
                                  placeholder="Les pré-inscriptions sont fermées..."
                                  class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors">{{ Setting::get('preregistration_closed_message', 'Les pré-inscriptions sont fermées...') }}</textarea>
                        <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">Affiché lorsque la pré-inscription est désactivée</p>
                    </div>
                </div>
            </div>

            <!-- Avantages -->
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800 overflow-hidden">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700 px-5 py-4">
                    <h5 class="text-base font-semibold text-slate-900 dark:text-white">
                        <i class="fas fa-gift mr-2 text-emerald-600 dark:text-emerald-400"></i>Avantages de la pré-inscription
                    </h5>
                </div>
                <div class="p-5 sm:p-6">
                    <div id="benefits-container" class="space-y-2">
                        @php
                            $benefits = Setting::get('preregistration_benefits', []);
                            if (is_string($benefits)) {
                                $benefits = json_decode($benefits, true) ?? [];
                            }
                        @endphp

                        @forelse($benefits as $index => $benefit)
                            <div class="benefit-item flex items-center gap-2 rounded-xl border border-indigo-100 dark:border-indigo-800 bg-indigo-50/50 dark:bg-indigo-900/10 p-2 pl-3">
                                <i class="fas fa-check-circle text-emerald-500"></i>
                                <input type="text" name="preregistration_benefits[]" value="{{ $benefit }}"
                                       placeholder="Avantage {{ $index + 1 }}"
                                       class="flex-1 rounded-xl border border-transparent bg-transparent px-2 py-1.5 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/30 transition-colors">
                                <button type="button" class="inline-flex items-center justify-center w-8 h-8 shrink-0 rounded-xl text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors" onclick="this.parentElement.remove()">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        @empty
                            <div class="benefit-item flex items-center gap-2 rounded-xl border border-indigo-100 dark:border-indigo-800 bg-indigo-50/50 dark:bg-indigo-900/10 p-2 pl-3">
                                <i class="fas fa-check-circle text-emerald-500"></i>
                                <input type="text" name="preregistration_benefits[]" value="Accès prioritaire lors du lancement"
                                       placeholder="Avantage 1"
                                       class="flex-1 rounded-xl border border-transparent bg-transparent px-2 py-1.5 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/30 transition-colors">
                                <button type="button" class="inline-flex items-center justify-center w-8 h-8 shrink-0 rounded-xl text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors" onclick="this.parentElement.remove()">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        @endforelse
                    </div>

                    <button type="button" onclick="addBenefit()"
                            class="mt-4 w-full inline-flex items-center justify-center gap-2 rounded-xl border-2 border-dashed border-slate-300 dark:border-slate-600 px-4 py-3 text-sm font-medium text-slate-500 dark:text-slate-400 hover:border-indigo-500 hover:text-indigo-500 dark:hover:border-indigo-500 dark:hover:text-indigo-400 transition-colors">
                        <i class="fas fa-plus"></i>Ajouter un avantage
                    </button>
                </div>
            </div>

            <!-- Options -->
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800 overflow-hidden">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700 px-5 py-4">
                    <h5 class="text-base font-semibold text-slate-900 dark:text-white">
                        <i class="fas fa-sliders-h mr-2 text-amber-500 dark:text-amber-400"></i>Options du formulaire
                    </h5>
                </div>
                <div class="p-5 sm:p-6 space-y-5">
                    <div>
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="font-semibold text-slate-900 dark:text-white">Téléphone obligatoire</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Les utilisateurs doivent fournir un numéro de téléphone</p>
                            </div>
                            <label class="relative inline-flex cursor-pointer items-center shrink-0">
                                <input type="checkbox" id="preregistration_require_phone" name="preregistration_require_phone" value="1"
                                       class="peer sr-only" {{ Setting::get('preregistration_require_phone', false) ? 'checked' : '' }}>
                                <div class="h-6 w-11 rounded-full bg-slate-300 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-slate-300 after:bg-white after:transition-all peer-checked:bg-primary-600 peer-checked:after:translate-x-5 peer-checked:after:border-white dark:bg-slate-600"></div>
                            </label>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="font-semibold text-slate-900 dark:text-white">Confirmation email obligatoire</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Les utilisateurs doivent confirmer leur email</p>
                            </div>
                            <label class="relative inline-flex cursor-pointer items-center shrink-0">
                                <input type="checkbox" id="preregistration_require_confirmation" name="preregistration_require_confirmation" value="1"
                                       class="peer sr-only" {{ Setting::get('preregistration_require_confirmation', true) ? 'checked' : '' }}>
                                <div class="h-6 w-11 rounded-full bg-slate-300 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-slate-300 after:bg-white after:transition-all peer-checked:bg-primary-600 peer-checked:after:translate-x-5 peer-checked:after:border-white dark:bg-slate-600"></div>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label for="preregistration_limit" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                            <i class="fas fa-users mr-1"></i>Limite de pré-inscriptions
                        </label>
                        <input type="number" id="preregistration_limit" name="preregistration_limit"
                               value="{{ Setting::get('preregistration_limit', 0) }}" min="0"
                               placeholder="0 = illimité"
                               class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors">
                        <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">Nombre maximum de pré-inscriptions (0 = illimité)</p>
                    </div>
                </div>
            </div>

            <!-- Notifications -->
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800 overflow-hidden">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700 px-5 py-4">
                    <h5 class="text-base font-semibold text-slate-900 dark:text-white">
                        <i class="fas fa-bell mr-2 text-sky-500 dark:text-sky-400"></i>Notifications
                    </h5>
                </div>
                <div class="p-5 sm:p-6">
                    <label for="preregistration_notification_email" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                        <i class="fas fa-envelope mr-1"></i>Email de notification admin
                    </label>
                    <input type="email" id="preregistration_notification_email" name="preregistration_notification_email"
                           value="{{ Setting::get('preregistration_notification_email', 'admin@vintapp.com') }}"
                           placeholder="admin@vintapp.com"
                           class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors">
                    <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">Recevra les notifications de nouvelles pré-inscriptions</p>
                </div>
            </div>
        </div>

        <!-- Boutons d'action -->
        <div class="mt-6 rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800 px-5 py-4 flex flex-col-reverse sm:flex-row justify-between gap-3">
            <a href="{{ route('admin.dashboard') }}"
               class="inline-flex justify-center items-center gap-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors w-full sm:w-auto">
                <i class="fas fa-arrow-left"></i>Retour
            </a>
            <button type="submit"
                    class="inline-flex justify-center items-center gap-2 w-full sm:w-auto rounded-xl bg-primary-600 hover:bg-primary-700 px-6 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
                <i class="fas fa-save"></i>Enregistrer les paramètres
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const enabledCheckbox = document.getElementById('preregistration_enabled');
        if (enabledCheckbox) {
            enabledCheckbox.addEventListener('change', function() {
                const badge = document.getElementById('status-badge');
                if (this.checked) {
                    badge.textContent = 'Activée';
                    badge.className = 'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-300';
                } else {
                    badge.textContent = 'Désactivée';
                    badge.className = 'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-900/30 dark:text-red-300';
                }
            });
        }
    });

    function addBenefit() {
        const container = document.getElementById('benefits-container');
        const count = container.querySelectorAll('.benefit-item').length + 1;

        const benefitHtml = `
            <div class="benefit-item flex items-center gap-2 rounded-xl border border-indigo-100 dark:border-indigo-800 bg-indigo-50/50 dark:bg-indigo-900/10 p-2 pl-3">
                <i class="fas fa-check-circle text-emerald-500"></i>
                <input type="text"
                       name="preregistration_benefits[]"
                       placeholder="Avantage ${count}"
                       class="flex-1 rounded-xl border border-transparent bg-transparent px-2 py-1.5 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/30 transition-colors">
                <button type="button" class="inline-flex items-center justify-center w-8 h-8 shrink-0 rounded-xl text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors" onclick="this.parentElement.remove()">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;

        container.insertAdjacentHTML('beforeend', benefitHtml);
    }
</script>
@endpush
