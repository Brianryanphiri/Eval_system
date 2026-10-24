@extends('settings.partials._layout')

@section('settings-content')

    <a href="{{ route('settings.overview') }}" class="settings-back">
        <i class="fa-solid fa-arrow-left"></i> Back to Settings
    </a>

    <div class="settings-panel__title">System Status</div>
    <div class="settings-panel__sub">
        Environment health and diagnostics. Share this page with support if you open a ticket.
    </div>

    {{-- Database --}}
    <div style="margin-bottom: 24px;">
        <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em;
                        color: var(--pts-green-dark); margin-bottom: 12px; padding-bottom: 8px;
                        border-bottom: 1px solid var(--pts-grey-light);">
            Database
        </div>

        <div style="padding: 16px 20px; border-radius: 11px; border: 1.5px solid {{ $dbOk ? '#BBE29E' : '#FECACA' }};
                        background: {{ $dbOk ? 'var(--pts-green-light)' : '#FEF2F2' }};">
            <div style="display: flex; align-items: center; gap: 12px;">
                <i class="fa-solid {{ $dbOk ? 'fa-circle-check' : 'fa-circle-exclamation' }}"
                    style="font-size: 18px; color: {{ $dbOk ? 'var(--pts-green-dark)' : '#991B1B' }};"></i>
                <div style="flex: 1;">
                    <div
                        style="font-size: 13.5px; font-weight: 700; color: {{ $dbOk ? 'var(--pts-green-dark)' : '#991B1B' }};">
                        {{ $dbOk ? 'Connected' : 'Connection failed' }}
                    </div>
                    @if ($dbVersion)
                        <div style="font-size: 12px; color: #374151; margin-top: 2px;">
                            MySQL version: {{ $dbVersion }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Environment --}}
    <div style="margin-bottom: 24px;">
        <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em;
                        color: var(--pts-green-dark); margin-bottom: 12px; padding-bottom: 8px;
                        border-bottom: 1px solid var(--pts-grey-light);">
            Environment
        </div>

        <div style="border: 1px solid var(--pts-grey-light); border-radius: 11px; overflow: hidden;">
            @foreach ($status as $label => $value)
                <div style="display: grid; grid-template-columns: 240px 1fr; padding: 11px 16px;
                                    border-bottom: 1px solid var(--pts-grey-light); font-size: 13px;
                                    {{ $loop->last ? 'border-bottom:none;' : '' }}">
                    <div style="font-weight: 600; color: var(--pts-grey); text-transform: capitalize;">
                        {{ str_replace('_', ' ', $label) }}
                    </div>
                    <div style="font-weight: 700; color: #111827; font-family: 'Courier New', monospace;">
                        {{ $value }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Writable directories --}}
    <div style="margin-bottom: 24px;">
        <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em;
                        color: var(--pts-green-dark); margin-bottom: 12px; padding-bottom: 8px;
                        border-bottom: 1px solid var(--pts-grey-light);">
            File Permissions
        </div>

        <div style="border: 1px solid var(--pts-grey-light); border-radius: 11px; overflow: hidden;">
            @foreach ($writable as $path => $ok)
                <div style="display: grid; grid-template-columns: 240px 1fr; padding: 11px 16px;
                                    border-bottom: 1px solid var(--pts-grey-light); font-size: 13px;
                                    align-items: center;
                                    {{ $loop->last ? 'border-bottom:none;' : '' }}">
                    <div
                        style="font-weight: 600; color: var(--pts-grey); font-family: 'Courier New', monospace; font-size: 12px;">
                        {{ $path }}
                    </div>
                    <div style="font-weight: 700; color: {{ $ok ? 'var(--pts-green-dark)' : '#991B1B' }};">
                        <i class="fa-solid {{ $ok ? 'fa-check' : 'fa-xmark' }}" style="margin-right: 6px;"></i>
                        {{ $ok ? 'Writable' : 'Not writable' }}
                    </div>
                </div>
            @endforeach
        </div>

        @if (!collect($writable)->every(fn($v) => $v))
            <div style="margin-top: 10px; padding: 12px 14px; background: #FFFBEB;
                                border-left: 3px solid #D97706; border-radius: 8px; font-size: 12.5px; color: #92400E;">
                <strong>Action needed:</strong> Some directories aren't writable. Contact your hosting provider
                or run <code
                    style="background: #fff; padding: 2px 6px; border-radius: 4px;">chmod -R 775 storage bootstrap/cache</code>.
            </div>
        @endif
    </div>

    {{-- Log --}}
    <div>
        <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em;
                        color: var(--pts-green-dark); margin-bottom: 12px; padding-bottom: 8px;
                        border-bottom: 1px solid var(--pts-grey-light);">
            Log File
        </div>

        <div style="border: 1px solid var(--pts-grey-light); border-radius: 11px; padding: 16px 20px;
                        display: flex; align-items: center; gap: 16px;">
            <div style="flex: 1;">
                <div style="font-size: 13.5px; font-weight: 700; color: #111827; margin-bottom: 4px;">
                    storage/logs/laravel.log
                </div>
                <div style="font-size: 12px; color: var(--pts-grey);">
                    {{ $logSize > 0 ? number_format($logSize / 1024, 2) . ' KB' : 'Empty' }}
                </div>
            </div>
            @if ($logSize > 0)
                <form method="POST" action="{{ route('settings.status.clear-log') }}"
                    onsubmit="return confirm('Clear the entire log file? This cannot be undone.');">
                    @csrf
                    <button type="submit" style="padding: 9px 16px; background: #fff; border: 1.5px solid #FECACA;
                                           border-radius: 9px; font-size: 12.5px; font-weight: 700;
                                           color: #DC2626; cursor: pointer;">
                        <i class="fa-solid fa-trash"></i> Clear Log
                    </button>
                </form>
            @else
                <span style="font-size: 12px; color: var(--pts-grey);">Nothing to clear</span>
            @endif
        </div>
    </div>

@endsection