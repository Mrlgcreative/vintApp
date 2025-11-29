@props([
    'src' => '',
    'alt' => '',
    'class' => '',
    'width' => null,
    'height' => null,
    'placeholder' => true,
    'aspectRatio' => null
])

@php
    $containerClass = 'lazy-container';
    if ($aspectRatio) {
        $containerClass .= ' aspect-ratio-' . $aspectRatio;
    }
    
    $imgClass = 'w-full h-full object-cover';
    if ($class) {
        $imgClass .= ' ' . $class;
    }
    
    $attributes = '';
    if ($width) {
        $attributes .= ' data-width="' . $width . '"';
    }
    if ($height) {
        $attributes .= ' data-height="' . $height . '"';
    }
@endphp

<div class="{{ $containerClass }}">
    <img 
        data-src="{{ $src }}" 
        alt="{{ $alt }}" 
        class="{{ $imgClass }}"
        loading="lazy"
        {!! $attributes !!}
        {{ $attributes }}
    >
</div>
