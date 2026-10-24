@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('styles')
    <style>
        .dash-actions {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .qa-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            color: #111827;
            text-decoration: none;
            transition: all 0.15s ease;
        }

        .qa-btn:hover {
            border-color: var(--pts-green);
            color: var(--pts-green-dark);
            background: var(--pts-green-light);
        }

        .qa-btn i {
            font-size: 12px;
        }

        .qa-btn.primary {
            background: var(--pts-green);
            color: #fff;
            border-color: var(--pts-green);
        }

        .qa-btn.primary:hover {
            background: var(--pts-green-dark);
            color: #fff;
        }

        /* ---------- Stat cards ---------- */
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 14px;
            padding: 18px 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .stat-card__top {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .stat-card__icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
        }

        .stat-card__icon.green {
            background: var(--pts-green-light);
            color: var(--pts-green-dark);
        }

        .stat-card__icon.blue {
            background: #EFF6FF;
            color: #2563EB;
        }

        .stat-card__icon.red {
            background: #FEF2F2;
            color: #DC2626;
        }

        .stat-card__icon.amber {
            background: #FFFBEB;
            color: #D97706;
        }

        .stat-card__value {
            font-size: 28px;
            font-weight: 800;
            color: #111827;
            line-height: 1;
        }

        .stat-card__label {
            font-size: 12.5px;
            color: var(--pts-grey);
            font-weight: 600;
        }

        .stat-delta {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11.5px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 20px;
            background: #F3F4F6;
            color: #6B7280;
        }

        .stat-delta.up {
            background: var(--pts-green-light);
            color: var(--pts-green-dark);
        }

        .stat-delta.down {
            background: #FEF2F2;
            color: #DC2626;
        }

        .stat-delta.down.inverted {
            background: var(--pts-green-light);
            color: var(--pts-green-dark);
        }

        .stat-delta.up.inverted {
            background: #FEF2F2;
            color: #DC2626;
        }

        /* ---------- Ring cards ---------- */
        .ring-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 16px;
        }

        .ring-card {
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 14px;
            padding: 20px;
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .ring-card__chart {
            position: relative;
            width: 120px;
            height: 120px;
            flex-shrink: 0;
        }

        .ring-card__chart svg {
            width: 100%;
            height: 100%;
            display: block;
        }

        .ring-card__ring-bg {
            fill: none;
            stroke: #F3F4F6;
            stroke-width: 10;
        }

        .ring-card__ring-fill {
            fill: none;
            stroke-width: 10;
            stroke-linecap: round;
            transform: rotate(-90deg);
            transform-origin: 60px 60px;
        }

        .ring-card__ring-fill.green {
            stroke: var(--pts-green);
        }

        .ring-card__ring-fill.blue {
            stroke: #2563EB;
        }

        .ring-card__center {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            pointer-events: none;
        }

        .ring-card__value {
            font-size: 22px;
            font-weight: 800;
            color: #111827;
            line-height: 1;
        }

        .ring-card__sub {
            font-size: 10px;
            color: var(--pts-grey);
            text-transform: uppercase;
            letter-spacing: 0.06em;
            font-weight: 700;
            margin-top: 3px;
        }

        .ring-card__body {
            flex: 1;
            min-width: 0;
        }

        .ring-card__title {
            font-size: 14px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 10px;
        }

        .ring-legend {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .ring-legend__row {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12.5px;
            color: #374151;
        }

        .ring-legend__dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .ring-legend__label {
            flex: 1;
            color: var(--pts-grey);
        }

        .ring-legend__value {
            font-weight: 700;
            color: #111827;
        }

        /* ---------- Panels ---------- */
        .panel {
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 14px;
            padding: 20px;
            margin-bottom: 16px;
        }

        .panel__head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .panel__title {
            font-size: 14px;
            font-weight: 700;
            color: #111827;
        }

        .panel__link {
            font-size: 12px;
            font-weight: 600;
            color: var(--pts-green-dark);
            text-decoration: none;
        }

        .panel__link:hover {
            text-decoration: underline;
        }

        /* ---------- Trend chart ---------- */
        .trend-chart-wrap {
            position: relative;
            width: 100%;
            height: 240px;
        }

        .trend-chart-wrap canvas {
            width: 100% !important;
            height: 100% !important;
        }

        /* ---------- Two-column layout ---------- */
        .dash-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.4fr) minmax(0, 1fr);
            gap: 16px;
            align-items: start;
        }

        .dash-col {
            display: flex;
            flex-direction: column;
            gap: 16px;
            min-width: 0;
        }

        /* ---------- Subject bars ---------- */
        .subj-row {
            margin-bottom: 14px;
        }

        .subj-row:last-child {
            margin-bottom: 0;
        }

        .subj-row__head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 6px;
        }

        .subj-row__name {
            font-size: 13px;
            font-weight: 600;
            color: #111827;
        }

        .subj-row__value {
            font-size: 12.5px;
            font-weight: 700;
            color: #111827;
        }

        .subj-row__track {
            height: 8px;
            background: var(--pts-bg);
            border-radius: 6px;
            overflow: hidden;
        }

        .subj-row__fill {
            height: 100%;
            border-radius: 6px;
            transition: width 0.4s ease;
        }

        /* ---------- Class comparison ---------- */
        .class-row {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }

        .class-row:last-child {
            margin-bottom: 0;
        }

        .class-row__label {
            width: 70px;
            font-size: 12.5px;
            font-weight: 700;
            color: #111827;
            flex-shrink: 0;
        }

        .class-row__track {
            flex: 1;
            height: 20px;
            background: var(--pts-bg);
            border-radius: 6px;
            overflow: hidden;
        }

        .class-row__fill {
            height: 100%;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding-right: 8px;
            color: #fff;
            font-size: 10.5px;
            font-weight: 700;
        }

        /* ---------- List items ---------- */
        .list-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 0;
            border-bottom: 1px solid var(--pts-grey-light);
        }

        .list-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .list-item:first-child {
            padding-top: 0;
        }

        .list-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: var(--pts-black);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12.5px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .list-avatar.green {
            background: var(--pts-green);
        }

        .list-body {
            flex: 1;
            min-width: 0;
        }

        .list-title {
            font-size: 13px;
            font-weight: 600;
            color: #111827;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .list-sub {
            font-size: 11.5px;
            color: var(--pts-grey);
            margin-top: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .list-badge {
            font-size: 11.5px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 20px;
            flex-shrink: 0;
        }

        .list-badge.green {
            background: var(--pts-green-light);
            color: var(--pts-green-dark);
        }

        .list-badge.red {
            background: #FEF2F2;
            color: #DC2626;
        }

        .list-badge.amber {
            background: #FFFBEB;
            color: #D97706;
        }

        .list-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            flex-shrink: 0;
        }

        .list-icon.green {
            background: var(--pts-green-light);
            color: var(--pts-green-dark);
        }

        .list-icon.amber {
            background: #FFFBEB;
            color: #D97706;
        }

        .list-icon.red {
            background: #FEF2F2;
            color: #DC2626;
        }

        .list-icon.blue {
            background: #EFF6FF;
            color: #2563EB;
        }

        /* ---------- Activity feed ---------- */
        .activity-item {
            display: flex;
            gap: 12px;
            padding: 11px 0;
            border-bottom: 1px solid var(--pts-grey-light);
        }

        .activity-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .activity-item:first-child {
            padding-top: 0;
        }

        .activity-text {
            font-size: 12.5px;
            color: #111827;
            line-height: 1.4;
        }

        .activity-time {
            font-size: 11px;
            color: var(--pts-grey);
            margin-top: 2px;
        }

        .empty-state {
            font-size: 12.5px;
            color: var(--pts-grey);
            padding: 8px 0;
            font-style: italic;
        }

        @media (max-width: 1100px) {
            .stat-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .ring-grid,
            .dash-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .stat-grid {
                grid-template-columns: 1fr;
            }

            .ring-card {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
@endsection

@section('content')

    @php
        $renderDelta = function (array $delta): string {
            if ($delta['value'] === null) {
                return '';
            }

            $dir = $delta['direction'];
            $invert = $delta['invert'] ?? false;

            $class = $dir;
            if ($dir !== 'flat' && $invert) {
                $class .= ' inverted';
            }

            $icon = $dir === 'up' ? 'fa-arrow-up' : ($dir === 'down' ? 'fa-arrow-down' : 'fa-minus');
            $value = $dir === 'flat' ? '0%' : $delta['value'] . '%';

            return '<span class="stat-delta ' . $class . '"><i class="fa-solid ' . $icon . '" style="font-size:9px;"></i> ' . $value . '</span>';
        };

        $ringCircumference = 2 * M_PI * 50;
    @endphp

    {{-- Quick actions --}}
    <div class="dash-actions">
        @can('results.enter')
            <a href="{{ route('marks.entry') }}" class="qa-btn primary">
                <i class="fa-solid fa-pen-to-square"></i> Enter Marks
            </a>
        @endcan
        @can('students.edit')
            <a href="{{ route('students.create') }}" class="qa-btn">
                <i class="fa-solid fa-user-plus"></i> Add Student
            </a>
        @endcan
        @can('assessment.create')
            <a href="{{ route('assessments.create') }}" class="qa-btn">
                <i class="fa-solid fa-plus"></i> New Assessment
            </a>
        @endcan
        @can('reports.generate')
            <a href="{{ route('reports.index') }}" class="qa-btn">
                <i class="fa-solid fa-file-lines"></i> Reports
            </a>
        @endcan
    </div>

    {{-- Stat cards --}}
    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-card__top">
                <div class="stat-card__icon blue"><i class="fa-solid fa-users"></i></div>
                {!! $renderDelta($deltas['students']) !!}
            </div>
            <div class="stat-card__value">{{ number_format($totalStudents) }}</div>
            <div class="stat-card__label">Total Students</div>
        </div>

        <div class="stat-card">
            <div class="stat-card__top">
                <div class="stat-card__icon green"><i class="fa-solid fa-chart-simple"></i></div>
                {!! $renderDelta($deltas['average']) !!}
            </div>
            <div class="stat-card__value">{{ $overallAverage }}</div>
            <div class="stat-card__label">Overall Average</div>
        </div>

        <div class="stat-card">
            <div class="stat-card__top">
                <div class="stat-card__icon green"><i class="fa-solid fa-square-check"></i></div>
                {!! $renderDelta($deltas['pass_rate']) !!}
            </div>
            <div class="stat-card__value">{{ $passRate }}</div>
            <div class="stat-card__label">Pass Rate</div>
        </div>

        <div class="stat-card">
            <div class="stat-card__top">
                <div class="stat-card__icon red"><i class="fa-solid fa-triangle-exclamation"></i></div>
                {!! $renderDelta($deltas['at_risk']) !!}
            </div>
            <div class="stat-card__value">{{ number_format($atRiskCount) }}</div>
            <div class="stat-card__label">Students At Risk</div>
        </div>
    </div>

    {{-- Rings --}}
    <div class="ring-grid">
        {{-- Attendance ring --}}
        <div class="ring-card">
            @php
                $attPct = $attendanceRing['percentage'] ?? 0;
                $attOffset = $ringCircumference - ($attPct / 100) * $ringCircumference;
            @endphp
            <div class="ring-card__chart">
                <svg viewBox="0 0 120 120">
                    <circle class="ring-card__ring-bg" cx="60" cy="60" r="50" />
                    <circle class="ring-card__ring-fill green" cx="60" cy="60" r="50"
                        stroke-dasharray="{{ $ringCircumference }}" stroke-dashoffset="{{ $attOffset }}" />
                </svg>
                <div class="ring-card__center">
                    <div class="ring-card__value">{{ $attendanceRing ? $attPct . '%' : '—' }}</div>
                    <div class="ring-card__sub">Attendance</div>
                </div>
            </div>
            <div class="ring-card__body">
                <div class="ring-card__title">Attendance Overview</div>
                @if ($attendanceRing)
                    <div class="ring-legend">
                        <div class="ring-legend__row">
                            <span class="ring-legend__dot" style="background:var(--pts-green);"></span>
                            <span class="ring-legend__label">Present</span>
                            <span class="ring-legend__value">{{ number_format($attendanceRing['present']) }}</span>
                        </div>
                        <div class="ring-legend__row">
                            <span class="ring-legend__dot" style="background:#DC2626;"></span>
                            <span class="ring-legend__label">Absent</span>
                            <span class="ring-legend__value">{{ number_format($attendanceRing['absent']) }}</span>
                        </div>
                        <div class="ring-legend__row">
                            <span class="ring-legend__dot" style="background:#D97706;"></span>
                            <span class="ring-legend__label">Late</span>
                            <span class="ring-legend__value">{{ number_format($attendanceRing['late']) }}</span>
                        </div>
                        <div class="ring-legend__row">
                            <span class="ring-legend__dot" style="background:#9CA3AF;"></span>
                            <span class="ring-legend__label">Excused</span>
                            <span class="ring-legend__value">{{ number_format($attendanceRing['excused']) }}</span>
                        </div>
                    </div>
                @else
                    <div class="empty-state">No attendance records for this term yet.</div>
                @endif
            </div>
        </div>

        {{-- Pass rate ring --}}
        <div class="ring-card">
            @php
                $passPct = $passRateValue ?? 0;
                $passOffset = $ringCircumference - ($passPct / 100) * $ringCircumference;
            @endphp
            <div class="ring-card__chart">
                <svg viewBox="0 0 120 120">
                    <circle class="ring-card__ring-bg" cx="60" cy="60" r="50" />
                    <circle class="ring-card__ring-fill blue" cx="60" cy="60" r="50"
                        stroke-dasharray="{{ $ringCircumference }}" stroke-dashoffset="{{ $passOffset }}" />
                </svg>
                <div class="ring-card__center">
                    <div class="ring-card__value">{{ $passRateValue !== null ? $passPct . '%' : '—' }}</div>
                    <div class="ring-card__sub">Pass Rate</div>
                </div>
            </div>
            <div class="ring-card__body">
                <div class="ring-card__title">Academic Standing</div>
                @if ($passRateValue !== null)
                    <div class="ring-legend">
                        <div class="ring-legend__row">
                            <span class="ring-legend__dot" style="background:#2563EB;"></span>
                            <span class="ring-legend__label">Passing (50%+)</span>
                            <span class="ring-legend__value">{{ $passPct }}%</span>
                        </div>
                        <div class="ring-legend__row">
                            <span class="ring-legend__dot" style="background:#DC2626;"></span>
                            <span class="ring-legend__label">Below threshold</span>
                            <span class="ring-legend__value">{{ round(100 - $passPct, 1) }}%</span>
                        </div>
                    </div>
                @else
                    <div class="empty-state">No exam results for this term yet.</div>
                @endif
            </div>
        </div>
    </div>

    {{-- Trend chart --}}
    <div class="panel">
        <div class="panel__head">
            <div class="panel__title">Performance Overview</div>
            <a href="{{ route('reports.index') }}" class="panel__link">View full report</a>
        </div>
        @if (count($trendLabels) >= 2)
            <div class="trend-chart-wrap">
                <canvas id="performanceTrendChart" data-labels='@json($trendLabels)' data-averages='@json($trendValues)'
                    data-pass-rates='@json($trendPassRate)'></canvas>
            </div>
        @else
            <div class="empty-state" style="padding: 30px 0;">
                Not enough data yet to plot a trend. Once two or more terms have results recorded, the performance line will
                appear here.
            </div>
        @endif
    </div>

    {{-- Two-column --}}
    <div class="dash-grid">

        {{-- LEFT --}}
        <div class="dash-col">

            <div class="panel">
                <div class="panel__head">
                    <div class="panel__title">Subject Performance</div>
                    <a href="{{ route('reports.subject-performance') }}" class="panel__link">Details</a>
                </div>
                @forelse ($subjectsAttention as $s)
                    @php
                        $color = $s['score'] >= 65 ? 'var(--pts-green)' : ($s['score'] >= 50 ? '#D97706' : '#DC2626');
                    @endphp
                    <div class="subj-row">
                        <div class="subj-row__head">
                            <span class="subj-row__name">{{ $s['name'] }}</span>
                            <span class="subj-row__value">{{ $s['score'] }}%</span>
                        </div>
                        <div class="subj-row__track">
                            <div class="subj-row__fill" style="width: {{ min(100, $s['score']) }}%; background: {{ $color }};">
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">No subject results for this term yet.</div>
                @endforelse
            </div>

            <div class="panel">
                <div class="panel__head">
                    <div class="panel__title">Class Comparison</div>
                    <a href="{{ route('classes.overview') }}" class="panel__link">All classes</a>
                </div>
                @forelse ($classComparison as $name => $score)
                    <div class="class-row">
                        <div class="class-row__label">{{ $name }}</div>
                        <div class="class-row__track">
                            <div class="class-row__fill"
                                style="width: {{ $score }}%; background: {{ $score >= 65 ? 'var(--pts-green)' : ($score >= 50 ? '#D97706' : '#DC2626') }};">
                                {{ $score }}%
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">No class averages for this term yet.</div>
                @endforelse
            </div>

            <div class="panel">
                <div class="panel__head">
                    <div class="panel__title">Students At Risk</div>
                    <a href="{{ route('learning.flags') }}" class="panel__link">View all</a>
                </div>
                @forelse ($atRiskStudents as $s)
                    <div class="list-item">
                        <div class="list-avatar">{{ strtoupper(substr($s['name'], 0, 1)) }}</div>
                        <div class="list-body">
                            <div class="list-title">{{ $s['name'] }}</div>
                            <div class="list-sub">{{ $s['reason'] }} · {{ $s['class'] }}</div>
                        </div>
                        <span class="list-badge red">AT RISK</span>
                    </div>
                @empty
                    <div class="empty-state">No at-risk students this term. Good news.</div>
                @endforelse
            </div>

        </div>

        {{-- RIGHT --}}
        <div class="dash-col">

            <div class="panel">
                <div class="panel__head">
                    <div class="panel__title">Recently Added Students</div>
                    <a href="{{ route('students.index') }}" class="panel__link">View all</a>
                </div>
                @forelse ($recentStudents as $s)
                    <div class="list-item">
                        <div class="list-avatar green">{{ strtoupper(substr($s['name'], 0, 1)) }}</div>
                        <div class="list-body">
                            <div class="list-title">{{ $s['name'] }}</div>
                            <div class="list-sub">{{ $s['class'] }} · {{ $s['when'] }}</div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">No students enrolled yet.</div>
                @endforelse
            </div>

            <div class="panel">
                <div class="panel__head">
                    <div class="panel__title">Top Performers</div>
                    <a href="{{ route('reports.merit') }}" class="panel__link">Merit list</a>
                </div>
                @forelse ($topStudents as $i => $s)
                    <div class="list-item">
                        <div class="list-avatar" style="background:var(--pts-green-light); color:var(--pts-green-dark);">
                            {{ $i + 1 }}</div>
                        <div class="list-body">
                            <div class="list-title">{{ $s['name'] }}</div>
                            <div class="list-sub">{{ $s['class'] }}</div>
                        </div>
                        <span class="list-badge green">{{ $s['score'] }}%</span>
                    </div>
                @empty
                    <div class="empty-state">No ranked results this term yet.</div>
                @endforelse
            </div>

            <div class="panel">
                <div class="panel__head">
                    <div class="panel__title">Upcoming Assessments</div>
                    <a href="{{ route('assessments.index') }}" class="panel__link">View all</a>
                </div>
                @forelse ($upcomingAssessments as $a)
                    <div class="list-item">
                        <div class="list-icon amber"><i class="fa-solid fa-file-pen"></i></div>
                        <div class="list-body">
                            <div class="list-title">{{ $a['name'] }}</div>
                            <div class="list-sub">{{ $a['class'] }} · {{ $a['subject'] }}</div>
                        </div>
                        <span class="list-badge amber">{{ $a['when'] }}</span>
                    </div>
                @empty
                    <div class="empty-state">No pending assessments. All caught up.</div>
                @endforelse
            </div>

            <div class="panel">
                <div class="panel__head">
                    <div class="panel__title">Recent Activity</div>
                    <a href="{{ route('settings.audit') }}" class="panel__link">Audit log</a>
                </div>
                @foreach ($activities as $a)
                    <div class="activity-item">
                        <div class="list-icon {{ $a['color'] }}"><i class="fa-solid {{ $a['icon'] }}"></i></div>
                        <div class="list-body">
                            <div class="activity-text">{!! $a['text'] !!}</div>
                            <div class="activity-time">{{ $a['when'] }}</div>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>

    </div>

@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        (function () {
            const canvas = document.getElementById('performanceTrendChart');
            if (!canvas || typeof Chart === 'undefined') return;

            const labels = JSON.parse(canvas.dataset.labels || '[]');
            const averages = JSON.parse(canvas.dataset.averages || '[]');
            const passRates = JSON.parse(canvas.dataset.passRates || '[]');

            const ctx = canvas.getContext('2d');
            const gradient = ctx.createLinearGradient(0, 0, 0, 240);
            gradient.addColorStop(0, 'rgba(122, 193, 66, 0.25)');
            gradient.addColorStop(1, 'rgba(122, 193, 66, 0.0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Average Score',
                            data: averages,
                            borderColor: '#7AC142',
                            backgroundColor: gradient,
                            borderWidth: 2.5,
                            fill: true,
                            tension: 0.35,
                            pointRadius: 3,
                            pointBackgroundColor: '#7AC142',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointHoverRadius: 5,
                        },
                        {
                            label: 'Pass Rate',
                            data: passRates,
                            borderColor: '#111827',
                            backgroundColor: 'transparent',
                            borderWidth: 2,
                            borderDash: [5, 4],
                            fill: false,
                            tension: 0.35,
                            pointRadius: 3,
                            pointBackgroundColor: '#111827',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointHoverRadius: 5,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: {
                            position: 'top',
                            align: 'end',
                            labels: {
                                usePointStyle: true,
                                pointStyle: 'circle',
                                boxWidth: 8,
                                boxHeight: 8,
                                font: { size: 12, weight: '600' },
                                color: '#374151',
                            }
                        },
                        tooltip: {
                            backgroundColor: '#111827',
                            titleColor: '#fff',
                            bodyColor: '#E5E7EB',
                            padding: 10,
                            cornerRadius: 8,
                            titleFont: { size: 12, weight: '700' },
                            bodyFont: { size: 12 },
                            callbacks: {
                                label: function (context) {
                                    return context.dataset.label + ': ' + context.parsed.y + '%';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { color: '#6B7280', font: { size: 11, weight: '500' } },
                            border: { display: false }
                        },
                        y: {
                            beginAtZero: false,
                            suggestedMin: 40,
                            suggestedMax: 100,
                            grid: { color: '#F3F4F6' },
                            ticks: {
                                color: '#6B7280',
                                font: { size: 11, weight: '500' },
                                callback: function (value) { return value + '%'; }
                            },
                            border: { display: false }
                        }
                    }
                }
            });
        })();
    </script>
@endsection