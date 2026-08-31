@extends('app')

@section('title', 'Nouvelle demande de support')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-950 py-8 md:py-10">
    <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
        <!-- En-tête -->
        <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
            <div class="flex items-start gap-4">
                <x-icon icon="fas fa-life-ring" size="lg" tone="primary" class="mt-0.5" />
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">Nouvelle demande de support</h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Décrivez votre problème et nous vous aiderons rapidement.</p>
                </div>
            </div>
            <x-button-outline href="{{ route('support.index') }}">
                <i class="fas fa-arrow-left mr-2"></i>
                Mes demandes
            </x-button-outline>
        </div>

        <!-- Messages flash -->
        @if(session('success'))
            <x-alert variant="success" class="mt-6" x-data="{ show: true }" x-show="show" x-transition>
                <div class="flex items-start gap-2">
                    <p class="flex-1">{{ session('success') }}</p>
                    <button @click="show = false" class="flex-shrink-0 hover:opacity-70"><i class="fas fa-times text-sm"></i></button>
                </div>
            </x-alert>
        @endif

        @if(session('error'))
            <x-alert variant="danger" class="mt-6" x-data="{ show: true }" x-show="show" x-transition>
                <div class="flex items-start gap-2">
                    <p class="flex-1">{{ session('error') }}</p>
                    <button @click="show = false" class="flex-shrink-0 hover:opacity-70"><i class="fas fa-times text-sm"></i></button>
                </div>
            </x-alert>
        @endif

        <div class="mt-8">
            <x-card>
                <form action="{{ route('support.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6 p-5 sm:p-6">
                    @csrf

                    <!-- Catégorie -->
                    <div>
                        <x-label icon="fas fa-tag" iconTone="primary">Catégorie <span class="text-red-500">*</span></x-label>
                        <x-select id="category" name="category" required class="@error('category') border-red-300 @enderror">
                            <option value="">Sélectionnez une catégorie</option>
                            <option value="technical" {{ old('category') === 'technical' ? 'selected' : '' }}>Problème technique</option>
                            <option value="account" {{ old('category') === 'account' ? 'selected' : '' }}>Compte utilisateur</option>
                            <option value="payment" {{ old('category') === 'payment' ? 'selected' : '' }}>Paiement</option>
                            <option value="order" {{ old('category') === 'order' ? 'selected' : '' }}>Commande</option>
                            <option value="general" {{ old('category') === 'general' ? 'selected' : '' }}>Question générale</option>
                        </x-select>
                        @error('category')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Choisissez la catégorie qui correspond le mieux à votre demande.</p>
                    </div>

                    <!-- Sujet -->
                    <div>
                        <x-label icon="fas fa-heading" iconTone="primary">Sujet <span class="text-gray-400 font-normal">(optionnel)</span></x-label>
                        <x-input type="text" id="subject" name="subject" value="{{ old('subject') }}" maxlength="255"
                                 placeholder="Ex: Problème de connexion, question sur un paiement..."
                                 class="@error('subject') border-red-300 @enderror" />
                        @error('subject')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Priorité -->
                    <div>
                        <x-label icon="fas fa-flag" iconTone="primary">Priorité</x-label>
                        <x-select id="priority" name="priority" class="@error('priority') border-red-300 @enderror">
                            <option value="normal" {{ old('priority', 'normal') === 'normal' ? 'selected' : '' }}>Normal</option>
                            <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Basse</option>
                            <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>Haute</option>
                            <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>Urgente</option>
                        </x-select>
                        @error('priority')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Message -->
                    <div>
                        <x-label icon="fas fa-comment-dots" iconTone="primary">Votre message <span class="text-red-500">*</span></x-label>
                        <x-textarea id="message" name="message" rows="8" maxlength="5000" required
                                    class="resize-none @error('message') border-red-300 @enderror"
                                    placeholder="Décrivez votre problème de manière détaillée...">{{ old('message') }}</x-textarea>
                        @error('message')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        <div class="mt-2 flex items-center justify-between">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Soyez aussi précis que possible pour obtenir une réponse rapide.</p>
                            <span class="text-sm text-gray-500 dark:text-gray-400"><span id="charCount">{{ strlen(old('message', '')) }}</span>/5000</span>
                        </div>
                    </div>

                    <!-- Pièces jointes -->
                    <div>
                        <x-label icon="fas fa-paperclip" iconTone="primary">Pièces jointes <span class="text-gray-400 font-normal">(optionnel)</span></x-label>
                        <input type="file"
                               id="attachments"
                               name="attachments[]"
                               multiple
                               accept="image/*,.pdf,.doc,.docx,.txt"
                               class="w-full cursor-pointer rounded-lg border border-dashed border-gray-300 bg-gray-50 px-3.5 py-2.5 text-sm text-gray-900 outline-none transition-colors file:mr-4 file:rounded-md file:border-0 file:bg-vinted-primary-100 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-vinted-primary-700 hover:border-vinted-primary-400 hover:file:bg-vinted-primary-100 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:file:bg-vinted-primary-500/20 dark:file:text-vinted-primary-300 @error('attachments.*') border-red-300 @enderror">
                        @error('attachments.*')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400">
                            <i class="fas fa-circle-info text-xs"></i>
                            Maximum 5 MB par fichier. Formats acceptés : images, PDF, documents.
                        </p>
                    </div>

                    <!-- Aperçu des fichiers -->
                    <div id="filePreview" class="hidden">
                        <div class="rounded-lg border border-vinted-primary-200 bg-vinted-primary-50 p-4 dark:border-vinted-primary-500/30 dark:bg-vinted-primary-500/10">
                            <p class="mb-2 text-sm font-semibold text-vinted-primary-800 dark:text-vinted-primary-300">Fichiers sélectionnés :</p>
                            <div id="fileList" class="space-y-2"></div>
                        </div>
                    </div>

                    <!-- Conseils -->
                    <div class="rounded-xl border border-amber-200 bg-amber-50 p-5 dark:border-amber-500/30 dark:bg-amber-500/10">
                        <div class="flex items-start gap-3">
                            <div class="mt-0.5 flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-full bg-amber-100 dark:bg-amber-500/20">
                                <i class="fas fa-lightbulb text-sm text-amber-600 dark:text-amber-300"></i>
                            </div>
                            <div>
                                <h6 class="mb-2 text-sm font-semibold text-amber-900 dark:text-amber-300">Conseils pour une réponse rapide</h6>
                                <ul class="space-y-1.5 text-sm text-amber-800 dark:text-amber-200/90">
                                    <li class="flex items-start gap-2"><i class="fas fa-circle text-[4px] mt-1.5 text-amber-500"></i>Soyez précis dans votre description</li>
                                    <li class="flex items-start gap-2"><i class="fas fa-circle text-[4px] mt-1.5 text-amber-500"></i>Incluez des captures d'écran si possible</li>
                                    <li class="flex items-start gap-2"><i class="fas fa-circle text-[4px] mt-1.5 text-amber-500"></i>Mentionnez les étapes pour reproduire le problème</li>
                                    <li class="flex items-start gap-2"><i class="fas fa-circle text-[4px] mt-1.5 text-amber-500"></i>Indiquez votre navigateur et système d'exploitation si pertinent</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Boutons -->
                    <div class="flex flex-col gap-3 pt-4 sm:flex-row sm:items-center sm:justify-between">
                        <x-button-outline href="{{ route('support.index') }}">
                            <i class="fas fa-times mr-2"></i>
                            Annuler
                        </x-button-outline>
                        <x-button-primary type="submit" class="sm:order-last">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Envoyer la demande
                        </x-button-primary>
                    </div>
                </form>
            </x-card>

            <!-- Informations supplémentaires -->
            <x-card class="mt-6">
                <div class="flex items-start gap-4 p-5">
                    <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-xl bg-vinted-primary-100 dark:bg-vinted-primary-500/20">
                        <i class="fas fa-clock text-lg text-vinted-primary-600 dark:text-vinted-primary-300"></i>
                    </div>
                    <div>
                        <h5 class="text-base font-semibold text-gray-900 dark:text-white">Temps de réponse</h5>
                        <p class="mt-1 text-sm leading-relaxed text-gray-600 dark:text-gray-400">
                            Notre équipe de support s'engage à répondre dans les <span class="font-semibold text-vinted-primary-600 dark:text-vinted-primary-400">24 heures ouvrables</span>.
                            Pour les demandes urgentes, nous faisons de notre mieux pour répondre dans les <span class="font-semibold text-vinted-primary-600 dark:text-vinted-primary-400">2 heures</span>.
                        </p>
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const messageTextarea = document.getElementById('message');
    const charCount = document.getElementById('charCount');

    if (messageTextarea && charCount) {
        messageTextarea.addEventListener('input', function() {
            const length = this.value.length;
            charCount.textContent = length;

            if (length > 4500) {
                charCount.classList.add('text-red-600', 'font-bold');
            } else {
                charCount.classList.remove('text-red-600', 'font-bold');
            }
        });
    }

    const attachmentsInput = document.getElementById('attachments');
    const filePreview = document.getElementById('filePreview');
    const fileList = document.getElementById('fileList');

    if (attachmentsInput && filePreview && fileList) {
        attachmentsInput.addEventListener('change', function() {
            const files = this.files;

            if (files.length > 0) {
                filePreview.classList.remove('hidden');
                fileList.innerHTML = '';

                Array.from(files).forEach((file) => {
                    const fileSize = (file.size / 1024 / 1024).toFixed(2);
                    const fileItem = document.createElement('div');
                    fileItem.className = 'flex items-center gap-2 text-sm text-vinted-primary-800 dark:text-vinted-primary-300';
                    fileItem.innerHTML = `
                        <i class="fas fa-file text-vinted-primary-600 dark:text-vinted-primary-400"></i>
                        <strong class="truncate">${file.name}</strong>
                        <span class="flex-shrink-0 text-vinted-primary-600 dark:text-vinted-primary-400">(${fileSize} MB)</span>
                    `;
                    fileList.appendChild(fileItem);
                });
            } else {
                filePreview.classList.add('hidden');
                fileList.innerHTML = '';
            }
        });
    }

    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Envoi en cours...';
            }
        });
    }
});
</script>
@endpush