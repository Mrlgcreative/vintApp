@props([
    'id' => 'confirm-modal',
    'title' => 'Êtes-vous sûr ?',
    'message' => null,
    'confirmLabel' => 'Confirmer',
    'cancelLabel' => 'Annuler',
    'variant' => 'danger',
    'icon' => 'fas fa-triangle-exclamation',
    'action' => null,
    'method' => 'POST',
    'confirmId' => null,
    'size' => 'md',
])

@php
    $tones = [
        'danger' => [
            'icon' => 'bg-vinted-danger-50 dark:bg-vinted-danger-500/10 text-vinted-danger-500',
            'confirm' => 'bg-vinted-danger-500 hover:bg-vinted-danger-600',
        ],
        'success' => [
            'icon' => 'bg-vinted-success-50 dark:bg-vinted-success-500/10 text-vinted-success-500',
            'confirm' => 'bg-vinted-success-600 hover:bg-vinted-success-700',
        ],
        'warning' => [
            'icon' => 'bg-vinted-warning-50 dark:bg-vinted-warning-500/10 text-vinted-warning-500',
            'confirm' => 'bg-vinted-warning-500 hover:bg-vinted-warning-600',
        ],
        'primary' => [
            'icon' => 'bg-vinted-primary-50 dark:bg-vinted-primary-500/10 text-vinted-primary-500',
            'confirm' => 'bg-vinted-primary-600 hover:bg-vinted-primary-700',
        ],
    ];
    $variantClasses = $tones[$variant] ?? $tones['danger'];
    $widths = [
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
    ];
    $widthClasses = $widths[$size] ?? 'max-w-md';
@endphp

<div
    id="{{ $id }}"
    class="fixed inset-0 z-[70] bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 hidden"
>
    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-2xl w-full {{ $widthClasses }} border border-gray-200 dark:border-gray-800">
        <div class="p-6">
            <div class="mx-auto w-12 h-12 rounded-full flex items-center justify-center mb-4 {{ $variantClasses['icon'] }}">
                <i class="{{ $icon }} text-lg"></i>
            </div>

            <h3 class="text-lg font-semibold text-center text-gray-900 dark:text-white mb-2">{{ $title }}</h3>

            @if($message)
                <p class="text-sm text-gray-500 dark:text-gray-400 text-center">{{ $message }}</p>
            @endif

            <div class="mt-5">{{ $slot }}</div>

            <div class="mt-6 flex gap-3">
                <button
                    type="button"
                    onclick="window.closeConfirmModal('{{ $id }}')"
                    class="flex-1 h-10 inline-flex items-center justify-center rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
                >
                    {{ $cancelLabel }}
                </button>

                @if($action)
                    <form method="POST" action="{{ $action }}" class="flex-1">
                        @csrf
                        <input type="hidden" name="_method" value="{{ $method }}">
                        <button
                            type="submit"
                            @if($confirmId) id="{{ $confirmId }}" @endif
                            class="w-full h-10 inline-flex items-center justify-center rounded-md text-white text-sm font-medium transition-colors {{ $variantClasses['confirm'] }}"
                        >
                            {{ $confirmLabel }}
                        </button>
                    </form>
                @else
                    <button
                        type="button"
                        @if($confirmId) id="{{ $confirmId }}" @endif
                        class="flex-1 h-10 inline-flex items-center justify-center rounded-md text-white text-sm font-medium transition-colors {{ $variantClasses['confirm'] }}"
                    >
                        {{ $confirmLabel }}
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
window.openConfirmModal = function (modalId = '{{ $id }}') {
    const modal = document.getElementById(modalId);
    if (modal) modal.classList.remove('hidden');
};

window.closeConfirmModal = function (modalId = '{{ $id }}') {
    const modal = document.getElementById(modalId);
    if (modal) modal.classList.add('hidden');
};
</script>
@endpush