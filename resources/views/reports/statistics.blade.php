@extends('layouts.app')

@section('title', 'Statistics')
@section('page-title', 'Statistics')

@section('styles')
    <style>
        .st-controls {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 18px;
            flex-wrap: wrap;
        }

        .st-select {
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

        .st-select:focus { border-color: var(--pts-green); }

        .st-pill {
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

        .st-pill:hover {
            border-color: var(--pts-green);
            color: var(--pts-green-dark);
        }

        .st-pill.active {
            background: var(--pts-green);
            color: #fff;
            border-color: var(--pts-green);
        }

        .st-spacer { flex: 1; }

        .st-actions { display: flex; gap: 8px; }

        .st-btn {
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

        .st-btn:hover {
            border-color: var(--pts-green);
            color: var(--pts-green-dark);
        }

        .st-btn.primary {
            background: var(--pts-black);
            color: #fff;
            border-color: var(--pts-black);
        }

        .st-btn.primary:hover {
            background: var(--pts-green-dark);
            border-color: var(--pts-green-dark);
            color: #fff;
        }

        /* KPI grid */
        .st-kpis {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }

        .st-kpi {
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 12px;
            padding: 18px 16px;
            text-align: center;
        }

        .st-kpi__label {
            font-size: 10.5px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--pts-grey);
            font-weight: 700;
            margin-bottom: 8px;
        }

        .st-kpi__value {
            font-size: 22px;
            font-weight: 800;
            color: #111827;
            line-height: 1;
        }

        .st-kpi__value.green { color: #16A34A; }
        .st-kpi__value.red { color: #DC2626; }
        .st-kpi__value.blue { color: #2563EB; }

        /* Two-column grid */
        .st-grid {
            display: grid;
            grid-template-columns: 1.4fr 1fr;
            gap: 18px;
            margin-bottom: 18px;
        }

        .st-panel {
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 14px;
            padding: 22px;
        }

        .st-panel__title {
            font-size: 15px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 18px;
        }

        /* Distribution bars */
        .st-band {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }

        .st-band:last-child { margin-bottom: 0; }

        .st-band__label {
            width: 70px;
            font-size: 12.5px;
            font-weight: 700;
            color: #374151;
            flex-shrink: 0;
        }

        .st-band__track {
            flex: 1;
            height: 24px;
            background: var(--pts-bg);
            border-radius: 6px;
            overflow: hidden;
            position: relative;
        }

        .st-band__fill {
            height: 100%;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding-right: 8px;
            color: #fff;
            font-size: 11px;
            font-weight: 800;
            min-width: 30px;
            transition: width 0.3s ease;
        }

        .st-band__meta {
            width: 80px;
            text-align: right;
            font-size: 12px;
            color: var(--pts-grey);
            flex-shrink: 0;
        }

        .st-band__meta strong {
            color: #111827;
            font-weight: 700;
        }

        /* Student list */
        .st-student {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 0;
            border-bottom: 1px solid var(--pts-grey-light);
        }

        .st-student:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .st-student__rank {
            width: 24px;
            height: 24px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 800;
            background: var(--pts-bg);
            color: var(--pts-grey);
            flex-shrink: 0;
        }

        .st-student__rank.top {
            background: #DCFCE7;
            color: #166534;
        }

        .st-student__rank.bottom {
            background: #FEE2E2;
            color: #991B1B;
        }

        .st-student__avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--pts-black);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .st-student__name {
            font-size: 13px;
            font-weight: 600;
            color: #111827;
        }

        .st-student__meta {
            font-size: 11px;
            color: var(--pts-grey);
            margin-top: 1px;
        }

        .st-student__score {
            margin-left: auto;
            font-size: 13px;
            font-weight: 800;
            padding: 3px 9px;
            border-radius: 20px;
            background: var(--pts-green-light);
            color: var(--pts-green-dark);
            flex-shrink: 0;
        }

        .st-student__score.low {
            background: #FEE2E2;
            color: #991B1B;
        }

        /* Subject stats table */
        .st-table-wrap {
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 14px;
            overflow: hidden;
        }

        .st-table {
            width: 100%;
            border-collapse: collapse;
        }

        .st-table thead th {
            text-align: center;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--pts-grey);
            font-weight: 700;
            padding: 14px 14px;
            background: var(--pts-bg);
            border-bottom: 1px solid var(--pts-grey-light);
        }

        .st-table thead th:first-child { text-align: left; }

        .st-table tbody td {
            padding: 14px 14px;
            font-size: 13px;
            color: #374151;
            text-align: center;
            border-bottom: 1px solid var(--pts-grey-light);
        }

        .st-table tbody td:first-child {
            text-align: left;
            font-weight: 700;
            color: #111827;
        }

        .st-table tbody tr:last-child td { border-bottom: none; }
        .st-table tbody tr:hover { background: #FAFBFC; }

        .st-table .mean-cell {
            font-weight: 800;
            color: #111827;
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

        /* Print header */
        .print-header {
            display: none;
            text-align: center;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 2px solid #000;
        }
        .print-header__school { font-size: 20px; font-weight: 800; color: #000; }
        .print-header__title { font-size: 14px; font-weight: 700; color: #333; margin-top: 4px; }
        .print-header__meta { font-size: 11px; color: #555; margin-top: 4px; }

        @media (max-width: 900px) {
            .st-kpis { grid-template-columns: repeat(3, 1fr); }
            .st-grid { grid-template-columns: 1fr; }
        }

        /* Print */
        @media print {
            .pts-sidebar,
            .pts-topbar,
            .pts-footer,
            .st-controls,
            .st-actions,
            .pts-flash { display: none !important; }

            .pts-main { margin-left: 0 !important; }
            .pts-content { padding: 0 !important; }

            .print-header { display: block !important; }

            .st-kpi,
            .st-panel,
            .st-table-wrap {
                box-shadow: none !important;
                border: 1px solid #333 !important;
                page-break-inside: avoid;
            }

            .st-band__fill,
            .st-student__rank,
            .st-student__avatar,
            .st-student__score {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            body { background: #fff !important; }

            @page {
                size: A4 portrait;
                margin: 10mm;
            }
        }
    </style>
@endsection

@section('content')

    {{-- Print header --}}
    <div class="print-header">
        <div class="print-header__school">{{ $school->name ?? 'School' }}</div>
        <div class="print-header__title">
            Statistics — {{ $selectedClass?->name ?? 'All Classes' }}
        </div>
        <div class="print-header__meta">
            {{ $currentTerm->name ?? 'Term' }}
            @if ($currentYear) · {{ $currentYear->label ?? '' }} @endif
            · Generated {{ now()->format('d M Y H:i') }}
        </div>
    </div>

    @if ($overall['count'] === 0)
        <div class="empty-state">
            <div class="empty-state__icon"><i class="fa-solid fa-calculator"></i></div>
            <div class="empty-state__title">No statistics available</div>
            <div class="empty-state__desc">
                No term results have been computed for the current term. Enter marks first, then check back.
            </div>
        </div>
    @else

        {{-- Controls --}}
        <div class="st-controls">
            @if ($allTerms->count() > 0)
                <form method="GET" action="{{ route('reports.statistics') }}" id="termForm">
                    <input type="hidden" name="class_id" value="{{ $selectedClassId }}">
                    <select name="term" class="st-select" onchange="document.getElementById('termForm').submit()">
                        @foreach ($allTerms as $t)
                            <option value="{{ $t->id }}" @selected($currentTermId == $t->id)>
                                {{ $t->name }} · {{ $t->academicYear?->label ?? '' }}
                            </option>
                        @endforeach
                    </select>
                </form>
            @endif

            <a href="{{ route('reports.statistics', ['class_id' => 'all', 'term' => $currentTermId]) }}"
               class="st-pill {{ $selectedClassId === 'all' ? 'active' : '' }}">
                All Classes
            </a>

            @foreach ($classes as $class)
                <a href="{{ route('reports.statistics', ['class_id' => $class->id, 'term' => $currentTermId]) }}"
                   class="st-pill {{ $selectedClassId == $class->id ? 'active' : '' }}">
                    {{ $class->name }}
                </a>
            @endforeach

            <div class="st-spacer"></div>

            <div class="st-actions">
                <button type="button" onclick="window.print()" class="st-btn primary">
                    <i class="fa-solid fa-print"></i> Print
                </button>
            </div>
        </div>

        {{-- KPI row --}}
        <div class="st-kpis">
            <div class="st-kpi">
                <div class="st-kpi__label">Students</div>
                <div class="st-kpi__value">{{ $overall['count'] }}</div>
            </div>
            <div class="st-kpi">
                <div class="st-kpi__label">Mean</div>
                <div class="st-kpi__value blue">{{ $overall['mean'] }}%</div>
            </div>
            <div class="st-kpi">
                <div class="st-kpi__label">Median</div>
                <div class="st-kpi__value">{{ $overall['median'] }}%</div>
            </div>
            <div class="st-kpi">
                <div class="st-kpi__label">Std Deviation</div>
                <div class="st-kpi__value">{{ $overall['stddev'] }}</div>
            </div>
            <div class="st-kpi">
                <div class="st-kpi__label">Highest</div>
                <div class="st-kpi__value green">{{ $overall['max'] }}%</div>
            </div>
            <div class="st-kpi">
                <div class="st-kpi__label">Lowest</div>
                <div class="st-kpi__value red">{{ $overall['min'] }}%</div>
            </div>
        </div>

        <div class="st-grid">

            {{-- Distribution --}}
            <div class="st-panel">
                <div class="st-panel__title">Score distribution</div>

                @foreach ($bands as $band)
                    <div class="st-band">
                        <div class="st-band__label">{{ $band['label'] }}</div>
                        <div class="st-band__track">
                            @if ($band['pct'] > 0)
                                <div class="st-band__fill" style="width: {{ $band['pct'] }}%; background: {{ $band['color'] }};">
                                    {{ $band['count'] }}
                                </div>
                            @endif
                        </div>
                        <div class="st-band__meta">
                            <strong>{{ $band['count'] }}</strong> ({{ $band['pct'] }}%)
                        </div>
                    </div>
                @endforeach

                <div style="margin-top: 18px; padding-top: 18px; border-top: 1px solid var(--pts-grey-light); display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px;">
                    <div>
                        <div class="st-kpi__label">Q1 (25th)</div>
                        <div style="font-size: 16px; font-weight: 800; color: #111827;">{{ $overall['q1'] }}%</div>
                    </div>
                    <div>
                        <div class="st-kpi__label">Median (50th)</div>
                        <div style="font-size: 16px; font-weight: 800; color: #111827;">{{ $overall['median'] }}%</div>
                    </div>
                    <div>
                        <div class="st-kpi__label">Q3 (75th)</div>
                        <div style="font-size: 16px; font-weight: 800; color: #111827;">{{ $overall['q3'] }}%</div>
                    </div>
                </div>
            </div>

            {{-- Top / Bottom --}}
            <div class="st-panel">
                <div class="st-panel__title">Top performers</div>
                @foreach ($top as $i => $tr)
                    @php $st = $tr->student ?? null; @endphp
                    @if ($st)
                        <div class="st-student">
                            <div class="st-student__rank top">{{ $i + 1 }}</div>
                            <div class="st-student__avatar">
                                {{ strtoupper(substr($st->first_name, 0, 1) . substr($st->last_name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="st-student__name">{{ $st->full_name }}</div>
                                <div class="st-student__meta">{{ $st->student_number }}</div>
                            </div>
                            <div class="st-student__score">{{ number_format($tr->overall_percentage, 1) }}%</div>
                        </div>
                    @endif
                @endforeach

                <div class="st-panel__title" style="margin-top: 20px; padding-top: 18px; border-top: 1px solid var(--pts-grey-light);">Needs attention</div>
                @foreach ($bottom as $i => $tr)
                    @php $st = $tr->student ?? null; @endphp
                    @if ($st)
                        <div class="st-student">
                            <div class="st-student__rank bottom">{{ $i + 1 }}</div>
                            <div class="st-student__avatar">
                                {{ strtoupper(substr($st->first_name, 0, 1) . substr($st->last_name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="st-student__name">{{ $st->full_name }}</div>
                                <div class="st-student__meta">{{ $st->student_number }}</div>
                            </div>
                            <div class="st-student__score low">{{ number_format($tr->overall_percentage, 1) }}%</div>
                        </div>
                    @endif
                @endforeach
            </div>

        </div>

        {{-- Subject statistics table --}}
        <div class="st-table-wrap">
            <table class="st-table">
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Students</th>
                        <th>Mean</th>
                        <th>Median</th>
                        <th>Std Dev</th>
                        <th>Min</th>
                        <th>Max</th>
                        <th>Pass Rate</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($subjectStats as $stat)
                        <tr>
                            <td>{{ $stat['subject'] }}</td>
                            <td>{{ $stat['count'] }}</td>
                            <td class="mean-cell">{{ $stat['mean'] }}%</td>
                            <td>{{ $stat['median'] }}%</td>
                            <td>{{ $stat['stddev'] }}</td>
                            <td>{{ $stat['min'] }}%</td>
                            <td>{{ $stat['max'] }}%</td>
                            <td>{{ $stat['pass_rate'] }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    @endif

@endsection