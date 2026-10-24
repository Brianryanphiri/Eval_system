@extends('settings.partials._layout')

@section('settings-content')

    <a href="{{ route('settings.overview') }}" class="settings-back">
        <i class="fa-solid fa-arrow-left"></i> Back to Settings
    </a>

    <div class="settings-panel__title">Guardian Portal</div>
    <div class="settings-panel__sub">
        Control what parents and guardians can see and do when they log in.
    </div>

    @if ($errors->any())
        <div style="background:#FEF2F2; color:#991B1B; border:1px solid #FECACA;
                            padding:12px 16px; border-radius:9px; font-size:13px; margin-bottom:18px;">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('settings.guardian-portal.update') }}">
        @csrf

        @php
            $groups = [
                'Access' => [
                    'guardian.enabled' => [
                        'Enable Guardian Portal',
                        'Master switch. When off, guardians cannot log in even if they have an account.',
                        true,
                    ],
                    'guardian.multiple_children' => [
                        'Multiple children',
                        'Allow guardians to link to more than one student and switch between them.',
                        false,
                    ],
                ],
                'What Guardians Can See' => [
                    'guardian.show_grades' => [
                        'Assessment grades',
                        'Show their child\'s assessment marks as soon as teachers save them.',
                        true,
                    ],
                    'guardian.show_attendance' => [
                        'Attendance record',
                        'Show present / absent / late counts and attendance percentage.',
                        true,
                    ],
                    'guardian.show_interventions' => [
                        'Active interventions',
                        'Show any intervention plans that staff have opened for their child.',
                        true,
                    ],
                    'guardian.show_report_cards' => [
                        'Download report cards',
                        'Give guardians the ability to download their child\'s report cards.',
                        true,
                    ],
                ],
                'What Guardians Can Do' => [
                    'guardian.can_message_teachers' => [
                        'Message teachers',
                        'Allow guardians to send direct messages to their child\'s teachers.',
                        false,
                    ],
                    'guardian.can_request_meetings' => [
                        'Request meetings',
                        'Allow guardians to request parent-teacher meetings.',
                        true,
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
                    @foreach ($toggles as $key => [$title, $desc, $isImportant])
                        @php $value = $settings[$key] ?? false; @endphp
                        <label style="display:flex; align-items:flex-start; gap:14px; padding: 12px 14px;
                                                  border-radius: 10px; cursor: pointer; transition: background 0.12s;
                                                  {{ $isImportant ? 'background: var(--pts-green-light);' : '' }}"
                            onmouseover="this.style.background='{{ $isImportant ? '#E0F0D0' : 'var(--pts-bg)' }}'"
                            onmouseout="this.style.background='{{ $isImportant ? 'var(--pts-green-light)' : 'transparent' }}'">
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
                <i class="fa-solid fa-check"></i> Save Settings
            </button>
        </div>
    </form>

@endsection