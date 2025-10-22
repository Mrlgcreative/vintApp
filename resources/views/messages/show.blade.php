@extends('app')

@section('title', 'Conversation avec ' . $otherUser->name)

@section('content')
<div class="whatsapp-container">
    <!-- En-tête style WhatsApp -->
    <div class="whatsapp-header">
        <div class="header-content">
            <a href="{{ route('messages.index') }}" class="back-btn">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div class="user-info">
                <div class="avatar-container">
                    @if($otherUser->avatar)
                        <img src="{{ Storage::url($otherUser->avatar) }}" 
                             alt="{{ $otherUser->name }}" 
                             class="user-avatar">
                    @else
                        <div class="user-avatar-placeholder">
                            {{ strtoupper(substr($otherUser->name, 0, 1)) }}
                        </div>
                    @endif
                    <div class="online-indicator"></div>
                </div>
                <div class="user-details">
                    <h6 class="user-name">{{ $otherUser->name }}</h6>
                    <small class="user-status">En ligne</small>
                </div>
            </div>
            <div class="header-actions">
                <button class="action-btn">
                    <i class="fas fa-phone"></i>
                </button>
                <button class="action-btn">
                    <i class="fas fa-video"></i>
                </button>
                <div class="dropdown">
                    <button class="action-btn" data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-search me-2"></i>Rechercher</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-ban me-2"></i>Bloquer</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="#"><i class="fas fa-trash me-2"></i>Supprimer</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Badge produit concerné -->
    @if($item)
        <div class="product-badge">
            <div class="product-info">
                @if($item->images && count($item->images) > 0)
                    <img src="{{ Storage::url($item->images[0]) }}" 
                         alt="{{ $item->name }}" 
                         class="product-thumb">
                @endif
                <div class="product-details">
                    <span class="product-name">{{ $item->name }}</span>
                    <span class="product-price">{{ $item->formatted_price }}</span>
                </div>
                <a href="{{ route('items.show', $item) }}" class="product-link">
                    <i class="fas fa-external-link-alt"></i>
                </a>
            </div>
        </div>

        <!-- Panel de réduction pour le vendeur -->
        @if($item->user_id === Auth::id())
            <div class="discount-panel" id="discountPanel">
                <div class="discount-header">
                    <div class="discount-title">
                        <i class="fas fa-percent text-success me-2"></i>
                        <span>Proposer une réduction</span>
                    </div>
                    <button class="btn btn-sm btn-light" id="toggleDiscountPanel">
                        <i class="fas fa-chevron-down" id="discountToggleIcon"></i>
                    </button>
                </div>
                
                <div class="discount-content" id="discountContent" style="display: none;">
                    <div class="current-price-info">
                        <span class="current-price-label">Prix actuel:</span>
                        <span class="current-price-value">{{ $item->formatted_price }}</span>
                    </div>
                    
                    <div class="discount-rates-grid">
                        <div class="rate-option" data-rate="5">
                            <div class="rate-percentage">5%</div>
                            <div class="rate-price">{{ $item->currency_symbol }} {{ number_format($item->price * 0.95, 2) }}</div>
                            <div class="rate-savings">-{{ $item->currency_symbol }} {{ number_format($item->price * 0.05, 2) }}</div>
                        </div>
                        <div class="rate-option" data-rate="10">
                            <div class="rate-percentage">10%</div>
                            <div class="rate-price">{{ $item->currency_symbol }} {{ number_format($item->price * 0.90, 2) }}</div>
                            <div class="rate-savings">-{{ $item->currency_symbol }} {{ number_format($item->price * 0.10, 2) }}</div>
                        </div>
                        <div class="rate-option" data-rate="15">
                            <div class="rate-percentage">15%</div>
                            <div class="rate-price">{{ $item->currency_symbol }} {{ number_format($item->price * 0.85, 2) }}</div>
                            <div class="rate-savings">-{{ $item->currency_symbol }} {{ number_format($item->price * 0.15, 2) }}</div>
                        </div>
                        <div class="rate-option" data-rate="20">
                            <div class="rate-percentage">20%</div>
                            <div class="rate-price">{{ $item->currency_symbol }} {{ number_format($item->price * 0.80, 2) }}</div>
                            <div class="rate-savings">-{{ $item->currency_symbol }} {{ number_format($item->price * 0.20, 2) }}</div>
                        </div>
                        <div class="rate-option" data-rate="25">
                            <div class="rate-percentage">25%</div>
                            <div class="rate-price">{{ $item->currency_symbol }} {{ number_format($item->price * 0.75, 2) }}</div>
                            <div class="rate-savings">-{{ $item->currency_symbol }} {{ number_format($item->price * 0.25, 2) }}</div>
                        </div>
                        <div class="rate-option" data-rate="30">
                            <div class="rate-percentage">30%</div>
                            <div class="rate-price">{{ $item->currency_symbol }} {{ number_format($item->price * 0.70, 2) }}</div>
                            <div class="rate-savings">-{{ $item->currency_symbol }} {{ number_format($item->price * 0.30, 2) }}</div>
                        </div>
                    </div>
                    
                    <div class="discount-actions">
                        <div class="selected-discount-info" id="selectedDiscountInfo" style="display: none;">
                            <span class="selected-rate"></span>
                            <span class="selected-price"></span>
                        </div>
                        <button class="apply-discount-btn" id="applyDiscountBtn" disabled>
                            <i class="fas fa-check me-2"></i>
                            Appliquer la réduction
                        </button>
                    </div>
                </div>
            </div>
        @endif
    @endif

    <!-- Zone des messages -->
    <div class="messages-container" id="messagesContainer">
        <div class="messages-wrapper">
            @if($messages->count() > 0)
                @foreach($messages as $message)
                    <div class="message-group {{ $message->sender_id === Auth::id() ? 'sent' : 'received' }}">
                        <div class="message-bubble">
                            @if($message->subject)
                                <div class="message-subject">
                                    <i class="fas fa-tag"></i>
                                    {{ $message->subject }}
                                </div>
                            @endif
                            
                            @if($message->content)
                                <div class="message-text">
                                    {!! nl2br(e($message->content)) !!}
                                </div>
                            @endif
                            
                            @if($message->attachment)
                                <div class="message-attachment">
                                    @if(Str::startsWith($message->attachment, 'items/') || in_array(pathinfo($message->attachment, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                        <img src="{{ Storage::url($message->attachment) }}" 
                                             alt="Image jointe" 
                                             class="attachment-image"
                                             onclick="showImageModal('{{ Storage::url($message->attachment) }}')">
                                    @else
                                        <a href="{{ Storage::url($message->attachment) }}" 
                                           target="_blank" 
                                           class="attachment-file">
                                            <i class="fas fa-paperclip"></i>
                                            <span>Fichier joint</span>
                                        </a>
                                    @endif
                                </div>
                            @endif
                            
                            <div class="message-time">
                                {{ $message->created_at->format('H:i') }}
                                @if($message->sender_id === Auth::id())
                                    <span class="message-status">
                                        @if($message->is_read)
                                            <i class="fas fa-check-double read"></i>
                                        @else
                                            <i class="fas fa-check-double"></i>
                                        @endif
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="empty-chat">
                    <div class="empty-icon">
                        <i class="fas fa-comments"></i>
                    </div>
                    <p>Aucun message dans cette conversation</p>
                    <small>Envoyez un message pour commencer</small>
                </div>
            @endif
        </div>
    </div>

    <!-- Zone de saisie style WhatsApp -->
    <div class="input-container">
        <form id="messageForm" method="POST" action="{{ route('messages.store') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="recipient_id" value="{{ $otherUser->id }}">
            
            <div class="input-wrapper">
                <button type="button" class="attachment-btn" onclick="document.getElementById('attachmentInput').click()">
                    <i class="fas fa-paperclip"></i>
                </button>
                
                <div class="text-input-container">
                    <textarea name="content" 
                              class="message-input" 
                              placeholder="Tapez un message..."
                              rows="1"
                              id="messageContent"></textarea>
                    
                    <input type="file" 
                           name="attachment" 
                           id="attachmentInput" 
                           class="d-none" 
                           accept="image/*,.pdf,.doc,.docx">
                </div>
                
                <button type="submit" class="send-btn">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
            
            <div id="attachmentPreview" class="attachment-preview" style="display: none;">
                <div class="preview-content">
                    <i class="fas fa-paperclip"></i>
                    <span id="attachmentName"></span>
                    <button type="button" onclick="removeAttachment()" class="remove-attachment">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal pour l'affichage des images -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-dark">
            <div class="modal-header border-0">
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-0">
                <img id="modalImage" src="" alt="Image" class="img-fluid">
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
/* Masquer navbar et footer pour l'expérience WhatsApp */
body {
    overflow: hidden;
}

footer,
.breadcrumb {
    display: none !important;
}

main.min-vh-100 {
    padding-top: 0 !important;
}

/* Variables adaptées aux couleurs de l'app */
:root {
    --app-primary: #7c3aed;
    --app-primary-dark: #6d28d9;
    --app-primary-light: #a855f7;
    --app-accent: #faf5ff;
    --app-gray: #f3f4f6;
    --app-dark-gray: #e5e7eb;
    --app-text: #1f2937;
    --app-light-text: #6b7280;
    --app-bg: #f9fafb;
    --sent-message: #ddd6fe;
    --received-message: #ffffff;
    --input-bg: #f3f4f6;
    --success-green: #10b981;
    --online-green: #22c55e;
}

.whatsapp-container {
    position: fixed;
    top: auto;
    left: 0;
    right: 0;
    bottom: 0;
    width: 100vw;
    height: 100vh;
    padding-bottom: 64px; /* Espace pour la navbar mobile */
    display: flex;
    flex-direction: column;
    background: var(--whatsapp-bg);
    background-image: 
        repeating-linear-gradient(
            45deg,
            transparent,
            transparent 1px,
            rgba(255,255,255,0.1) 1px,
            rgba(255,255,255,0.1) 10px
        );
    z-index: 1000;
}

/* En-tête avec couleurs de l'app */
.whatsapp-header {
    background: var(--app-primary);
    color: white;
    padding: 0;
    box-shadow: 0 2px 5px rgba(124, 58, 237, 0.15);
    z-index: 1000;
}

.header-content {
    display: flex;
    align-items: center;
    padding: 12px 16px;
    gap: 12px;
    top:20%;
}

.back-btn {
    color: white;
    text-decoration: none;
    font-size: 20px;
    padding: 8px;
    border-radius: 50%;
    transition: background-color 0.2s;
}

.back-btn:hover {
    background-color: rgba(255,255,255,0.1);
    color: white;
}

.user-info {
    display: flex;
    align-items: center;
    flex: 1;
    gap: 12px;
    cursor: pointer;
}

.avatar-container {
    position: relative;
}

.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}

.user-avatar-placeholder {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--app-primary-dark);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 16px;
}

.online-indicator {
    position: absolute;
    bottom: 2px;
    right: 2px;
    width: 12px;
    height: 12px;
    background: var(--online-green);
    border: 2px solid white;
    border-radius: 50%;
}

.user-details {
    flex: 1;
}

.user-name {
    margin: 0;
    font-size: 16px;
    font-weight: 500;
    color: white;
}

.user-status {
    font-size: 13px;
    color: rgba(255,255,255,0.8);
}

.header-actions {
    display: flex;
    gap: 8px;
}

.action-btn {
    background: none;
    border: none;
    color: white;
    font-size: 18px;
    padding: 8px;
    border-radius: 50%;
    cursor: pointer;
    transition: background-color 0.2s;
}

.action-btn:hover {
    background-color: rgba(255,255,255,0.1);
}

/* Badge produit */
.product-badge {
    background: #FFF3CD;
    border: 1px solid #FFEAA7;
    padding: 12px 16px;
    border-radius: 0;
}

.product-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.product-thumb {
    width: 48px;
    height: 48px;
    border-radius: 8px;
    object-fit: cover;
}

.product-details {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.product-name {
    font-weight: 600;
    color: var(--whatsapp-text);
}

/* Panel de réduction */
.discount-panel {
    background: #E8F5E8;
    border: 1px solid #C3E6C3;
    border-top: none;
}

.discount-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 16px;
    cursor: pointer;
    transition: background-color 0.2s;
}

.discount-header:hover {
    background-color: rgba(124, 58, 237, 0.05);
}

.discount-title {
    display: flex;
    align-items: center;
    font-weight: 600;
    color: #2d5a2d;
}

.discount-content {
    padding: 0 16px 16px;
    border-top: 1px solid #C3E6C3;
}

.current-price-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
    padding: 12px;
    background: white;
    border-radius: 8px;
    border: 1px solid #e0e0e0;
}

.current-price-label {
    font-weight: 500;
    color: #666;
}

.current-price-value {
    font-weight: 700;
    color: var(--app-primary);
    font-size: 1.1rem;
}

.discount-rates-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 8px;
    margin-bottom: 16px;
}

.rate-option {
    background: white;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    padding: 12px 8px;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s;
}

.rate-option:hover {
    border-color: var(--app-primary);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(124, 58, 237, 0.1);
}

.rate-option.selected {
    border-color: var(--app-primary);
    background: var(--app-primary);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(124, 58, 237, 0.2);
}

.rate-percentage {
    font-weight: 700;
    font-size: 1.1rem;
    margin-bottom: 4px;
}

.rate-price {
    font-weight: 600;
    color: #28a745;
    margin-bottom: 2px;
}

.rate-option.selected .rate-price {
    color: #e8f5e8;
}

.rate-savings {
    font-size: 0.85rem;
    color: #666;
}

.rate-option.selected .rate-savings {
    color: rgba(255, 255, 255, 0.8);
}

.discount-actions {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.selected-discount-info {
    background: white;
    padding: 12px;
    border-radius: 8px;
    border: 1px solid var(--app-primary);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.selected-rate {
    font-weight: 600;
    color: var(--app-primary);
}

.selected-price {
    font-weight: 700;
    color: #28a745;
    font-size: 1.1rem;
}

.apply-discount-btn {
    background: var(--app-primary);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.apply-discount-btn:hover:not(:disabled) {
    background: var(--app-primary-dark);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(124, 58, 237, 0.3);
}

.apply-discount-btn:disabled {
    background: #ccc;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

.product-price {
    color: var(--app-primary);
    font-weight: 700;
    font-size: 16px;
}

.product-link {
    color: var(--app-primary);
    text-decoration: none;
    font-size: 16px;
    padding: 8px;
}

/* Zone des messages */
.messages-container {
    flex: 1;
    overflow-y: auto;
    padding: 16px;
    background: var(--whatsapp-bg);
    background-image: url("data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M20 20c0-11.046-8.954-20-20-20s-20 8.954-20 20 8.954 20 20 20 20-8.954 20-20zm-30 0c0-5.523 4.477-10 10-10s10 4.477 10 10-4.477 10-10 10-10-4.477-10-10z'/%3E%3C/g%3E%3C/svg%3E");
    scroll-behavior: smooth;
}

.messages-wrapper {
    max-width: 800px;
    margin: 0 auto;
}

.message-group {
    margin-bottom: 8px;
    display: flex;
}

.message-group.sent {
    justify-content: flex-end;
}

.message-group.received {
    justify-content: flex-start;
}

.message-bubble {
    max-width: 70%;
    min-width: 120px;
    padding: 8px 12px;
    border-radius: 18px;
    position: relative;
    word-wrap: break-word;
    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    animation: messageAppear 0.3s ease-out;
}

@keyframes messageAppear {
    from {
        opacity: 0;
        transform: scale(0.8) translateY(10px);
    }
    to {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

.message-group.sent .message-bubble {
    background: var(--sent-message);
    border-bottom-right-radius: 4px;
}

.message-group.received .message-bubble {
    background: var(--received-message);
    border-bottom-left-radius: 4px;
}

.message-subject {
    background: rgba(0,0,0,0.1);
    padding: 6px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 4px;
}

.message-text {
    font-size: 14px;
    line-height: 1.4;
    color: var(--whatsapp-text);
    margin-bottom: 4px;
}

.message-attachment {
    margin: 8px 0;
}

.attachment-image {
    max-width: 200px;
    max-height: 200px;
    border-radius: 8px;
    cursor: pointer;
    transition: transform 0.2s;
}

.attachment-image:hover {
    transform: scale(1.02);
}

.attachment-file {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 12px;
    background: rgba(0,0,0,0.05);
    border-radius: 8px;
    text-decoration: none;
    color: var(--app-primary);
    font-size: 14px;
}

.message-time {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 4px;
    font-size: 11px;
    color: var(--whatsapp-light-text);
    margin-top: 24px;
}

.message-status i {
    font-size: 12px;
    color: var(--whatsapp-light-text);
}

.message-status i.read {
    color: var(--whatsapp-blue);
}

/* État vide */
.empty-chat {
    text-align: center;
    padding: 64px 32px;
    color: var(--whatsapp-light-text);
}

.empty-icon {
    font-size: 64px;
    margin-bottom: 16px;
    opacity: 0.3;
}

/* Zone de saisie */
.input-container {
    background: #F0F0F0;
    padding: 8px 16px;
    border-top: 1px solid var(--whatsapp-dark-gray);
   
}

.input-wrapper {
    display: flex;
    align-items: flex-end;
    gap: 8px;
    background: white;
    border-radius: 24px;
    padding: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.attachment-btn {
    background: none;
    border: none;
    color: var(--whatsapp-light-text);
    font-size: 20px;
    padding: 8px;
    border-radius: 50%;
    cursor: pointer;
    transition: all 0.2s;
}

.attachment-btn:hover {
    background: var(--whatsapp-gray);
    color: var(--app-primary);
}

.text-input-container {
    flex: 1;
    position: relative;
}

.message-input {
    width: 100%;
    border: none;
    outline: none;
    background: transparent;
    font-size: 15px;
    line-height: 1.4;
    max-height: 100px;
    min-height: 20px;
    resize: none;
    font-family: inherit;
    color: var(--whatsapp-text);
}

.message-input::placeholder {
    color: var(--whatsapp-light-text);
}

.send-btn {
    background: var(--app-primary);
    border: none;
    color: white;
    font-size: 16px;
    padding: 10px;
    border-radius: 50%;
    cursor: pointer;
    transition: all 0.2s;
    min-width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.send-btn:hover {
    background: var(--app-primary-dark);
    transform: scale(1.05);
}

.attachment-preview {
    margin-top: 8px;
    padding: 8px 12px;
    background: rgba(124, 58, 237, 0.1);
    border-radius: 8px;
    border: 1px solid var(--app-primary);
}

.preview-content {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    color: var(--app-primary);
}

.remove-attachment {
    background: none;
    border: none;
    color: #dc3545;
    cursor: pointer;
    margin-left: auto;
    padding: 4px;
    border-radius: 50%;
}

.remove-attachment:hover {
    background: rgba(220, 53, 69, 0.1);
}

/* Responsive */
@media (max-width: 768px) {

    .whatsapp-container {
        height: calc(100vh - 64px);
        top:5%;
    }
    
    .input-container{
    background: #F0F0F0;
    padding: 8px 16px;
    margin-bottom:10%;
    border-top: 1px solid var(--whatsapp-dark-gray);
    }

    .message-bubble {
        max-width: 85%;
    }
    
    .header-actions .action-btn:not(:last-child) {
        display: none;
    }

    
    .product-badge {
        padding: 8px 12px;
    }
    
    .messages-container {
        padding: 12px;
    }
}

/* Scrollbar */
.messages-container::-webkit-scrollbar {
    width: 6px;
}

.messages-container::-webkit-scrollbar-track {
    background: transparent;
}

.messages-container::-webkit-scrollbar-thumb {
    background: rgba(0,0,0,0.2);
    border-radius: 3px;
}

.messages-container::-webkit-scrollbar-thumb:hover {
    background: rgba(0,0,0,0.3);
}

/* Dropdown */
.dropdown-menu {
    border-radius: 8px;
    border: none;
    box-shadow: 0 8px 24px rgba(0,0,0,0.15);
    padding: 8px 0;
}

.dropdown-item {
    padding: 12px 16px;
    font-size: 14px;
    border-radius: 0;
    transition: background-color 0.2s;
}

.dropdown-item:hover {
    background-color: var(--whatsapp-gray);
}

.dropdown-item i {
    width: 16px;
    text-align: center;
/* Zone de saisie */

}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('messagesContainer');
    const messageInput = document.getElementById('messageContent');
    const attachmentInput = document.getElementById('attachmentInput');
    const attachmentPreview = document.getElementById('attachmentPreview');
    const attachmentName = document.getElementById('attachmentName');
    const messageForm = document.getElementById('messageForm');

    // Auto-scroll vers le bas
    if (container) {
        container.scrollTop = container.scrollHeight;
    }

    // Auto-resize du textarea
    if (messageInput) {
        messageInput.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 100) + 'px';
        });

        // Enter pour envoyer (Shift+Enter pour nouvelle ligne)
        messageInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                messageForm.dispatchEvent(new Event('submit'));
            }
        });
    }

    // Gestion des fichiers joints
    if (attachmentInput) {
        attachmentInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                attachmentName.textContent = file.name;
                attachmentPreview.style.display = 'block';
            } else {
                attachmentPreview.style.display = 'none';
            }
        });
    }

    // Fonction pour créer un nouveau message dans l'interface
    function appendNewMessage(message, isAuthor) {
        const messageGroup = document.createElement('div');
        messageGroup.className = `message-group ${isAuthor ? 'sent' : 'received'}`;
        
        const messageBubble = document.createElement('div');
        messageBubble.className = 'message-bubble';
        
        // Contenu du message
        if (message.content) {
            const messageText = document.createElement('div');
            messageText.className = 'message-text';
            messageText.innerHTML = message.content.replace(/\n/g, '<br>');
            messageBubble.appendChild(messageText);
        }
        
        // Pièce jointe
        if (message.attachment) {
            const attachmentDiv = document.createElement('div');
            attachmentDiv.className = 'message-attachment';
            
            if (message.attachment.match(/\.(jpg|jpeg|png|gif)$/i)) {
                const img = document.createElement('img');
                img.src = message.attachment;
                img.className = 'attachment-image';
                img.onclick = () => showImageModal(message.attachment);
                attachmentDiv.appendChild(img);
            } else {
                const link = document.createElement('a');
                link.href = message.attachment;
                link.className = 'attachment-file';
                link.innerHTML = `<i class="fas fa-file"></i> Pièce jointe`;
                attachmentDiv.appendChild(link);
            }
            messageBubble.appendChild(attachmentDiv);
        }
        
        // Horodatage
        const timeDiv = document.createElement('div');
        timeDiv.className = 'message-time';
        timeDiv.textContent = new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
        messageBubble.appendChild(timeDiv);
        
        messageGroup.appendChild(messageBubble);
        return messageGroup;
    }

    // Envoi du formulaire optimisé
    if (messageForm) {
        messageForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitButton = this.querySelector('.send-btn');
            const originalIcon = submitButton.innerHTML;
            const messageContent = this.querySelector('#messageContent').value.trim();
            const attachmentFile = this.querySelector('#attachmentInput').files[0];
            
            if (!messageContent && !attachmentFile) return;
            
            // Désactiver le bouton et changer l'icône
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            
            // Ajouter immédiatement le message dans l'interface
            const tempMessage = {
                content: messageContent,
                attachment: attachmentFile ? URL.createObjectURL(attachmentFile) : null
            };
            const newMessageElement = appendNewMessage(tempMessage, true);
            document.querySelector('.messages-wrapper').appendChild(newMessageElement);
            
            // Scroll vers le bas
            container.scrollTop = container.scrollHeight;
            
            // Réinitialiser le formulaire
            this.querySelector('#messageContent').value = '';
            this.querySelector('#attachmentInput').value = '';
            if (attachmentPreview) {
                attachmentPreview.style.display = 'none';
            }
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    // En cas d'erreur, supprimer le message temporaire et afficher l'erreur
                    newMessageElement.remove();
                    alert(data.error || 'Erreur lors de l\'envoi du message');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                newMessageElement.remove();
                alert('Une erreur est survenue lors de l\'envoi');
            })
            .finally(() => {
                // Réactiver le bouton
                submitButton.disabled = false;
                submitButton.innerHTML = originalIcon;
            });
        });
    }

    // Gestion du panel de réduction
    const toggleDiscountPanel = document.getElementById('toggleDiscountPanel');
    const discountContent = document.getElementById('discountContent');
    const discountToggleIcon = document.getElementById('discountToggleIcon');
    const rateOptions = document.querySelectorAll('.rate-option');
    const applyDiscountBtn = document.getElementById('applyDiscountBtn');
    const selectedDiscountInfo = document.getElementById('selectedDiscountInfo');

    if (toggleDiscountPanel) {
        toggleDiscountPanel.addEventListener('click', function() {
            const isVisible = discountContent.style.display !== 'none';
            discountContent.style.display = isVisible ? 'none' : 'block';
            discountToggleIcon.classList.toggle('fa-chevron-down', isVisible);
            discountToggleIcon.classList.toggle('fa-chevron-up', !isVisible);
        });
    }

    // Gestion de la sélection des taux de réduction
    let selectedRate = null;
    
    rateOptions.forEach(option => {
        option.addEventListener('click', function() {
            // Retirer la sélection précédente
            rateOptions.forEach(opt => opt.classList.remove('selected'));
            
            // Ajouter la sélection à l'option actuelle
            this.classList.add('selected');
            
            // Récupérer les informations
            selectedRate = this.dataset.rate;
            const ratePercentage = this.querySelector('.rate-percentage').textContent;
            const ratePrice = this.querySelector('.rate-price').textContent;
            
            // Afficher les informations sélectionnées
            selectedDiscountInfo.querySelector('.selected-rate').textContent = `Réduction de ${ratePercentage}`;
            selectedDiscountInfo.querySelector('.selected-price').textContent = `Prix final: ${ratePrice}`;
            selectedDiscountInfo.style.display = 'flex';
            
            // Activer le bouton d'application
            applyDiscountBtn.disabled = false;
        });
    });

    // Gestion de l'application de la réduction
    if (applyDiscountBtn) {
        applyDiscountBtn.addEventListener('click', function() {
            if (!selectedRate) return;
            
            const originalText = this.innerHTML;
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Application...';
            
            const formData = new FormData();
            formData.append('item_id', '{{ $item ? $item->id : '' }}');
            formData.append('buyer_id', '{{ $otherUser->id }}');
            formData.append('discount_percentage', selectedRate);
            formData.append('expires_hours', 24);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            
            fetch('/discounts/apply', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                
                if (data.success) {
                    // Afficher un message de succès
                    alert('Réduction appliquée avec succès ! Le client a été notifié.');
                    
                    // Masquer le panel de réduction
                    discountContent.style.display = 'none';
                    discountToggleIcon.classList.add('fa-chevron-down');
                    discountToggleIcon.classList.remove('fa-chevron-up');
                    
                    // Optionnel: recharger la page pour afficher le message automatique
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    console.error('Server error:', data.error);
                    alert(data.error || 'Erreur lors de l\'application de la réduction');
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                alert('Une erreur est survenue lors de l\'application de la réduction: ' + error.message);
            })
            .finally(() => {
                this.disabled = false;
                this.innerHTML = originalText;
            });
        });
    }
});

function showImageModal(src) {
    document.getElementById('modalImage').src = src;
    new bootstrap.Modal(document.getElementById('imageModal')).show();
}

function removeAttachment() {
    document.getElementById('attachmentInput').value = '';
    document.getElementById('attachmentPreview').style.display = 'none';
}
</script>
@endpush