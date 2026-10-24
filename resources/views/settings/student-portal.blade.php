@extends('settings.partials._layout')

@section('settings-content')

    <a href="{{ route('settings.overview') }}" class="settings-back">
        <i class="fa-solid fa-arrow-left"></i> Back to Settings
    </a>

    <div class="settings-panel__title">Student Portal</div>
    <div class="settings-panel__sub">
        Control what students can see and do when they log in with their own account.
    </div>

    @if ($errors->any())
        <div style="background:#FEF2F2; color:#991B1B; border:1px solid #FECACA;
                            padding:12px 16px; border-radius:9px; font-size:13px; margin-bottom:18px;">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('settings.student-portal.update') }}">
        @csrf

        @php
            $groups = [
                'Access' => [
                    'portal.students_enabled' => [
                        'Enable Student Portal',
                        'Master switch. When off, students cannot log in even if they have an account.',
                        true,
                    ],
                    'portal.allow_self_profile_edit' => [
                        'Allow profile self-edit',
                        'Students can update their own photo and contact information.',
                        false,
                    ],
                ],
                'What Students Can See' => [
                    'portal.show_grades' => [
                        'Their assessment grades',
                        'Show marks for individual assessments once they have been marked.',
                        true,
                    ],
                    'portal.show_rank' => [
                        'Their class rank',
                        'Show their position in class (e.g. 14 of 86). Off by default to reduce pressure.',
                        false,
                    ],
                    'portal.show_attendance' => [
                        'Their attendance record',
                        'Show present / absent / late counts and attendance percentage.',
                        true,
                    ],
                    'portal.show_interventions' => [
                        'Their active interventions',
                        'Show any intervention plans that staff have opened for them.',
                        false,
                    ],
                    'portal.show_report_cards' => [
                        'Download their report cards',
                        'Give students the ability to download their own termly report cards.',
                        true,
                    ],
                    'portal.show_upcoming_assessments' => [
                        'Upcoming assessments',
                        'Show a calendar of tests and exams scheduled for their class.',
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