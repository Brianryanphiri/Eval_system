@extends('layouts.app')

@section('title', 'Attendance Summary')
@section('page-title', 'Attendance Summary')

@section('styles')
    <style>
        /* Controls */
        .as-controls {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 18px;
            flex-wrap: wrap;
        }

        .as-select {
            padding: 9px 14px;
            border: 1.5px solid var(--pts-grey-light);
            border-radius: 9px;
            font-size: 13px;
            color: #374151;
            background: #fff;
            outline: none;
            cursor: pointer;
            font-family: inherit;
        }

        .as-select:focus {
            border-color: var(--pts-green);
        }

        .as-pill {
            padding: 8px 14px;
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 8px;
            font-size: 12.5px;
            font-weight: 600;
            color: #374151;
            text-decoration: none;
            transition: all 0.15s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .as-pill:hover {
            border-color: var(--pts-green);
            color: var(--pts-green-dark);
        }

        .as-pill.active {
            background: var(--pts-green);
            color: #fff;
            border-color: var(--pts-green);
        }

        .as-spacer {
            flex: 1;
        }

        .as-btn {
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

        .as-btn:hover {
            border-color: var(--pts-green);
            color: var(--pts-green-dark);
        }

        .as-btn.primary {
            background: var(--pts-green);
            color: #fff;
            border-color: var(--pts-green);
        }

        .as-btn.primary:hover {
            background: var(--pts-green-dark);
            border-color: var(--pts-green-dark);
        }

        /* KPI row */
        .as-kpis {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }

        .as-kpi {
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 12px;
            padding: 18px;
            position: relative;
            overflow: hidden;
        }

        .as-kpi::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
        }

        .as-kpi.overall::before {
            background: #2563EB;
        }

        .as-kpi.best::before {
            background: #16A34A;
        }

        .as-kpi.worst::before {
            background: #DC2626;
        }

        .as-kpi.classes::before {
            background: #6B7280;
        }

        .as-kpi__label {
            font-size: 10.5px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--pts-grey);
            font-weight: 700;
            margin-bottom: 8px;
        }

        .as-kpi__value {
            font-size: 24px;
            font-weight: 800;
            color: #111827;
            line-height: 1;
        }

        .as-kpi__sub {
            font-size: 11.5px;
            color: var(--pts-grey);
            margin-top: 4px;
        }

        /* Two-column grid */
        .as-grid {
            display: grid;
            grid-template-columns: 1fr 1.6fr;
            gap: 18px;
            margin-bottom: 18px;
        }

        .as-panel {
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 14px;
            padding: 22px;
        }

        .as-panel__title {
            font-size: 15px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .as-panel__title i {
            color: var(--pts-green-dark);
            font-size: 14px;
        }

        /* Per-class rate bars */
        .as-class-row {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 14px;
        }

        .as-class-row:last-child {
            margin-bottom: 0;
        }

        .as-class-label {
            width: 90px;
            font-size: 12.5px;
            font-weight: 700;
            color: #111827;
            flex-shrink: 0;
        }

        .as-class-track {
            flex: 1;
            height: 22px;
            background: var(--pts-bg);
            border-radius: 6px;
            overflow: hidden;
        }

        .as-class-fill {
            height: 100%;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding-right: 8px;
            color: #fff;
            font-size: 11px;
            font-weight: 800;
        }

        /* Student table */
        .as-table-wrap {
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 14px;
            overflow: hidden;
        }

        .as-table {
            width: 100%;
            border-collapse: collapse;
        }

        .as-table thead th {
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

        .as-table thead th.center {
            text-align: center;
        }

        .as-table thead th.right {
            text-align: right;
        }

        .as-table tbody td {
            padding: 13px 20px;
            font-size: 13.5px;
            color: #374151;
            border-bottom: 1px solid var(--pts-grey-light);
        }

        .as-table tbody tr:last-child td {
            border-bottom: none;
        }

        .as-table tbody tr:hover {
            background: #FAFBFC;
        }

        .as-student {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: inherit;
        }

        .as-avatar {
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

        .as-name {
            font-size: 13.5px;
            font-weight: 600;
            color: #111827;
        }

        .as-meta {
            font-size: 11.5px;
            color: var(--pts-grey);
            margin-top: 1px;
        }

        /* Attendance percentage pill */
        .as-pct {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 800;
        }

        .as-pct.good {
            background: #DCFCE7;
            color: #166534;
        }

        .as-pct.warn {
            background: #FEF3C7;
            color: #92400E;
        }

        .as-pct.bad {
            background: #FEE2E2;
            color: #991B1B;
        }

        /* Mini distribution bar */
        .as-mini {
            display: inline-flex;
            height: 8px;
            width: 100%;
            max-width: 120px;
            border-radius: 4px;
            overflow: hidden;
            background: var(--pts-bg);
            margin: 0 auto;
        }

        .as-mini__seg {
            height: 100%;
        }

        .as-mini__seg.present {
            background: #16A34A;
        }

        .as-mini__seg.absent {
            background: #DC2626;
        }

        .as-mini__seg.late {
            background: #D97706;
        }

        .as-mini__seg.excused {
            background: #2563EB;
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
            max-width: 480px;
            margin: 0 auto;
        }

        @media (max-width: 900px) {
            .as-kpis {
                grid-template-columns: repeat(2, 1fr);
            }

            .as-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')

    {{-- Controls --}}
    <div class="as-controls">
        @if ($allTerms->count() > 0)
            <form method="GET" action="{{ route('attendance.summary') }}" id="asTermForm">
                <input type="hidden" name="class_id" value="{{ $selectedClassId }}">
                <select name="term" class="as-select" onchange="document.getElementById('asTermForm').submit()">
                    @foreach ($allTerms as $t)
                        <option value="{{ $t->id }}" @selected($currentTermId == $t->id)>
                            {{ $t->name }} · {{ $t->academicYear?->label ?? '' }}
                        </option>
                    @endforeach
                </select>
            </form>
        @endif

        <a href="{{ route('attendance.summary', ['class_id' => 'all', 'term' => $currentTermId]) }}"
            class="as-pill {{ $selectedClassId === 'all' ? 'active' : '' }}">
            All Classes
        </a>
        @foreach ($classes as $class)
            <a href="{{ route('attendance.summary', ['class_id' => $class->id, 'term' => $currentTermId]) }}"
                class="as-pill {{ $selectedClassId == $class->id ? 'active' : '' }}">
                {{ $class->name }}
            </a>
        @endforeach

        <div class="as-spacer"></div>

        <a href="{{ route('attendance.index', ['class_id' => $selectedClassId !== 'all' ? $selectedClassId : null, 'term' => $currentTermId]) }}"
            class="as-btn primary">
            <i class="fa-solid fa-clipboard-check"></i> Take Attendance
        </a>
    </div>

    {{-- KPIs --}}
    @php
        $worstClassId = null;
        $bestClassId = null;
        if (!empty($classRates)) {
            $minRate = min($classRates);
            $maxRate = max($classRates);
            $worstClassId = array_search($minRate, $classRates);
            $bestClassId = array_search($maxRate, $classRates);
        }
    @endphp

    <div class="as-kpis">
        <div class="as-kpi overall">
            <div class="as-kpi__label">Overall Attendance</div>
            <div class="as-kpi__value">
                {{ $overallRate !== null ? $overallRate . '%' : '—' }}
            </div>
            <div class="as-kpi__sub">{{ $currentTerm->name ?? 'Current term' }}</div>
        </div>

        <div class="as-kpi classes">
            <div class="as-kpi__label">Classes Tracked</div>
            <div class="as-kpi__value">{{ count($classRates) }}</div>
            <div class="as-kpi__sub">{{ $studentRows->count() }} students</div>
        </div>

        <div class="as-kpi best">
            <div class="as-kpi__label">Best Attendance</div>
            <div class="as-kpi__value">
                {{ !empty($classRates) ? max($classRates) . '%' : '—' }}
            </div>
            <div class="as-kpi__sub">
                @if ($bestClassId)
                    {{ $classes->firstWhere('id', $bestClassId)?->name ?? '—' }}
                @else
                    No data
                @endif
            </div>
        </div>

        <div class="as-kpi worst">
            <div class="as-kpi__label">Lowest Attendance</div>
            <div class="as-kpi__value">
                {{ !empty($classRates) ? min($classRates) . '%' : '—' }}
            </div>
            <div class="as-kpi__sub">
                @if ($worstClassId)
                    {{ $classes->firstWhere('id', $worstClassId)?->name ?? '—' }}
                @else
                    No data
                @endif
            </div>
        </div>
    </div>

    @if (empty($classRates) && $studentRows->isEmpty())
        <div class="empty-state">
            <div class="empty-state__icon"><i class="fa-solid fa-calendar-check"></i></div>
            <div class="empty-state__title">No attendance recorded yet</div>
            <div class="empty-state__desc">
                Once you save attendance for any class, this page will show the summary — attendance rates per class, plus
                per-student breakdowns.
            </div>
        </div>
    @else

        <div class="as-grid">

            {{-- Left: Per-class bars --}}
            <div class="as-panel">
                <div class="as-panel__title">
                    <i class="fa-solid fa-chart-simple"></i> Per-class attendance
                </div>

                @if (empty($classRates))
                    <div style="text-align:center; padding: 20px; color: var(--pts-grey); font-size: 13px;">
                        No per-class data.
                    </div>
                @else
                    @foreach ($classRates as $classId => $rate)
                        @php
                            $cls = $classes->firstWhere('id', $classId);
                            $barColor = $rate >= 90 ? '#16A34A' : ($rate >= 75 ? '#D97706' : '#DC2626');
                        @endphp
                        <div class="as-class-row">
                            <div class="as-class-label">{{ $cls?->name ?? '—' }}</div>
                            <div class="as-class-track">
                                <div class="as-class-fill" style="width: {{ $rate }}%; background: {{ $barColor }};">
                                    {{ $rate }}%
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            {{-- Right: Students table --}}
            <div class="as-panel">
                <div class="as-panel__title">
                    <i class="fa-solid fa-users"></i> Students
                    @if ($selectedClass)
                        <span style="font-weight:500; color: var(--pts-grey); font-size: 13px;">· {{ $selectedClass->name }}</span>
                    @endif
                </div>

                @if ($studentRows->isEmpty())
                    <div style="text-align:center; padding: 30px; color: var(--pts-grey); font-size: 13px;">
                        No student-level data yet.
                    </div>
                @else
                    <div style="max-height: 500px; overflow-y: auto; margin: -12px -22px 0; padding: 0 22px;">
                        @foreach ($studentRows as $row)
                            @php
                                $pct = (float) $row->percentage;
                                $pctClass = $pct >= 90 ? 'good' : ($pct >= 75 ? 'warn' : 'bad');

                                $total = $row->days_present + $row->days_absent + $row->days_late + $row->days_excused;
                                if ($total === 0)
                                    $total = 1;
                            @endphp
                            <div
                                style="display: flex; align-items: center; gap: 12px; padding: 12px 0; border-bottom: 1px solid var(--pts-grey-light);">
                                <a href="{{ route('attendance.student', $row->student_id) }}" class="as-student"
                                    style="flex:1; min-width: 0;">
                                    <div class="as-avatar">
                                        {{ strtoupper(substr($row->first_name, 0, 1) . substr($row->last_name, 0, 1)) }}
                                    </div>
                                    <div style="min-width: 0;">
                                        <div class="as-name">{{ $row->first_name }} {{ $row->last_name }}</div>
                                        <div class="as-meta">{{ $row->student_number }}</div>
                                    </div>
                                </a>

                                <div class="as-mini"
                                    title="{{ $row->days_present }} present / {{ $row->days_absent }} absent / {{ $row->days_late }} late">
                                    @if ($row->days_present > 0)
                                        <div class="as-mini__seg present" style="flex-basis: {{ ($row->days_present / $total) * 100 }}%;">
                                        </div>
                                    @endif
                                    @if ($row->days_absent > 0)
                                        <div class="as-mini__seg absent" style="flex-basis: {{ ($row->days_absent / $total) * 100 }}%;">
                                        </div>
                                    @endif
                                    @if ($row->days_late > 0)
                                        <div class="as-mini__seg late" style="flex-basis: {{ ($row->days_late / $total) * 100 }}%;"></div>
                                    @endif
                                    @if ($row->days_excused > 0)
                                        <div class="as-mini__seg excused" style="flex-basis: {{ ($row->days_excused / $total) * 100 }}%;">
                                        </div>
                                    @endif
                                </div>

                                <span class="as-pct {{ $pctClass }}" style="min-width: 60px; justify-content: center;">
                                    {{ $pct }}%
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

    @endif

@endsection