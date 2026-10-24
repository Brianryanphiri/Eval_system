@extends('settings.partials._layout')

@section('settings-content')

    <a href="{{ route('settings.overview') }}" class="settings-back">
        <i class="fa-solid fa-arrow-left"></i> Back to Settings
    </a>

    <div class="settings-panel__title">Feature Flags</div>
    <div class="settings-panel__sub">
        Turn entire modules on or off for this school. Useful for phased rollouts or hiding features not yet in use.
    </div>

    <form method="POST" action="{{ route('settings.flags.update') }}">
        @csrf

        @php
            $groups = [
                'Core Modules' => [
                    'features.interventions' => [
                        'Interventions',
                        'Intervention tracking and the intervention button on student profiles.',
                    ],
                    'features.analytics' => [
                        'Analytics',
                        'Question-level performance analytics and topic mastery.',
                    ],
                    'features.report_cards' => [
                        'Report cards',
                        'Generate, print, and email report cards.',
                    ],
                    'features.bulk_import' => [
                        'Bulk student import',
                        'CSV import of students, guardians, and staff.',
                    ],
                ],
                'Portals' => [
                    'features.student_portal' => [
                        'Student portal',
                        'Let students log in to view their own results.',
                    ],
                    'features.guardian_portal' => [
                        'Guardian portal',
                        'Let guardians log in to view their children\'s progress.',
                    ],
                    'features.public_portal' => [
                        'Public school page',
                        'A public landing page for the school with contact info and term dates.',
                    ],
                ],
                'Advanced' => [
                    'features.sms_notifications' => [
                        'SMS notifications',
                        'Send text messages to guardians. Requires an SMS gateway.',
                    ],
                    'features.ai_insights' => [
                        'AI insights',
                        'Auto-generated narrative insights for teachers and admins.',
                    ],
                    'features.api_access' => [
                        'API access',
                        'Allow external integrations to pull school data via API keys.',
                    ],
                ],
            ];
        @endphp

        @foreach ($groups as $groupLabel => $toggles)
            <div style="margin-bottom: 26px;">
                <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em;
                                    color: var(--pts-green-dark); margin-bottom: 12px; padding-bottom: 8px;
                                    border-bottom: 1px solid var(--pts-grey-light);">
                    {{ $groupLabel }}
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
        @endforeach

        <div style="display:flex; justify-content: flex-end; gap: 10px; margin-top: 28px; padding-top: 20px;
                        border-top: 1px solid var(--pts-grey-light);">
            <a href="{{ route('settings.overview') }}" style="padding: 11px 20px; border: 1.5px solid var(--pts-grey-light); border-radius: 9px;
                          font-size: 13px; font-weight: 700; color: #374151; text-decoration: none; background: #fff;">
                Cancel
            </a>
            <button type="submit" style="padding: 11px 24px; background: var(--pts-black); color: #fff; border: none;
                               border-radius: 9px; font-size: 13px; font-weight: 700; cursor: pointer;">
                <i class="fa-solid fa-check"></i> Save Feature Flags
            </button>
        </div>
    </form>

@endsection