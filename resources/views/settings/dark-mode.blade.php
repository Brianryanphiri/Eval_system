@extends('settings.partials._layout')

@section('settings-content')

    <a href="{{ route('settings.overview') }}" class="settings-back">
        <i class="fa-solid fa-arrow-left"></i> Back to Settings
    </a>

    <div class="settings-panel__title">Theme</div>
    <div class="settings-panel__sub">
        Set the default light/dark appearance and control whether users can override it.
    </div>

    <form method="POST" action="{{ route('settings.dark-mode.update') }}">
        @csrf

        <div style="margin-bottom: 26px;">
            <label
                style="display:block; font-size:11.5px; font-weight:700; color:#374151; margin-bottom:8px; text-transform:uppercase; letter-spacing:0.04em;">
                Default Theme
            </label>

            @php $current = $settings['branding.default_theme'] ?? 'light'; @endphp

            <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap: 12px;">
                <label style="border: 2px solid {{ $current === 'light' ? 'var(--pts-green)' : 'var(--pts-grey-light)' }};
                                  border-radius: 10px; padding: 16px; text-align: center; cursor: pointer;
                                  background: {{ $current === 'light' ? 'var(--pts-green-light)' : '#fff' }};">
                    <input type="radio" name="branding.default_theme" value="light" @checked($current === 'light')
                        style="display:none;">
                    <div
                        style="width: 60px; height: 40px; margin: 0 auto 10px; border-radius: 6px;
                                    background: #fff; border: 1px solid #ddd; display:flex; align-items:center; justify-content:center;">
                        <i class="fa-regular fa-sun" style="color: #D97706;"></i>
                    </div>
                    <div style="font-size: 13px; font-weight: 700; color: #111827;">Light</div>
                    <div style="font-size: 11px; color: var(--pts-grey); margin-top: 3px;">Always light</div>
                </label>

                <label style="border: 2px solid {{ $current === 'dark' ? 'var(--pts-green)' : 'var(--pts-grey-light)' }};
                                  border-radius: 10px; padding: 16px; text-align: center; cursor: pointer;
                                  background: {{ $current === 'dark' ? 'var(--pts-green-light)' : '#fff' }};">
                    <input type="radio" name="branding.default_theme" value="dark" @checked($current === 'dark')
                        style="display:none;">
                    <div style="width: 60px; height: 40px; margin: 0 auto 10px; border-radius: 6px;
                                    background: #1a1a1a; display:flex; align-items:center; justify-content:center;">
                        <i class="fa-solid fa-moon" style="color: #a78bfa;"></i>
                    </div>
                    <div style="font-size: 13px; font-weight: 700; color: #111827;">Dark</div>
                    <div style="font-size: 11px; color: var(--pts-grey); margin-top: 3px;">Always dark</div>
                </label>

                <label style="border: 2px solid {{ $current === 'system' ? 'var(--pts-green)' : 'var(--pts-grey-light)' }};
                                  border-radius: 10px; padding: 16px; text-align: center; cursor: pointer;
                                  background: {{ $current === 'system' ? 'var(--pts-green-light)' : '#fff' }};">
                    <input type="radio" name="branding.default_theme" value="system" @checked($current === 'system')
                        style="display:none;">
                    <div style="width: 60px; height: 40px; margin: 0 auto 10px; border-radius: 6px;
                                    background: linear-gradient(90deg, #fff 50%, #1a1a1a 50%);
                                    display:flex; align-items:center; justify-content:center; border: 1px solid #ddd;">
                        <i class="fa-solid fa-circle-half-stroke" style="color: var(--pts-grey);"></i>
                    </div>
                    <div style="font-size: 13px; font-weight: 700; color: #111827;">System</div>
                    <div style="font-size: 11px; color: var(--pts-grey); margin-top: 3px;">Follow OS</div>
                </label>
            </div>
        </div>

        <div style="margin-bottom: 26px;">
            <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em;
                            color: var(--pts-green-dark); margin-bottom: 12px; padding-bottom: 8px;
                            border-bottom: 1px solid var(--pts-grey-light);">
                User Options
            </div>

            @php
                $toggles = [
                    'branding.allow_user_switch' => [
                        'Allow users to switch theme',
                        'If off, everyone is locked to the default theme above.',
                    ],
                    'branding.dark_accent_auto' => [
                        'Auto-adjust accent color in dark mode',
                        'Slightly brighten the accent color in dark mode for better contrast.',
                    ],
                ];
            @endphp

            <div style="display: flex; flex-direction: column; gap: 4px;">
                @foreach ($toggles as $key => [$title, $desc])
                    @php $value = $settings[$key] ?? false; @endphp
                    <label style="display:flex; align-items:flex-start; gap:14px; padding: 12px 14px;
                                          border-radius: 10px; cursor: pointer;"
                        onmouseover="this.style.background='var(--pts-bg)'" onmouseout="this.style.background='transparent'">
                        <input type="hidden" name="{{ $key }}" value="0">
                        <input type="checkbox" name="{{ $key }}" value="1" @checked($value) style="margin-top: 3px; width: 18px; height: 18px;
                                              accent-color: var(--pts-green); cursor: pointer;">
                        <div style="flex: 1;">
                            <div style="font-size: 13.5px; font-weight: 700; color: #111827; margin-bottom: 3px;">
                                {{ $title }}
                            </div>
                            <div style="font-size: 12px; color: var(--pts-grey); line-height: 1.5;">
                                {{ $desc }}
                            </div>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>

        <div style="display:flex; justify-content: flex-end; gap: 10px; margin-top: 28px; padding-top: 20px;
                        border-top: 1px solid var(--pts-grey-light);">
            <a href="{{ route('settings.overview') }}" style="padding: 11px 20px; border: 1.5px solid var(--pts-grey-light); border-radius: 9px;
                          font-size: 13px; font-weight: 700; color: #374151; text-decoration: none; background: #fff;">
                Cancel
            </a>
            <button type="submit" style="padding: 11px 24px; background: var(--pts-black); color: #fff; border: none;
                               border-radius: 9px; font-size: 13px; font-weight: 700; cursor: pointer;">
                <i class="fa-solid fa-check"></i> Save Theme
            </button>
        </div>
    </form>

@endsection