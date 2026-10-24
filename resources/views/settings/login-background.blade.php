@extends('settings.partials._layout')

@section('settings-content')

    <a href="{{ route('settings.overview') }}" class="settings-back">
        <i class="fa-solid fa-arrow-left"></i> Back to Settings
    </a>

    <div class="settings-panel__title">Login Background</div>
    <div class="settings-panel__sub">
        Upload a background image for the sign-in page. Shown behind the login form.
    </div>

    @if ($errors->any())
        <div style="background:#FEF2F2; color:#991B1B; border:1px solid #FECACA;
                            padding:12px 16px; border-radius:9px; font-size:13px; margin-bottom:18px;">
            {{ $errors->first() }}
        </div>
    @endif

    @php $bgPath = $settings['branding.login_bg_path'] ?? null; @endphp

    <form method="POST" action="{{ route('settings.login-background.update') }}" enctype="multipart/form-data">
        @csrf

        <div style="margin-bottom: 24px;">
            <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em;
                            color: var(--pts-green-dark); margin-bottom: 12px; padding-bottom: 8px;
                            border-bottom: 1px solid var(--pts-grey-light);">
                Current Background
            </div>

            @if ($bgPath)
                <div style="border: 1px solid var(--pts-grey-light); border-radius: 10px; overflow: hidden;
                                    margin-bottom: 14px; position: relative;">
                    <img src="{{ \Storage::disk('public')->url($bgPath) }}" alt="Login background"
                        style="width: 100%; max-height: 240px; object-fit: cover; display: block;">
                </div>
                <label
                    style="display:flex; align-items:center; gap:8px; font-size: 13px; color: #991B1B; cursor: pointer; margin-bottom: 20px;">
                    <input type="hidden" name="remove_background" value="0">
                    <input type="checkbox" name="remove_background" value="1"
                        style="width:16px; height:16px; accent-color: #DC2626;">
                    Remove this background (revert to default)
                </label>
            @else
                <div
                    style="padding: 30px; background: var(--pts-bg); border-radius: 10px; text-align: center; margin-bottom: 20px;">
                    <i class="fa-solid fa-image"
                        style="font-size: 32px; color: var(--pts-grey-light); display:block; margin-bottom: 10px;"></i>
                    <div style="font-size: 13px; font-weight: 700; color: #111827; margin-bottom: 4px;">
                        No custom background
                    </div>
                    <div style="font-size: 12px; color: var(--pts-grey);">
                        Using the default app graphic. Upload a new image below.
                    </div>
                </div>
            @endif

            <label style="display:block; font-size:11.5px; font-weight:700; color:#374151; margin-bottom:6px;">
                {{ $bgPath ? 'Replace with a new image' : 'Upload an image' }}
            </label>
            <input type="file" name="background" accept="image/*" style="width:100%; padding:10px; border:1.5px solid var(--pts-grey-light);
                              border-radius:9px; font-size:13px; font-family:inherit; background:#fff;">
            <div style="font-size:11px; color:var(--pts-grey); margin-top:5px;">
                JPG, PNG, or WebP. Max 4 MB. Recommended size: 1920×1080 or larger.
            </div>
        </div>

        <div style="margin-bottom: 24px;">
            <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em;
                            color: var(--pts-green-dark); margin-bottom: 12px; padding-bottom: 8px;
                            border-bottom: 1px solid var(--pts-grey-light);">
                Display Options
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div>
                    <label style="display:block; font-size:11.5px; font-weight:700; color:#374151; margin-bottom:6px;">
                        Dark overlay (%)
                    </label>
                    <input type="number" name="branding.login_bg_overlay"
                        value="{{ $settings['branding.login_bg_overlay'] ?? 55 }}" min="0" max="100" style="width:100%; padding:10px 12px; border:1.5px solid var(--pts-grey-light);
                                      border-radius:9px; font-size:13.5px; font-family:inherit; outline:none;">
                    <div style="font-size:11px; color:var(--pts-grey); margin-top:5px;">
                        Darkens the image behind text. 40–70 is usually good.
                    </div>
                </div>

                <div>
                    <label style="display:block; font-size:11.5px; font-weight:700; color:#374151; margin-bottom:6px;">
                        Blur (px)
                    </label>
                    <input type="number" name="branding.login_bg_blur"
                        value="{{ $settings['branding.login_bg_blur'] ?? 0 }}" min="0" max="20" style="width:100%; padding:10px 12px; border:1.5px solid var(--pts-grey-light);
                                      border-radius:9px; font-size:13.5px; font-family:inherit; outline:none;">
                    <div style="font-size:11px; color:var(--pts-grey); margin-top:5px;">
                        Softens the background. Use 2–6 for a subtle effect.
                    </div>
                </div>
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
                <i class="fa-solid fa-check"></i> Save Background
            </button>
        </div>
    </form>

@endsection