@extends('layouts.app')

@section('title', 'My Profile')

@section('page-title', 'My Profile')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}">Dashboard</a>
    <span class="sep">/</span>
    <span>My Profile</span>
@endsection

@section('styles')
    <style>
        .pf-wrap {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 320px;
            gap: 20px;
            align-items: start;
        }

        @media (max-width: 1024px) {
            .pf-wrap {
                grid-template-columns: 1fr;
            }
        }

        .pf-card {
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 12px;
            overflow: hidden;
        }

        .pf-card__head {
            padding: 16px 20px;
            border-bottom: 1px solid var(--pts-grey-light);
            background: var(--pts-bg);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .pf-card__head i {
            color: var(--pts-green-dark);
            font-size: 14px;
        }

        .pf-card__title {
            font-size: 13px;
            font-weight: 700;
            color: #111827;
        }

        .pf-card__body {
            padding: 20px 24px;
        }

        .pf-field {
            margin-bottom: 16px;
        }

        .pf-field:last-child {
            margin-bottom: 0;
        }

        .pf-label {
            display: block;
            font-size: 11.5px;
            font-weight: 700;
            color: #374151;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .pf-input {
            width: 100%;
            padding: 11px 14px;
            border: 1.5px solid var(--pts-grey-light);
            border-radius: 9px;
            font-size: 13.5px;
            font-family: inherit;
            outline: none;
            transition: border-color 0.15s;
            background: #fff;
            color: #111827;
        }

        .pf-input:focus {
            border-color: var(--pts-green);
        }

        .pf-input:disabled {
            background: var(--pts-bg);
            color: var(--pts-grey);
            cursor: not-allowed;
        }

        .pf-hint {
            font-size: 11.5px;
            color: var(--pts-grey);
            margin-top: 5px;
        }

        .pf-error {
            font-size: 12px;
            color: var(--pts-red);
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .pf-actions {
            margin-top: 20px;
            padding-top: 18px;
            border-top: 1px solid var(--pts-grey-light);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .pf-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 10px 18px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            font-family: inherit;
            text-decoration: none;
            transition: all 0.15s;
        }

        .pf-btn.primary {
            background: var(--pts-green);
            color: #fff;
        }

        .pf-btn.primary:hover {
            background: var(--pts-green-dark);
        }

        .pf-avatar-block {
            text-align: center;
            padding: 24px 16px;
        }

        .pf-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: var(--pts-green);
            color: var(--pts-black);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            font-weight: 700;
            margin: 0 auto 14px;
        }

        .pf-avatar-name {
            font-size: 15px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 3px;
        }

        .pf-avatar-role {
            font-size: 11.5px;
            color: var(--pts-grey);
            text-transform: capitalize;
        }

        .pf-detail {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 9px 0;
            border-bottom: 1px solid var(--pts-grey-light);
            font-size: 12.5px;
        }

        .pf-detail:last-child {
            border-bottom: none;
        }

        .pf-detail__label {
            color: var(--pts-grey);
            font-weight: 500;
        }

        .pf-detail__value {
            color: #111827;
            font-weight: 600;
            max-width: 60%;
            text-align: right;
            word-wrap: break-word;
        }

        /* Avatar photo card */
        .pf-photo-row {
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .pf-photo-preview {
            width: 88px;
            height: 88px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--pts-green-light);
            flex-shrink: 0;
        }

        .pf-photo-fallback {
            width: 88px;
            height: 88px;
            border-radius: 50%;
            background: var(--pts-green);
            color: var(--pts-black);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 34px;
            font-weight: 700;
            border: 3px solid var(--pts-green-light);
            flex-shrink: 0;
        }

        .pf-photo-actions {
            flex: 1;
            min-width: 220px;
        }

        .pf-file-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 16px;
            background: var(--pts-bg);
            color: #111827;
            border-radius: 8px;
            font-size: 12.5px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            font-family: inherit;
        }

        .pf-file-btn:hover {
            background: var(--pts-grey-light);
        }

        .pf-remove-btn {
            background: none;
            border: none;
            color: var(--pts-red);
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            padding: 0;
            font-family: inherit;
        }

        .pf-remove-btn:hover {
            text-decoration: underline;
        }
    </style>
@endsection

@section('content')

    <div class="pf-wrap">

        {{-- LEFT — forms --}}
        <div>

            {{-- Photo --}}
            <div class="pf-card" style="margin-bottom:20px;">
                <div class="pf-card__head">
                    <i class="fa-solid fa-camera"></i>
                    <div class="pf-card__title">Profile Photo</div>
                </div>
                <div class="pf-card__body">

                    <div class="pf-photo-row">

                        {{-- Preview --}}
                        <div>
                            @if ($user->avatar_url)
                                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" id="avatarPreview"
                                    class="pf-photo-preview">
                            @else
                                <div id="avatarPreviewFallback" class="pf-photo-fallback">
                                    {{ $user->initials }}
                                </div>
                                <img src="" alt="" id="avatarPreview" class="pf-photo-preview"
                                    style="display:none;">
                            @endif
                        </div>

                        {{-- Controls --}}
                        <div class="pf-photo-actions">

                            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                {{-- Preserve existing fields so this form doesn't wipe them --}}
                                <input type="hidden" name="name" value="{{ $user->name }}">
                                <input type="hidden" name="email" value="{{ $user->email }}">
                                <input type="hidden" name="phone" value="{{ $user->phone }}">

                                <label for="avatar" class="pf-file-btn">
                                    <i class="fa-solid fa-upload"></i>
                                    Choose photo
                                </label>
                                <input type="file" id="avatar" name="avatar"
                                    accept="image/png,image/jpeg,image/webp" style="display:none;"
                                    onchange="previewAvatar(this);">

                                <div class="pf-hint">JPG, PNG or WebP. Max 2 MB.</div>

                                <div style="margin-top:12px;">
                                    <button type="submit" class="pf-btn primary" id="avatarSubmitBtn"
                                        style="display:none;">
                                        <i class="fa-solid fa-check"></i>
                                        Save photo
                                    </button>
                                </div>

                                @error('avatar')
                                    <div class="pf-error" style="margin-top:8px;">
                                        <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                                    </div>
                                @enderror
                            </form>

                            @if ($user->avatar_url)
                                <form method="POST" action="{{ route('profile.avatar.destroy') }}" style="margin-top:10px;"
                                    data-confirm="Remove your profile photo?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="pf-remove-btn">
                                        <i class="fa-solid fa-trash"></i> Remove photo
                                    </button>
                                </form>
                            @endif

                        </div>

                    </div>

                </div>
            </div>

            {{-- Basic info --}}
            <div class="pf-card" style="margin-bottom:20px;">
                <div class="pf-card__head">
                    <i class="fa-solid fa-user"></i>
                    <div class="pf-card__title">Personal Information</div>
                </div>
                <div class="pf-card__body">
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="pf-field">
                            <label for="name" class="pf-label">Full Name</label>
                            <input type="text" id="name" name="name" class="pf-input"
                                value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="pf-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="pf-field">
                            <label for="email" class="pf-label">Email Address</label>
                            <input type="email" id="email" name="email" class="pf-input"
                                value="{{ old('email', $user->email) }}" required>
                            @if ($user->email_verified_at)
                                <div class="pf-hint">
                                    <i class="fa-solid fa-circle-check" style="color:var(--pts-green-dark);"></i>
                                    Email verified
                                </div>
                            @else
                                <div class="pf-hint">
                                    <i class="fa-solid fa-circle-info"></i>
                                    Not yet verified
                                </div>
                            @endif
                            @error('email')
                                <div class="pf-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="pf-field">
                            <label for="phone" class="pf-label">Phone Number</label>
                            <input type="text" id="phone" name="phone" class="pf-input"
                                value="{{ old('phone', $user->phone) }}" placeholder="+265 999 000 000">
                            @error('phone')
                                <div class="pf-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="pf-actions">
                            <button type="submit" class="pf-btn primary">
                                <i class="fa-solid fa-check"></i>
                                Save changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Password --}}
            <div class="pf-card">
                <div class="pf-card__head">
                    <i class="fa-solid fa-lock"></i>
                    <div class="pf-card__title">Change Password</div>
                </div>
                <div class="pf-card__body">
                    <form method="POST" action="{{ route('profile.password') }}">
                        @csrf
                        @method('PUT')

                        <div class="pf-field">
                            <label for="current_password" class="pf-label">Current Password</label>
                            <input type="password" id="current_password" name="current_password" class="pf-input" required
                                autocomplete="current-password">
                            @error('current_password')
                                <div class="pf-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="pf-field">
                            <label for="password" class="pf-label">New Password</label>
                            <input type="password" id="password" name="password" class="pf-input" required
                                autocomplete="new-password">
                            <div class="pf-hint">Minimum 8 characters.</div>
                            @error('password')
                                <div class="pf-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="pf-field">
                            <label for="password_confirmation" class="pf-label">Confirm New Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="pf-input"
                                required autocomplete="new-password">
                        </div>

                        <div class="pf-actions">
                            <button type="submit" class="pf-btn primary">
                                <i class="fa-solid fa-key"></i>
                                Update password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

        {{-- RIGHT — summary card --}}
        <aside>
            <div class="pf-card">
                <div class="pf-avatar-block">
                    @if ($user->avatar_url)
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                            style="width:80px; height:80px; border-radius:50%; object-fit:cover; margin:0 auto 14px; border:3px solid var(--pts-green-light); display:block;">
                    @else
                        <div class="pf-avatar">
                            {{ $user->initials }}
                        </div>
                    @endif
                    <div class="pf-avatar-name">{{ $user->name }}</div>
                    <div class="pf-avatar-role">{{ str_replace('_', ' ', $user->role) }}</div>
                </div>
                <div class="pf-card__body" style="padding-top:0;">
                    <div class="pf-detail">
                        <span class="pf-detail__label">Email</span>
                        <span class="pf-detail__value">{{ $user->email }}</span>
                    </div>
                    <div class="pf-detail">
                        <span class="pf-detail__label">Phone</span>
                        <span class="pf-detail__value">{{ $user->phone ?? '—' }}</span>
                    </div>
                    <div class="pf-detail">
                        <span class="pf-detail__label">Role</span>
                        <span class="pf-detail__value">{{ str_replace('_', ' ', $user->role) }}</span>
                    </div>
                    @if ($user->school)
                        <div class="pf-detail">
                            <span class="pf-detail__label">School</span>
                            <span class="pf-detail__value">{{ $user->school->name }}</span>
                        </div>
                    @endif
                    @if ($user->last_login_at)
                        <div class="pf-detail">
                            <span class="pf-detail__label">Last login</span>
                            <span class="pf-detail__value">{{ $user->last_login_at->diffForHumans() }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </aside>

    </div>

@endsection

@section('scripts')
    <script>
        function previewAvatar(input) {
            if (!input.files || !input.files[0]) return;

            const reader = new FileReader();

            reader.onload = function (e) {
                const img = document.getElementById('avatarPreview');
                const fallback = document.getElementById('avatarPreviewFallback');
                const btn = document.getElementById('avatarSubmitBtn');

                if (img) {
                    img.src = e.target.result;
                    img.style.display = 'block';
                }
                if (fallback) {
                    fallback.style.display = 'none';
                }
                if (btn) {
                    btn.style.display = 'inline-flex';
                }
            };

            reader.readAsDataURL(input.files[0]);
        }
    </script>
@endsection