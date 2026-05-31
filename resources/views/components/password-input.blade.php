@props([
    'name',
    'id' => null,
    'label' => 'Password',
    'required' => false,
    'placeholder' => '',
    'autocomplete' => null,
])

@php
    $inputId = $id ?? 'pwd_' . preg_replace('/[^a-z0-9_]/i', '_', $name);
@endphp

<div {{ $attributes->merge(['class' => 'form-group']) }}>
    <label for="{{ $inputId }}">{{ $label }}@if($required)<span class="req-mark" aria-hidden="true"> *</span>@endif</label>
    <div class="password-field">
        <input
            type="password"
            name="{{ $name }}"
            id="{{ $inputId }}"
            class="form-control"
            @if($required) required @endif
            @if($placeholder) placeholder="{{ $placeholder }}" @endif
            @if($autocomplete) autocomplete="{{ $autocomplete }}" @endif
        >
        <button
            type="button"
            class="password-toggle"
            data-toggle-password="{{ $inputId }}"
            aria-label="Tampilkan password"
            aria-pressed="false"
        >
            <svg class="icon-eye" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                <circle cx="12" cy="12" r="3"/>
            </svg>
            <svg class="icon-eye-off" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
                <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
                <line x1="1" y1="1" x2="23" y2="23"/>
            </svg>
        </button>
    </div>
</div>
