@extends('layouts.app')

@section('title', 'Take Attendance')
@section('page-title', 'Take Attendance')

@section('styles')
    <style>
        /* ---------- Controls ---------- */
        .ta-controls {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 18px;
            flex-wrap: wrap;
        }

        .ta-select,
        .ta-input {
            padding: 9px 14px;
            border: 1.5px solid var(--pts-grey-light);
            border-radius: 9px;
            font-size: 13px;
            color: #374151;
            background: #fff;
            outline: none;
            font-family: inherit;
        }

        .ta-select {
            cursor: pointer;
        }

        .ta-select:focus,
        .ta-input:focus {
            border-color: var(--pts-green);
        }

        .ta-spacer {
            flex: 1;
        }

        .ta-btn {
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

        .ta-btn:hover {
            border-color: var(--pts-green);
            color: var(--pts-green-dark);
        }

        .ta-btn.primary {
            background: var(--pts-green);
            color: #fff;
            border-color: var(--pts-green);
        }

        .ta-btn.primary:hover {
            background: var(--pts-green-dark);
            border-color: var(--pts-green-dark);
        }

        /* ---------- Summary strip ---------- */
        .ta-summary {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 12px;
            margin-bottom: 18px;
        }

        .ta-summary__card {
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 12px;
            padding: 16px 18px;
        }

        .ta-summary__label {
            font-size: 10.5px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--pts-grey);
            font-weight: 700;
            margin-bottom: 6px;
        }

        .ta-summary__value {
            font-size: 22px;
            font-weight: 800;
            color: #111827;
            line-height: 1;
        }

        .ta-summary__value.green {
            color: #16A34A;
        }

        .ta-summary__value.red {
            color: #DC2626;
        }

        .ta-summary__value.amber {
            color: #D97706;
        }

        .ta-summary__value.blue {
            color: #2563EB;
        }

        /* ---------- Bulk actions ---------- */
        .ta-bulk {
            display: flex;
            gap: 8px;
            align-items: center;
            margin-bottom: 12px;
        }

        .ta-bulk__label {
            font-size: 12.5px;
            color: var(--pts-grey);
            margin-right: 4px;
        }

        /* ---------- Student table ---------- */
        .ta-table-wrap {
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 14px;
            overflow: hidden;
        }

        .ta-table {
            width: 100%;
            border-collapse: collapse;
        }

        .ta-table thead th {
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

        .ta-table thead th.center {
            text-align: center;
        }

        .ta-table tbody td {
            padding: 12px 20px;
            font-size: 13.5px;
            color: #374151;
            border-bottom: 1px solid var(--pts-grey-light);
            vertical-align: middle;
        }

        .ta-table tbody tr:last-child td {
            border-bottom: none;
        }

        .ta-table tbody tr:hover {
            background: #FAFBFC;
        }

        .ta-student {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .ta-avatar {
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

        .ta-name {
            font-size: 13.5px;
            font-weight: 600;
            color: #111827;
        }

        .ta-meta {
            font-size: 11.5px;
            color: var(--pts-grey);
            margin-top: 1px;
        }

        /* ---------- Status buttons ---------- */
        .ta-status-group {
            display: inline-flex;
            border: 1px solid var(--pts-grey-light);
            border-radius: 8px;
            overflow: hidden;
            background: #fff;
        }

        .ta-status-btn {
            padding: 7px 12px;
            font-size: 11.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            background: #fff;
            color: var(--pts-grey);
            cursor: pointer;
            border: none;
            border-right: 1px solid var(--pts-grey-light);
            font-family: inherit;
            transition: all 0.1s;
        }

        .ta-status-btn:last-child {
            border-right: none;
        }

        .ta-status-btn:hover {
            background: var(--pts-bg);
        }

        .ta-status-btn.active.present {
            background: #16A34A;
            color: #fff;
        }

        .ta-status-btn.active.absent {
            background: #DC2626;
            color: #fff;
        }

        .ta-status-btn.active.late {
            background: #D97706;
            color: #fff;
        }

        .ta-status-btn.active.excused {
            background: #2563EB;
            color: #fff;
        }

        .ta-status-btn.active.sick {
            background: #D97706;
            color: #fff;
        }

        /* ---------- Reason input ---------- */
        .ta-reason {
            width: 180px;
            padding: 7px 10px;
            border: 1.5px solid var(--pts-grey-light);
            border-radius: 8px;
            font-size: 12.5px;
            color: #374151;
            background: #fff;
            outline: none;
            font-family: inherit;
        }

        .ta-reason:focus {
            border-color: var(--pts-green);
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

        /* Sticky save bar */
        .ta-save-bar {
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

        .ta-save-bar__info {
            font-size: 13px;
            color: #D1D5DB;
        }

        .ta-save-bar__info strong {
            color: #fff;
            font-weight: 700;
        }

        .ta-save-bar__btn {
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

        .ta-save-bar__btn:hover {
            background: var(--pts-green-dark);
        }

        @media (max-width: 900px) {
            .ta-summary {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
@endsection

@section('content')

    {{-- Filters --}}
    <form method="GET" action="{{ route('attendance.index') }}" class="ta-controls" id="taFilterForm">

        {{-- Term --}}
        @if ($allTerms->count() > 0)
            <select name="term" class="ta-select" onchange="document.getElementById('taFilterForm').submit()">
                @foreach ($allTerms as $t)
                    <option value="{{ $t->id }}" @selected($currentTermId == $t->id)>
                        {{ $t->name }} · {{ $t->academicYear?->label ?? '' }}
                    </option>
                @endforeach
            </select>
        @endif

        {{-- Class --}}
        <select name="class_id" class="ta-select" onchange="document.getElementById('taFilterForm').submit()">
            <option value="">— Select a class —</option>
            @foreach ($classes as $class)
                <option value="{{ $class->id }}" @selected($selectedClassId == $class->id)>{{ $class->name }}</option>
            @endforeach
        </select>

        {{-- Date --}}
        <input type="date" name="date" class="ta-input" value="{{ $date }}"
            onchange="document.getElementById('taFilterForm').submit()">

        {{-- Period --}}
        <select name="period" class="ta-select" onchange="document.getElementById('taFilterForm').submit()">
            <option value="full-day" @selected($period === 'full-day')>Full Day</option>
            <option value="morning" @selected($period === 'morning')>Morning</option>
            <option value="afternoon" @selected($period === 'afternoon')>Afternoon</option>
        </select>

        <div class="ta-spacer"></div>

        <a href="{{ route('attendance.summary', ['term' => $currentTermId, 'class_id' => $selectedClassId ?: 'all']) }}"
            class="ta-btn">
            <i class="fa-solid fa-chart-simple"></i> View Summary
        </a>
    </form>

    @if (!$selectedClass || !$currentTerm)
        <div class="empty-state">
            <div class="empty-state__icon"><i class="fa-solid fa-clipboard-check"></i></div>
            <div class="empty-state__title">Select a class to begin</div>
            <div class="empty-state__desc">
                Pick a class and a date above. We'll show you every student in that class so you can mark attendance.
            </div>
        </div>
    @elseif ($students->isEmpty())
        <div class="empty-state">
            <div class="empty-state__icon"><i class="fa-solid fa-users-slash"></i></div>
            <div class="empty-state__title">No students in this class</div>
            <div class="empty-state__desc">
                There are no active students in <strong>{{ $selectedClass->name }}</strong> for the current academic year.
            </div>
        </div>
    @else

        @php
            // Build the map of current statuses (either from saved records or default 'present')
            $currentStatuses = [];
            $currentReasons = [];
            foreach ($students as $student) {
                $record = $existingRecords->get($student->id);
                $currentStatuses[$student->id] = $record?->status ?? 'present';
                $currentReasons[$student->id] = $record?->reason ?? '';
            }

            $counts = [
                'present' => 0,
                'absent' => 0,
                'late' => 0,
                'excused' => 0,
                'sick' => 0,
            ];
            foreach ($currentStatuses as $s) {
                if (isset($counts[$s]))
                    $counts[$s]++;
            }
        @endphp

        {{-- Summary strip --}}
        <div class="ta-summary">
            <div class="ta-summary__card">
                <div class="ta-summary__label">Total Students</div>
                <div class="ta-summary__value">{{ $students->count() }}</div>
            </div>
            <div class="ta-summary__card">
                <div class="ta-summary__label">Present</div>
                <div class="ta-summary__value green" data-count="present">{{ $counts['present'] }}</div>
            </div>
            <div class="ta-summary__card">
                <div class="ta-summary__label">Absent</div>
                <div class="ta-summary__value red" data-count="absent">{{ $counts['absent'] }}</div>
            </div>
            <div class="ta-summary__card">
                <div class="ta-summary__label">Late</div>
                <div class="ta-summary__value amber" data-count="late">{{ $counts['late'] }}</div>
            </div>
            <div class="ta-summary__card">
                <div class="ta-summary__label">Excused / Sick</div>
                <div class="ta-summary__value blue" data-count="excused_sick">
                    {{ $counts['excused'] + $counts['sick'] }}
                </div>
            </div>
        </div>

        {{-- Bulk actions --}}
        <div class="ta-bulk">
            <span class="ta-bulk__label">Mark all as:</span>
            <button type="button" class="ta-btn" onclick="setAll('present')">Present</button>
            <button type="button" class="ta-btn" onclick="setAll('absent')">Absent</button>
            <button type="button" class="ta-btn" onclick="setAll('late')">Late</button>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route('attendance.store') }}" id="taForm">
            @csrf
            <input type="hidden" name="class_id" value="{{ $selectedClass->id }}">
            <input type="hidden" name="term_id" value="{{ $currentTerm->id }}">
            <input type="hidden" name="date" value="{{ $date }}">
            <input type="hidden" name="period" value="{{ $period }}">

            <div class="ta-table-wrap">
                <table class="ta-table">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th class="center" style="width: 500px;">Status</th>
                            <th style="width: 200px;">Reason / Note</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($students as $student)
                            @php
                                $status = $currentStatuses[$student->id];
                                $reason = $currentReasons[$student->id];
                            @endphp
                            <tr data-student-row="{{ $student->id }}">
                                <td>
                                    <div class="ta-student">
                                        <div class="ta-avatar">
                                            {{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="ta-name">{{ $student->full_name }}</div>
                                            <div class="ta-meta">{{ $student->student_number }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="center">
                                    <input type="hidden" name="records[{{ $student->id }}][status]" value="{{ $status }}"
                                        data-status-input="{{ $student->id }}">

                                    <div class="ta-status-group">
                                        @foreach (['present', 'absent', 'late', 'excused', 'sick'] as $option)
                                            <button type="button"
                                                class="ta-status-btn {{ $option }} {{ $status === $option ? 'active' : '' }}"
                                                data-student="{{ $student->id }}" data-status="{{ $option }}"
                                                onclick="setStatus({{ $student->id }}, '{{ $option }}')">
                                                {{ ucfirst($option) }}
                                            </button>
                                        @endforeach
                                    </div>
                                </td>
                                <td>
                                    <input type="text" name="records[{{ $student->id }}][reason]" class="ta-reason"
                                        placeholder="Optional" value="{{ $reason }}" maxlength="255">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Sticky save bar --}}
            <div class="ta-save-bar">
                <div class="ta-save-bar__info">
                    Marking attendance for <strong>{{ $selectedClass->name }}</strong>
                    on <strong>{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</strong>
                    (<strong>{{ ucfirst($period) }}</strong>)
                </div>
                <button type="submit" class="ta-save-bar__btn">
                    <i class="fa-solid fa-floppy-disk"></i> Save Attendance
                </button>
            </div>
        </form>

        <script>
            function setStatus(studentId, status) {
                // Update the hidden input
                const input = document.querySelector('input[data-status-input="' + studentId + '"]');
                if (input) input.value = status;

                // Update the buttons
                document.querySelectorAll('.ta-status-btn[data-student="' + studentId + '"]').forEach(function (btn) {
                    btn.classList.toggle('active', btn.dataset.status === status);
                });

                // Update summary counts
                recalcCounts();
            }

            function setAll(status) {
                document.querySelectorAll('.ta-status-btn[data-status="' + status + '"]').forEach(function (btn) {
                    btn.click();
                });
            }

            function recalcCounts() {
                const counts = { present: 0, absent: 0, late: 0, excused: 0, sick: 0 };
                document.querySelectorAll('input[data-status-input]').forEach(function (input) {
                    if (counts[input.value] !== undefined) counts[input.value]++;
                });

                document.querySelectorAll('[data-count]').forEach(function (el) {
                    const key = el.dataset.count;
                    if (key === 'excused_sick') {
                        el.textContent = counts.excused + counts.sick;
                    } else if (counts[key] !== undefined) {
                        el.textContent = counts[key];
                    }
                });
            }
        </script>
    @endif

@endsection