@extends('settings.partials._layout')

@section('settings-content')

    <div class="profile-settings">

        <!-- Back link -->
        <a href="{{ route('settings.overview') }}" class="profile-settings__back">
            <i class="fa-solid fa-arrow-left"></i> Back to Settings
        </a>

        <!-- Page header -->
        <div class="profile-settings__header">
            <div class="profile-settings__header-icon">
                <i class="fa-solid fa-school"></i>
            </div>
            <div>
                <h1 class="profile-settings__title">Institution Profile</h1>
                <p class="profile-settings__subtitle">
                    Everything about your school: identity, contact, branding, and preferences.
                </p>
            </div>
        </div>

        <!-- Error banner -->
        @if ($errors->any())
            <div class="profile-settings__alert profile-settings__alert--error">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('settings.profile.update') }}" enctype="multipart/form-data"
            class="profile-settings__form">
            @csrf

            <!-- ============ IDENTITY ============ -->
            <section class="profile-settings__section">
                <div class="profile-settings__section-header">
                    <div class="profile-settings__section-icon">
                        <i class="fa-solid fa-fingerprint"></i>
                    </div>
                    <div>
                        <span class="profile-settings__section-label">Identity</span>
                        <span class="profile-settings__section-desc">Basic information about your institution</span>
                    </div>
                </div>

                <div class="profile-settings__grid profile-settings__grid--3-1-1">
                    <div class="profile-settings__field">
                        <label class="profile-settings__label" for="name">
                            <i class="fa-solid fa-school-flag profile-settings__label-icon"></i>
                            School Name <span class="required">*</span>
                        </label>
                        <div class="profile-settings__input-wrap">
                            <i class="fa-solid fa-building-columns profile-settings__input-icon"></i>
                            <input type="text" id="name" name="name" value="{{ old('name', $school->name) }}" required
                                class="profile-settings__input profile-settings__input--icon"
                                placeholder="e.g. Kamuzu Academy">
                        </div>
                    </div>
                    <div class="profile-settings__field">
                        <label class="profile-settings__label" for="motto">
                            <i class="fa-solid fa-quote-left profile-settings__label-icon"></i>
                            Motto / Tagline
                        </label>
                        <div class="profile-settings__input-wrap">
                            <i class="fa-solid fa-comment-dots profile-settings__input-icon"></i>
                            <input type="text" id="motto" name="motto" value="{{ old('motto', $school->motto) }}"
                                class="profile-settings__input profile-settings__input--icon"
                                placeholder="Knowledge is Power">
                        </div>
                    </div>
                    <div class="profile-settings__field">
                        <label class="profile-settings__label" for="founded_year">
                            <i class="fa-solid fa-calendar profile-settings__label-icon"></i>
                            Founded Year
                        </label>
                        <div class="profile-settings__input-wrap">
                            <i class="fa-solid fa-calendar-check profile-settings__input-icon"></i>
                            <input type="number" id="founded_year" name="founded_year"
                                value="{{ old('founded_year', $school->founded_year) }}" min="1800"
                                max="{{ date('Y') + 1 }}" class="profile-settings__input profile-settings__input--icon"
                                placeholder="1985">
                        </div>
                    </div>
                </div>

                <div class="profile-settings__grid profile-settings__grid--4">
                    <!-- Institution Type -->
                    <div class="profile-settings__field">
                        <label class="profile-settings__label">
                            <i class="fa-solid fa-layer-group profile-settings__label-icon"></i>
                            Institution Type
                        </label>
                        <div class="profile-settings__custom-select" data-name="institution_type" data-value="{{ old('institution_type', $school->institution_type) }}">
                            <div class="profile-settings__custom-trigger">
                                <i class="fa-solid fa-sitemap profile-settings__custom-trigger-icon"></i>
                                <span class="profile-settings__custom-value" data-placeholder="Select type">Select type</span>
                                <i class="fa-solid fa-chevron-down profile-settings__custom-caret"></i>
                            </div>
                            <div class="profile-settings__custom-menu">
                                <div class="profile-settings__custom-option" data-value="">Select type</div>
                                <div class="profile-settings__custom-option" data-value="primary">Primary School</div>
                                <div class="profile-settings__custom-option" data-value="secondary">Secondary School</div>
                                <div class="profile-settings__custom-option" data-value="international">International School</div>
                                <div class="profile-settings__custom-option" data-value="college">College</div>
                            </div>
                            <input type="hidden" name="institution_type" value="{{ old('institution_type', $school->institution_type) }}">
                        </div>
                    </div>

                    <!-- Academic System -->
                    <div class="profile-settings__field">
                        <label class="profile-settings__label">
                            <i class="fa-solid fa-graduation-cap profile-settings__label-icon"></i>
                            Academic System
                        </label>
                        <div class="profile-settings__custom-select" data-name="academic_system" data-value="{{ old('academic_system', $school->academic_system) }}">
                            <div class="profile-settings__custom-trigger">
                                <i class="fa-solid fa-book-open profile-settings__custom-trigger-icon"></i>
                                <span class="profile-settings__custom-value" data-placeholder="Select system">Select system</span>
                                <i class="fa-solid fa-chevron-down profile-settings__custom-caret"></i>
                            </div>
                            <div class="profile-settings__custom-menu">
                                <div class="profile-settings__custom-option" data-value="primary">Primary</div>
                                <div class="profile-settings__custom-option" data-value="secondary">Secondary</div>
                                <div class="profile-settings__custom-option" data-value="igcse">IGCSE</div>
                                <div class="profile-settings__custom-option" data-value="ib">IB Diploma</div>
                                <div class="profile-settings__custom-option" data-value="alevel">A-Level</div>
                            </div>
                            <input type="hidden" name="academic_system" value="{{ old('academic_system', $school->academic_system) }}">
                        </div>
                    </div>

                    <div class="profile-settings__field">
                        <label class="profile-settings__label" for="country">
                            <i class="fa-solid fa-globe profile-settings__label-icon"></i>
                            Country
                        </label>
                        <div class="profile-settings__input-wrap">
                            <i class="fa-solid fa-location-dot profile-settings__input-icon"></i>
                            <input type="text" id="country" name="country" value="{{ old('country', $school->country) }}"
                                   class="profile-settings__input profile-settings__input--icon" placeholder="Malawi">
                        </div>
                    </div>
                    <div class="profile-settings__field">
                        <label class="profile-settings__label" for="registration_number">
                            <i class="fa-solid fa-file-contract profile-settings__label-icon"></i>
                            Registration No.
                        </label>
                        <div class="profile-settings__input-wrap">
                            <i class="fa-solid fa-hashtag profile-settings__input-icon"></i>
                            <input type="text" id="registration_number" name="registration_number"
                                   value="{{ old('registration_number', $school->registration_number) }}"
                                   class="profile-settings__input profile-settings__input--icon" placeholder="Ministry Reg. No.">
                        </div>
                    </div>
                </div>
            </section>

            <!-- ============ ADDRESS ============ -->
            <section class="profile-settings__section">
                <div class="profile-settings__section-header">
                    <div class="profile-settings__section-icon">
                        <i class="fa-solid fa-map-location-dot"></i>
                    </div>
                    <div>
                        <span class="profile-settings__section-label">Address</span>
                        <span class="profile-settings__section-desc">Physical and postal location</span>
                    </div>
                </div>

                <div class="profile-settings__grid profile-settings__grid--2-1-1-1">
                    <div class="profile-settings__field">
                        <label class="profile-settings__label" for="address_street">
                            <i class="fa-solid fa-road profile-settings__label-icon"></i>
                            Street
                        </label>
                        <div class="profile-settings__input-wrap">
                            <i class="fa-solid fa-signs-post profile-settings__input-icon"></i>
                            <input type="text" id="address_street" name="address_street"
                                   value="{{ old('address_street', $school->address_street) }}"
                                   class="profile-settings__input profile-settings__input--icon" placeholder="Private Bag 301">
                        </div>
                    </div>
                    <div class="profile-settings__field">
                        <label class="profile-settings__label" for="address_city">
                            <i class="fa-solid fa-city profile-settings__label-icon"></i>
                            City
                        </label>
                        <div class="profile-settings__input-wrap">
                            <i class="fa-solid fa-building profile-settings__input-icon"></i>
                            <input type="text" id="address_city" name="address_city"
                                   value="{{ old('address_city', $school->address_city) }}"
                                   class="profile-settings__input profile-settings__input--icon" placeholder="Lilongwe">
                        </div>
                    </div>
                    <div class="profile-settings__field">
                        <label class="profile-settings__label" for="address_province">
                            <i class="fa-solid fa-map profile-settings__label-icon"></i>
                            Province
                        </label>
                        <div class="profile-settings__input-wrap">
                            <i class="fa-solid fa-map-pin profile-settings__input-icon"></i>
                            <input type="text" id="address_province" name="address_province"
                                   value="{{ old('address_province', $school->address_province) }}"
                                   class="profile-settings__input profile-settings__input--icon" placeholder="Central">
                        </div>
                    </div>
                    <div class="profile-settings__field">
                        <label class="profile-settings__label" for="address_postal_code">
                            <i class="fa-solid fa-envelopes-bulk profile-settings__label-icon"></i>
                            Postal Code
                        </label>
                        <div class="profile-settings__input-wrap">
                            <i class="fa-solid fa-mailbox profile-settings__input-icon"></i>
                            <input type="text" id="address_postal_code" name="address_postal_code"
                                   value="{{ old('address_postal_code', $school->address_postal_code) }}"
                                   class="profile-settings__input profile-settings__input--icon" placeholder="301">
                        </div>
                    </div>
                </div>
            </section>

            <!-- ============ CONTACT ============ -->
            <section class="profile-settings__section">
                <div class="profile-settings__section-header">
                    <div class="profile-settings__section-icon">
                        <i class="fa-solid fa-address-book"></i>
                    </div>
                    <div>
                        <span class="profile-settings__section-label">Contact</span>
                        <span class="profile-settings__section-desc">How parents and partners can reach you</span>
                    </div>
                </div>

                <div class="profile-settings__grid profile-settings__grid--2">
                    <div class="profile-settings__field">
                        <label class="profile-settings__label" for="phone">
                            <i class="fa-solid fa-phone profile-settings__label-icon"></i>
                            Primary Phone
                        </label>
                        <div class="profile-settings__input-wrap">
                            <i class="fa-solid fa-phone-volume profile-settings__input-icon"></i>
                            <input type="text" id="phone" name="phone" value="{{ old('phone', $school->phone) }}"
                                   class="profile-settings__input profile-settings__input--icon" placeholder="+265 1 234 567">
                        </div>
                    </div>
                    <div class="profile-settings__field">
                        <label class="profile-settings__label" for="phone_secondary">
                            <i class="fa-solid fa-phone-flip profile-settings__label-icon"></i>
                            Secondary Phone
                        </label>
                        <div class="profile-settings__input-wrap">
                            <i class="fa-solid fa-mobile-screen profile-settings__input-icon"></i>
                            <input type="text" id="phone_secondary" name="phone_secondary"
                                   value="{{ old('phone_secondary', $school->phone_secondary) }}"
                                   class="profile-settings__input profile-settings__input--icon" placeholder="Admissions, accounts">
                        </div>
                    </div>
                </div>

                <div class="profile-settings__grid profile-settings__grid--2" style="margin-top:16px;">
                    <div class="profile-settings__field">
                        <label class="profile-settings__label" for="email">
                            <i class="fa-solid fa-envelope profile-settings__label-icon"></i>
                            Email
                        </label>
                        <div class="profile-settings__input-wrap">
                            <i class="fa-solid fa-at profile-settings__input-icon"></i>
                            <input type="email" id="email" name="email" value="{{ old('email', $school->email) }}"
                                   class="profile-settings__input profile-settings__input--icon" placeholder="info@school.com">
                        </div>
                    </div>
                    <div class="profile-settings__field">
                        <label class="profile-settings__label" for="website">
                            <i class="fa-solid fa-link profile-settings__label-icon"></i>
                            Website
                        </label>
                        <div class="profile-settings__input-wrap">
                            <i class="fa-solid fa-globe profile-settings__input-icon"></i>
                            <input type="url" id="website" name="website" value="{{ old('website', $school->website) }}"
                                   class="profile-settings__input profile-settings__input--icon" placeholder="https://school.com">
                        </div>
                    </div>
                </div>

                <div class="profile-settings__field" style="margin-top:16px;">
                    <label class="profile-settings__label" for="email_signature">
                        <i class="fa-solid fa-signature profile-settings__label-icon"></i>
                        Email Signature
                    </label>
                    <div class="profile-settings__input-wrap">
                        <i class="fa-solid fa-pen-nib profile-settings__input-icon profile-settings__input-icon--textarea"></i>
                        <textarea id="email_signature" name="email_signature" rows="3"
                                  class="profile-settings__textarea profile-settings__textarea--icon"
                                  placeholder="Mrs. Chikondi Phiri&#10;Head Teacher, Kamuzu Academy&#10;+265 1 234 567">{{ old('email_signature', $school->email_signature) }}</textarea>
                    </div>
                    <p class="profile-settings__hint"><i class="fa-solid fa-circle-info"></i> Appended to the bottom of report-card emails and guardian updates.</p>
                </div>
            </section>

            <!-- ============ SIGNATORY ============ -->
            <section class="profile-settings__section">
                <div class="profile-settings__section-header">
                    <div class="profile-settings__section-icon">
                        <i class="fa-solid fa-user-tie"></i>
                    </div>
                    <div>
                        <span class="profile-settings__section-label">Head Teacher / Principal</span>
                        <span class="profile-settings__section-desc">Official signatory details for documents</span>
                    </div>
                </div>

                <div class="profile-settings__grid profile-settings__grid--2">
                    <div class="profile-settings__field">
                        <label class="profile-settings__label" for="principal_name">
                            <i class="fa-solid fa-user profile-settings__label-icon"></i>
                            Full Name
                        </label>
                        <div class="profile-settings__input-wrap">
                            <i class="fa-solid fa-user-pen profile-settings__input-icon"></i>
                            <input type="text" id="principal_name" name="principal_name"
                                   value="{{ old('principal_name', $school->principal_name) }}"
                                   class="profile-settings__input profile-settings__input--icon" placeholder="Mrs. Chikondi Phiri">
                        </div>
                        <p class="profile-settings__hint"><i class="fa-solid fa-circle-info"></i> Printed on report card signature blocks.</p>
                    </div>
                    <div class="profile-settings__field">
                        <label class="profile-settings__label" for="principal_signature">
                            <i class="fa-solid fa-file-signature profile-settings__label-icon"></i>
                            Signature Image
                        </label>
                        <div class="profile-settings__file-wrapper">
                            <input type="file" id="principal_signature" name="principal_signature" accept="image/*"
                                   class="profile-settings__file-input">
                            @if ($school->principal_signature_path)
                                <div class="profile-settings__file-preview">
                                    <img src="{{ \Storage::disk('public')->url($school->principal_signature_path) }}"
                                         alt="Signature preview" class="profile-settings__signature-preview">
                                </div>
                            @endif
                        </div>
                        <p class="profile-settings__hint"><i class="fa-solid fa-circle-info"></i> PNG with transparent background works best. Max 1 MB.</p>
                    </div>
                </div>
            </section>

            <!-- ============ BRANDING ============ -->
            <section class="profile-settings__section">
                <div class="profile-settings__section-header">
                    <div class="profile-settings__section-icon">
                        <i class="fa-solid fa-palette"></i>
                    </div>
                    <div>
                        <span class="profile-settings__section-label">Branding</span>
                        <span class="profile-settings__section-desc">Visual identity used across the system</span>
                    </div>
                </div>

                <div class="profile-settings__grid profile-settings__grid--3">

                    <!-- Logo -->
                    <div class="profile-settings__field">
                        <label class="profile-settings__label">
                            <i class="fa-solid fa-image profile-settings__label-icon"></i>
                            School Logo
                        </label>
                        <div class="profile-settings__branding-card">
                            <div class="profile-settings__branding-preview">
                                @if ($school->logo_path)
                                    <img src="{{ \Storage::disk('public')->url($school->logo_path) }}" alt="Logo" class="profile-settings__logo-preview">
                                @else
                                    <div class="profile-settings__logo-placeholder">P</div>
                                @endif
                            </div>
                            <input type="file" name="logo" accept="image/*" class="profile-settings__file-input profile-settings__file-input--small">
                            <p class="profile-settings__hint"><i class="fa-solid fa-circle-info"></i> PNG/JPG. Max 2 MB</p>
                        </div>
                    </div>

                    <!-- Favicon -->
                    <div class="profile-settings__field">
                        <label class="profile-settings__label">
                            <i class="fa-solid fa-star profile-settings__label-icon"></i>
                            Favicon
                        </label>
                        <div class="profile-settings__branding-card">
                            <div class="profile-settings__branding-preview">
                                @if ($school->favicon_path)
                                    <img src="{{ \Storage::disk('public')->url($school->favicon_path) }}" alt="Favicon" class="profile-settings__favicon-preview">
                                @else
                                    <span class="profile-settings__favicon-placeholder">No favicon yet</span>
                                @endif
                            </div>
                            <input type="file" name="favicon" accept="image/*,.ico" class="profile-settings__file-input profile-settings__file-input--small">
                            <p class="profile-settings__hint"><i class="fa-solid fa-circle-info"></i> PNG/ICO. Max 512 KB</p>
                        </div>
                    </div>

                    <!-- Colors -->
                    <div class="profile-settings__field">
                        <label class="profile-settings__label">
                            <i class="fa-solid fa-droplet profile-settings__label-icon"></i>
                            Brand Colours
                        </label>
                        <div class="profile-settings__branding-card profile-settings__branding-card--colours">
                            <div class="profile-settings__colour-row">
                                <span class="profile-settings__colour-label"><i class="fa-solid fa-circle"></i> Primary</span>
                                <input type="color" name="primary_color"
                                       value="{{ old('primary_color', $school->primary_color ?? '#0F0F0F') }}"
                                       class="profile-settings__colour-input">
                            </div>
                            <div class="profile-settings__colour-row">
                                <span class="profile-settings__colour-label"><i class="fa-solid fa-circle-half-stroke"></i> Accent</span>
                                <input type="color" name="accent_color"
                                       value="{{ old('accent_color', $school->accent_color ?? '#7AC142') }}"
                                       class="profile-settings__colour-input">
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ============ PREFERENCES ============ -->
            <section class="profile-settings__section">
                <div class="profile-settings__section-header">
                    <div class="profile-settings__section-icon">
                        <i class="fa-solid fa-sliders"></i>
                    </div>
                    <div>
                        <span class="profile-settings__section-label">Preferences</span>
                        <span class="profile-settings__section-desc">Regional and system settings</span>
                    </div>
                </div>

                <div class="profile-settings__grid profile-settings__grid--2-1-1">

                    <!-- Timezone -->
                    <div class="profile-settings__field">
                        <label class="profile-settings__label">
                            <i class="fa-solid fa-clock profile-settings__label-icon"></i>
                            Time Zone
                        </label>
                        <div class="profile-settings__custom-select" data-name="timezone" data-value="{{ old('timezone', $school->timezone) }}">
                            <div class="profile-settings__custom-trigger">
                                <i class="fa-solid fa-earth-africa profile-settings__custom-trigger-icon"></i>
                                <span class="profile-settings__custom-value" data-placeholder="Select timezone">Select timezone</span>
                                <i class="fa-solid fa-chevron-down profile-settings__custom-caret"></i>
                            </div>
                            <div class="profile-settings__custom-menu">
                                @foreach ($timezones as $tz)
                                    <div class="profile-settings__custom-option" data-value="{{ $tz }}">{{ $tz }}</div>
                                @endforeach
                            </div>
                            <input type="hidden" name="timezone" value="{{ old('timezone', $school->timezone) }}">
                        </div>
                    </div>

                    <!-- Currency -->
                    <div class="profile-settings__field">
                        <label class="profile-settings__label">
                            <i class="fa-solid fa-coins profile-settings__label-icon"></i>
                            Currency
                        </label>
                        <div class="profile-settings__custom-select" data-name="currency" data-value="{{ old('currency', $school->currency) }}">
                            <div class="profile-settings__custom-trigger">
                                <i class="fa-solid fa-money-bill-wave profile-settings__custom-trigger-icon"></i>
                                <span class="profile-settings__custom-value" data-placeholder="Select currency">Select currency</span>
                                <i class="fa-solid fa-chevron-down profile-settings__custom-caret"></i>
                            </div>
                            <div class="profile-settings__custom-menu">
                                @foreach ($currencies as $code => $label)
                                    <div class="profile-settings__custom-option" data-value="{{ $code }}">{{ $code }} ({{ $label }})</div>
                                @endforeach
                            </div>
                            <input type="hidden" name="currency" value="{{ old('currency', $school->currency) }}">
                        </div>
                    </div>

                    <!-- Public Portal -->
                    <div class="profile-settings__field">
                        <label class="profile-settings__label" for="public_portal_enabled">
                            <i class="fa-solid fa-toggle-on profile-settings__label-icon"></i>
                            Public Portal
                        </label>
                        <label class="profile-settings__toggle-label">
                            <input type="checkbox" id="public_portal_enabled" name="public_portal_enabled" value="1"
                                   @checked(old('public_portal_enabled', $school->public_portal_enabled))
                                   class="profile-settings__toggle-input">
                            <span class="profile-settings__toggle-text">
                                <i class="fa-solid fa-eye"></i> Enabled
                            </span>
                        </label>
                    </div>
                </div>
            </section>

            <!-- ============ ACTIONS ============ -->
            <div class="profile-settings__actions">
                <a href="{{ route('settings.overview') }}" class="profile-settings__btn profile-settings__btn--secondary">
                    <i class="fa-solid fa-xmark"></i> Cancel
                </a>
                <button type="submit" class="profile-settings__btn profile-settings__btn--primary">
                    <i class="fa-solid fa-check"></i> Save Changes
                </button>
            </div>

        </form>
    </div>

    {{-- ============ STYLES ============ --}}
    <style>
        :root {
            --green: #5B9E3E;
            --green-dark: #4A8432;
            --green-light: #F0FDF4;
            --green-border: #DCFCE7;
        }

        .profile-settings {
            width: 100%;
            max-width: 100%;
            margin: 0;
            padding: 0 0 48px;
            box-sizing: border-box;
        }

        .profile-settings__back {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-size: 13px;
            font-weight: 600;
            color: #6B7280;
            text-decoration: none;
            margin-bottom: 20px;
            transition: color 0.15s, gap 0.15s;
        }
        .profile-settings__back:hover { color: #0F0F0F; gap: 10px; }
        .profile-settings__back i { font-size: 11px; }

        .profile-settings__header {
            display: flex;
            align-items: center;
            gap: 18px;
            margin-bottom: 32px;
            padding: 24px 28px;
            background: linear-gradient(135deg, #0F0F0F 0%, #1a1a1a 100%);
            border-radius: 16px;
            color: #fff;
            position: relative;
            overflow: hidden;
        }
        .profile-settings__header::after {
            content: '';
            position: absolute;
            top: -50%; right: -10%;
            width: 300px; height: 300px;
            background: radial-gradient(circle, rgba(91, 158, 62, 0.15) 0%, transparent 70%);
            pointer-events: none;
        }
        .profile-settings__header-icon {
            width: 56px; height: 56px;
            border-radius: 14px;
            background: rgba(91, 158, 62, 0.15);
            border: 1px solid rgba(91, 158, 62, 0.3);
            display: flex; align-items: center; justify-content: center;
            font-size: 24px; color: #7AC142; flex-shrink: 0;
        }
        .profile-settings__title {
            font-size: 22px; font-weight: 800; color: #fff;
            margin: 0 0 6px; letter-spacing: -0.01em;
        }
        .profile-settings__subtitle {
            font-size: 13.5px; color: rgba(255, 255, 255, 0.65);
            margin: 0; line-height: 1.5;
        }

        .profile-settings__alert {
            display: flex; align-items: center; gap: 12px;
            padding: 14px 20px; border-radius: 12px;
            font-size: 13px; margin-bottom: 24px; font-weight: 500;
        }
        .profile-settings__alert--error {
            background: #FEF2F2; color: #991B1B; border: 1px solid #FECACA;
        }
        .profile-settings__alert i { font-size: 16px; flex-shrink: 0; }

        .profile-settings__form { display: flex; flex-direction: column; gap: 0; }
        .profile-settings__section {
            background: #fff; border: 1px solid #E5E7EB;
            border-radius: 16px; padding: 26px 28px 28px;
            margin-bottom: 20px;
            transition: box-shadow 0.2s, border-color 0.2s;
        }
        .profile-settings__section:hover {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border-color: #D1D5DB;
        }
        .profile-settings__section-header {
            display: flex; align-items: center; gap: 14px;
            margin-bottom: 22px; padding-bottom: 16px;
            border-bottom: 1px solid #F3F4F6;
        }
        .profile-settings__section-icon {
            width: 40px; height: 40px; border-radius: 11px;
            background: var(--green-light); border: 1px solid var(--green-border);
            display: flex; align-items: center; justify-content: center;
            font-size: 16px; color: var(--green); flex-shrink: 0;
        }
        .profile-settings__section-label {
            display: block; font-size: 13px; font-weight: 800;
            text-transform: uppercase; letter-spacing: 0.06em;
            color: #0F0F0F; line-height: 1.3;
        }
        .profile-settings__section-desc {
            display: block; font-size: 12px; color: #9CA3AF;
            font-weight: 400; margin-top: 2px;
        }

        .profile-settings__grid { display: grid; gap: 18px; }
        .profile-settings__grid--3-1-1 { grid-template-columns: 2fr 1fr 1fr; }
        .profile-settings__grid--4 { grid-template-columns: repeat(4, 1fr); }
        .profile-settings__grid--2-1-1-1 { grid-template-columns: 2fr 1fr 1fr 1fr; }
        .profile-settings__grid--2 { grid-template-columns: 1fr 1fr; }
        .profile-settings__grid--3 { grid-template-columns: 1fr 1fr 1fr; }
        .profile-settings__grid--2-1-1 { grid-template-columns: 2fr 1fr 1fr; }
        .profile-settings__grid + .profile-settings__grid { margin-top: 18px; }

        .profile-settings__field { display: flex; flex-direction: column; min-width: 0; }
        .profile-settings__label {
            display: flex; align-items: center; gap: 7px;
            font-size: 11px; font-weight: 700; color: #374151;
            margin-bottom: 7px; text-transform: uppercase; letter-spacing: 0.04em;
        }
        .profile-settings__label-icon { color: var(--green); font-size: 12px; width: 14px; text-align: center; }
        .profile-settings__label .required { color: #EF4444; font-weight: 800; margin-left: 2px; }

        .profile-settings__input-wrap { position: relative; display: flex; align-items: center; }
        .profile-settings__input-icon {
            position: absolute; left: 14px; font-size: 13px; color: #9CA3AF;
            pointer-events: none; transition: color 0.15s; z-index: 1;
        }
        .profile-settings__input-icon--textarea { top: 14px; }
        .profile-settings__input-wrap:focus-within .profile-settings__input-icon { color: var(--green); }

        .profile-settings__input,
        .profile-settings__textarea {
            width: 100%; padding: 12px 14px;
            border: 1.5px solid #E5E7EB; border-radius: 10px;
            font-size: 13.5px; font-family: inherit; outline: none;
            background: #fff; color: #111827;
            transition: border-color 0.15s, box-shadow 0.15s, background 0.15s;
            box-sizing: border-box;
        }
        .profile-settings__input--icon,
        .profile-settings__textarea--icon { padding-left: 40px; }
        .profile-settings__input:focus,
        .profile-settings__textarea:focus {
            border-color: var(--green);
            box-shadow: 0 0 0 4px rgba(91, 158, 62, 0.1);
            background: #FAFFFE;
        }
        .profile-settings__input::placeholder,
        .profile-settings__textarea::placeholder { color: #9CA3AF; font-weight: 400; }
        .profile-settings__textarea { resize: vertical; min-height: 80px; }

        /* ========== CUSTOM SELECT ========== */
        .profile-settings__custom-select {
            position: relative;
            width: 100%;
            font-family: inherit;
        }
        .profile-settings__custom-trigger {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 14px;
            border: 1.5px solid #E5E7EB;
            border-radius: 10px;
            background: #fff;
            cursor: pointer;
            height: 46px;
            box-sizing: border-box;
            transition: border-color 0.15s, box-shadow 0.15s, background 0.15s;
            user-select: none;
        }
        .profile-settings__custom-trigger:hover { border-color: #D1D5DB; }
        .profile-settings__custom-select.is-open .profile-settings__custom-trigger {
            border-color: var(--green);
            box-shadow: 0 0 0 4px rgba(91, 158, 62, 0.1);
            background: #FAFFFE;
        }
        .profile-settings__custom-trigger-icon {
            font-size: 13px;
            color: #9CA3AF;
            flex-shrink: 0;
            transition: color 0.15s;
        }
        .profile-settings__custom-select.is-open .profile-settings__custom-trigger-icon,
        .profile-settings__custom-select.has-value .profile-settings__custom-trigger-icon {
            color: var(--green);
        }
        .profile-settings__custom-value {
            flex: 1;
            font-size: 13.5px;
            font-weight: 500;
            color: #111827;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .profile-settings__custom-value.is-placeholder {
            color: #9CA3AF;
            font-weight: 400;
        }
        .profile-settings__custom-caret {
            font-size: 11px;
            color: #9CA3AF;
            flex-shrink: 0;
            transition: color 0.15s, transform 0.2s;
        }
        .profile-settings__custom-select.is-open .profile-settings__custom-caret {
            color: var(--green);
            transform: rotate(180deg);
        }

        .profile-settings__custom-menu {
            position: absolute;
            top: calc(100% + 6px);
            left: 0;
            right: 0;
            background: #fff;
            border: 1.5px solid #E5E7EB;
            border-radius: 12px;
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.12), 0 4px 12px rgba(0, 0, 0, 0.06);
            padding: 6px;
            z-index: 100;
            max-height: 260px;
            overflow-y: auto;
            display: none;
            animation: profileDropdownIn 0.15s ease-out;
        }
        .profile-settings__custom-select.is-open .profile-settings__custom-menu {
            display: block;
        }
        @keyframes profileDropdownIn {
            from { opacity: 0; transform: translateY(-4px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .profile-settings__custom-option {
            padding: 10px 12px;
            font-size: 13.5px;
            font-weight: 500;
            color: #374151;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.1s, color 0.1s;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .profile-settings__custom-option:hover {
            background: var(--green-light);
            color: var(--green-dark);
        }
        .profile-settings__custom-option.is-selected {
            background: var(--green);
            color: #fff;
            font-weight: 700;
        }
        .profile-settings__custom-option.is-selected:hover {
            background: var(--green-dark);
            color: #fff;
        }

        /* Custom scrollbar for dropdown */
        .profile-settings__custom-menu::-webkit-scrollbar { width: 8px; }
        .profile-settings__custom-menu::-webkit-scrollbar-track { background: transparent; }
        .profile-settings__custom-menu::-webkit-scrollbar-thumb {
            background: #D1D5DB; border-radius: 4px;
        }
        .profile-settings__custom-menu::-webkit-scrollbar-thumb:hover { background: #9CA3AF; }

        /* ========== end custom select ========== */

        .profile-settings__hint {
            display: flex; align-items: center; gap: 5px;
            font-size: 11px; color: #9CA3AF; margin: 6px 0 0; line-height: 1.4;
        }
        .profile-settings__hint i { font-size: 10px; color: #D1D5DB; }

        .profile-settings__file-wrapper { display: flex; flex-direction: column; gap: 10px; }
        .profile-settings__file-input {
            width: 100%; padding: 10px 14px;
            border: 1.5px dashed #D1D5DB; border-radius: 10px;
            font-size: 12.5px; font-family: inherit; outline: none;
            background: #FAFAFA; color: #374151; cursor: pointer;
            transition: border-color 0.15s, background 0.15s;
            box-sizing: border-box;
        }
        .profile-settings__file-input:hover { border-color: var(--green); background: var(--green-light); }
        .profile-settings__file-input--small { padding: 8px 12px; font-size: 11.5px; }
        .profile-settings__file-input::file-selector-button {
            font-family: inherit; font-size: 12px; font-weight: 600;
            color: #fff; background: var(--green); border: none;
            border-radius: 7px; padding: 6px 14px; margin-right: 10px;
            cursor: pointer; transition: background 0.15s;
        }
        .profile-settings__file-input::file-selector-button:hover { background: var(--green-dark); }
        .profile-settings__file-preview {
            display: flex; align-items: center; justify-content: center;
            padding: 12px; background: #F9FAFB;
            border: 1px solid #F3F4F6; border-radius: 10px;
        }
        .profile-settings__signature-preview {
            max-height: 50px; background: #fff; padding: 8px;
            border: 1px solid #E5E7EB; border-radius: 8px;
        }

        .profile-settings__branding-card {
            padding: 18px; background: #F9FAFB; border-radius: 14px;
            border: 1px solid #F3F4F6; text-align: center;
            display: flex; flex-direction: column; align-items: center;
            gap: 12px; flex: 1; min-height: 180px; justify-content: center;
        }
        .profile-settings__branding-card--colours {
            gap: 14px; text-align: left; align-items: stretch; justify-content: center;
        }
        .profile-settings__branding-preview {
            display: flex; align-items: center; justify-content: center; min-height: 60px;
        }
        .profile-settings__logo-preview { max-height: 70px; max-width: 100%; object-fit: contain; }
        .profile-settings__logo-placeholder {
            width: 64px; height: 64px; border-radius: 50%;
            background: #0F0F0F; color: #7AC142;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 28px; font-weight: 800;
        }
        .profile-settings__favicon-preview { height: 36px; width: auto; }
        .profile-settings__favicon-placeholder { font-size: 11px; color: #9CA3AF; font-weight: 500; }
        .profile-settings__colour-row { display: flex; align-items: center; gap: 12px; }
        .profile-settings__colour-label {
            display: flex; align-items: center; gap: 6px;
            font-size: 11.5px; font-weight: 600; color: #6B7280; min-width: 72px;
        }
        .profile-settings__colour-label i { font-size: 9px; color: #9CA3AF; }
        .profile-settings__colour-input {
            flex: 1; height: 38px; border: 1.5px solid #E5E7EB;
            border-radius: 9px; cursor: pointer; background: #fff;
            padding: 3px; transition: border-color 0.15s;
        }
        .profile-settings__colour-input:hover { border-color: var(--green); }

        .profile-settings__toggle-label {
            display: flex; align-items: center; gap: 10px;
            padding: 12px 16px; border: 1.5px solid #E5E7EB;
            border-radius: 10px; background: #fff; cursor: pointer;
            font-size: 13px; font-weight: 500; color: #374151;
            transition: border-color 0.15s, background 0.15s; min-height: 46px;
        }
        .profile-settings__toggle-label:hover { border-color: var(--green); background: var(--green-light); }
        .profile-settings__toggle-input {
            width: 18px; height: 18px; accent-color: var(--green); cursor: pointer;
        }
        .profile-settings__toggle-text { display: flex; align-items: center; gap: 7px; }
        .profile-settings__toggle-text i { color: var(--green); font-size: 13px; }

        .profile-settings__actions {
            display: flex; justify-content: flex-end; gap: 12px;
            margin-top: 8px; padding-top: 24px; border-top: 1px solid #E5E7EB;
        }
        .profile-settings__btn {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 12px 26px; border-radius: 10px;
            font-size: 13px; font-weight: 700; font-family: inherit;
            text-decoration: none; cursor: pointer;
            transition: all 0.18s; border: none; line-height: 1;
        }
        .profile-settings__btn--secondary {
            background: #fff; color: #374151; border: 1.5px solid #E5E7EB;
        }
        .profile-settings__btn--secondary:hover { background: #F9FAFB; border-color: #D1D5DB; }
        .profile-settings__btn--primary {
            background: var(--green); color: #fff; border: 1.5px solid var(--green);
            box-shadow: 0 2px 8px rgba(91, 158, 62, 0.25);
        }
        .profile-settings__btn--primary:hover {
            background: var(--green-dark); border-color: var(--green-dark);
            box-shadow: 0 4px 14px rgba(91, 158, 62, 0.35);
            transform: translateY(-1px);
        }
        .profile-settings__btn i { font-size: 12px; }

        @media (max-width: 1024px) {
            .profile-settings__grid--4 { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 900px) {
            .profile-settings__grid--3-1-1,
            .profile-settings__grid--2-1-1-1,
            .profile-settings__grid--3,
            .profile-settings__grid--2-1-1 { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 640px) {
            .profile-settings__header { flex-direction: column; text-align: center; padding: 22px 20px; }
            .profile-settings__grid--3-1-1,
            .profile-settings__grid--4,
            .profile-settings__grid--2-1-1-1,
            .profile-settings__grid--2,
            .profile-settings__grid--3,
            .profile-settings__grid--2-1-1 { grid-template-columns: 1fr; }
            .profile-settings__section { padding: 20px 18px 22px; }
            .profile-settings__actions { flex-direction: column-reverse; }
            .profile-settings__btn { justify-content: center; width: 100%; }
            .profile-settings__title { font-size: 19px; }
        }
    </style>

    {{-- ============ CUSTOM SELECT SCRIPT ============ --}}
    <script>
    (function () {
        'use strict';

        const selects = document.querySelectorAll('.profile-settings__custom-select');

        selects.forEach(function (select) {
            const trigger = select.querySelector('.profile-settings__custom-trigger');
            const valueEl = select.querySelector('.profile-settings__custom-value');
            const options = select.querySelectorAll('.profile-settings__custom-option');
            const hiddenInput = select.querySelector('input[type="hidden"]');
            const placeholder = valueEl.dataset.placeholder || 'Select';

            // Initialise from existing value
            const initialValue = select.dataset.value || '';
            let hasInitial = false;
            options.forEach(function (opt) {
                if (opt.dataset.value === initialValue && initialValue !== '') {
                    opt.classList.add('is-selected');
                    valueEl.textContent = opt.textContent.trim();
                    valueEl.classList.remove('is-placeholder');
                    hasInitial = true;
                }
            });
            if (!hasInitial) {
                valueEl.textContent = placeholder;
                valueEl.classList.add('is-placeholder');
            } else {
                select.classList.add('has-value');
            }

            // Open / close dropdown
            trigger.addEventListener('click', function (e) {
                e.stopPropagation();
                // Close all other dropdowns
                selects.forEach(function (other) {
                    if (other !== select) other.classList.remove('is-open');
                });
                select.classList.toggle('is-open');
            });

            // Option selection
            options.forEach(function (opt) {
                opt.addEventListener('click', function (e) {
                    e.stopPropagation();

                    options.forEach(function (o) { o.classList.remove('is-selected'); });
                    opt.classList.add('is-selected');

                    const val = opt.dataset.value;
                    const label = opt.textContent.trim();

                    valueEl.textContent = label;
                    valueEl.classList.remove('is-placeholder');
                    hiddenInput.value = val;

                    if (val === '') {
                        valueEl.classList.add('is-placeholder');
                        select.classList.remove('has-value');
                    } else {
                        select.classList.add('has-value');
                    }

                    select.classList.remove('is-open');
                });
            });
        });

        // Close on outside click
        document.addEventListener('click', function () {
            selects.forEach(function (s) { s.classList.remove('is-open'); });
        });

        // Close on Escape
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                selects.forEach(function (s) { s.classList.remove('is-open'); });
            }
        });
    })();
    </script>

@endsection