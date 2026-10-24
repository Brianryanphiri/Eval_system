@extends('settings.partials._layout')

@section('settings-content')

    <a href="{{ route('settings.overview') }}" class="settings-back">
        <i class="fa-solid fa-arrow-left"></i> Back to Settings
    </a>

    <div class="settings-panel__title">{{ $title }}</div>
    <div class="settings-panel__sub">{{ $description }}</div>

    {{-- Status badge --}}
    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 22px;">
        <span style="font-size: 10.5px; font-weight: 800; padding: 4px 12px; border-radius: 20px;
                         letter-spacing: 0.08em; text-transform: uppercase;
                         background: {{ $status === 'In progress' ? 'var(--pts-green-light)' : 'var(--pts-bg)' }};
                         color: {{ $status === 'In progress' ? 'var(--pts-green-dark)' : '#374151' }};">
            {{ $status }}
        </span>
        <span style="font-size: 12.5px; color: var(--pts-grey);">
            This feature is on the roadmap. Contact support to prioritise it for your school.
        </span>
    </div>

    {{-- Warning for dangerous operations --}}
    @if ($mode === 'warning')
        <div style="padding: 14px 18px; background: #FFFBEB; border-left: 4px solid #D97706;
                            border-radius: 10px; margin-bottom: 22px;">
            <div style="display: flex; align-items: flex-start; gap: 12px;">
                <i class="fa-solid fa-triangle-exclamation" style="color: #D97706; font-size: 16px; margin-top: 2px;"></i>
                <div>
                    <div style="font-size: 13px; font-weight: 700; color: #92400E; margin-bottom: 4px;">
                        This is a destructive operation
                    </div>
                    <div style="font-size: 12.5px; color: #78350F; line-height: 1.55;">
                        Rollover actions move or archive data permanently. We're building them carefully with
                        dry-run previews and rollback safety so nothing is lost by accident.
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Feature preview --}}
    @if (!empty($features))
        <div style="margin-bottom: 22px;">
            <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em;
                                color: var(--pts-green-dark); margin-bottom: 12px; padding-bottom: 8px;
                                border-bottom: 1px solid var(--pts-grey-light);">
                What this will do
            </div>

            <div style="display: flex; flex-direction: column; gap: 8px;">
                @foreach ($features as [$featureTitle, $featureDesc])
                    <div style="display: flex; gap: 14px; padding: 14px 18px; background: var(--pts-bg);
                                            border-radius: 11px; align-items: flex-start;">
                        <div style="width: 32px; height: 32px; border-radius: 9px; background: var(--pts-green-light);
                                                color: var(--pts-green-dark); display: flex; align-items: center;
                                                justify-content: center; flex-shrink: 0; margin-top: 2px;">
                            <i class="fa-solid fa-check" style="font-size: 13px;"></i>
                        </div>
                        <div style="flex: 1;">
                            <div style="font-size: 13.5px; font-weight: 700; color: #111827; margin-bottom: 3px;">
                                {{ $featureTitle }}
                            </div>
                            <div style="font-size: 12.5px; color: var(--pts-grey); line-height: 1.55;">
                                {{ $featureDesc }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Contact / interest CTA --}}
    <div style="padding: 20px; background: var(--pts-black); border-radius: 12px;
                    display: flex; align-items: center; justify-content: space-between; gap: 20px; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 240px;">
            <div style="font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 4px;">
                Want this sooner?
            </div>
            <div style="font-size: 12.5px; color: #9CA3AF; line-height: 1.5;">
                Send us a note and we'll bump it up the priority list for your school.
            </div>
        </div>
        <a href="{{ route('settings.feedback') }}" style="display: inline-flex; align-items: center; gap: 8px;
                      padding: 11px 20px; background: var(--pts-green); color: var(--pts-black);
                      border-radius: 9px; font-size: 13px; font-weight: 700; text-decoration: none;
                      white-space: nowrap;">
            <i class="fa-solid fa-paper-plane"></i> Request this feature
        </a>
    </div>

@endsection