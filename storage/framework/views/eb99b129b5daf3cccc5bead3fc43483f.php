<?php $__env->startSection('content'); ?>
<div class="container py-5">
    <div class="row g-4">
        <!-- Galerie d'images verticale -->
        <div class="col-lg-1 d-none d-lg-block">
            <?php if($item->images && count($item->images) > 0): ?>
                <div class="d-flex flex-column align-items-center gap-3 product-thumbnails-wrapper">
                    <?php $__currentLoopData = $item->images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="thumbnail-container <?php echo e($index === 0 ? 'active' : ''); ?>" data-index="<?php echo e($index); ?>">
                            <img src="<?php echo e(Storage::url($image)); ?>" 
                                 class="product-thumb" 
                                 alt="Miniature <?php echo e($index + 1); ?>">
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
        </div>
        <!-- Image principale -->
        <div class="col-lg-5">
            <div class="main-image-container">
                <?php if($item->images && count($item->images) > 0): ?>
                    <img id="mainProductImg" 
                         src="<?php echo e(Storage::url($item->images[0])); ?>" 
                         class="main-product-image" 
                         alt="<?php echo e($item->name); ?>">
                    <div class="image-overlay">
                        <i class="fas fa-search-plus"></i>
                    </div>
                <?php else: ?>
                    <div class="no-image-placeholder">
                        <i class="fas fa-image fa-4x"></i>
                        <p class="mt-3">Aucune image disponible</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <!-- Card produit -->
        <div class="col-lg-6">
            <div class="product-details-card">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="flex-grow-1">
                        <h1 class="product-title"><?php echo e($item->name); ?></h1>
                        <div class="badges-wrapper mt-3">
                            <span class="custom-badge badge-category">
                                <i class="fas fa-tag me-1"></i>
                                <?php echo e($item->category->name); ?>

                            </span>
                            <?php if($item->brand): ?>
                                <span class="custom-badge badge-brand">
                                    <i class="fas fa-copyright me-1"></i>
                                    <?php echo e($item->brand->name); ?>

                                </span>
                            <?php endif; ?>
                            <span class="custom-badge badge-condition-<?php echo e($item->condition); ?>">
                                <i class="fas fa-certificate me-1"></i>
                                <?php echo e(ucfirst(str_replace('_', ' ', $item->condition))); ?>

                            </span>
                        </div>
                    </div>
                    <?php if(auth()->guard()->check()): ?>
                        <button class="favorite-button" data-item-id="<?php echo e($item->id); ?>">
                            <i class="fas fa-heart"></i>
                        </button>
                    <?php endif; ?>
                </div>
                
                <div class="price-section mb-4">
                    <div class="d-flex align-items-baseline gap-3">
                        <span class="product-price"><?php echo e($item->formatted_price); ?></span>
                        <div class="stock-badge">
                            <?php if($item->quantity > 0): ?> 
                                <i class="fas fa-check-circle text-success me-1"></i>
                                <span class="text-success fw-medium">En stock</span>
                            <?php else: ?> 
                                <i class="fas fa-times-circle text-danger me-1"></i>
                                <span class="text-danger fw-medium">Rupture de stock</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="product-meta mb-4">
                    <div class="row g-3">
                        <div class="col-6 col-md-4">
                            <div class="meta-item">
                                <i class="fas fa-eye meta-icon"></i>
                                <div>
                                    <small class="meta-label">Vues</small>
                                    <div class="meta-value"><?php echo e($item->views); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="meta-item">
                                <i class="fas fa-boxes meta-icon"></i>
                                <div>
                                    <small class="meta-label">Quantité</small>
                                    <div class="meta-value"><?php echo e($item->quantity); ?></div>
                                </div>
                            </div>
                        </div>
                        <?php if($item->color): ?>
                        <div class="col-6 col-md-4">
                            <div class="meta-item">
                                <i class="fas fa-palette meta-icon"></i>
                                <div>
                                    <small class="meta-label">Couleur</small>
                                    <div class="meta-value"><?php echo e($item->color); ?></div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if($item->size): ?>
                        <div class="col-6 col-md-4">
                            <div class="meta-item">
                                <i class="fas fa-ruler meta-icon"></i>
                                <div>
                                    <small class="meta-label">Taille</small>
                                    <div class="meta-value">
                                        <span class="size-badge"><?php echo e($item->size); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if($item->item_number): ?>
                        <div class="col-6 col-md-4">
                            <div class="meta-item">
                                <i class="fas fa-barcode meta-icon"></i>
                                <div>
                                    <small class="meta-label">N° Article</small>
                                    <div class="meta-value"><?php echo e($item->item_number); ?></div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="col-6 col-md-4">
                            <div class="meta-item">
                                <i class="fas fa-calendar-alt meta-icon"></i>
                                <div>
                                    <small class="meta-label">Publié le</small>
                                    <div class="meta-value"><?php echo e($item->created_at->format('d/m/Y')); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="cart-section mb-4">
                    <form method="POST" action="<?php echo e(route('cart.add', $item->id)); ?>" id="addToCartForm">
                        <?php echo csrf_field(); ?>
                        <div class="quantity-selector mb-3">
                            <label class="form-label fw-medium">Quantité</label>
                            <div class="input-group quantity-input-group">
                                <button type="button" class="btn btn-quantity" onclick="decrementQuantity()">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" name="quantity" id="quantityInput" value="1" min="1" max="<?php echo e($item->quantity); ?>" class="form-control text-center quantity-input">
                                <button type="button" class="btn btn-quantity" onclick="incrementQuantity()">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-add-to-cart w-100" id="addToCartBtn">
                            <i class="fas fa-shopping-cart me-2"></i>
                            <span>Ajouter au panier</span>
                        </button>
                    </form>
                </div>
                
                <div class="description-section mb-4">
                    <h5 class="section-title">
                        <i class="fas fa-align-left me-2"></i>
                        Description
                    </h5>
                    <p class="description-text"><?php echo e($item->description); ?></p>
                </div>
                
                <?php if($item->specifications && is_array($item->specifications) && count($item->specifications) > 0): ?>
                    <div class="specifications-section mb-4">
                        <h5 class="section-title">
                            <i class="fas fa-list-ul me-2"></i>
                            Spécifications
                        </h5>
                        <div class="specifications-grid">
                            <?php $__currentLoopData = $item->specifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="spec-item">
                                    <span class="spec-key"><?php echo e(is_string($key) ? ucfirst($key) : ''); ?></span>
                                    <span class="spec-value"><?php echo e(is_string($value) ? $value : (is_array($value) ? json_encode($value) : '')); ?></span>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <div class="seller-section mb-4">
                    <h5 class="section-title">
                        <i class="fas fa-user-circle me-2"></i>
                        Vendeur
                    </h5>
                    <div class="seller-card">
                        <div class="d-flex align-items-center">
                            <div class="seller-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="seller-name"><?php echo e($item->user->name); ?></div>
                                <small class="seller-info">
                                    <i class="fas fa-calendar me-1"></i>
                                    Membre depuis <?php echo e($item->user->created_at->format('M Y')); ?>

                                </small>
                            </div>
                            <div class="seller-rating">
                                <i class="fas fa-star text-warning"></i>
                                <span class="ms-1">4.8</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Réductions disponibles -->
                <?php if(auth()->guard()->check()): ?>
                    <?php if(Auth::id() !== $item->user_id): ?>
                        <div id="discountSection" class="discount-alert" style="display: none;">
                            <div class="discount-content">
                                <div class="discount-icon">
                                    <i class="fas fa-tag"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="discount-title">Réduction disponible !</h6>
                                    <div id="discountInfo" class="discount-details"></div>
                                </div>
                            </div>
                            <button class="btn btn-apply-discount mt-3 w-100" onclick="applyDiscount()">
                                <i class="fas fa-check me-2"></i>
                                Appliquer la réduction
                            </button>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <div class="action-buttons">
                    <?php if(auth()->guard()->check()): ?>
                        <?php if(Auth::id() !== $item->user_id): ?>
                            <!-- Bouton de contact avec demande de réduction -->
                            <form id="contactForm" method="POST" action="<?php echo e(route('contact.seller', $item)); ?>">
                                <?php echo csrf_field(); ?>
                                <button type="button" class="btn btn-discount-request mb-3 w-100" data-bs-toggle="modal" data-bs-target="#contactModal">
                                    <i class="fas fa-percentage me-2"></i>
                                    Demander une réduction
                                </button>
                            </form>
                            
                            <!-- Bouton contact simple -->
                            <button class="btn btn-contact-seller w-100" onclick="contactSeller()">
                                <i class="fas fa-envelope me-2"></i>
                                Contacter le vendeur
                            </button>
                        <?php else: ?>
                            <div class="owner-actions">
                                <a href="<?php echo e(route('items.edit', $item)); ?>" class="btn btn-edit-item">
                                    <i class="fas fa-edit me-2"></i>
                                    Modifier
                                </a>
                                <button class="btn btn-delete-item" onclick="deleteItem()">
                                    <i class="fas fa-trash me-2"></i>
                                    Supprimer
                                </button>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <a href="<?php echo e(route('login')); ?>" class="btn btn-login-required w-100">
                            <i class="fas fa-sign-in-alt me-2"></i>
                            Se connecter pour acheter
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Articles similaires -->
    <?php if($similarItems->count() > 0): ?>
        <div class="similar-items-section mt-5">
            <div class="section-header mb-4">
                <h3 class="section-main-title">
                    <i class="fas fa-heart me-2"></i>
                    Vous aimerez aussi
                </h3>
                <p class="section-subtitle">Découvrez d'autres articles similaires</p>
            </div>
            <div class="row g-4">
                <?php $__currentLoopData = $similarItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $similarItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="similar-item-card">
                            <div class="similar-item-image-wrapper">
                                <?php if($similarItem->images && count($similarItem->images) > 0): ?>
                                    <img src="<?php echo e(Storage::url($similarItem->images[0])); ?>" 
                                         class="similar-item-image" 
                                         alt="<?php echo e($similarItem->name); ?>">
                                <?php else: ?>
                                    <div class="similar-item-no-image">
                                        <i class="fas fa-image"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="similar-item-overlay">
                                    <a href="<?php echo e(route('items.show', $similarItem)); ?>" class="btn-view-item">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="similar-item-body">
                                <h6 class="similar-item-name"><?php echo e(Str::limit($similarItem->name, 35)); ?></h6>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="similar-item-price"><?php echo e($similarItem->formatted_price); ?></span>
                                    <a href="<?php echo e(route('items.show', $similarItem)); ?>" class="btn-quick-view">
                                        Voir <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Modal de demande de réduction -->
<?php if(auth()->guard()->check()): ?>
    <?php if(Auth::id() !== $item->user_id): ?>
        <div class="modal fade" id="contactModal" tabindex="-1" aria-labelledby="contactModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content modern-modal">
                    <div class="modal-header-modern">
                        <div>
                            <h5 class="modal-title-modern" id="contactModalLabel">
                                <i class="fas fa-percentage me-2"></i>
                                Demander une réduction
                            </h5>
                            <p class="modal-subtitle">Négociez directement avec le vendeur</p>
                        </div>
                        <button type="button" class="btn-close-modern" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body p-4">
                        <!-- Aperçu du produit -->
                        <div class="product-preview-modal mb-4">
                            <div class="row g-3 align-items-center">
                                <div class="col-md-4">
                                    <?php if($item->images && count($item->images) > 0): ?>
                                        <img src="<?php echo e(Storage::url($item->images[0])); ?>" 
                                             class="preview-image" 
                                             alt="<?php echo e($item->name); ?>">
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-8">
                                    <h6 class="preview-title"><?php echo e($item->name); ?></h6>
                                    <p class="preview-description"><?php echo e(Str::limit($item->description, 120)); ?></p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="preview-price"><?php echo e($item->formatted_price); ?></span>
                                        <span class="preview-category">
                                            <i class="fas fa-tag me-1"></i>
                                            <?php echo e($item->category->name); ?>

                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Message personnalisé -->
                        <div class="mb-4">
                            <label for="customMessage" class="form-label-modern">
                                <i class="fas fa-comment-dots me-2"></i>
                                Votre message (optionnel)
                            </label>
                            <textarea name="custom_message" 
                                      id="customMessage" 
                                      class="form-control-modern" 
                                      rows="4" 
                                      placeholder="Bonjour, je suis très intéressé(e) par votre produit. Serait-il possible de négocier le prix ?"></textarea>
                            <small class="form-text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Un message automatique sera envoyé si vous laissez ce champ vide
                            </small>
                        </div>

                        <!-- Informations sur le processus -->
                        <div class="info-box">
                            <div class="info-box-header">
                                <i class="fas fa-lightbulb me-2"></i>
                                Comment ça fonctionne ?
                            </div>
                            <ul class="info-box-list">
                                <li><i class="fas fa-check me-2"></i>Votre demande est envoyée instantanément au vendeur</li>
                                <li><i class="fas fa-check me-2"></i>Le vendeur peut vous proposer une réduction personnalisée</li>
                                <li><i class="fas fa-check me-2"></i>La réduction est appliquée automatiquement si acceptée</li>
                                <li><i class="fas fa-check me-2"></i>Vous recevez une notification de la réponse</li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-footer-modern">
                        <button type="button" class="btn btn-modal-cancel" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>
                            Annuler
                        </button>
                        <button type="button" class="btn btn-modal-submit" id="submitDiscountBtn" onclick="submitDiscountRequest()">
                            <i class="fas fa-paper-plane me-2"></i>
                            Envoyer la demande
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>


<?php $__env->startPush('styles'); ?>
<style>
/* ===== VARIABLES & BASE ===== */
:root {
    --primary-purple: #6A0DAD;
    --primary-purple-dark: #4f0080;
    --primary-purple-light: #8B0DC7;
    --gradient-primary: linear-gradient(135deg, #6A0DAD 0%, #8B0DC7 100%);
    --gradient-secondary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --success-color: #10b981;
    --warning-color: #f59e0b;
    --danger-color: #ef4444;
    --text-dark: #1f2937;
    --text-muted: #6b7280;
    --bg-light: #f9fafb;
    --border-color: rgba(106, 13, 173, 0.1);
    --shadow-sm: 0 2px 8px rgba(106, 13, 173, 0.08);
    --shadow-md: 0 4px 16px rgba(106, 13, 173, 0.12);
    --shadow-lg: 0 8px 32px rgba(106, 13, 173, 0.16);
    --shadow-xl: 0 12px 48px rgba(106, 13, 173, 0.2);
    --radius-sm: 10px;
    --radius-md: 16px;
    --radius-lg: 20px;
    --radius-xl: 24px;
    --transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

body {
    background: linear-gradient(to bottom, #f9fafb 0%, #ffffff 100%);
    color: var(--text-dark);
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
}

/* ===== ANIMATIONS ===== */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes slideInRight {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

@keyframes slideOutRight {
    from { transform: translateX(0); opacity: 1; }
    to { transform: translateX(100%); opacity: 0; }
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

@keyframes shimmer {
    0% { background-position: -1000px 0; }
    100% { background-position: 1000px 0; }
}

/* ===== GALLERY THUMBNAILS ===== */
.product-thumbnails-wrapper {
    position: sticky;
    top: 100px;
}

.thumbnail-container {
    width: 70px;
    height: 70px;
    border-radius: var(--radius-md);
    overflow: hidden;
    border: 3px solid transparent;
    transition: var(--transition);
    cursor: pointer;
    position: relative;
}

.thumbnail-container::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(106, 13, 173, 0.1), rgba(139, 13, 199, 0.1));
    opacity: 0;
    transition: var(--transition);
}

.thumbnail-container:hover::before,
.thumbnail-container.active::before {
    opacity: 1;
}

.thumbnail-container.active {
    border-color: var(--primary-purple);
    box-shadow: 0 0 0 4px rgba(106, 13, 173, 0.1);
    transform: scale(1.05);
}

.thumbnail-container:hover {
    transform: translateY(-4px) scale(1.05);
    box-shadow: var(--shadow-md);
}

.product-thumb {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: var(--transition);
}

.thumbnail-container:hover .product-thumb {
    transform: scale(1.1);
}

/* ===== MAIN IMAGE ===== */
.main-image-container {
    position: relative;
    background: white;
    border-radius: var(--radius-xl);
    padding: 20px;
    box-shadow: var(--shadow-lg);
    overflow: hidden;
    transition: var(--transition);
}

.main-image-container:hover {
    box-shadow: var(--shadow-xl);
    transform: translateY(-5px);
}

.main-product-image {
    width: 100%;
    height: 480px;
    object-fit: contain;
    border-radius: var(--radius-lg);
    transition: var(--transition);
}

.image-overlay {
    position: absolute;
    inset: 20px;
    background: rgba(0, 0, 0, 0.7);
    backdrop-filter: blur(10px);
    border-radius: var(--radius-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: var(--transition);
    cursor: zoom-in;
}

.main-image-container:hover .image-overlay {
    opacity: 1;
}

.image-overlay i {
    color: white;
    font-size: 3rem;
    animation: pulse 2s infinite;
}

.no-image-placeholder {
    height: 480px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
    border-radius: var(--radius-lg);
    color: var(--text-muted);
}

/* ===== PRODUCT DETAILS CARD ===== */
.product-details-card {
    background: white;
    border-radius: var(--radius-xl);
    padding: 2rem;
    box-shadow: var(--shadow-lg);
    position: sticky;
    top: 20px;
    animation: fadeIn 0.6s ease-out;
}

.product-title {
    font-size: 2rem;
    font-weight: 800;
    color: var(--text-dark);
    line-height: 1.2;
    margin-bottom: 0;
}

.badges-wrapper {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.custom-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.5rem 1rem;
    border-radius: var(--radius-sm);
    font-size: 0.875rem;
    font-weight: 600;
    transition: var(--transition);
}

.badge-category {
    background: linear-gradient(135deg, rgba(106, 13, 173, 0.1), rgba(139, 13, 199, 0.15));
    color: var(--primary-purple);
    border: 1px solid rgba(106, 13, 173, 0.2);
}

.badge-brand {
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(139, 92, 246, 0.15));
    color: #6366f1;
    border: 1px solid rgba(99, 102, 241, 0.2);
}

.badge-condition-new {
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(5, 150, 105, 0.15));
    color: var(--success-color);
    border: 1px solid rgba(16, 185, 129, 0.2);
}

.badge-condition-used,
.badge-condition-like_new {
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(217, 119, 6, 0.15));
    color: var(--warning-color);
    border: 1px solid rgba(245, 158, 11, 0.2);
}

.custom-badge:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-sm);
}

/* ===== FAVORITE BUTTON ===== */
.favorite-button {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    border: 2px solid var(--border-color);
    background: white;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: var(--transition);
    flex-shrink: 0;
}

.favorite-button:hover {
    background: rgba(239, 68, 68, 0.1);
    border-color: #ef4444;
    transform: scale(1.1);
}

.favorite-button i {
    color: #ef4444;
    font-size: 1.25rem;
    transition: var(--transition);
}

.favorite-button:hover i {
    transform: scale(1.2);
}

/* ===== PRICE SECTION ===== */
.price-section {
    background: linear-gradient(135deg, rgba(106, 13, 173, 0.05), rgba(139, 13, 199, 0.08));
    padding: 1.5rem;
    border-radius: var(--radius-lg);
    border: 2px solid rgba(106, 13, 173, 0.1);
}

.product-price {
    font-size: 2.5rem;
    font-weight: 900;
    background: var(--gradient-primary);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.stock-badge {
    display: inline-flex;
    align-items: center;
    font-size: 0.95rem;
}

/* ===== PRODUCT META ===== */
.product-meta {
    background: var(--bg-light);
    padding: 1.5rem;
    border-radius: var(--radius-lg);
    border: 1px solid var(--border-color);
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    background: white;
    border-radius: var(--radius-md);
    transition: var(--transition);
}

.meta-item:hover {
    box-shadow: var(--shadow-sm);
    transform: translateY(-2px);
}

.meta-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--gradient-primary);
    color: white;
    border-radius: var(--radius-sm);
    font-size: 1rem;
    flex-shrink: 0;
}

.meta-label {
    color: var(--text-muted);
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.meta-value {
    font-weight: 700;
    color: var(--text-dark);
    font-size: 1rem;
}

.size-badge {
    background: var(--gradient-primary);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: var(--radius-sm);
    font-weight: 600;
}

/* ===== CART SECTION ===== */
.cart-section {
    background: var(--bg-light);
    padding: 1.5rem;
    border-radius: var(--radius-lg);
    border: 1px solid var(--border-color);
}

.quantity-input-group {
    max-width: 180px;
}

.btn-quantity {
    width: 45px;
    height: 45px;
    background: white;
    border: 2px solid var(--border-color);
    color: var(--primary-purple);
    border-radius: var(--radius-sm);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: var(--transition);
    font-weight: 600;
}

.btn-quantity:hover {
    background: var(--primary-purple);
    color: white;
    border-color: var(--primary-purple);
    transform: scale(1.05);
}

.quantity-input {
    border: 2px solid var(--border-color);
    font-weight: 700;
    color: var(--text-dark);
    font-size: 1.125rem;
    height: 45px;
}

.quantity-input:focus {
    border-color: var(--primary-purple);
    box-shadow: 0 0 0 4px rgba(106, 13, 173, 0.1);
}

.btn-add-to-cart {
    background: var(--gradient-primary);
    color: white;
    border: none;
    padding: 1rem 2rem;
    border-radius: var(--radius-md);
    font-weight: 700;
    font-size: 1.125rem;
    transition: var(--transition);
    position: relative;
    overflow: hidden;
}

.btn-add-to-cart::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    transition: left 0.5s;
}

.btn-add-to-cart:hover::before {
    left: 100%;
}

.btn-add-to-cart:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

/* ===== SECTIONS ===== */
.section-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--text-dark);
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
}

.section-title i {
    color: var(--primary-purple);
}

.description-section {
    background: var(--bg-light);
    padding: 1.5rem;
    border-radius: var(--radius-lg);
    border-left: 4px solid var(--primary-purple);
}

.description-text {
    color: var(--text-muted);
    line-height: 1.7;
    margin: 0;
}

/* ===== SPECIFICATIONS ===== */
.specifications-section {
    background: var(--bg-light);
    padding: 1.5rem;
    border-radius: var(--radius-lg);
    border: 1px solid var(--border-color);
}

.specifications-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1rem;
}

.spec-item {
    background: white;
    padding: 1rem;
    border-radius: var(--radius-md);
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: var(--transition);
}

.spec-item:hover {
    box-shadow: var(--shadow-sm);
    transform: translateX(5px);
}

.spec-key {
    font-weight: 600;
    color: var(--text-muted);
    font-size: 0.875rem;
}

.spec-value {
    font-weight: 700;
    color: var(--text-dark);
}

/* ===== SELLER SECTION ===== */
.seller-section {
    background: linear-gradient(135deg, rgba(106, 13, 173, 0.03), rgba(139, 13, 199, 0.05));
    padding: 1.5rem;
    border-radius: var(--radius-lg);
    border: 1px solid var(--border-color);
}

.seller-card {
    background: white;
    padding: 1.25rem;
    border-radius: var(--radius-md);
    transition: var(--transition);
}

.seller-card:hover {
    box-shadow: var(--shadow-md);
}

.seller-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: var(--gradient-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    margin-right: 1rem;
    flex-shrink: 0;
}

.seller-name {
    font-weight: 700;
    color: var(--text-dark);
    font-size: 1.125rem;
    margin-bottom: 0.25rem;
}

.seller-info {
    color: var(--text-muted);
    font-size: 0.875rem;
}

.seller-rating {
    display: flex;
    align-items: center;
    font-weight: 700;
    color: var(--text-dark);
    font-size: 1.125rem;
}

/* ===== DISCOUNT ALERT ===== */
.discount-alert {
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(5, 150, 105, 0.15));
    border: 2px solid rgba(16, 185, 129, 0.3);
    border-radius: var(--radius-lg);
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    animation: fadeIn 0.5s ease-out;
}

.discount-content {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
}

.discount-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: var(--success-color);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.discount-title {
    font-weight: 700;
    color: var(--success-color);
    margin-bottom: 0.5rem;
}

.discount-details {
    color: var(--text-muted);
    font-size: 0.9375rem;
}

.btn-apply-discount {
    background: var(--success-color);
    color: white;
    border: none;
    padding: 0.875rem;
    border-radius: var(--radius-md);
    font-weight: 600;
    transition: var(--transition);
}

.btn-apply-discount:hover {
    background: #059669;
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

/* ===== ACTION BUTTONS ===== */
.action-buttons {
    display: grid;
    gap: 1rem;
}

.btn-discount-request {
    background: var(--gradient-primary);
    color: white;
    border: none;
    padding: 1rem;
    border-radius: var(--radius-md);
    font-weight: 600;
    transition: var(--transition);
}

.btn-discount-request:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.btn-contact-seller {
    background: white;
    color: var(--primary-purple);
    border: 2px solid var(--primary-purple);
    padding: 1rem;
    border-radius: var(--radius-md);
    font-weight: 600;
    transition: var(--transition);
}

.btn-contact-seller:hover {
    background: var(--primary-purple);
    color: white;
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.owner-actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.btn-edit-item {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
    border: none;
    padding: 1rem;
    border-radius: var(--radius-md);
    font-weight: 600;
    text-align: center;
    text-decoration: none;
    transition: var(--transition);
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.btn-edit-item:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
    color: white;
}

.btn-delete-item {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
    border: none;
    padding: 1rem;
    border-radius: var(--radius-md);
    font-weight: 600;
    transition: var(--transition);
}

.btn-delete-item:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.btn-login-required {
    background: var(--gradient-primary);
    color: white;
    border: none;
    padding: 1.25rem;
    border-radius: var(--radius-md);
    font-weight: 700;
    font-size: 1.125rem;
    text-decoration: none;
    text-align: center;
    transition: var(--transition);
    display: block;
}

.btn-login-required:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-xl);
    color: white;
}

/* ===== SIMILAR ITEMS ===== */
.similar-items-section {
    background: white;
    padding: 3rem;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-md);
}

.section-header {
    text-align: center;
}

.section-main-title {
    font-size: 2rem;
    font-weight: 800;
    color: var(--text-dark);
    margin-bottom: 0.5rem;
}

.section-subtitle {
    color: var(--text-muted);
    font-size: 1.125rem;
}

.similar-item-card {
    background: white;
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    transition: var(--transition);
    height: 100%;
}

.similar-item-card:hover {
    box-shadow: var(--shadow-lg);
    transform: translateY(-8px);
}

.similar-item-image-wrapper {
    position: relative;
    overflow: hidden;
    height: 200px;
    background: var(--bg-light);
}

.similar-item-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: var(--transition);
}

.similar-item-card:hover .similar-item-image {
    transform: scale(1.1);
}

.similar-item-overlay {
    position: absolute;
    inset: 0;
    background: rgba(106, 13, 173, 0.8);
    backdrop-filter: blur(5px);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: var(--transition);
}

.similar-item-card:hover .similar-item-overlay {
    opacity: 1;
}

.btn-view-item {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: white;
    color: var(--primary-purple);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    transition: var(--transition);
}

.btn-view-item:hover {
    transform: scale(1.2);
}

.similar-item-no-image {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-muted);
    font-size: 3rem;
}

.similar-item-body {
    padding: 1.25rem;
}

.similar-item-name {
    font-weight: 700;
    color: var(--text-dark);
    margin-bottom: 0.75rem;
    min-height: 2.5rem;
}

.similar-item-price {
    font-weight: 800;
    background: var(--gradient-primary);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-size: 1.25rem;
}

.btn-quick-view {
    color: var(--primary-purple);
    font-weight: 600;
    text-decoration: none;
    font-size: 0.875rem;
    transition: var(--transition);
}

.btn-quick-view:hover {
    color: var(--primary-purple-dark);
    transform: translateX(5px);
}

/* ===== MODERN MODAL ===== */
.modern-modal {
    border: none;
    border-radius: var(--radius-xl);
    overflow: hidden;
    box-shadow: var(--shadow-xl);
}

.modal-header-modern {
    background: var(--gradient-primary);
    color: white;
    padding: 2rem;
    border: none;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}

.modal-title-modern {
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0;
}

.modal-subtitle {
    color: rgba(255, 255, 255, 0.9);
    font-size: 0.9375rem;
    margin-top: 0.25rem;
    margin-bottom: 0;
}

.btn-close-modern {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: var(--transition);
}

.btn-close-modern:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: scale(1.1);
}

.product-preview-modal {
    background: var(--bg-light);
    padding: 1.5rem;
    border-radius: var(--radius-lg);
}

.preview-image {
    width: 100%;
    height: 150px;
    object-fit: cover;
    border-radius: var(--radius-md);
}

.preview-title {
    font-weight: 700;
    color: var(--text-dark);
    margin-bottom: 0.5rem;
}

.preview-description {
    color: var(--text-muted);
    font-size: 0.9375rem;
    margin-bottom: 1rem;
}

.preview-price {
    font-weight: 800;
    background: var(--gradient-primary);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-size: 1.5rem;
}

.preview-category {
    background: rgba(106, 13, 173, 0.1);
    color: var(--primary-purple);
    padding: 0.5rem 1rem;
    border-radius: var(--radius-sm);
    font-size: 0.875rem;
    font-weight: 600;
}

.form-label-modern {
    font-weight: 600;
    color: var(--text-dark);
    margin-bottom: 0.5rem;
}

.form-control-modern {
    border: 2px solid var(--border-color);
    border-radius: var(--radius-md);
    padding: 0.875rem;
    transition: var(--transition);
    font-size: 0.9375rem;
}

.form-control-modern:focus {
    border-color: var(--primary-purple);
    box-shadow: 0 0 0 4px rgba(106, 13, 173, 0.1);
    outline: none;
}

.form-text-muted {
    color: var(--text-muted);
    font-size: 0.875rem;
    margin-top: 0.5rem;
    display: block;
}

.info-box {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.05), rgba(99, 102, 241, 0.08));
    border: 1px solid rgba(59, 130, 246, 0.2);
    border-radius: var(--radius-lg);
    padding: 1.5rem;
}

.info-box-header {
    font-weight: 700;
    color: #3b82f6;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
}

.info-box-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.info-box-list li {
    padding: 0.5rem 0;
    color: var(--text-muted);
    display: flex;
    align-items: flex-start;
}

.info-box-list li i {
    color: #3b82f6;
    margin-top: 0.25rem;
}

.modal-footer-modern {
    border: none;
    padding: 1.5rem 2rem;
    background: var(--bg-light);
    display: flex;
    gap: 1rem;
}

.btn-modal-cancel {
    flex: 1;
    background: white;
    color: var(--text-dark);
    border: 2px solid var(--border-color);
    padding: 0.875rem;
    border-radius: var(--radius-md);
    font-weight: 600;
    transition: var(--transition);
}

.btn-modal-cancel:hover {
    background: var(--bg-light);
    transform: translateY(-2px);
}

.btn-modal-submit {
    flex: 2;
    background: var(--gradient-primary);
    color: white;
    border: none;
    padding: 0.875rem;
    border-radius: var(--radius-md);
    font-weight: 600;
    transition: var(--transition);
}

.btn-modal-submit:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

/* ===== RESPONSIVE ===== */
@media (max-width: 991.98px) {
    .product-details-card {
        position: relative;
        top: 0;
    }
    
    .main-product-image {
        height: 350px;
    }
    
    .product-title {
        font-size: 1.5rem;
    }
    
    .product-price {
        font-size: 2rem;
    }
}

@media (max-width: 767.98px) {
    .main-product-image {
        height: 280px;
    }
    
    .product-details-card {
        padding: 1.5rem;
    }
    
    .product-title {
        font-size: 1.25rem;
    }
    
    .product-price {
        font-size: 1.75rem;
    }
    
    .similar-items-section {
        padding: 2rem 1rem;
    }
    
    .section-main-title {
        font-size: 1.5rem;
    }
    
    .owner-actions {
        grid-template-columns: 1fr;
    }
    
    .specifications-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 374.98px) {
    .product-meta .row {
        gap: 0.5rem;
    }
    
    .meta-item {
        padding: 0.5rem;
    }
}
</style>
<?php $__env->stopPush(); ?>

<script>
// ===== GALERIE D'IMAGES =====
document.addEventListener('DOMContentLoaded', function() {
    const thumbnails = document.querySelectorAll('.thumbnail-container');
    const mainImage = document.getElementById('mainProductImg');
    
    if (thumbnails.length > 0 && mainImage) {
        thumbnails.forEach(thumb => {
            thumb.addEventListener('click', function() {
                // Retirer la classe active de tous les thumbnails
                thumbnails.forEach(t => t.classList.remove('active'));
                
                // Ajouter la classe active au thumbnail cliqué
                this.classList.add('active');
                
                // Changer l'image principale avec animation
                mainImage.style.opacity = '0';
                setTimeout(() => {
                    const img = this.querySelector('.product-thumb');
                    mainImage.src = img.src;
                    mainImage.style.opacity = '1';
                }, 150);
            });
        });
    }
});

// ===== GESTION DE LA QUANTITÉ =====
function incrementQuantity() {
    const input = document.getElementById('quantityInput');
    const max = parseInt(input.getAttribute('max'));
    const currentValue = parseInt(input.value);
    
    if (currentValue < max) {
        input.value = currentValue + 1;
    }
}

function decrementQuantity() {
    const input = document.getElementById('quantityInput');
    const min = parseInt(input.getAttribute('min'));
    const currentValue = parseInt(input.value);
    
    if (currentValue > min) {
        input.value = currentValue - 1;
    }
}

// ===== CONTACT VENDEUR =====
function contactSeller() {
    window.location.href = `/messages/conversation/<?php echo e($item->user_id); ?>?item=<?php echo e($item->id); ?>`;
}

// ===== DEMANDE DE RÉDUCTION =====
function submitDiscountRequest() {
    const form = document.getElementById('contactForm');
    const customMessage = document.getElementById('customMessage').value;
    const submitBtn = document.getElementById('submitDiscountBtn');
    
    // Ajouter le message personnalisé au formulaire
    if (customMessage.trim()) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'custom_message';
        input.value = customMessage;
        form.appendChild(input);
    }
    
    // Désactiver le bouton et afficher l'état de chargement
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Envoi en cours...';
    
    // Afficher une notification
    showNotification('Envoi de votre demande en cours...', 'info');
    
    // Soumettre le formulaire après un court délai
    setTimeout(() => {
        form.submit();
    }, 500);
}

// ===== VÉRIFIER LES RÉDUCTIONS DISPONIBLES =====
<?php if(auth()->guard()->check()): ?>
    <?php if(Auth::id() !== $item->user_id): ?>
        document.addEventListener('DOMContentLoaded', function() {
            checkAvailableDiscounts();
        });
        
        function checkAvailableDiscounts() {
            fetch(`/discounts/item/<?php echo e($item->id); ?>/available`)
                .then(response => response.json())
                .then(discounts => {
                    if (discounts.length > 0) {
                        const discount = discounts[0];
                        showDiscountSection(discount);
                    }
                })
                .catch(error => {
                    console.error('Erreur lors de la vérification des réductions:', error);
                });
        }
        
        function showDiscountSection(discount) {
            const section = document.getElementById('discountSection');
            const info = document.getElementById('discountInfo');
            
            const savings = discount.original_price - discount.final_price;
            const formattedSavings = new Intl.NumberFormat('fr-FR').format(savings);
            const formattedFinalPrice = new Intl.NumberFormat('fr-FR').format(discount.final_price);
            const currencySymbol = '<?php echo e($item->currency_symbol); ?>';
            
            info.innerHTML = `
                <div class="mb-2">
                    <strong class="d-block mb-1">Réduction de ${discount.discount_percentage}% !</strong>
                    <small class="text-muted d-block">
                        Prix original: <span class="text-decoration-line-through">${currencySymbol} ${new Intl.NumberFormat('fr-FR').format(discount.original_price)}</span>
                    </small>
                    <small class="d-block mt-1">
                        <span class="fw-bold" style="color: var(--success-color);">
                            Nouveau prix: ${currencySymbol} ${formattedFinalPrice}
                        </span>
                    </small>
                    <small class="text-muted d-block mt-1">
                        Économie: <span class="fw-bold" style="color: var(--success-color);">${currencySymbol} ${formattedSavings}</span>
                    </small>
                    <small class="text-muted d-block mt-1">
                        <i class="fas fa-clock me-1"></i>
                        Valable jusqu'au ${new Date(discount.expires_at).toLocaleDateString('fr-FR')}
                    </small>
                </div>
            `;
            
            section.style.display = 'block';
            section.dataset.discountId = discount.id;
            
            updateAddToCartButton(true);
        }
        
        function applyDiscount() {
            const section = document.getElementById('discountSection');
            const discountId = section.dataset.discountId;
            
            if (!discountId) return;
            
            fetch(`/discounts/${discountId}/apply`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const priceElement = document.querySelector('.product-price');
                    if (priceElement) {
                        const currencySymbol = '<?php echo e($item->currency_symbol); ?>';
                        priceElement.parentElement.innerHTML = `
                            <div class="d-flex align-items-baseline gap-3 flex-wrap">
                                <span class="text-decoration-line-through text-muted" style="font-size: 1.5rem;"><?php echo e($item->formatted_price); ?></span>
                                <span class="product-price" style="color: var(--success-color);">
                                    ${currencySymbol} ${new Intl.NumberFormat('fr-FR').format(data.final_price)}
                                </span>
                                <span class="badge" style="background: var(--success-color); font-size: 0.875rem;">
                                    -${data.discount_percentage}%
                                </span>
                            </div>
                        `;
                    }
                    
                    section.style.display = 'none';
                    updateAddToCartButton(true);
                    showNotification(data.message, 'success');
                } else {
                    showNotification(data.error || 'Erreur lors de l\'application de la réduction', 'danger');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                showNotification('Une erreur est survenue', 'danger');
            });
        }
    <?php endif; ?>
<?php endif; ?>

// ===== SUPPRESSION D'ARTICLE =====
function deleteItem() {
    if (confirm('Êtes-vous sûr de vouloir supprimer cet article ? Cette action est irréversible.')) {
        fetch(`/items/<?php echo e($item->id); ?>`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Article supprimé avec succès', 'success');
                setTimeout(() => {
                    window.location.href = '/dashboard';
                }, 1500);
            } else {
                showNotification('Erreur lors de la suppression', 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Une erreur est survenue', 'danger');
        });
    }
}

// ===== GESTION DES FAVORIS =====
const favoriteBtn = document.querySelector('.favorite-button');
if (favoriteBtn) {
    favoriteBtn.addEventListener('click', function(e) {
        e.preventDefault();
        const itemId = this.dataset.itemId;
        
        fetch(`/items/${itemId}/favorite`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const icon = this.querySelector('i');
                if (data.is_favorite) {
                    icon.classList.remove('far');
                    icon.classList.add('fas');
                    this.style.background = 'rgba(239, 68, 68, 0.1)';
                    this.style.borderColor = '#ef4444';
                } else {
                    icon.classList.remove('fas');
                    icon.classList.add('far');
                    this.style.background = 'white';
                    this.style.borderColor = 'var(--border-color)';
                }
                showNotification(data.message, 'success');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Une erreur est survenue', 'danger');
        });
    });
}

// ===== SYSTÈME DE NOTIFICATIONS =====
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = 'custom-notification';
    
    const iconMap = {
        success: 'check-circle',
        danger: 'exclamation-circle',
        warning: 'exclamation-triangle',
        info: 'info-circle'
    };
    
    const colorMap = {
        success: 'var(--success-color)',
        danger: 'var(--danger-color)',
        warning: 'var(--warning-color)',
        info: '#3b82f6'
    };
    
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 320px;
        max-width: 400px;
        background: white;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-xl);
        padding: 1.25rem;
        animation: slideInRight 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border-left: 4px solid ${colorMap[type]};
    `;
    
    notification.innerHTML = `
        <div class="d-flex align-items-start gap-3">
            <div style="
                width: 40px;
                height: 40px;
                border-radius: 50%;
                background: ${colorMap[type]}15;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            ">
                <i class="fas fa-${iconMap[type]}" style="color: ${colorMap[type]}; font-size: 1.25rem;"></i>
            </div>
            <div class="flex-grow-1">
                <p style="margin: 0; font-weight: 600; color: var(--text-dark); line-height: 1.5;">
                    ${message}
                </p>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" style="
                background: none;
                border: none;
                color: var(--text-muted);
                cursor: pointer;
                padding: 0;
                width: 24px;
                height: 24px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 50%;
                transition: var(--transition);
            " onmouseover="this.style.background='var(--bg-light)'" onmouseout="this.style.background='none'">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        if (notification.parentNode) {
            notification.style.animation = 'slideOutRight 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
            setTimeout(() => notification.remove(), 400);
        }
    }, 5000);
}

// ===== MISE À JOUR DU BOUTON PANIER =====
function updateAddToCartButton(hasDiscount) {
    const addToCartBtn = document.getElementById('addToCartBtn');
    if (addToCartBtn && hasDiscount) {
        addToCartBtn.innerHTML = `
            <i class="fas fa-shopping-cart me-2"></i>
            <span>Ajouter avec réduction</span>
            <i class="fas fa-tag ms-2"></i>
        `;
        addToCartBtn.style.background = 'linear-gradient(135deg, var(--success-color), #059669)';
    }
}

// ===== SMOOTH SCROLL POUR LES ANCRES =====
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});
</script>
</script>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\gloir\Desktop\projet\vintapp\resources\views/items/show.blade.php ENDPATH**/ ?>