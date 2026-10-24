@extends('layouts.app')

@section('title', 'Grade Analysis')
@section('page-title', 'Grade Analysis')

@section('styles')
    <style>
        .ga-controls {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 18px;
            flex-wrap: wrap;
        }

        .ga-select {
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

        .ga-select:focus {
            border-color: var(--pts-green);
        }

        .ga-pill {
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

        .ga-pill:hover {
            border-color: var(--pts-green);
            color: var(--pts-green-dark);
        }

        .ga-pill.active {
            background: var(--pts-green);
            color: #fff;
            border-color: var(--pts-green);
        }

        .ga-spacer {
            flex: 1;
        }

        .ga-actions {
            display: flex;
            gap: 8px;
        }

        .ga-btn {
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

        .ga-btn:hover {
            border-color: var(--pts-green);
            color: var(--pts-green-dark);
        }

        .ga-btn.primary {
            background: var(--pts-black);
            color: #fff;
            border-color: var(--pts-black);
        }

        .ga-btn.primary:hover {
            background: var(--pts-green-dark);
            border-color: var(--pts-green-dark);
            color: #fff;
        }

        /* KPI cards */
        .ga-kpis {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }

        .ga-kpi {
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 12px;
            padding: 18px 16px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .ga-kpi::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
        }

        .ga-kpi.a::before {
            background: #16A34A;
        }

        .ga-kpi.b::before {
            background: #2563EB;
        }

        .ga-kpi.c::before {
            background: #D97706;
        }

        .ga-kpi.d::before {
            background: #DC2626;
        }

        .ga-kpi.e::before {
            background: #991B1B;
        }

        .ga-kpi.f::before {
            background: #7F1D1D;
        }

        .ga-kpi__count {
            font-size: 26px;
            font-weight: 800;
            color: #111827;
            line-height: 1;
            margin-bottom: 4px;
        }

        .ga-kpi__label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--pts-grey);
            margin-bottom: 6px;
        }

        .ga-kpi__pct {
            font-size: 12px;
            font-weight: 700;
            color: #374151;
        }

        /* Distribution bars */
        .ga-bars {
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 14px;
            padding: 24px;
            margin-bottom: 20px;
        }

        .ga-bars__title {
            font-size: 15px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 20px;
        }

        .ga-bar-row {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 14px;
        }

        .ga-bar-row:last-child {
            margin-bottom: 0;
        }

        .ga-bar-label {
            width: 100px;
            font-size: 12.5px;
            font-weight: 700;
            color: #111827;
            flex-shrink: 0;
        }

        .ga-bar-track {
            flex: 1;
            height: 26px;
            background: var(--pts-bg);
            border-radius: 8px;
            overflow: hidden;
            display: flex;
        }

        .ga-bar-seg {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10.5px;
            font-weight: 800;
            color: #fff;
            min-width: 0;
            transition: flex-basis 0.3s;
        }

        .ga-bar-seg.a {
            background: #16A34A;
        }

        .ga-bar-seg.b {
            background: #2563EB;
        }

        .ga-bar-seg.c {
            background: #D97706;
        }

        .ga-bar-seg.d {
            background: #DC2626;
        }

        .ga-bar-seg.e {
            background: #991B1B;
        }

        .ga-bar-seg.f {
            background: #7F1D1D;
        }

        .ga-bar-meta {
            width: 140px;
            text-align: right;
            font-size: 12px;
            color: var(--pts-grey);
            flex-shrink: 0;
        }

        .ga-bar-meta strong {
            color: #111827;
            font-weight: 700;
        }

        /* Table */
        .ga-table-wrap {
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 14px;
            overflow: hidden;
        }

        .ga-table-head {
            padding: 18px 22px;
            border-bottom: 1px solid var(--pts-grey-light);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .ga-table-title {
            font-size: 15px;
            font-weight: 700;
            color: #111827;
        }

        .ga-table {
            width: 100%;
            border-collapse: collapse;
        }

        .ga-table thead th {
            text-align: center;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--pts-grey);
            font-weight: 700;
            padding: 12px 14px;
            background: var(--pts-bg);
            border-bottom: 1px solid var(--pts-grey-light);
        }

        .ga-table thead th:first-child {
            text-align: left;
        }

        .ga-table tbody td {
            padding: 12px 14px;
            font-size: 13px;
            color: #374151;
            text-align: center;
            border-bottom: 1px solid var(--pts-grey-light);
        }

        .ga-table tbody td:first-child {
            text-align: left;
            font-weight: 600;
            color: #111827;
        }

        .ga-table tbody tr:last-child td {
            border-bottom: none;
        }

        .ga-table tbody tr:hover {
            background: #FAFBFC;
        }

        .ga-cell {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 34px;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
        }

        .ga-cell.a {
            background: #DCFCE7;
            color: #166534;
        }

        .ga-cell.b {
            background: #DBEAFE;
            color: #1E40AF;
        }

        .ga-cell.c {
            background: #FEF3C7;
            color: #92400E;
        }

        .ga-cell.d {
            background: #FFE4E6;
            color: #9F1239;
        }

        .ga-cell.e {
            background: #FEE2E2;
            color: #991B1B;
        }

        .ga-cell.f {
            background: #FEE2E2;
            color: #7F1D1D;
        }

        .ga-cell.zero {
            background: transparent;
            color: var(--pts-grey-light);
        }

        .ga-table tbody tr.total-row td {
            background: var(--pts-bg);
            font-weight: 700;
            color: #111827;
            border-top: 2px solid var(--pts-grey-light);
        }

        /* Empty */
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

        .print-header__school {
            font-size: 20px;
            font-weight: 800;
            color: #000;
        }

        .print-header__title {
            font-size: 14px;
            font-weight: 700;
            color: #333;
            margin-top: 4px;
        }

        .print-header__meta {
            font-size: 11px;
            color: #555;
            margin-top: 4px;
        }

        /* Responsive */
        @media (max-width: 900px) {
            .ga-kpis {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        /* Print */
        @media print {

            .pts-sidebar,
            .pts-topbar,
            .pts-footer,
            .ga-controls,
            .ga-actions,
            .pts-flash {
                display: none !important;
            }

            .pts-main {
                margin-left: 0 !important;
            }

            .pts-content {
                padding: 0 !important;
            }

            .print-header {
                display: block !important;
            }

            .ga-kpi,
            .ga-bars,
            .ga-table-wrap {
                box-shadow: none !important;
                border: 1px solid #333 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .ga-bar-seg,
            .ga-cell,
            .ga-kpi::before {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            body {
                background: #fff !important;
            }

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
            Grade Analysis — {{ $selectedClass?->name ?? 'All Classes' }}
        </div>
        <div class="print-header__meta">
            {{ $currentTerm->name ?? 'Term' }}
            @if ($currentYear) · {{ $currentYear->label ?? '' }} @endif
            · Generated {{ now()->format('d M Y H:i') }}
        </div>
    </div>

    @if ($subjects->isEmpty())
        <div class="empty-state">
            <div class="empty-state__icon"><i class="fa-solid fa-chart-column"></i></div>
            <div class="empty-state__title">No grade data yet</div>
            <div class="empty-state__desc">
                No subject results have been computed for the current term. Enter marks first, then check back.
            </div>
        </div>
    @else

        {{-- Controls --}}
        <div class="ga-controls">
            @if ($allTerms->count() > 0)
                <form method="GET" action="{{ route('reports.grade-analysis') }}" id="termForm">
                    <input type="hidden" name="class_id" value="{{ $selectedClassId }}">
                    <select name="term" class="ga-select" onchange="document.getElementById('termForm').submit()">
                        @foreach ($allTerms as $t)
                            <option value="{{ $t->id }}" @selected($currentTermId == $t->id)>
                                {{ $t->name }} · {{ $t->academicYear?->label ?? '' }}
                            </option>
                        @endforeach
                    </select>
                </form>
            @endif

            <a href="{{ route('reports.grade-analysis', ['class_id' => 'all', 'term' => $currentTermId]) }}"
                class="ga-pill {{ $selectedClassId === 'all' ? 'active' : '' }}">
                All Classes
            </a>

            @foreach ($classes as $class)
                <a href="{{ route('reports.grade-analysis', ['class_id' => $class->id, 'term' => $currentTermId]) }}"
                    class="ga-pill {{ $selectedClassId == $class->id ? 'active' : '' }}">
                    {{ $class->name }}
                </a>
            @endforeach

            <div class="ga-spacer"></div>

            <div class="ga-actions">
                <button type="button" onclick="window.print()" class="ga-btn primary">
                    <i class="fa-solid fa-print"></i> Print
                </button>
            </div>
        </div>

        {{-- KPI cards --}}
        <div class="ga-kpis">
            @foreach (['A', 'B', 'C', 'D', 'E', 'F'] as $letter)
                <div class="ga-kpi {{ strtolower($letter) }}">
                    <div class="ga-kpi__count">{{ $overallDistribution[$letter] }}</div>
                    <div class="ga-kpi__label">Grade {{ $letter }}</div>
                    <div class="ga-kpi__pct">{{ $overallPercentages[$letter] }}%</div>
                </div>
            @endforeach
        </div>

        {{-- Distribution bars per subject --}}
        <div class="ga-bars">
            <div class="ga-bars__title">Grade distribution by subject</div>

            @foreach ($subjects as $subject)
                @php
                    $dist = $subjectDistribution[$subject->id] ?? [];
                    $total = $subjectTotals[$subject->id] ?? 0;
                    $avg = $subjectAverages[$subject->id] ?? 0;
                    $passRate = $subjectPassRates[$subject->id] ?? 0;
                @endphp
                @if ($total > 0)
                    <div class="ga-bar-row">
                        <div class="ga-bar-label">{{ $subject->name }}</div>
                        <div class="ga-bar-track">
                            @foreach (['A', 'B', 'C', 'D', 'E', 'F'] as $letter)
                                @php
                                    $n = $dist[$letter] ?? 0;
                                    $pct = $total > 0 ? ($n / $total) * 100 : 0;
                                @endphp
                                @if ($n > 0)
                                    <div class="ga-bar-seg {{ strtolower($letter) }}" style="flex-basis: {{ $pct }}%;"
                                        title="{{ $letter }}: {{ $n }} ({{ round($pct, 1) }}%)">
                                        @if ($pct >= 5)
                                            {{ $n }}
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        <div class="ga-bar-meta">
                            avg <strong>{{ $avg }}%</strong> · pass <strong>{{ $passRate }}%</strong>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        {{-- Detailed table --}}
        <div class="ga-table-wrap">
            <div class="ga-table-head">
                <div class="ga-table-title">Detailed breakdown</div>
            </div>
            <table class="ga-table">
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>A</th>
                        <th>B</th>
                        <th>C</th>
                        <th>D</th>
                        <th>E</th>
                        <th>F</th>
                        <th>Total</th>
                        <th>Avg</th>
                        <th>Pass rate</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($subjects as $subject)
                        @php
                            $dist = $subjectDistribution[$subject->id] ?? [];
                            $total = $subjectTotals[$subject->id] ?? 0;
                            $avg = $subjectAverages[$subject->id] ?? 0;
                            $passRate = $subjectPassRates[$subject->id] ?? 0;
                        @endphp
                        <tr>
                            <td>{{ $subject->name }}</td>
                            @foreach (['A', 'B', 'C', 'D', 'E', 'F'] as $letter)
                                @php $n = $dist[$letter] ?? 0; @endphp
                                <td>
                                    <span class="ga-cell {{ $n > 0 ? strtolower($letter) : 'zero' }}">
                                        {{ $n }}
                                    </span>
                                </td>
                            @endforeach
                            <td><strong>{{ $total }}</strong></td>
                            <td>{{ $avg }}%</td>
                            <td>{{ $passRate }}%</td>
                        </tr>
                    @endforeach

                    <tr class="total-row">
                        <td>All subjects</td>
                        @foreach (['A', 'B', 'C', 'D', 'E', 'F'] as $letter)
                            <td>{{ $overallDistribution[$letter] }}</td>
                        @endforeach
                        <td>{{ $grandTotal }}</td>
                        <td>—</td>
                        <td>—</td>
                    </tr>
                </tbody>
            </table>
        </div>

    @endif

@endsection