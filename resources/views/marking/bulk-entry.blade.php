@extends('layouts.app')

@section('title', 'Bulk Marks Entry')
@section('page-title', 'Bulk Marks Entry')

@section('styles')
    <style>
        /* Controls */
        .bm-controls {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 18px;
            flex-wrap: wrap;
        }

        .bm-select {
            padding: 9px 14px;
            border: 1.5px solid var(--pts-grey-light);
            border-radius: 9px;
            font-size: 13px;
            color: #374151;
            background: #fff;
            outline: none;
            cursor: pointer;
            font-family: inherit;
            min-width: 260px;
        }

        .bm-select:focus {
            border-color: var(--pts-green);
        }

        .bm-spacer {
            flex: 1;
        }

        .bm-btn {
            padding: 9px 14px;
            background: #fff;
            border: 1.5px solid var(--pts-grey-light);
            border-radius: 9px;
            font-size: 12.5px;
            font-weight: 600;
            color: #374151;
            text-decoration: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-family: inherit;
            transition: all 0.15s;
        }

        .bm-btn:hover {
            border-color: var(--pts-green);
            color: var(--pts-green-dark);
        }

        .bm-btn.primary {
            background: var(--pts-green);
            color: #fff;
            border-color: var(--pts-green);
        }

        .bm-btn.primary:hover {
            background: var(--pts-green-dark);
            border-color: var(--pts-green-dark);
        }

        /* Assessment info card */
        .bm-info {
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 14px;
            padding: 18px 22px;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 24px;
            flex-wrap: wrap;
        }

        .bm-info__block {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .bm-info__label {
            font-size: 10.5px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--pts-grey);
            font-weight: 700;
        }

        .bm-info__value {
            font-size: 15px;
            font-weight: 700;
            color: #111827;
        }

        .bm-status {
            margin-left: auto;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 11.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .bm-status.draft {
            background: #F3F4F6;
            color: #374151;
        }

        .bm-status.submitted {
            background: #FFFBEB;
            color: #D97706;
        }

        .bm-status.locked {
            background: #E9F5DD;
            color: #5E9A31;
        }

        /* Locked warning */
        .bm-locked {
            background: #FEF2F2;
            border: 1.5px solid #FECACA;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 13px;
            color: #991B1B;
        }

        .bm-locked i {
            font-size: 18px;
            color: #DC2626;
        }

        /* Grid table */
        .bm-grid-wrap {
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 14px;
            overflow: hidden;
        }

        .bm-grid {
            width: 100%;
            border-collapse: collapse;
        }

        .bm-grid thead th {
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--pts-grey);
            font-weight: 700;
            padding: 14px 20px;
            background: var(--pts-bg);
            border-bottom: 1px solid var(--pts-grey-light);
        }

        .bm-grid thead th.center {
            text-align: center;
        }

        .bm-grid thead th.right {
            text-align: right;
        }

        .bm-grid tbody td {
            padding: 12px 20px;
            font-size: 13.5px;
            color: #374151;
            border-bottom: 1px solid var(--pts-grey-light);
            vertical-align: middle;
        }

        .bm-grid tbody tr:last-child td {
            border-bottom: none;
        }

        .bm-grid tbody tr:hover {
            background: #FAFBFC;
        }

        .bm-student {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .bm-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: var(--pts-black);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .bm-name {
            font-size: 13.5px;
            font-weight: 600;
            color: #111827;
        }

        .bm-meta {
            font-size: 11.5px;
            color: var(--pts-grey);
            margin-top: 1px;
        }

        /* Mark input */
        .bm-input-wrap {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .bm-input {
            width: 90px;
            padding: 8px 10px;
            border: 1.5px solid var(--pts-grey-light);
            border-radius: 8px;
            font-size: 14px;
            font-weight: 700;
            color: #111827;
            text-align: center;
            outline: none;
            font-family: inherit;
            transition: all 0.15s;
        }

        .bm-input:focus {
            border-color: var(--pts-green);
            box-shadow: 0 0 0 3px rgba(122, 193, 66, 0.15);
        }

        .bm-input.error {
            border-color: var(--pts-red);
            background: #FEF2F2;
        }

        .bm-max {
            font-size: 12px;
            color: var(--pts-grey);
            font-weight: 600;
        }

        /* Computed cells */
        .bm-pct {
            font-size: 13.5px;
            font-weight: 800;
            color: #111827;
        }

        .bm-grade {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 28px;
            height: 28px;
            border-radius: 7px;
            font-size: 12.5px;
            font-weight: 800;
            padding: 0 8px;
        }

        .bm-grade.a {
            background: #DCFCE7;
            color: #166534;
        }

        .bm-grade.b {
            background: #DBEAFE;
            color: #1E40AF;
        }

        .bm-grade.c {
            background: #FEF3C7;
            color: #92400E;
        }

        .bm-grade.d {
            background: #FFE4E6;
            color: #9F1239;
        }

        .bm-grade.e,
        .bm-grade.f {
            background: #FEE2E2;
            color: #991B1B;
        }

        .bm-grade.empty {
            background: var(--pts-bg);
            color: var(--pts-grey);
        }

        /* Sticky save bar */
        .bm-save-bar {
            position: sticky;
            bottom: 16px;
            margin-top: 20px;
            background: var(--pts-black);
            color: #fff;
            border-radius: 12px;
            padding: 14px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        .bm-save-bar__info {
            font-size: 13px;
            color: #D1D5DB;
        }

        .bm-save-bar__info strong {
            color: #fff;
            font-weight: 700;
        }

        .bm-save-bar__btn {
            padding: 10px 22px;
            background: var(--pts-green);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-family: inherit;
            transition: background 0.15s;
        }

        .bm-save-bar__btn:hover {
            background: var(--pts-green-dark);
        }

        .bm-save-bar__btn:disabled {
            background: var(--pts-grey);
            cursor: not-allowed;
            opacity: 0.6;
        }

        /* Empty state */
        .empty-state {
            padding: 60px 24px;
            text-align: center;
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 14px;
        }

        .empty-state__icon {
            width: 64px;
            height: 64px;
            border-radius: 16px;
            background: var(--pts-green-light);
            color: var(--pts-green-dark);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            margin-bottom: 18px;
        }

        .empty-state__title {
            font-size: 16px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 6px;
        }

        .empty-state__desc {
            font-size: 13.5px;
            color: var(--pts-grey);
            line-height: 1.5;
        }
    </style>
@endsection

@section('content')

    {{-- Controls --}}
    <div class="bm-controls">
        <form method="GET" action="{{ route('marks.entry') }}" id="bmForm">
            <select name="assessment_id" class="bm-select" onchange="document.getElementById('bmForm').submit()">
                <option value="">— Select an assessment —</option>
                @foreach ($assessments as $a)
                    <option value="{{ $a->id }}" @selected($selectedAssessmentId == $a->id)>
                        {{ $a->classroom?->name ?? '—' }} · {{ $a->subject?->name ?? '—' }} ·
                        {{ $a->name }} ({{ $a->total_marks }} marks)
                    </option>
                @endforeach
            </select>
        </form>

        <div class="bm-spacer"></div>

        @if ($selectedAssessment)
            <a href="{{ route('assessments.show', $selectedAssessment->id) }}" class="bm-btn">
                <i class="fa-solid fa-eye"></i> View Assessment
            </a>
            <a href="{{ route('assessments.index') }}" class="bm-btn">
                <i class="fa-solid fa-list"></i> All Assessments
            </a>
        @endif
    </div>

    @if (!$selectedAssessment)
        <div class="empty-state">
            <div class="empty-state__icon"><i class="fa-solid fa-file-pen"></i></div>
            <div class="empty-state__title">Select an assessment to begin</div>
            <div class="empty-state__desc">
                Choose an assessment from the dropdown above. You'll see every student in that class with a box to enter their
                mark.
            </div>
        </div>
    @elseif ($students->isEmpty())
        <div class="empty-state">
            <div class="empty-state__icon"><i class="fa-solid fa-users-slash"></i></div>
            <div class="empty-state__title">No students in this class</div>
            <div class="empty-state__desc">
                There are no active students enrolled in
                <strong>{{ $selectedAssessment->classroom?->name ?? 'this class' }}</strong>.
            </div>
        </div>
    @else

        @php
            $maxMark = (float) ($selectedAssessment->total_marks ?? 100);
            $passMark = (float) ($selectedAssessment->pass_mark ?? 50);
            $isLocked = $selectedAssessment->isLocked();
        @endphp

        {{-- Assessment info card --}}
        <div class="bm-info">
            <div class="bm-info__block">
                <div class="bm-info__label">Assessment</div>
                <div class="bm-info__value">{{ $selectedAssessment->name }}</div>
            </div>
            <div class="bm-info__block">
                <div class="bm-info__label">Class</div>
                <div class="bm-info__value">{{ $selectedAssessment->classroom?->name ?? '—' }}</div>
            </div>
            <div class="bm-info__block">
                <div class="bm-info__label">Subject</div>
                <div class="bm-info__value">{{ $selectedAssessment->subject?->name ?? '—' }}</div>
            </div>
            <div class="bm-info__block">
                <div class="bm-info__label">Max Marks</div>
                <div class="bm-info__value">{{ (int) $maxMark }}</div>
            </div>
            <div class="bm-info__block">
                <div class="bm-info__label">Pass Mark</div>
                <div class="bm-info__value">{{ (int) $passMark }}</div>
            </div>
            <div class="bm-status {{ $selectedAssessment->status }}">
                {{ ucfirst($selectedAssessment->status) }}
            </div>
        </div>

        @if ($isLocked)
            <div class="bm-locked">
                <i class="fa-solid fa-lock"></i>
                <div>
                    <strong>This assessment is locked.</strong>
                    Marks cannot be edited until the assessment is unlocked by an admin.
                </div>
            </div>
        @endif

        {{-- Form --}}
        <form method="POST" action="{{ route('marks.entry.store') }}" id="bmSaveForm">
            @csrf
            <input type="hidden" name="assessment_id" value="{{ $selectedAssessment->id }}">

            <div class="bm-grid-wrap">
                <table class="bm-grid">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th class="center" style="width: 220px;">Mark</th>
                            <th class="center" style="width: 120px;">%</th>
                            <th class="center" style="width: 100px;">Grade</th>
                            <th class="right" style="width: 140px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($students as $student)
                            @php
                                $existing = $existingMarks->get($student->id);
                                $value = $existing?->total_obtained;
                            @endphp
                            <tr data-student-row="{{ $student->id }}">
                                <td>
                                    <div class="bm-student">
                                        <div class="bm-avatar">
                                            {{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="bm-name">{{ $student->full_name }}</div>
                                            <div class="bm-meta">{{ $student->student_number }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="center">
                                    <div class="bm-input-wrap">
                                        <input type="number" name="marks[{{ $student->id }}]" class="bm-input"
                                            data-student="{{ $student->id }}" data-max="{{ $maxMark }}" data-pass="{{ $passMark }}"
                                            min="0" max="{{ $maxMark }}" step="0.5" placeholder="—"
                                            value="{{ $value !== null ? rtrim(rtrim(number_format((float) $value, 1, '.', ''), '0'), '.') : '' }}"
                                            @if ($isLocked) disabled @endif oninput="recalcRow(this)">
                                        <span class="bm-max">/ {{ (int) $maxMark }}</span>
                                    </div>
                                </td>
                                <td class="center">
                                    <span class="bm-pct" data-pct-for="{{ $student->id }}">
                                        {{ $existing ? number_format((float) $existing->percentage, 1) . '%' : '—' }}
                                    </span>
                                </td>
                                <td class="center">
                                    @php
                                        $grade = $existing?->grade_letter;
                                        $gradeLower = $grade ? strtolower($grade) : null;
                                    @endphp
                                    <span class="bm-grade {{ $gradeLower ?? 'empty' }}" data-grade-for="{{ $student->id }}">
                                        {{ $grade ?? '—' }}
                                    </span>
                                </td>
                                <td class="right">
                                    <span
                                        class="bm-status {{ $existing && $existing->passed ? 'locked' : ($existing ? 'draft' : 'draft') }}"
                                        data-status-for="{{ $student->id }}">
                                        @if ($existing)
                                            {{ $existing->passed ? 'Pass' : 'Fail' }}
                                        @else
                                            Not marked
                                        @endif
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="bm-save-bar">
                <div class="bm-save-bar__info">
                    Entering marks for <strong>{{ $selectedAssessment->name }}</strong>
                    ({{ $selectedAssessment->classroom?->name }} · {{ $selectedAssessment->subject?->name }})
                </div>
                <button type="submit" class="bm-save-bar__btn" @if ($isLocked) disabled @endif>
                    <i class="fa-solid fa-floppy-disk"></i> Save All Marks
                </button>
            </div>
        </form>

        <script>
            // Grades scales for the school (fetched server-side to keep it fast)
            const gradeBands = @json($gradeBands ?? []);

            function letterFor(pct) {
                for (const band of gradeBands) {
                    if (pct >= band.min_score && pct <= band.max_score) {
                        return band.letter;
                    }
                }
                return '—';
            }

            function recalcRow(input) {
                const studentId = input.dataset.student;
                const max = parseFloat(input.dataset.max) || 100;
                const passMark = parseFloat(input.dataset.pass) || 50;
                const raw = input.value.trim();

                const pctEl = document.querySelector('[data-pct-for="' + studentId + '"]');
                const gradeEl = document.querySelector('[data-grade-for="' + studentId + '"]');
                const statusEl = document.querySelector('[data-status-for="' + studentId + '"]');

                if (raw === '') {
                    pctEl.textContent = '—';
                    gradeEl.textContent = '—';
                    gradeEl.className = 'bm-grade empty';
                    statusEl.textContent = 'Not marked';
                    statusEl.className = 'bm-status draft';
                    input.classList.remove('error');
                    return;
                }

                const mark = parseFloat(raw);
                if (isNaN(mark) || mark < 0 || mark > max) {
                    input.classList.add('error');
                    pctEl.textContent = '—';
                    gradeEl.textContent = '—';
                    gradeEl.className = 'bm-grade empty';
                    statusEl.textContent = 'Invalid';
                    statusEl.className = 'bm-status';
                    return;
                }

                input.classList.remove('error');

                const pct = (mark / max) * 100;
                const letter = letterFor(pct);
                const passed = pct >= passMark;

                pctEl.textContent = pct.toFixed(1) + '%';
                gradeEl.textContent = letter;
                gradeEl.className = 'bm-grade ' + (letter ? letter.toLowerCase() : 'empty');

                statusEl.textContent = passed ? 'Pass' : 'Fail';
                statusEl.className = 'bm-status ' + (passed ? 'locked' : 'draft');
            }
        </script>
    @endif

@endsection