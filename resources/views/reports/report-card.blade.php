@extends('layouts.app')

@section('title', 'Report Card')
@section('page-title', 'Report Card')

@section('styles')
    <style>
        /* ---------- Toolbar ---------- */
        .report-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .report-toolbar__back {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--pts-grey);
            text-decoration: none;
            font-weight: 600;
        }

        .report-toolbar__back:hover {
            color: var(--pts-green-dark);
        }

        .report-toolbar__actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-secondary {
            padding: 10px 18px;
            background: #fff;
            border: 1.5px solid var(--pts-grey-light);
            border-radius: 9px;
            font-size: 13px;
            font-weight: 700;
            color: #374151;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            font-family: inherit;
        }

        .btn-secondary:hover {
            border-color: var(--pts-green);
            color: var(--pts-green-dark);
        }

        .btn-primary {
            padding: 10px 20px;
            background: var(--pts-black);
            color: #fff;
            border: none;
            border-radius: 9px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-family: inherit;
        }

        .btn-primary:hover {
            background: var(--pts-green-dark);
        }

        /* ---------- Card shell ---------- */
        .card-shell {
            max-width: 800px;
            margin: 0 auto;
        }

        /* ---------- Card ---------- */
        .card {
            background: #fff;
            border: 2.5px solid
                {{ $primary }}
            ;
            padding: 4px;
            box-shadow: 0 4px 24px rgba(15, 15, 15, 0.06);
        }

        .card-inner {
            border: 1px solid
                {{ $primary }}
            ;
            padding: 16px 18px 14px;
        }

        .rule-accent {
            height: 3px;
            background:
                {{ $accent }}
            ;
            margin: 0 0 14px;
        }

        /* ---------- Header ---------- */
        .card-hdr {
            display: table;
            width: 100%;
            padding-bottom: 12px;
            border-bottom: 2px solid
                {{ $primary }}
            ;
            margin-bottom: 14px;
        }

        .card-hdr-l {
            display: table-cell;
            width: 58px;
            vertical-align: middle;
        }

        .card-hdr-c {
            display: table-cell;
            vertical-align: middle;
            padding-left: 12px;
        }

        .card-hdr-r {
            display: table-cell;
            width: 130px;
            vertical-align: middle;
            text-align: right;
        }

        .card-logo {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            border: 2px solid
                {{ $primary }}
            ;
            background: #fff;
            text-align: center;
            line-height: 48px;
            overflow: hidden;
        }

        .card-logo img {
            width: 48px;
            height: 48px;
            object-fit: contain;
        }

        .card-logo-fallback {
            color:
                {{ $accent }}
            ;
            font-size: 20px;
            font-weight: 800;
        }

        .card-school-name {
            font-size: 17px;
            font-weight: 800;
            color:
                {{ $primary }}
            ;
            text-transform: uppercase;
            letter-spacing: 0.02em;
            line-height: 1.1;
        }

        .card-school-motto {
            font-size: 9.5px;
            font-style: italic;
            color:
                {{ $accent }}
            ;
            margin-top: 2px;
        }

        .card-school-meta {
            font-size: 9px;
            color: #6B7280;
            margin-top: 3px;
        }

        .card-year-badge strong {
            display: block;
            font-size: 12px;
            color:
                {{ $primary }}
            ;
            font-weight: 800;
        }

        .card-year-badge span {
            font-size: 9.5px;
            color: #6B7280;
        }

        /* ---------- Title ---------- */
        .card-title {
            text-align: center;
            margin-bottom: 14px;
        }

        .card-title-main {
            font-size: 14px;
            font-weight: 800;
            letter-spacing: 0.22em;
            text-transform: uppercase;
            color:
                {{ $primary }}
            ;
        }

        .card-title-sub {
            font-size: 9px;
            letter-spacing: 0.28em;
            text-transform: uppercase;
            color:
                {{ $accent }}
            ;
            margin-top: 3px;
            font-weight: 700;
        }

        .card-title-rule {
            width: 90px;
            height: 2px;
            background:
                {{ $accent }}
            ;
            margin: 8px auto 0;
        }

        /* ---------- Info strip ---------- */
        .card-info {
            display: table;
            width: 100%;
            table-layout: fixed;
            margin-bottom: 14px;
        }

        .card-info-row {
            display: table-row;
        }

        .card-info-cell {
            display: table-cell;
            padding: 4px 10px 4px 0;
            vertical-align: bottom;
        }

        .card-info-label {
            font-size: 8.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #6B7280;
            white-space: nowrap;
        }

        .card-info-value {
            display: inline-block;
            font-size: 10.5px;
            font-weight: 700;
            color: #111827;
            border-bottom: 1px dotted #9CA3AF;
            padding: 0 3px 1px;
            min-width: 90px;
        }

        /* ---------- Marks table ---------- */
        .card-marks {
            border: 1px solid
                {{ $primary }}
            ;
        }

        table.card-marks-table {
            width: 100%;
            border-collapse: collapse;
        }

        table.card-marks-table th {
            font-size: 8.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #fff;
            background:
                {{ $primary }}
            ;
            padding: 7px 6px;
            border-right: 1px solid rgba(255, 255, 255, 0.15);
            text-align: center;
        }

        table.card-marks-table th.subj,
        table.card-marks-table td.subj {
            text-align: left;
        }

        table.card-marks-table th:last-child {
            border-right: none;
        }

        table.card-marks-table td {
            font-size: 10px;
            color: #374151;
            padding: 6px 6px;
            border-right: 1px solid #E5E7EB;
            border-bottom: 1px solid #E5E7EB;
            text-align: center;
        }

        table.card-marks-table td:last-child {
            border-right: none;
        }

        table.card-marks-table tbody tr:nth-child(even) td {
            background: #FAFBFC;
        }

        .card-sn {
            font-weight: 700;
            color:
                {{ $primary }}
            ;
        }

        .card-subj-name {
            font-weight: 700;
            color: #111827;
        }

        .card-grade {
            display: inline-block;
            padding: 1px 7px;
            border-radius: 4px;
            font-weight: 800;
            font-size: 9.5px;
            background: #E9F5DD;
            color: #3F6B1F;
            min-width: 18px;
        }

        .card-grade-fail {
            background: #FEE2E2;
            color: #991B1B;
        }

        table.card-marks-table tr.grand td {
            background: #F5F6F8;
            font-weight: 800;
            color:
                {{ $primary }}
            ;
            border-top: 1.5px solid
                {{ $primary }}
            ;
            border-bottom: none;
            font-size: 10.5px;
        }

        table.card-marks-table tr.grand td.label {
            text-align: right;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            font-size: 9px;
        }

        /* ---------- Result block ---------- */
        .card-result {
            display: table;
            width: 100%;
            border: 1px solid
                {{ $primary }}
            ;
            border-top: none;
            margin-bottom: 14px;
        }

        .card-result-cell {
            display: table-cell;
            vertical-align: middle;
            padding: 10px 14px;
            border-right: 1px solid
                {{ $primary }}
            ;
        }

        .card-result-cell:last-child {
            border-right: none;
        }

        .card-result-headline {
            width: 42%;
            background:
                {{ $accent }}
            ;
            color: #fff;
            text-align: center;
        }

        .card-result-headline .pct {
            font-size: 20px;
            font-weight: 800;
            line-height: 1;
        }

        .card-result-headline .lbl {
            font-size: 8px;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            margin-top: 3px;
            font-weight: 700;
        }

        .card-result-meta .k {
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #6B7280;
            font-weight: 700;
            display: block;
            margin-bottom: 2px;
        }

        .card-result-meta .v {
            font-size: 11px;
            font-weight: 800;
            color:
                {{ $primary }}
            ;
            display: block;
        }

        /* ---------- Attendance ---------- */
        .card-attendance {
            display: table;
            width: 100%;
            border: 1px solid #E5E7EB;
            margin-bottom: 14px;
            table-layout: fixed;
        }

        .card-att-cell {
            display: table-cell;
            padding: 8px 12px;
            border-right: 1px solid #E5E7EB;
            text-align: center;
        }

        .card-att-cell:last-child {
            border-right: none;
        }

        .card-att-cell .k {
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #6B7280;
            font-weight: 700;
        }

        .card-att-cell .v {
            font-size: 13px;
            font-weight: 800;
            color:
                {{ $primary }}
            ;
            margin-top: 3px;
        }

        /* ---------- Comments ---------- */
        .card-comment {
            margin-bottom: 10px;
            border: 1px solid #E5E7EB;
            border-left: 3px solid
                {{ $accent }}
            ;
        }

        .card-comment-lbl {
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color:
                {{ $accent }}
            ;
            font-weight: 800;
            padding: 6px 12px 0;
        }

        .card-comment-txt {
            font-size: 10px;
            color: #374151;
            line-height: 1.5;
            padding: 4px 12px 8px;
        }

        .card-comment-txt.empty {
            color: #9CA3AF;
            font-style: italic;
        }

        /* ---------- Grading key ---------- */
        .card-key {
            margin-top: 6px;
            margin-bottom: 14px;
            border-top: 1px solid #E5E7EB;
            padding-top: 8px;
        }

        .card-key-title {
            font-size: 8px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color:
                {{ $primary }}
            ;
            margin-bottom: 5px;
        }

        .card-key-grid {
            display: table;
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
        }

        .card-key-row {
            display: table-row;
        }

        .card-key-item {
            display: table-cell;
            font-size: 8.5px;
            color: #374151;
            padding: 2px 8px 2px 0;
            border-bottom: 1px dotted #E5E7EB;
        }

        .card-key-item strong {
            color:
                {{ $primary }}
            ;
            font-weight: 800;
            margin-right: 3px;
        }

        /* ---------- Signatures ---------- */
        .card-sigs {
            display: table;
            width: 100%;
            table-layout: fixed;
            margin-top: 26px;
            border-spacing: 18px 0;
        }

        .card-sig-cell {
            display: table-cell;
            text-align: center;
            vertical-align: bottom;
        }

        .card-sig-line {
            border-top: 1px solid #9CA3AF;
            margin-bottom: 4px;
        }

        .card-sig-img {
            max-height: 32px;
            margin-bottom: -10px;
        }

        .card-sig-role {
            font-size: 8px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #6B7280;
        }

        .card-sig-name {
            font-size: 9.5px;
            font-weight: 800;
            color:
                {{ $primary }}
            ;
            margin-top: 1px;
        }

        .card-issue-line {
            text-align: right;
            font-size: 9px;
            color: #6B7280;
            margin-top: 14px;
        }

        .card-issue-line strong {
            color:
                {{ $primary }}
            ;
        }

        /* ---------- Card footer ---------- */
        .card-foot {
            display: table;
            width: 100%;
            margin-top: 10px;
            padding-top: 8px;
            border-top: 1px solid #E5E7EB;
            font-size: 8px;
            color: #6B7280;
        }

        .card-foot span {
            display: table-cell;
        }

        .card-foot span:last-child {
            text-align: right;
        }

        /* ---------- Email modal ---------- */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 15, 15, 0.55);
            z-index: 100;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-overlay.open {
            display: flex;
        }

        .modal-card {
            background: #fff;
            border-radius: 14px;
            padding: 26px;
            max-width: 480px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
        }

        .modal-card__title {
            font-size: 16px;
            font-weight: 800;
            color: #111827;
            margin-bottom: 6px;
        }

        .modal-card__sub {
            font-size: 12.5px;
            color: var(--pts-grey);
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }

        .form-select,
        .form-textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1.5px solid var(--pts-grey-light);
            border-radius: 9px;
            font-size: 13px;
            font-family: inherit;
            outline: none;
            background: #fff;
        }

        .form-select:focus,
        .form-textarea:focus {
            border-color: var(--pts-green);
            box-shadow: 0 0 0 3px rgba(122, 193, 66, 0.15);
        }

        .form-textarea {
            resize: vertical;
            min-height: 80px;
        }

        .modal-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 18px;
        }

        .modal-actions .btn-cancel {
            padding: 10px 16px;
            border: 1.5px solid var(--pts-grey-light);
            background: #fff;
            border-radius: 9px;
            font-size: 13px;
            font-weight: 700;
            color: #374151;
            cursor: pointer;
            font-family: inherit;
        }

        .modal-actions .btn-cancel:hover {
            border-color: var(--pts-grey);
        }

        /* ---------- Print ---------- */
        @media print {
            @page {
                size: A4 portrait;
                margin: 12mm 11mm;
            }

            .pts-sidebar,
            .pts-topbar,
            .pts-footer,
            .pts-toasts,
            .pts-confirm,
            .pts-sidebar__overlay,
            .report-toolbar,
            .modal-overlay {
                display: none !important;
            }

            .pts-main {
                margin-left: 0 !important;
                min-height: 0 !important;
                display: block !important;
            }

            .pts-content {
                padding: 0 !important;
            }

            body {
                background: #fff !important;
            }

            .card-shell {
                max-width: none !important;
                margin: 0 !important;
            }

            .card {
                box-shadow: none !important;
            }
        }

        /* ---------- Small screens ---------- */
        @media (max-width: 700px) {
            .card-inner {
                padding: 12px;
            }

            .card-hdr-r {
                display: none;
            }

            .card-info-value {
                min-width: 60px;
            }
        }
    </style>
@endsection

@section('content')

    @php
        $logoUrl = !empty($school->logo_path)
            ? \Storage::disk('public')->url($school->logo_path)
            : null;

        $signatureUrl = !empty($school->principal_signature_path)
            ? \Storage::disk('public')->url($school->principal_signature_path)
            : null;

        $hasAttendance = is_array($attendance)
            && ($attendance['attended'] !== null || $attendance['percentage'] !== null);

        $hasGradingKey = !empty($gradingKey);
    @endphp

    <div class="report-toolbar">
        <a href="{{ route('students.show', $student->id) }}" class="report-toolbar__back">
            <i class="fa-solid fa-arrow-left"></i> Back to Student
        </a>
        <div class="report-toolbar__actions">
            <button type="button" class="btn-secondary" onclick="window.print()">
                <i class="fa-solid fa-print"></i> Print
            </button>
            <a href="{{ route('reports.report-card.download', $student->id) }}" class="btn-secondary">
                <i class="fa-solid fa-file-arrow-down"></i> Download PDF
            </a>
            <button type="button" class="btn-primary" onclick="openEmailModal()">
                <i class="fa-solid fa-envelope"></i> Email
            </button>
        </div>
    </div>

    <div class="card-shell">
        <div class="card">
            <div class="card-inner">

                <div class="card-hdr">
                    <div class="card-hdr-l">
                        <div class="card-logo">
                            @if ($logoUrl)
                                <img src="{{ $logoUrl }}" alt="{{ $school->name }}">
                            @else
                                <span class="card-logo-fallback">{{ strtoupper(substr($school->name ?? 'S', 0, 1)) }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-hdr-c">
                        <div class="card-school-name">{{ $school->name ?? 'School' }}</div>
                        @if (!empty($school->motto))
                            <div class="card-school-motto">"{{ $school->motto }}"</div>
                        @endif
                        <div class="card-school-meta">
                            {{ $address }}
                            @if (!empty($school->phone)) &nbsp;&middot;&nbsp; {{ $school->phone }} @endif
                            @if (!empty($school->email)) &nbsp;&middot;&nbsp; {{ $school->email }} @endif
                        </div>
                    </div>
                    <div class="card-hdr-r card-year-badge">
                        <strong>{{ $academicYear }}</strong>
                        <span>{{ $term }}</span>
                    </div>
                </div>

                <div class="rule-accent"></div>

                <div class="card-title">
                    <div class="card-title-main">Student Academic Report</div>
                    <div class="card-title-sub">Official Report Card</div>
                    <div class="card-title-rule"></div>
                </div>

                <table class="card-info">
                    <tr class="card-info-row">
                        <td class="card-info-cell" width="46%">
                            <span class="card-info-label">Student Name:</span>
                            <span class="card-info-value">{{ $student->name }}</span>
                        </td>
                        <td class="card-info-cell" width="27%">
                            <span class="card-info-label">Student No.:</span>
                            <span class="card-info-value">{{ $student->student_number }}</span>
                        </td>
                        <td class="card-info-cell" width="27%">
                            <span class="card-info-label">Class:</span>
                            <span class="card-info-value">{{ $student->class }}</span>
                        </td>
                    </tr>
                    <tr class="card-info-row">
                        <td class="card-info-cell">
                            <span class="card-info-label">Term:</span>
                            <span class="card-info-value">{{ $term }}</span>
                        </td>
                        <td class="card-info-cell">
                            <span class="card-info-label">Academic Year:</span>
                            <span class="card-info-value">{{ $academicYear }}</span>
                        </td>
                        <td class="card-info-cell">
                            <span class="card-info-label">Position:</span>
                            <span class="card-info-value">{{ $student->position }}</span>
                        </td>
                    </tr>
                </table>

                <div class="card-marks">
                    <table class="card-marks-table">
                        <thead>
                            <tr>
                                <th width="5%">SN</th>
                                <th class="subj" width="28%">Subject</th>
                                <th width="11%">CA</th>
                                <th width="11%">Exam</th>
                                <th width="13%">Total</th>
                                <th width="11%">Pass Marks</th>
                                <th width="8%">Grade</th>
                                <th width="13%">Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($rows as $row)
                                <tr>
                                    <td class="card-sn">{{ str_pad((string) $row['no'], 2, '0', STR_PAD_LEFT) }}</td>
                                    <td class="subj"><span class="card-subj-name">{{ $row['subject'] }}</span></td>
                                    <td>{{ $row['ca'] !== null ? $row['ca'] : '—' }}</td>
                                    <td>{{ $row['exam'] !== null ? $row['exam'] : '—' }}</td>
                                    <td><strong>{{ $row['total'] !== null ? $row['total'] : '—' }}</strong></td>
                                    <td>{{ $row['pass_marks'] }}</td>
                                    <td>
                                        @if ($row['grade'] !== null)
                                            <span class="card-grade {{ $row['passed'] ? '' : 'card-grade-fail' }}">
                                                {{ $row['grade'] }}
                                            </span>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>{{ $row['remark'] ?: '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" style="padding:14px; color:#6B7280; font-style:italic;">
                                        No subject results recorded for this term.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if (!empty($rows))
                            <tr class="grand">
                                <td colspan="4" class="label">Grand Total</td>
                                <td>{{ $grandTotal }} / {{ $grandOutOf }}</td>
                                <td colspan="3"></td>
                            </tr>
                        @endif
                    </table>
                </div>

                @if (!empty($rows))
                    <div class="card-result">
                        <div class="card-result-cell card-result-headline">
                            <div class="pct">{{ $overall }}%</div>
                            <div class="lbl">Overall Average</div>
                        </div>
                        <div class="card-result-cell card-result-meta">
                            <span class="k">Achievement Level</span>
                            <span class="v">{{ $achievementLevel }}</span>
                        </div>
                        <div class="card-result-cell card-result-meta">
                            <span class="k">Promotion Status</span>
                            <span class="v">{{ $promotionStatus }}</span>
                        </div>
                    </div>
                @endif

                @if ($hasAttendance)
                    <div class="card-attendance">
                        <div class="card-att-cell">
                            <div class="k">Days Attended</div>
                            <div class="v">{{ $attendance['attended'] ?? '-' }}</div>
                        </div>
                        <div class="card-att-cell">
                            <div class="k">School Days</div>
                            <div class="v">{{ $attendance['total'] ?? '-' }}</div>
                        </div>
                        <div class="card-att-cell">
                            <div class="k">Attendance</div>
                            <div class="v">
                                {{ $attendance['percentage'] !== null ? $attendance['percentage'] . '%' : '-' }}
                            </div>
                        </div>
                    </div>
                @endif

                <div class="card-comment">
                    <div class="card-comment-lbl">Class Teacher's Comment</div>
                    <div class="card-comment-txt {{ $teacherComment ? '' : 'empty' }}">
                        {{ $teacherComment ?: 'No comment recorded for this term.' }}
                    </div>
                </div>

                <div class="card-comment">
                    <div class="card-comment-lbl">Head Teacher's Comment</div>
                    <div class="card-comment-txt {{ $headComment ? '' : 'empty' }}">
                        {{ $headComment ?: 'No comment recorded for this term.' }}
                    </div>
                </div>

                @if ($hasGradingKey)
                    <div class="card-key">
                        <div class="card-key-title">Grading Key</div>
                        <div class="card-key-grid">
                            @foreach (array_chunk($gradingKey, 3) as $chunk)
                                <div class="card-key-row">
                                    @foreach ($chunk as $item)
                                        <div class="card-key-item">
                                            <strong>{{ $item['letter'] }}</strong>
                                            {{ $item['range'] }} &middot; {{ $item['label'] }}
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="card-sigs">
                    <div class="card-sig-cell">
                        <div class="card-sig-line"></div>
                        <div class="card-sig-role">Class Teacher</div>
                    </div>
                    <div class="card-sig-cell">
                        @if ($signatureUrl)
                            <img src="{{ $signatureUrl }}" alt="Signature" class="card-sig-img">
                        @endif
                        <div class="card-sig-line"></div>
                        <div class="card-sig-role">Head Teacher</div>
                        @if (!empty($school->principal_name))
                            <div class="card-sig-name">{{ $school->principal_name }}</div>
                        @endif
                    </div>
                    <div class="card-sig-cell">
                        <div class="card-sig-line"></div>
                        <div class="card-sig-role">Parent / Guardian</div>
                    </div>
                </div>

                <div class="card-issue-line">
                    <strong>Date of Issue:</strong> {{ now()->format('d F Y') }}
                </div>

                <div class="card-foot">
                    <span>
                        {{ $school->name ?? '' }}
                        @if (!empty($school->registration_number))
                            &nbsp;&middot;&nbsp; Reg. No.: {{ $school->registration_number }}
                        @endif
                    </span>
                    <span>Generated {{ now()->format('d M Y, H:i') }}</span>
                </div>

            </div>
        </div>
    </div>

    <div id="emailModal" class="modal-overlay" onclick="if (event.target === this) closeEmailModal()">
        <div class="modal-card">
            <div class="modal-card__title">Email report card</div>
            <div class="modal-card__sub">The PDF will be attached automatically.</div>

            <form method="POST" action="{{ route('reports.report-card.email', $student->id) }}">
                @csrf

                <label class="form-label">Send to</label>
                <select name="guardian_id" class="form-select" style="margin-bottom:14px;">
                    <option value="">All guardians</option>
                    @foreach (($guardians ?? collect()) as $g)
                        <option value="{{ $g->id }}">{{ $g->name }} ({{ $g->email }})</option>
                    @endforeach
                </select>

                <label class="form-label">Optional message</label>
                <textarea name="message" class="form-textarea" rows="3"
                    placeholder="Add a short note to accompany the report card..."></textarea>

                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeEmailModal()">Cancel</button>
                    <button type="submit" class="btn-primary">
                        <i class="fa-solid fa-paper-plane"></i> Send report card
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEmailModal() {
            document.getElementById('emailModal').classList.add('open');
        }

        function closeEmailModal() {
            document.getElementById('emailModal').classList.remove('open');
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeEmailModal();
        });
    </script>

@endsection