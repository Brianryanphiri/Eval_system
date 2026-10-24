<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Report Card - {{ $student->name }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 11mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10.5px;
            color: #111827;
            line-height: 1.35;
        }

        /* =========================================================
           Outer double frame in the school's own colours
           ========================================================= */
        .frame {
            border: 2.5px solid
                {{ $primary }}
            ;
            padding: 4px;
        }

        .frame-inner {
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

        /* =========================================================
           Header
           ========================================================= */
        .hdr {
            display: table;
            width: 100%;
            padding-bottom: 12px;
            border-bottom: 2px solid
                {{ $primary }}
            ;
            margin-bottom: 14px;
        }

        .hdr-l {
            display: table-cell;
            width: 58px;
            vertical-align: middle;
        }

        .hdr-c {
            display: table-cell;
            vertical-align: middle;
            padding-left: 12px;
        }

        .hdr-r {
            display: table-cell;
            width: 130px;
            vertical-align: middle;
            text-align: right;
        }

        .logo {
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

        .logo img {
            width: 48px;
            height: 48px;
            object-fit: contain;
        }

        .logo-fallback {
            color:
                {{ $accent }}
            ;
            font-size: 20px;
            font-weight: 800;
        }

        .school-name {
            font-size: 17px;
            font-weight: 800;
            color:
                {{ $primary }}
            ;
            text-transform: uppercase;
            letter-spacing: 0.02em;
            line-height: 1.1;
        }

        .school-motto {
            font-size: 9.5px;
            font-style: italic;
            color:
                {{ $accent }}
            ;
            margin-top: 2px;
        }

        .school-meta {
            font-size: 9px;
            color: #6B7280;
            margin-top: 3px;
        }

        .year-badge strong {
            display: block;
            font-size: 12px;
            color:
                {{ $primary }}
            ;
            font-weight: 800;
        }

        .year-badge span {
            font-size: 9.5px;
            color: #6B7280;
        }

        /* =========================================================
           Title
           ========================================================= */
        .title-block {
            text-align: center;
            margin-bottom: 14px;
        }

        .title-main {
            font-size: 14px;
            font-weight: 800;
            letter-spacing: 0.22em;
            text-transform: uppercase;
            color:
                {{ $primary }}
            ;
        }

        .title-sub {
            font-size: 9px;
            letter-spacing: 0.28em;
            text-transform: uppercase;
            color:
                {{ $accent }}
            ;
            margin-top: 3px;
            font-weight: 700;
        }

        .title-rule {
            width: 90px;
            height: 2px;
            background:
                {{ $accent }}
            ;
            margin: 8px auto 0;
        }

        /* =========================================================
           Fill-in info strip (underlined fields, not boxes)
           ========================================================= */
        .info-table {
            display: table;
            width: 100%;
            table-layout: fixed;
            margin-bottom: 14px;
        }

        .info-row {
            display: table-row;
        }

        .info-cell {
            display: table-cell;
            padding: 4px 10px 4px 0;
            vertical-align: bottom;
        }

        .info-label {
            font-size: 8.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #6B7280;
            white-space: nowrap;
        }

        .info-value {
            display: inline-block;
            font-size: 10.5px;
            font-weight: 700;
            color: #111827;
            border-bottom: 1px dotted #9CA3AF;
            padding: 0 3px 1px;
            min-width: 90px;
        }

        /* =========================================================
           Marks table
           ========================================================= */
        .marks-wrap {
            border: 1px solid
                {{ $primary }}
            ;
            margin-bottom: 0;
        }

        table.marks {
            width: 100%;
            border-collapse: collapse;
        }

        table.marks th {
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

        table.marks th.subj,
        table.marks td.subj {
            text-align: left;
        }

        table.marks th:last-child {
            border-right: none;
        }

        table.marks td {
            font-size: 10px;
            color: #374151;
            padding: 6px 6px;
            border-right: 1px solid #E5E7EB;
            border-bottom: 1px solid #E5E7EB;
            text-align: center;
        }

        table.marks td:last-child {
            border-right: none;
        }

        table.marks tbody tr:nth-child(even) td {
            background: #FAFBFC;
        }

        .sn {
            font-weight: 700;
            color:
                {{ $primary }}
            ;
        }

        .subj-name {
            font-weight: 700;
            color: #111827;
        }

        .grade {
            display: inline-block;
            padding: 1px 7px;
            border-radius: 4px;
            font-weight: 800;
            font-size: 9.5px;
            background: #E9F5DD;
            color: #3F6B1F;
            min-width: 18px;
        }

        .grade-fail {
            background: #FEE2E2;
            color: #991B1B;
        }

        table.marks tr.grand td {
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

        table.marks tr.grand td.label {
            text-align: right;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            font-size: 9px;
        }

        /* =========================================================
           Result block (to the right of grand total, reference-style)
           ========================================================= */
        .result-block {
            display: table;
            width: 100%;
            border: 1px solid
                {{ $primary }}
            ;
            border-top: none;
            margin-bottom: 14px;
        }

        .rb-cell {
            display: table-cell;
            vertical-align: middle;
            padding: 10px 14px;
            border-right: 1px solid
                {{ $primary }}
            ;
        }

        .rb-cell:last-child {
            border-right: none;
        }

        .rb-result {
            width: 42%;
            background:
                {{ $accent }}
            ;
            color: #fff;
            text-align: center;
        }

        .rb-result .pct {
            font-size: 20px;
            font-weight: 800;
            line-height: 1;
        }

        .rb-result .lbl {
            font-size: 8px;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            margin-top: 3px;
            font-weight: 700;
        }

        .rb-meta .k {
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #6B7280;
            font-weight: 700;
            display: block;
            margin-bottom: 2px;
        }

        .rb-meta .v {
            font-size: 11px;
            font-weight: 800;
            color:
                {{ $primary }}
            ;
            display: block;
        }

        .rb-meta+.rb-meta {
            margin-top: 0;
        }

        /* =========================================================
           Attendance
           ========================================================= */
        .attendance {
            display: table;
            width: 100%;
            border: 1px solid #E5E7EB;
            margin-bottom: 14px;
            table-layout: fixed;
        }

        .att-cell {
            display: table-cell;
            padding: 8px 12px;
            border-right: 1px solid #E5E7EB;
            text-align: center;
        }

        .att-cell:last-child {
            border-right: none;
        }

        .att-cell .k {
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #6B7280;
            font-weight: 700;
        }

        .att-cell .v {
            font-size: 13px;
            font-weight: 800;
            color:
                {{ $primary }}
            ;
            margin-top: 3px;
        }

        /* =========================================================
           Comments
           ========================================================= */
        .comment {
            margin-bottom: 10px;
            border: 1px solid #E5E7EB;
            border-left: 3px solid
                {{ $accent }}
            ;
            page-break-inside: avoid;
        }

        .comment-lbl {
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color:
                {{ $accent }}
            ;
            font-weight: 800;
            padding: 6px 12px 0;
        }

        .comment-txt {
            font-size: 10px;
            color: #374151;
            line-height: 1.5;
            padding: 4px 12px 8px;
        }

        .comment-txt.empty {
            color: #9CA3AF;
            font-style: italic;
        }

        /* =========================================================
           Grading key
           ========================================================= */
        .key-block {
            margin-top: 6px;
            margin-bottom: 14px;
            border-top: 1px solid #E5E7EB;
            padding-top: 8px;
        }

        .key-title {
            font-size: 8px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color:
                {{ $primary }}
            ;
            margin-bottom: 5px;
        }

        .key-grid {
            display: table;
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
        }

        .key-row {
            display: table-row;
        }

        .key-item {
            display: table-cell;
            font-size: 8.5px;
            color: #374151;
            padding: 2px 8px 2px 0;
            border-bottom: 1px dotted #E5E7EB;
        }

        .key-item strong {
            color:
                {{ $primary }}
            ;
            font-weight: 800;
            margin-right: 3px;
        }

        /* =========================================================
           Signatures + issue date
           ========================================================= */
        .sig-table {
            display: table;
            width: 100%;
            table-layout: fixed;
            margin-top: 26px;
            border-spacing: 18px 0;
        }

        .sig-cell {
            display: table-cell;
            text-align: center;
            vertical-align: bottom;
        }

        .sig-line {
            border-top: 1px solid #9CA3AF;
            margin-bottom: 4px;
        }

        .sig-img {
            max-height: 32px;
            margin-bottom: -10px;
        }

        .sig-role {
            font-size: 8px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #6B7280;
        }

        .sig-name {
            font-size: 9.5px;
            font-weight: 800;
            color:
                {{ $primary }}
            ;
            margin-top: 1px;
        }

        .issue-line {
            text-align: right;
            font-size: 9px;
            color: #6B7280;
            margin-top: 14px;
        }

        .issue-line strong {
            color:
                {{ $primary }}
            ;
        }

        /* =========================================================
           Footer
           ========================================================= */
        .foot {
            display: table;
            width: 100%;
            margin-top: 10px;
            padding-top: 8px;
            border-top: 1px solid #E5E7EB;
            font-size: 8px;
            color: #6B7280;
        }

        .foot span {
            display: table-cell;
        }

        .foot span:last-child {
            text-align: right;
        }
    </style>
</head>

<body>

    @php
        $hasLogo = !empty($files['logo']) && file_exists($files['logo']);
        $hasSignature = !empty($files['signature']) && file_exists($files['signature']);
        $hasAttendance = is_array($attendance) && ($attendance['attended'] !== null || $attendance['percentage'] !== null);
        $hasGradingKey = !empty($gradingKey);
    @endphp

    <div class="frame">
        <div class="frame-inner">

            {{-- ================= Header ================= --}}
            <div class="hdr">
                <div class="hdr-l">
                    <div class="logo">
                        @if ($hasLogo)
                            <img src="{{ $files['logo'] }}" alt="{{ $school->name }}">
                        @else
                            <span class="logo-fallback">{{ strtoupper(substr($school->name ?? 'S', 0, 1)) }}</span>
                        @endif
                    </div>
                </div>
                <div class="hdr-c">
                    <div class="school-name">{{ $school->name ?? 'School' }}</div>
                    @if (!empty($school->motto))
                        <div class="school-motto">"{{ $school->motto }}"</div>
                    @endif
                    <div class="school-meta">
                        {{ $address }}
                        @if (!empty($school->phone)) &nbsp;&middot;&nbsp; {{ $school->phone }} @endif
                        @if (!empty($school->email)) &nbsp;&middot;&nbsp; {{ $school->email }} @endif
                    </div>
                </div>
                <div class="hdr-r year-badge">
                    <strong>{{ $academicYear }}</strong>
                    <span>{{ $term }}</span>
                </div>
            </div>

            <div class="rule-accent"></div>

            {{-- ================= Title ================= --}}
            <div class="title-block">
                <div class="title-main">Student Academic Report</div>
                <div class="title-sub">Official Report Card</div>
                <div class="title-rule"></div>
            </div>

            {{-- ================= Info strip ================= --}}
            <table class="info-table">
                <tr class="info-row">
                    <td class="info-cell" width="46%">
                        <span class="info-label">Student Name:</span>
                        <span class="info-value">{{ $student->name }}</span>
                    </td>
                    <td class="info-cell" width="27%">
                        <span class="info-label">Student No.:</span>
                        <span class="info-value">{{ $student->student_number }}</span>
                    </td>
                    <td class="info-cell" width="27%">
                        <span class="info-label">Class:</span>
                        <span class="info-value">{{ $student->class }}</span>
                    </td>
                </tr>
                <tr class="info-row">
                    <td class="info-cell">
                        <span class="info-label">Term:</span>
                        <span class="info-value">{{ $term }}</span>
                    </td>
                    <td class="info-cell">
                        <span class="info-label">Academic Year:</span>
                        <span class="info-value">{{ $academicYear }}</span>
                    </td>
                    <td class="info-cell">
                        <span class="info-label">Position:</span>
                        <span class="info-value">{{ $student->position }}</span>
                    </td>
                </tr>
            </table>

            {{-- ================= Marks table ================= --}}
            <div class="marks-wrap">
                <table class="marks">
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
                                <td class="sn">{{ str_pad((string) $row['no'], 2, '0', STR_PAD_LEFT) }}</td>
                                <td class="subj"><span class="subj-name">{{ $row['subject'] }}</span></td>
                                <td>{{ $row['ca'] !== null ? $row['ca'] : '&mdash;' }}</td>
                                <td>{{ $row['exam'] !== null ? $row['exam'] : '&mdash;' }}</td>
                                <td><strong>{{ $row['total'] !== null ? $row['total'] : '&mdash;' }}</strong></td>
                                <td>{{ $row['pass_marks'] }}</td>
                                <td>
                                    @if ($row['grade'] !== null)
                                        <span class="grade {{ $row['passed'] ? '' : 'grade-fail' }}">
                                            {{ $row['grade'] }}
                                        </span>
                                    @else
                                        &mdash;
                                    @endif
                                </td>
                                <td>{{ $row['remark'] ?: '&mdash;' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="padding: 14px; color:#6B7280; font-style: italic;">
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

            {{-- ================= Result block ================= --}}
            @if (!empty($rows))
                <div class="result-block">
                    <div class="rb-cell rb-result">
                        <div class="pct">{{ $overall }}%</div>
                        <div class="lbl">Overall Average</div>
                    </div>
                    <div class="rb-cell rb-meta">
                        <span class="k">Achievement Level</span>
                        <span class="v">{{ $achievementLevel }}</span>
                    </div>
                    <div class="rb-cell rb-meta">
                        <span class="k">Promotion Status</span>
                        <span class="v">{{ $promotionStatus }}</span>
                    </div>
                </div>
            @endif

            {{-- ================= Attendance ================= --}}
            @if ($hasAttendance)
                <div class="attendance">
                    <div class="att-cell">
                        <div class="k">Days Attended</div>
                        <div class="v">{{ $attendance['attended'] ?? '-' }}</div>
                    </div>
                    <div class="att-cell">
                        <div class="k">School Days</div>
                        <div class="v">{{ $attendance['total'] ?? '-' }}</div>
                    </div>
                    <div class="att-cell">
                        <div class="k">Attendance</div>
                        <div class="v">
                            {{ $attendance['percentage'] !== null ? $attendance['percentage'] . '%' : '-' }}
                        </div>
                    </div>
                </div>
            @endif

            {{-- ================= Comments ================= --}}
            <div class="comment">
                <div class="comment-lbl">Class Teacher's Comment</div>
                <div class="comment-txt {{ $teacherComment ? '' : 'empty' }}">
                    {{ $teacherComment ?: 'No comment recorded for this term.' }}
                </div>
            </div>

            <div class="comment">
                <div class="comment-lbl">Head Teacher's Comment</div>
                <div class="comment-txt {{ $headComment ? '' : 'empty' }}">
                    {{ $headComment ?: 'No comment recorded for this term.' }}
                </div>
            </div>

            {{-- ================= Grading key ================= --}}
            @if ($hasGradingKey)
                <div class="key-block">
                    <div class="key-title">Grading Key</div>
                    <div class="key-grid">
                        @foreach (array_chunk($gradingKey, 3) as $chunk)
                            <div class="key-row">
                                @foreach ($chunk as $item)
                                    <div class="key-item">
                                        <strong>{{ $item['letter'] }}</strong>
                                        {{ $item['range'] }} &mdash; {{ $item['label'] }}
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- ================= Signatures ================= --}}
            <div class="sig-table">
                <div class="sig-cell">
                    <div class="sig-line"></div>
                    <div class="sig-role">Class Teacher</div>
                </div>
                <div class="sig-cell">
                    @if ($hasSignature)
                        <img src="{{ $files['signature'] }}" alt="Signature" class="sig-img">
                    @endif
                    <div class="sig-line"></div>
                    <div class="sig-role">Head Teacher</div>
                    @if (!empty($school->principal_name))
                        <div class="sig-name">{{ $school->principal_name }}</div>
                    @endif
                </div>
                <div class="sig-cell">
                    <div class="sig-line"></div>
                    <div class="sig-role">Parent / Guardian</div>
                </div>
            </div>

            <div class="issue-line">
                <strong>Date of Issue:</strong> {{ now()->format('d F Y') }}
            </div>

            {{-- ================= Footer ================= --}}
            <div class="foot">
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

</body>

</html>