@extends('settings.partials._layout')

@section('settings-content')

    <a href="{{ route('settings.overview') }}" class="settings-back">
        <i class="fa-solid fa-arrow-left"></i> Back to Settings
    </a>

    <div class="settings-panel__title">Session Management</div>
    <div class="settings-panel__sub">
        Control how long users stay signed in, and what ends a session early.
    </div>

    <form method="POST" action="{{ route('settings.sessions.update') }}">
        @csrf

        @php
            $toggles = [
                'session.force_logout_on_password' => [
                    'Log out everywhere on password change',
                    'When a user changes their password, all of their other active sessions are signed out.',
                ],
                'session.single_device_only' => [
                    'Single device only',
                    'Signing in on a new device automatically signs the user out everywhere else.',
                ],
                'session.log_out_on_browser_close' => [
                    'Log out on browser close',
                    'End the session as soon as the browser is closed, instead of persisting it.',
                ],
            ];

            $numbers = [
                'session.lifetime_minutes' => [
                    'Session lifetime (minutes)',
                    'Minutes of inactivity before a user is automatically signed out.',
                    5, 10080,
                ],
                'session.remember_me_days' => [
                    'Remember me duration (days)',
                    'How long a "remember me" session stays valid.',
                    1, 365,
                ],
            ];
        @endphp

        <div style="margin-bottom: 26px;">
            <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em;
                        color: var(--pts-green-dark); margin-bottom: 12px; padding-bottom: 8px;
                        border-bottom: 1px solid var(--pts-grey-light);">
                Behaviour
            </div>

            <div style="display: flex; flex-direction: column; gap: 4px;">
                @foreach ($toggles as $key => [$title, $desc])
                    @php $value = $settings[$key] ?? false; @endphp
                    <label style="display:flex; align-items:flex-start; gap:14px; padding: 12px 14px;
                                  border-radius: 10px; cursor: pointer;"
                        onmouseover="this.style.background='var(--pts-bg)'" onmouseout="this.style.background='transparent'">
                        <input type="hidden" name="{{ $key }}" value="0">
                        <input type="checkbox" name="{{ $key }}" value="1" @checked($value)
                               style="margin-top: 3px; width: 18px; height: 18px;
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

        <div style="margin-bottom: 26px;">
            <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em;
                        color: var(--pts-green-dark); margin-bottom: 12px; padding-bottom: 8px;
                        border-bottom: 1px solid var(--pts-grey-light);">
                Timings
            </div>

            <div style="display: flex; flex-direction: column; gap: 12px;">
                @foreach ($numbers as $key => [$title, $desc, $min, $max])
                    <div style="display:grid; grid-template-columns: 1fr 140px; gap: 16px; align-items: center;
                                padding: 12px 14px; background: var(--pts-bg); border-radius: 10px;">
                        <div>
                            <div style="font-size: 13.5px; font-weight: 700; color: #111827; margin-bottom: 3px;">
                                {{ $title }}
                            </div>
                            <div style="font-size: 12px; color: var(--pts-grey); line-height: 1.5;">
                                {{ $desc }}
                            </div>
                        </div>
                        <input type="number" name="{{ $key }}" value="{{ $settings[$key] ?? 0 }}"
                               min="{{ $min }}" max="{{ $max }}"
                               style="width:100%; padding:10px 12px; border:1.5px solid var(--pts-grey-light);
                                      border-radius:9px; font-size:13.5px; font-weight:700; text-align:center;
                                      font-family:inherit; outline:none;">
                    </div>
                @endforeach
            </div>
        </div>

        <div style="display:flex; justify-content: flex-end; gap: 10px; margin-top: 28px; padding-top: 20px;
                    border-top: 1px solid var(--pts-grey-light);">
            <a href="{{ route('settings.overview') }}"
               style="padding: 11px 20px; border: 1.5px solid var(--pts-grey-light); border-radius: 9px;
                      font-size: 13px; font-weight: 700; color: #374151; text-decoration: none; background: #fff;">
                Cancel
            </a>
            <button type="submit"
                    style="padding: 11px 24px; background: var(--pts-black); color: #fff; border: none;
                           border-radius: 9px; font-size: 13px; font-weight: 700; cursor: pointer;">
                <i class="fa-solid fa-check"></i> Save Settings
            </button>
        </div>
    </form>

@endsection