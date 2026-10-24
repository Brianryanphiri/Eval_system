@extends('settings.partials._layout')

@section('settings-content')

    <a href="{{ route('settings.overview') }}" class="settings-back">
        <i class="fa-solid fa-arrow-left"></i> Back to Settings
    </a>

    <div class="settings-panel__title">{{ $title }}</div>
    <div class="settings-panel__sub">
        Configuration options for {{ $title }} will appear here.
    </div>

    <div class="settings-placeholder">
        <i class="fa-solid fa-hammer"></i>
        <div style="font-size:15px; font-weight:700; color:#111827; margin-bottom:6px;">
            Coming soon
        </div>
        <div>
            We're building the {{ $title }} module. Check back shortly.
        </div>
    </div>

@endsection