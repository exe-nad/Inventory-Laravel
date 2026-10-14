@props(['type' => 'info'])

@php
    $class = match($type) {
        'success' => 'alert-success',
        'danger', 'error' => 'alert-danger',
        'warning' => 'alert-warning',
        default => 'alert-info',
    };
@endphp

<div class="alert {{ $class }}">
    <span>{{ $slot }}</span>
    <button type="button" onclick="this.parentElement.style.display='none'" style="background:none;border:none;cursor:pointer;font-weight:bold;font-size:1.1rem;color:inherit;">&times;</button>
</div>
