@extends('settings.partials._layout')

@section('settings-content')

    <a href="{{ route('settings.overview') }}" class="settings-back">
        <i class="fa-solid fa-arrow-left"></i> Back to Settings
    </a>

    <div class="settings-panel__title">Two-Factor Authentication</div>
    <div class="settings-panel__sub">
        Add an extra verification step when staff and admins sign in.
    </div>

    <form method="POST" action="{{ route('settings.two-factor.update') }}">
        @csrf

        @php
            $toggles = [
                '2fa.available' => [
                    'Enable two-factor authentication',
                    'Allow users to turn on 2FA for their own account.',
                ],
                '2fa.required_admins' => [
                    'Require for admins',
                    'Force every admin account to use 2FA.',
                ],
                '2fa.required_teachers' => [
                    'Require for teachers',
                    'Force every teacher account to use 2FA.',
                ],
                '2fa.required_all' => [
                    'Require for all users',
                    'Force every account on the system to use 2FA, including guardians and students.',
                ],
                '2fa.method_email' => [
                    'Allow email codes',
                    'Users can receive their verification code by email.',
                ],
                '2fa.method_totp' => [
                    'Allow authenticator apps',
                    'Users can generate codes with an app such as Google Authenticator.',
                ],
            ];
        @endphp

        <div style="margin-bottom: 26px;">
            <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em;
                            color: var(--pts-green-dark); margin-bottom: 12px; padding-bottom: 8px;
                            border-bottom: 1px solid var(--pts-grey-light);">
                Rules
            </div>

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

        <div style="margin-bottom: 26px; padding: 16px; background: var(--pts-bg); border-radius: 10px;">
            <label style="display:block; font-size:11.5px; font-weight:700; color:#374151; margin-bottom:5px;">
                Remember trusted device for
            </label>
            <div style="display:flex; align-items:center; gap:10px;">
                <input type="number" name="2fa.remember_days" value="{{ $settings['2fa.remember_days'] ?? 30 }}" min="0"
                    max="365" style="width:100px; padding:10px 12px; border:1.5px solid var(--pts-grey-light);
                                  border-radius:9px; font-size:13.5px; font-family:inherit; outline:none;">
                <span style="font-size:13px; color:var(--pts-grey);">days</span>
            </div>
            <div style="font-size:11px; color:var(--pts-grey); margin-top:6px;">
                A device that passes 2FA once won't need to again until this many days have passed.
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
                <i class="fa-solid fa-check"></i> Save Settings
            </button>
        </div>
    </form>

@endsection