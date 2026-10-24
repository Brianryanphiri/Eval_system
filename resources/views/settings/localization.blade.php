@extends('settings.partials._layout')

@section('settings-content')

    <a href="{{ route('settings.overview') }}" class="settings-back">
        <i class="fa-solid fa-arrow-left"></i> Back to Settings
    </a>

    <div class="settings-panel__title">Localization</div>
    <div class="settings-panel__sub">
        Language, date formats, and regional preferences for this school.
    </div>

    <form method="POST" action="{{ route('settings.localization.update') }}">
        @csrf

        @php
            $languages = [
                'en' => 'English',
                'fr' => 'Français',
                'pt' => 'Português',
                'sw' => 'Kiswahili',
                'ny' => 'Chichewa',
                'zh' => '中文',
                'ar' => 'العربية',
            ];

            $dateFormats = [
                'd M Y' => '31 Dec 2026',
                'd/m/Y' => '31/12/2026',
                'm/d/Y' => '12/31/2026',
                'Y-m-d' => '2026-12-31',
                'D, d M Y' => 'Thu, 31 Dec 2026',
            ];

            $timeFormats = [
                'H:i' => '17:30 (24-hour)',
                'h:i A' => '5:30 PM (12-hour)',
            ];
        @endphp

        <div style="margin-bottom: 26px;">
            <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em;
                            color: var(--pts-green-dark); margin-bottom: 12px; padding-bottom: 8px;
                            border-bottom: 1px solid var(--pts-grey-light);">
                Language & Region
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                <div>
                    <label style="display:block; font-size:11.5px; font-weight:700; color:#374151; margin-bottom:5px;">
                        Default Language
                    </label>
                    <select name="locale[language]"
                        style="width:100%; padding:10px 12px; border:1.5px solid var(--pts-grey-light);
                                       border-radius:9px; font-size:13.5px; font-family:inherit; outline:none; background:#fff;">
                        @foreach ($languages as $code => $label)
                            <option value="{{ $code }}" @selected(($settings['locale.language'] ?? 'en') === $code)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="display:block; font-size:11.5px; font-weight:700; color:#374151; margin-bottom:5px;">
                        Week starts on
                    </label>
                    <select name="locale[week_start]"
                        style="width:100%; padding:10px 12px; border:1.5px solid var(--pts-grey-light);
                                       border-radius:9px; font-size:13.5px; font-family:inherit; outline:none; background:#fff;">
                        <option value="monday" @selected(($settings['locale.week_start'] ?? 'monday') === 'monday')>Monday
                        </option>
                        <option value="sunday" @selected(($settings['locale.week_start'] ?? '') === 'sunday')>Sunday</option>
                    </select>
                </div>
            </div>
        </div>

        <div style="margin-bottom: 26px;">
            <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em;
                            color: var(--pts-green-dark); margin-bottom: 12px; padding-bottom: 8px;
                            border-bottom: 1px solid var(--pts-grey-light);">
                Formatting
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap: 14px;">
                <div>
                    <label style="display:block; font-size:11.5px; font-weight:700; color:#374151; margin-bottom:5px;">
                        Date Format
                    </label>
                    <select name="locale[date_format]"
                        style="width:100%; padding:10px 12px; border:1.5px solid var(--pts-grey-light);
                                       border-radius:9px; font-size:13.5px; font-family:inherit; outline:none; background:#fff;">
                        @foreach ($dateFormats as $format => $example)
                            <option value="{{ $format }}" @selected(($settings['locale.date_format'] ?? 'd M Y') === $format)>
                                {{ $example }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="display:block; font-size:11.5px; font-weight:700; color:#374151; margin-bottom:5px;">
                        Time Format
                    </label>
                    <select name="locale[time_format]"
                        style="width:100%; padding:10px 12px; border:1.5px solid var(--pts-grey-light);
                                       border-radius:9px; font-size:13.5px; font-family:inherit; outline:none; background:#fff;">
                        @foreach ($timeFormats as $format => $example)
                            <option value="{{ $format }}" @selected(($settings['locale.time_format'] ?? 'H:i') === $format)>
                                {{ $example }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="display:block; font-size:11.5px; font-weight:700; color:#374151; margin-bottom:5px;">
                        Number Format
                    </label>
                    <select name="locale[number_format]"
                        style="width:100%; padding:10px 12px; border:1.5px solid var(--pts-grey-light);
                                       border-radius:9px; font-size:13.5px; font-family:inherit; outline:none; background:#fff;">
                        <option value="1,234.56" @selected(($settings['locale.number_format'] ?? '1,234.56') === '1,234.56')>
                            1,234.56 (English)</option>
                        <option value="1.234,56" @selected(($settings['locale.number_format'] ?? '') === '1.234,56')>1.234,56
                            (European)</option>
                        <option value="1 234,56" @selected(($settings['locale.number_format'] ?? '') === '1 234,56')>1 234,56
                            (French)</option>
                    </select>
                </div>
            </div>
        </div>

        <div style="padding: 14px; background: var(--pts-bg); border-radius: 9px; font-size: 12.5px;
                        color: var(--pts-grey); line-height: 1.5; margin-bottom: 26px;">
            <strong style="color: #111827;">Note:</strong> Language changes may not affect all pages immediately.
            Interface translations are being added progressively. Date and number formats are applied immediately.
        </div>

        <div style="display:flex; justify-content: flex-end; gap: 10px; margin-top: 28px; padding-top: 20px;
                        border-top: 1px solid var(--pts-grey-light);">
            <a href="{{ route('settings.overview') }}" style="padding: 11px 20px; border: 1.5px solid var(--pts-grey-light); border-radius: 9px;
                          font-size: 13px; font-weight: 700; color: #374151; text-decoration: none; background: #fff;">
                Cancel
            </a>
            <button type="submit" style="padding: 11px 24px; background: var(--pts-black); color: #fff; border: none;
                               border-radius: 9px; font-size: 13px; font-weight: 700; cursor: pointer;">
                <i class="fa-solid fa-check"></i> Save Localization
            </button>
        </div>
    </form>

@endsection