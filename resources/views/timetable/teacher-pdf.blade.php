<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Timetable - {{ $teacher->name }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 12mm 10mm;
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
            padding: 14px 16px 12px;
        }

        .rule-accent {
            height: 3px;
            background:
                {{ $accent }}
            ;
            margin: 0 0 12px;
        }

        /* ---------- Header ---------- */
        .hdr {
            display: table;
            width: 100%;
            padding-bottom: 10px;
            border-bottom: 2px solid
                {{ $primary }}
            ;
            margin-bottom: 12px;
        }

        .hdr-l {
            display: table-cell;
            width: 54px;
            vertical-align: middle;
        }

        .hdr-c {
            display: table-cell;
            vertical-align: middle;
            padding-left: 12px;
        }

        .hdr-r {
            display: table-cell;
            width: 200px;
            vertical-align: middle;
            text-align: right;
        }

        .logo {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            border: 2px solid
                {{ $primary }}
            ;
            background: #fff;
            text-align: center;
            line-height: 44px;
            overflow: hidden;
        }

        .logo img {
            width: 44px;
            height: 44px;
            object-fit: contain;
        }

        .logo-fallback {
            color:
                {{ $accent }}
            ;
            font-size: 18px;
            font-weight: 800;
        }

        .school-name {
            font-size: 15px;
            font-weight: 800;
            color:
                {{ $primary }}
            ;
            text-transform: uppercase;
            letter-spacing: 0.02em;
            line-height: 1.1;
        }

        .school-motto {
            font-size: 9px;
            font-style: italic;
            color:
                {{ $accent }}
            ;
            margin-top: 2px;
        }

        .school-meta {
            font-size: 8.5px;
            color: #6B7280;
            margin-top: 2px;
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
            font-size: 9px;
            color: #6B7280;
        }

        /* ---------- Title ---------- */
        .title-block {
            text-align: center;
            margin-bottom: 12px;
        }

        .title-main {
            font-size: 13px;
            font-weight: 800;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color:
                {{ $primary }}
            ;
        }

        .title-sub {
            font-size: 9px;
            letter-spacing: 0.24em;
            text-transform: uppercase;
            color:
                {{ $accent }}
            ;
            margin-top: 3px;
            font-weight: 700;
        }

        .title-rule {
            width: 80px;
            height: 2px;
            background:
                {{ $accent }}
            ;
            margin: 7px auto 0;
        }

        /* ---------- Info strip ---------- */
        .info-strip {
            display: table;
            width: 100%;
            table-layout: fixed;
            margin-bottom: 10px;
        }

        .info-cell {
            display: table-cell;
            font-size: 10px;
            color: #374151;
            padding: 3px 0;
        }

        .info-cell strong {
            color:
                {{ $primary }}
            ;
            font-weight: 800;
            margin-right: 4px;
            text-transform: uppercase;
            font-size: 8.5px;
            letter-spacing: 0.06em;
        }

        /* ---------- Grid ---------- */
        .grid-wrap {
            border: 1px solid
                {{ $primary }}
            ;
        }

        table.grid {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        table.grid th {
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #fff;
            background:
                {{ $primary }}
            ;
            padding: 7px 6px;
            text-align: center;
            border-right: 1px solid rgba(255, 255, 255, 0.15);
        }

        table.grid th:last-child {
            border-right: none;
        }

        table.grid th.period-col {
            width: 120px;
            text-align: left;
        }

        table.grid td {
            border: 1px solid #E5E7EB;
            padding: 6px 5px;
            text-align: center;
            vertical-align: middle;
            font-size: 10px;
        }

        table.grid td.period-cell {
            background: #FAFBFC;
            text-align: left;
            padding: 6px 8px;
        }

        .period-name {
            font-size: 10px;
            font-weight: 800;
            color: #111827;
        }

        .period-time {
            font-size: 8.5px;
            color: #6B7280;
            font-family: 'DejaVu Sans Mono', monospace;
            margin-top: 1px;
        }

        table.grid tr.break-row td {
            background: #F3F4F6;
            color: #9CA3AF;
            font-style: italic;
            font-size: 9px;
        }

        .slot-class {
            font-size: 10px;
            font-weight: 800;
            color: #111827;
            line-height: 1.2;
        }

        .slot-subject {
            font-size: 8.5px;
            color: #6B7280;
            margin-top: 1px;
        }

        .empty-slot {
            color: #C1C5CC;
            font-size: 9.5px;
        }

        /* ---------- Footer ---------- */
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
        $days = \App\Models\ClassTimetable::days();
    @endphp

    <div class="frame">
        <div class="frame-inner">

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

            <div class="title-block">
                <div class="title-main">Teacher Timetable</div>
                <div class="title-sub">{{ $teacher->name }}</div>
                <div class="title-rule"></div>
            </div>

            <div class="info-strip">
                <div class="info-cell" style="width: 40%;">
                    <strong>Teacher:</strong> {{ $teacher->name }}
                </div>
                <div class="info-cell" style="width: 30%;">
                    <strong>Term:</strong> {{ $term }}
                </div>
                <div class="info-cell" style="width: 30%; text-align: right;">
                    <strong>Academic Year:</strong> {{ $academicYear }}
                </div>
            </div>

            <div class="grid-wrap">
                <table class="grid">
                    <thead>
                        <tr>
                            <th class="period-col">Period</th>
                            @foreach ($days as $dayNum => $dayName)
                                <th>{{ $dayName }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($periods as $period)
                            <tr class="{{ $period->is_break ? 'break-row' : '' }}">
                                <td class="period-cell">
                                    <div class="period-name">{{ $period->name }}</div>
                                    <div class="period-time">{{ $period->time_label }}</div>
                                </td>

                                @foreach ($days as $dayNum => $dayName)
                                    @php
                                        $slot = $grid[$dayNum][$period->id] ?? null;
                                    @endphp

                                    <td>
                                        @if ($period->is_break)
                                            {{ $period->name }}
                                        @elseif ($slot)
                                            <div class="slot-class">{{ $slot->class_name }}</div>
                                            <div class="slot-subject">{{ $slot->subject_name }}</div>
                                        @else
                                            <span class="empty-slot">-</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

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