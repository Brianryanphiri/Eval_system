@extends('settings.partials._layout')

@section('settings-content')

    <a href="{{ route('settings.overview') }}" class="settings-back">
        <i class="fa-solid fa-arrow-left"></i> Back to Settings
    </a>

    <div class="settings-panel__title">Password Policy</div>
    <div class="settings-panel__sub">
        Rules applied whenever a user sets or changes their password.
    </div>

    <form method="POST" action="{{ route('settings.password-policy.update') }}">
        @csrf

        @php
            $toggles = [
                'password.require_uppercase' => [
                    'Require uppercase letter',
                    'Password must contain at least one uppercase letter.',
                ],
                'password.require_lowercase' => [
                    'Require lowercase letter',
                    'Password must contain at least one lowercase letter.',
                ],
                'password.require_number' => [
                    'Require number',
                    'Password must contain at least one digit.',
                ],
                'password.require_symbol' => [
                    'Require symbol',
                    'Password must contain at least one symbol, e.g. ! @ # $.',
                ],
            ];

            $numbers = [
                'password.min_length' => [
                    'Minimum length',
                    'The shortest password a user is allowed to set.',
                    6, 64,
                ],
                'password.expiry_days' => [
                    'Password expiry (days)',
                    'Days before a password must be changed. Set to 0 to disable expiry.',
                    0, 365,
                ],
                'password.prevent_reuse_count' => [
                    'Prevent reuse count',
                    'How many of a user\'s past passwords cannot be reused.',
                    0, 20,
                ],
            ];
        @endphp

        <div style="margin-bottom: 26px;">
            <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em;
                        color: var(--pts-green-dark); margin-bottom: 12px; padding-bottom: 8px;
                        border-bottom: 1px solid var(--pts-grey-light);">
                Composition Rules
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
                Length &amp; Expiry
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