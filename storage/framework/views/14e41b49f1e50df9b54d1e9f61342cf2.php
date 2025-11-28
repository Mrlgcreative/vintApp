<?php $__env->startSection('title', 'Nouvelle demande de support'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-white to-blue-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- En-tête -->
        <div class="flex items-center gap-4 mb-8">
            <a href="<?php echo e(route('support.index')); ?>" 
               class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-white shadow-md hover:shadow-lg hover:scale-105 transition-all duration-200 group">
                <svg class="w-5 h-5 text-gray-600 group-hover:text-purple-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-1">Nouvelle demande de support</h1>
                <p class="text-gray-600">Décrivez votre problème et nous vous aiderons rapidement</p>
            </div>
        </div>

        <!-- Messages flash -->
        <?php if(session('success')): ?>
            <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 flex items-start gap-3" x-data="{ show: true }" x-show="show" x-transition>
                <svg class="w-6 h-6 text-green-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <p class="text-green-800 flex-1"><?php echo e(session('success')); ?></p>
                <button @click="show = false" class="text-green-600 hover:text-green-800">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 flex items-start gap-3" x-data="{ show: true }" x-show="show" x-transition>
                <svg class="w-6 h-6 text-red-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <p class="text-red-800 flex-1"><?php echo e(session('error')); ?></p>
                <button @click="show = false" class="text-red-600 hover:text-red-800">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        <?php endif; ?>

        <!-- Formulaire -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="p-6 sm:p-8">
                <form action="<?php echo e(route('support.store')); ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
                    <?php echo csrf_field(); ?>

                    <!-- Catégorie -->
                    <div>
                        <label for="category" class="block text-sm font-semibold text-gray-900 mb-2">
                            Catégorie <span class="text-red-500">*</span>
                        </label>
                        <select id="category" 
                                name="category" 
                                required
                                class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all duration-200 outline-none <?php $__errorArgs = ['category'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <option value="">Sélectionnez une catégorie</option>
                            <option value="technical" <?php echo e(old('category') === 'technical' ? 'selected' : ''); ?>>🔧 Problème technique</option>
                            <option value="account" <?php echo e(old('category') === 'account' ? 'selected' : ''); ?>>👤 Compte utilisateur</option>
                            <option value="payment" <?php echo e(old('category') === 'payment' ? 'selected' : ''); ?>>💳 Paiement</option>
                            <option value="order" <?php echo e(old('category') === 'order' ? 'selected' : ''); ?>>📦 Commande</option>
                            <option value="general" <?php echo e(old('category') === 'general' ? 'selected' : ''); ?>>💬 Question générale</option>
                        </select>
                        <?php $__errorArgs = ['category'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <p class="mt-2 text-sm text-gray-500">Choisissez la catégorie qui correspond le mieux à votre demande</p>
                    </div>

                    <!-- Sujet -->
                    <div>
                        <label for="subject" class="block text-sm font-semibold text-gray-900 mb-2">
                            Sujet <span class="text-gray-400 font-normal">(optionnel)</span>
                        </label>
                        <input type="text" 
                               id="subject" 
                               name="subject" 
                               value="<?php echo e(old('subject')); ?>"
                               maxlength="255"
                               placeholder="Ex: Problème de connexion, question sur un paiement..."
                               class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all duration-200 outline-none <?php $__errorArgs = ['subject'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <?php $__errorArgs = ['subject'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Priorité -->
                    <div>
                        <label for="priority" class="block text-sm font-semibold text-gray-900 mb-2">
                            Priorité
                        </label>
                        <select id="priority" 
                                name="priority"
                                class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all duration-200 outline-none <?php $__errorArgs = ['priority'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <option value="normal" <?php echo e(old('priority', 'normal') === 'normal' ? 'selected' : ''); ?>>Normal</option>
                            <option value="low" <?php echo e(old('priority') === 'low' ? 'selected' : ''); ?>>Basse</option>
                            <option value="high" <?php echo e(old('priority') === 'high' ? 'selected' : ''); ?>>Haute</option>
                            <option value="urgent" <?php echo e(old('priority') === 'urgent' ? 'selected' : ''); ?>>Urgente</option>
                        </select>
                        <?php $__errorArgs = ['priority'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Message -->
                    <div>
                        <label for="message" class="block text-sm font-semibold text-gray-900 mb-2">
                            Votre message <span class="text-red-500">*</span>
                        </label>
                        <textarea id="message" 
                                  name="message" 
                                  rows="8" 
                                  maxlength="5000"
                                  required
                                  placeholder="Décrivez votre problème de manière détaillée..."
                                  class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all duration-200 outline-none resize-none <?php $__errorArgs = ['message'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"><?php echo e(old('message')); ?></textarea>
                        <?php $__errorArgs = ['message'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <div class="flex justify-between items-center mt-2">
                            <p class="text-sm text-gray-500">Soyez aussi précis que possible pour obtenir une réponse rapide</p>
                            <span class="text-sm text-gray-500">
                                <span id="charCount"><?php echo e(strlen(old('message', ''))); ?></span>/5000
                            </span>
                        </div>
                    </div>

                    <!-- Pièces jointes -->
                    <div>
                        <label for="attachments" class="block text-sm font-semibold text-gray-900 mb-2">
                            Pièces jointes <span class="text-gray-400 font-normal">(optionnel)</span>
                        </label>
                        <div class="relative">
                            <input type="file" 
                                   id="attachments" 
                                   name="attachments[]" 
                                   multiple
                                   accept="image/*,.pdf,.doc,.docx,.txt"
                                   class="w-full px-4 py-3 rounded-xl border-2 border-dashed border-gray-300 hover:border-purple-400 focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all duration-200 outline-none file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 <?php $__errorArgs = ['attachments.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        </div>
                        <?php $__errorArgs = ['attachments.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <p class="mt-2 text-sm text-gray-500 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            Maximum 5 MB par fichier. Formats acceptés: images, PDF, documents
                        </p>
                    </div>

                    <!-- Aperçu des fichiers -->
                    <div id="filePreview" class="hidden">
                        <div class="p-4 rounded-xl bg-blue-50 border border-blue-200">
                            <p class="font-semibold text-blue-900 mb-2">Fichiers sélectionnés:</p>
                            <div id="fileList" class="space-y-2"></div>
                        </div>
                    </div>

                    <!-- Conseils -->
                    <div class="p-5 rounded-xl bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200">
                        <div class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-amber-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M11 3a1 1 0 10-2 0v1a1 1 0 102 0V3zM15.657 5.757a1 1 0 00-1.414-1.414l-.707.707a1 1 0 001.414 1.414l.707-.707zM18 10a1 1 0 01-1 1h-1a1 1 0 110-2h1a1 1 0 011 1zM5.05 6.464A1 1 0 106.464 5.05l-.707-.707a1 1 0 00-1.414 1.414l.707.707zM5 10a1 1 0 01-1 1H3a1 1 0 110-2h1a1 1 0 011 1zM8 16v-1h4v1a2 2 0 11-4 0zM12 14c.015-.34.208-.646.477-.859a4 4 0 10-4.954 0c.27.213.462.519.476.859h4.002z"/>
                            </svg>
                            <div>
                                <h6 class="font-semibold text-amber-900 mb-2">Conseils pour une réponse rapide</h6>
                                <ul class="space-y-1 text-sm text-amber-800">
                                    <li class="flex items-start gap-2">
                                        <span class="text-amber-600 mt-1">•</span>
                                        <span>Soyez précis dans votre description</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <span class="text-amber-600 mt-1">•</span>
                                        <span>Incluez des captures d'écran si possible</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <span class="text-amber-600 mt-1">•</span>
                                        <span>Mentionnez les étapes pour reproduire le problème</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <span class="text-amber-600 mt-1">•</span>
                                        <span>Indiquez votre navigateur et système d'exploitation si pertinent</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Boutons -->
                    <div class="flex flex-col sm:flex-row justify-between gap-4 pt-4">
                        <a href="<?php echo e(route('support.index')); ?>" 
                           class="inline-flex items-center justify-center px-6 py-3 rounded-xl border-2 border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 hover:border-gray-400 transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Annuler
                        </a>
                        <button type="submit" 
                                class="inline-flex items-center justify-center px-8 py-3 rounded-xl bg-gradient-to-r from-purple-600 to-blue-600 text-white font-semibold hover:from-purple-700 hover:to-blue-700 shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                            Envoyer la demande
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Informations supplémentaires -->
        <div class="mt-6 p-6 rounded-2xl bg-white shadow-lg border border-gray-100">
            <div class="flex items-start gap-4">
                <div class="p-3 rounded-xl bg-purple-100">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h5 class="text-lg font-bold text-gray-900 mb-2">Temps de réponse</h5>
                    <p class="text-gray-600 leading-relaxed">
                        Notre équipe de support s'engage à répondre dans les <span class="font-semibold text-purple-600">24 heures ouvrables</span>.
                        Pour les demandes urgentes, nous faisons de notre mieux pour répondre dans les <span class="font-semibold text-purple-600">2 heures</span>.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
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
                
                Array.from(files).forEach((file, index) => {
                    const fileSize = (file.size / 1024 / 1024).toFixed(2);
                    const fileItem = document.createElement('div');
                    fileItem.className = 'flex items-center gap-2 text-sm text-blue-900';
                    fileItem.innerHTML = `
                        <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                        </svg>
                        <strong>${file.name}</strong> 
                        <span class="text-blue-600">(${fileSize} MB)</span>
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
                submitBtn.innerHTML = '<svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Envoi en cours...';
            }
        });
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\gloir\Desktop\vintApp\resources\views/support/create.blade.php ENDPATH**/ ?>