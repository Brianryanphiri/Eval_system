@extends('settings.partials._layout')

@section('settings-content')

    <a href="{{ route('settings.overview') }}" class="settings-back">
        <i class="fa-solid fa-arrow-left"></i> Back to Settings
    </a>

    <div class="settings-panel__title">Data Retention</div>
    <div class="settings-panel__sub">
        How long student records, marks, and logs are kept before being archived or removed.
    </div>

    <form method="POST" action="{{ route('settings.retention.update') }}">
        @csrf

        @php
            $numbers = [
                'retention.assessment_results_years' => [
                    'Assessment results',
                    'Keep scored assessments per student for this many years.',
                    1, 50,
                ],
                'retention.student_marks_years' => [
                    'Individual question marks',
                    'Keep the raw mark for each question on each assessment.',
                    1, 50,
                ],
                'retention.keep_report_cards_years' => [
                    'Report cards',
                    'Historical report cards are usually kept permanently. Set 99 to keep forever.',
                    1, 99,
                ],
                'retention.inactive_students_years' => [
                    'Inactive student records',
                    'How long to keep a student after they leave or are marked inactive.',
                    1, 50,
                ],
                'retention.audit_log_months' => [
                    'Audit log entries',
                    'How many months of settings-change and login history to keep.',
                    1, 120,
                ],
            ];
        @endphp

        <div style="margin-bottom: 26px;">
            <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em;
                        color: var(--pts-green-dark); margin-bottom: 12px; padding-bottom: 8px;
                        border-bottom: 1px solid var(--pts-grey-light);">
                Retention Periods
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

        <div style="margin-bottom: 26px;">
            <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em;
                        color: var(--pts-green-dark); margin-bottom: 12px; padding-bottom: 8px;
                        border-bottom: 1px solid var(--pts-grey-light);">
                Automation
            </div>

            <label style="display:flex; align-items:flex-start; gap:14px; padding: 12px 14px;
                          border-radius: 10px; cursor: pointer;"
                   onmouseover="this.style.background='var(--pts-bg)'"
                   onmouseout="this.style.background='transparent'">
                <input type="hidden" name="retention.auto_archive_graduates" value="0">
                <input type="checkbox" name="retention.auto_archive_graduates" value="1"
                       @checked($settings['retention.auto_archive_graduates'] ?? true)
                       style="margin-top: 3px; width: 18px; height: 18px;
                              accent-color: var(--pts-green); cursor: pointer;">
                <div style="flex: 1;">
                    <div style="font-size: 13.5px; font-weight: 700; color: #111827; margin-bottom: 3px;">
                        Auto-archive graduating cohorts
                    </div>
                    <div style="font-size: 12px; color: var(--pts-grey); line-height: 1.5;">
                        When a year closes, students marked as graduated are moved to an alumni archive.
                    </div>
                </div>
            </label>
        </div>

        <div style="padding: 14px; background: #FFFBEB; border-left: 3px solid #D97706; border-radius: 8px;
                    font-size: 12.5px; color: #92400E; line-height: 1.5; margin-bottom: 26px;">
            <strong>Note:</strong> Retention rules are enforced by an automated process. Lowering a value does
            not immediately delete existing data — it schedules deletion for the next maintenance run.
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