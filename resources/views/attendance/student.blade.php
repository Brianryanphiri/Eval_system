@extends('layouts.app')

@section('title', 'Attendance — ' . $student->full_name)
@section('page-title', $student->full_name)

@section('breadcrumbs')
    <a href="{{ route('attendance.summary') }}">Attendance Summary</a>
    <span class="sep">/</span>
    <span>{{ $student->full_name }}</span>
@endsection

@section('styles')
    <style>
        /* Back link */
        .sd-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 500;
            color: var(--pts-grey);
            text-decoration: none;
            margin-bottom: 16px;
        }

        .sd-back:hover {
            color: var(--pts-green-dark);
        }

        /* Student header */
        .sd-header {
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 14px;
            padding: 22px;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 18px;
            flex-wrap: wrap;
        }

        .sd-avatar {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: var(--pts-black);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            font-weight: 800;
            flex-shrink: 0;
        }

        .sd-info {
            flex: 1;
            min-width: 0;
        }

        .sd-name {
            font-size: 20px;
            font-weight: 800;
            color: #111827;
            margin-bottom: 4px;
        }

        .sd-meta {
            font-size: 13px;
            color: var(--pts-grey);
        }

        .sd-meta strong {
            color: #374151;
            font-weight: 700;
        }

        /* Overall rate chip */
        .sd-rate {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 20px;
            border-radius: 12px;
            font-size: 18px;
            font-weight: 800;
            flex-shrink: 0;
        }

        .sd-rate.good {
            background: #DCFCE7;
            color: #166534;
        }

        .sd-rate.warn {
            background: #FEF3C7;
            color: #92400E;
        }

        .sd-rate.bad {
            background: #FEE2E2;
            color: #991B1B;
        }

        .sd-rate.none {
            background: #F3F4F6;
            color: #6B7280;
            font-size: 14px;
        }

        /* Two-column grid */
        .sd-grid {
            display: grid;
            grid-template-columns: 1fr 1.6fr;
            gap: 18px;
            margin-bottom: 18px;
        }

        .sd-panel {
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 14px;
            padding: 22px;
        }

        .sd-panel__title {
            font-size: 15px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .sd-panel__title i {
            color: var(--pts-green-dark);
            font-size: 14px;
        }

        /* Summary blocks per term */
        .sd-term {
            padding: 14px 0;
            border-bottom: 1px solid var(--pts-grey-light);
        }

        .sd-term:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .sd-term:first-child {
            padding-top: 0;
        }

        .sd-term__head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .sd-term__name {
            font-size: 13.5px;
            font-weight: 700;
            color: #111827;
        }

        .sd-term__year {
            font-size: 11.5px;
            color: var(--pts-grey);
            font-weight: 500;
        }

        .sd-term__pct {
            font-size: 15px;
            font-weight: 800;
            padding: 4px 12px;
            border-radius: 20px;
        }

        .sd-term__pct.good {
            background: #DCFCE7;
            color: #166534;
        }

        .sd-term__pct.warn {
            background: #FEF3C7;
            color: #92400E;
        }

        .sd-term__pct.bad {
            background: #FEE2E2;
            color: #991B1B;
        }

        .sd-term__pct.none {
            background: #F3F4F6;
            color: #6B7280;
        }

        /* Summary stats row */
        .sd-term__stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
        }

        .sd-stat {
            padding: 8px;
            background: var(--pts-bg);
            border-radius: 8px;
            text-align: center;
        }

        .sd-stat__value {
            font-size: 16px;
            font-weight: 800;
            color: #111827;
            line-height: 1;
        }

        .sd-stat__value.present {
            color: #16A34A;
        }

        .sd-stat__value.absent {
            color: #DC2626;
        }

        .sd-stat__value.late {
            color: #D97706;
        }

        .sd-stat__value.excused {
            color: #2563EB;
        }

        .sd-stat__label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--pts-grey);
            font-weight: 700;
            margin-top: 4px;
        }

        /* Recent records list */
        .sd-record {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid var(--pts-grey-light);
        }

        .sd-record:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .sd-record__date {
            width: 90px;
            font-size: 13px;
            font-weight: 700;
            color: #111827;
            flex-shrink: 0;
        }

        .sd-record__dow {
            font-size: 11px;
            color: var(--pts-grey);
            font-weight: 500;
        }

        .sd-record__class {
            flex: 1;
            font-size: 13px;
            color: var(--pts-grey);
            min-width: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sd-record__reason {
            font-size: 12px;
            color: var(--pts-grey);
            font-style: italic;
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sd-status {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            flex-shrink: 0;
        }

        .sd-status.green {
            background: #DCFCE7;
            color: #166534;
        }

        .sd-status.red {
            background: #FEE2E2;
            color: #991B1B;
        }

        .sd-status.amber {
            background: #FEF3C7;
            color: #92400E;
        }

        .sd-status.blue {
            background: #DBEAFE;
            color: #1E40AF;
        }

        .sd-status.grey {
            background: #F3F4F6;
            color: #6B7280;
        }

        /* Empty state */
        .empty-mini {
            text-align: center;
            padding: 30px 20px;
            color: var(--pts-grey);
            font-size: 13px;
        }

        @media (max-width: 900px) {
            .sd-grid {
                grid-template-columns: 1fr;
            }

            .sd-term__stats {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
@endsection

@section('content')

    <a href="{{ route('attendance.summary') }}" class="sd-back">
        <i class="fa-solid fa-arrow-left"></i> Back to Attendance Summary
    </a>

    {{-- Student header --}}
    <div class="sd-header">
        <div class="sd-avatar">
            {{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}
        </div>
        <div class="sd-info">
            <div class="sd-name">{{ $student->full_name }}</div>
            <div class="sd-meta">
                <strong>{{ $student->student_number }}</strong>
                @if ($student->currentClassroom())
                    · {{ $student->currentClassroom()->name }}
                @endif
            </div>
        </div>

        @php
            $latest = $summaries->first();
            $latestPct = $latest ? (float) $latest->percentage : null;
            $rateClass = $latestPct === null ? 'none'
                : ($latestPct >= 90 ? 'good' : ($latestPct >= 75 ? 'warn' : 'bad'));
        @endphp

        @if ($latest)
            <div class="sd-rate {{ $rateClass }}">
                <i class="fa-solid fa-calendar-check"></i>
                {{ $latestPct }}%
                <span style="font-size: 12px; font-weight: 600; opacity: 0.7;">
                    {{ $latest->term_name }}
                </span>
            </div>
        @else
            <div class="sd-rate none">
                <i class="fa-solid fa-circle-info"></i>
                No attendance yet
            </div>
        @endif
    </div>

    <div class="sd-grid">

        {{-- Left: Term history --}}
        <div class="sd-panel">
            <div class="sd-panel__title">
                <i class="fa-solid fa-clock-rotate-left"></i> Term History
            </div>

            @if ($summaries->isEmpty())
                <div class="empty-mini">
                    No attendance has been recorded for this student yet.
                </div>
            @else
                @foreach ($summaries as $s)
                    @php
                        $pct = (float) $s->percentage;
                        $pctClass = $pct >= 90 ? 'good' : ($pct >= 75 ? 'warn' : 'bad');
                    @endphp
                    <div class="sd-term">
                        <div class="sd-term__head">
                            <div>
                                <div class="sd-term__name">{{ $s->term_name ?? 'Term' }}</div>
                                <div class="sd-term__year">{{ $s->year_label ?? '' }}</div>
                            </div>
                            <div class="sd-term__pct {{ $pctClass }}">{{ $pct }}%</div>
                        </div>
                        <div class="sd-term__stats">
                            <div class="sd-stat">
                                <div class="sd-stat__value present">{{ $s->days_present }}</div>
                                <div class="sd-stat__label">Present</div>
                            </div>
                            <div class="sd-stat">
                                <div class="sd-stat__value absent">{{ $s->days_absent }}</div>
                                <div class="sd-stat__label">Absent</div>
                            </div>
                            <div class="sd-stat">
                                <div class="sd-stat__value late">{{ $s->days_late }}</div>
                                <div class="sd-stat__label">Late</div>
                            </div>
                            <div class="sd-stat">
                                <div class="sd-stat__value excused">{{ $s->days_excused }}</div>
                                <div class="sd-stat__label">Excused</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        {{-- Right: Recent records --}}
        <div class="sd-panel">
            <div class="sd-panel__title">
                <i class="fa-solid fa-list"></i> Recent Records
                <span style="font-weight:500; color: var(--pts-grey); font-size: 13px;">
                    · last {{ count($recent) }}
                </span>
            </div>

            @if (empty($recent))
                <div class="empty-mini">
                    No individual attendance records yet.
                </div>
            @else
                @foreach ($recent as $r)
                    @php
                        $date = $r['date'];
                        $dateStr = $date ? \Carbon\Carbon::parse($date)->format('d M') : '—';
                        $dow = $date ? \Carbon\Carbon::parse($date)->format('D') : '';
                    @endphp
                    <div class="sd-record">
                        <div class="sd-record__date">
                            {{ $dateStr }}
                            <div class="sd-record__dow">{{ $dow }}</div>
                        </div>
                        <div class="sd-record__class">
                            {{ $r['class'] ?? '—' }}
                        </div>
                        @if (!empty($r['reason']))
                            <div class="sd-record__reason">
                                "{{ $r['reason'] }}"
                            </div>
                        @endif
                        <span class="sd-status {{ $r['status_color'] }}">
                            {{ $r['status_label'] }}
                        </span>
                    </div>
                @endforeach
            @endif
        </div>

    </div>

@endsection