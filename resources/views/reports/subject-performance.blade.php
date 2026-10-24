@extends('layouts.app')

@section('title', 'Subject Performance')
@section('page-title', 'Subject Performance')

@section('styles')
    <style>
        .sp-controls {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 18px;
            flex-wrap: wrap;
        }

        .sp-select {
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

        .sp-select:focus { border-color: var(--pts-green); }

        .sp-pill {
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

        .sp-pill:hover {
            border-color: var(--pts-green);
            color: var(--pts-green-dark);
        }

        .sp-pill.active {
            background: var(--pts-green);
            color: #fff;
            border-color: var(--pts-green);
        }

        .sp-spacer { flex: 1; }

        .sp-actions { display: flex; gap: 8px; }

        .sp-btn {
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

        .sp-btn:hover {
            border-color: var(--pts-green);
            color: var(--pts-green-dark);
        }

        .sp-btn.primary {
            background: var(--pts-black);
            color: #fff;
            border-color: var(--pts-black);
        }

        .sp-btn.primary:hover {
            background: var(--pts-green-dark);
            border-color: var(--pts-green-dark);
            color: #fff;
        }

        /* Summary KPIs */
        .sp-summary {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }

        .sp-summary__card {
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 12px;
            padding: 18px;
        }

        .sp-summary__label {
            font-size: 10.5px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--pts-grey);
            font-weight: 700;
            margin-bottom: 8px;
        }

        .sp-summary__value {
            font-size: 22px;
            font-weight: 800;
            color: #111827;
            line-height: 1;
        }

        .sp-summary__sub {
            font-size: 12px;
            color: var(--pts-grey);
            margin-top: 4px;
        }

        /* Table */
        .sp-table-wrap {
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 14px;
            overflow: hidden;
        }

        .sp-table {
            width: 100%;
            border-collapse: collapse;
        }

        .sp-table thead th {
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

        .sp-table thead th:first-child {
            text-align: left;
        }

        .sp-table tbody td {
            padding: 14px 14px;
            font-size: 13px;
            color: #374151;
            text-align: center;
            border-bottom: 1px solid var(--pts-grey-light);
        }

        .sp-table tbody td:first-child {
            text-align: left;
            font-weight: 700;
            color: #111827;
            font-size: 13.5px;
        }

        .sp-table tbody tr:last-child td { border-bottom: none; }
        .sp-table tbody tr:hover { background: #FAFBFC; }

        /* Status coloring on subject name */
        .sp-table tbody tr.row-critical td:first-child {
            border-left: 4px solid #DC2626;
            padding-left: 10px;
        }
        .sp-table tbody tr.row-warning td:first-child {
            border-left: 4px solid #D97706;
            padding-left: 10px;
        }
        .sp-table tbody tr.row-good td:first-child {
            border-left: 4px solid #16A34A;
            padding-left: 10px;
        }

        /* Mini distribution bar */
        .dist-mini {
            display: flex;
            height: 16px;
            border-radius: 4px;
            overflow: hidden;
            background: var(--pts-bg);
            width: 100%;
            max-width: 180px;
            margin: 0 auto;
        }

        .dist-mini__seg {
            height: 100%;
            min-width: 0;
        }

        .dist-mini__seg.a { background: #16A34A; }
        .dist-mini__seg.b { background: #2563EB; }
        .dist-mini__seg.c { background: #D97706; }
        .dist-mini__seg.d { background: #DC2626; }
        .dist-mini__seg.e { background: #991B1B; }
        .dist-mini__seg.f { background: #7F1D1D; }

        /* Score cells */
        .sp-mean {
            font-size: 14px;
            font-weight: 800;
            color: #111827;
        }

        .sp-pass {
            font-weight: 700;
        }
        .sp-pass.high { color: #16A34A; }
        .sp-pass.mid { color: #D97706; }
        .sp-pass.low { color: #DC2626; }

        /* Delta */
        .sp-delta {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
        }

        .sp-delta.up { background: #DCFCE7; color: #166534; }
        .sp-delta.down { background: #FEE2E2; color: #991B1B; }
        .sp-delta.flat { background: #F3F4F6; color: #6B7280; }
        .sp-delta.none { background: #F3F4F6; color: #9CA3AF; }

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
            .sp-summary { grid-template-columns: repeat(2, 1fr); }
        }

        /* Print */
        @media print {
            .pts-sidebar,
            .pts-topbar,
            .pts-footer,
            .sp-controls,
            .sp-actions,
            .pts-flash { display: none !important; }

            .pts-main { margin-left: 0 !important; }
            .pts-content { padding: 0 !important; }

            .print-header { display: block !important; }

            .sp-summary__card,
            .sp-table-wrap {
                box-shadow: none !important;
                border: 1px solid #333 !important;
            }

            .sp-table tbody tr,
            .sp-summary__card,
            .dist-mini__seg,
            .sp-delta {
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
            Subject Performance — {{ $selectedClass?->name ?? 'All Classes' }}
        </div>
        <div class="print-header__meta">
            {{ $currentTerm->name ?? 'Term' }}
            @if ($currentYear) · {{ $currentYear->label ?? '' }} @endif
            · Generated {{ now()->format('d M Y H:i') }}
        </div>
    </div>

    @if (empty($rows))
        <div class="empty-state">
            <div class="empty-state__icon"><i class="fa-solid fa-book-open"></i></div>
            <div class="empty-state__title">No subject data yet</div>
            <div class="empty-state__desc">
                No subject results have been computed for the current term. Enter marks first, then check back.
            </div>
        </div>
    @else

        {{-- Controls --}}
        <div class="sp-controls">
            @if ($allTerms->count() > 0)
                <form method="GET" action="{{ route('reports.subject-performance') }}" id="termForm">
                    <input type="hidden" name="class_id" value="{{ $selectedClassId }}">
                    <select name="term" class="sp-select" onchange="document.getElementById('termForm').submit()">
                        @foreach ($allTerms as $t)
                            <option value="{{ $t->id }}" @selected($currentTermId == $t->id)>
                                {{ $t->name }} · {{ $t->academicYear?->label ?? '' }}
                            </option>
                        @endforeach
                    </select>
                </form>
            @endif

            <a href="{{ route('reports.subject-performance', ['class_id' => 'all', 'term' => $currentTermId]) }}"
               class="sp-pill {{ $selectedClassId === 'all' ? 'active' : '' }}">
                All Classes
            </a>

            @foreach ($classes as $class)
                <a href="{{ route('reports.subject-performance', ['class_id' => $class->id, 'term' => $currentTermId]) }}"
                   class="sp-pill {{ $selectedClassId == $class->id ? 'active' : '' }}">
                    {{ $class->name }}
                </a>
            @endforeach

            <div class="sp-spacer"></div>

            <div class="sp-actions">
                <button type="button" onclick="window.print()" class="sp-btn primary">
                    <i class="fa-solid fa-print"></i> Print
                </button>
            </div>
        </div>

        {{-- Summary --}}
        @if ($schoolStats)
            <div class="sp-summary">
                <div class="sp-summary__card">
                    <div class="sp-summary__label">Subjects analysed</div>
                    <div class="sp-summary__value">{{ $schoolStats['subject_count'] }}</div>
                    <div class="sp-summary__sub">{{ $currentTerm->name ?? '' }} · {{ $selectedClass?->name ?? 'All classes' }}</div>
                </div>
                <div class="sp-summary__card">
                    <div class="sp-summary__label">Overall mean</div>
                    <div class="sp-summary__value">{{ $schoolStats['overall_mean'] }}%</div>
                    <div class="sp-summary__sub">Average across all subjects</div>
                </div>
                <div class="sp-summary__card">
                    <div class="sp-summary__label">Overall pass rate</div>
                    <div class="sp-summary__value">{{ $schoolStats['overall_pass_rate'] }}%</div>
                    <div class="sp-summary__sub">Across all subject results</div>
                </div>
                <div class="sp-summary__card">
                    <div class="sp-summary__label">Best subject</div>
                    <div class="sp-summary__value" style="font-size: 18px;">{{ $schoolStats['best_subject'] }}</div>
                    <div class="sp-summary__sub">Worst: {{ $schoolStats['worst_subject'] }}</div>
                </div>
            </div>
        @endif

        {{-- Table --}}
        <div class="sp-table-wrap">
            <table class="sp-table">
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Students</th>
                        <th>Mean</th>
                        <th>Median</th>
                        <th>Range</th>
                        <th>Pass rate</th>
                        <th>Delta</th>
                        <th>Distribution</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $row)
                        @php
                            if ($row['mean'] < 50) {
                                $rowClass = 'row-critical';
                            } elseif ($row['mean'] < 65) {
                                $rowClass = 'row-warning';
                            } else {
                                $rowClass = 'row-good';
                            }

                            $passClass = $row['pass_rate'] >= 80 ? 'high' : ($row['pass_rate'] >= 60 ? 'mid' : 'low');

                            $deltaClass = 'none';
                            $deltaIcon = '';
                            if ($row['delta'] !== null) {
                                if ($row['delta'] > 0.1) {
                                    $deltaClass = 'up';
                                    $deltaIcon = '▲';
                                } elseif ($row['delta'] < -0.1) {
                                    $deltaClass = 'down';
                                    $deltaIcon = '▼';
                                } else {
                                    $deltaClass = 'flat';
                                    $deltaIcon = '—';
                                }
                            }

                            // For the mini distribution bar
                            $total = $row['count'];
                            $dist = $row['distribution'];
                        @endphp
                        <tr class="{{ $rowClass }}">
                            <td>{{ $row['subject']->name }}</td>
                            <td>{{ $total }}</td>
                            <td><span class="sp-mean">{{ $row['mean'] }}%</span></td>
                            <td>{{ $row['median'] }}%</td>
                            <td>{{ $row['min'] }}–{{ $row['max'] }}%</td>
                            <td><span class="sp-pass {{ $passClass }}">{{ $row['pass_rate'] }}%</span></td>
                            <td>
                                @if ($row['delta'] !== null)
                                    <span class="sp-delta {{ $deltaClass }}">
                                        {{ $deltaIcon }} {{ number_format(abs($row['delta']), 1) }}
                                    </span>
                                @else
                                    <span class="sp-delta none">—</span>
                                @endif
                            </td>
                            <td>
                                <div class="dist-mini">
                                    @foreach (['A', 'B', 'C', 'D', 'E', 'F'] as $letter)
                                        @php
                                            $n = $dist[$letter] ?? 0;
                                            $pct = $total > 0 ? ($n / $total) * 100 : 0;
                                        @endphp
                                        @if ($n > 0)
                                            <div class="dist-mini__seg {{ strtolower($letter) }}"
                                                 style="flex-basis: {{ $pct }}%;"
                                                 title="{{ $letter }}: {{ $n }}"></div>
                                        @endif
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    @endif

@endsection