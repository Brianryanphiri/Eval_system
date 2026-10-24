@extends('layouts.app')

@section('title', 'Broadsheet')
@section('page-title', 'Broadsheet')

@section('styles')
    <style>
        /* ---------- Controls ---------- */
        .bs-controls {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 18px;
            flex-wrap: wrap;
        }

        .bs-select {
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

        .bs-select:focus { border-color: var(--pts-green); }

        .bs-pill {
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

        .bs-pill:hover {
            border-color: var(--pts-green);
            color: var(--pts-green-dark);
        }

        .bs-pill.active {
            background: var(--pts-green);
            color: #fff;
            border-color: var(--pts-green);
        }

        .bs-spacer { flex: 1; }

        .bs-actions {
            display: flex;
            gap: 8px;
        }

        .bs-btn {
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

        .bs-btn:hover {
            border-color: var(--pts-green);
            color: var(--pts-green-dark);
        }

        .bs-btn.primary {
            background: var(--pts-black);
            color: #fff;
            border-color: var(--pts-black);
        }

        .bs-btn.primary:hover {
            background: var(--pts-green-dark);
            border-color: var(--pts-green-dark);
            color: #fff;
        }

        .bs-btn i { font-size: 11px; }

        /* ---------- Sheet ---------- */
        .sheet-wrap {
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 14px;
            overflow: auto;
            max-height: calc(100vh - 250px);
        }

        .sheet {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 12.5px;
        }

        .sheet thead th {
            position: sticky;
            top: 0;
            background: var(--pts-bg);
            border-bottom: 1px solid var(--pts-grey-light);
            padding: 10px 12px;
            text-align: center;
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--pts-grey);
            white-space: nowrap;
            z-index: 2;
        }

        .sheet thead th a {
            color: var(--pts-grey);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .sheet thead th a:hover { color: var(--pts-green-dark); }

        .sheet thead th:first-child {
            text-align: left;
            left: 0;
            z-index: 3;
            position: sticky;
        }

        .sheet tbody td {
            padding: 9px 12px;
            border-bottom: 1px solid var(--pts-grey-light);
            text-align: center;
            color: #374151;
            white-space: nowrap;
            transition: background 0.1s;
        }

        .sheet tbody td:first-child {
            text-align: left;
            font-weight: 600;
            color: #111827;
            position: sticky;
            left: 0;
            background: var(--pts-white);
            z-index: 1;
            border-right: 1px solid var(--pts-grey-light);
        }

        .sheet tbody tr:hover td { background: #FAFBFC; }
        .sheet tbody tr:hover td:first-child { background: #FAFBFC; }
        .sheet tbody tr:last-child td { border-bottom: none; }

        .sheet tbody tr.row-fail td:first-child {
            border-left: 3px solid #DC2626;
            padding-left: 9px;
        }

        /* ---------- Cells ---------- */
        .rank-cell {
            color: var(--pts-grey);
            font-weight: 700;
            font-size: 11px;
        }

        .pos {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 24px;
            height: 24px;
            padding: 0 6px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 800;
        }

        .pos.top-3 {
            background: var(--pts-green-light);
            color: var(--pts-green-dark);
        }

        .cell-stack {
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            gap: 1px;
            min-width: 46px;
        }

        .mark {
            display: inline-block;
            min-width: 40px;
            padding: 3px 7px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 12px;
        }

        .mark.pass-a { background: #DCFCE7; color: #166534; }
        .mark.pass-b { background: #DBEAFE; color: #1E40AF; }
        .mark.pass-c { background: #FEF3C7; color: #92400E; }
        .mark.pass-d { background: #FFE4E6; color: #9F1239; }
        .mark.pass-e { background: #FEE2E2; color: #991B1B; }
        .mark.fail   { background: #FEE2E2; color: #991B1B; }

        .delta {
            font-size: 10px;
            font-weight: 700;
            line-height: 1.2;
        }
        .delta.up   { color: #16A34A; }
        .delta.down { color: #DC2626; }
        .delta.flat { color: var(--pts-grey); }

        .cell-meta {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 0.03em;
            text-transform: uppercase;
        }

        .cell-meta.above { color: #16A34A; }
        .cell-meta.below { color: #DC2626; }

        .avg {
            font-weight: 800;
            color: #111827;
            font-size: 13px;
        }

        /* ---------- Footer rows ---------- */
        .sheet tfoot td {
            padding: 10px 12px;
            background: var(--pts-bg);
            border-top: 2px solid var(--pts-grey-light);
            font-size: 12px;
            text-align: center;
            color: #374151;
            position: sticky;
            bottom: 0;
            z-index: 2;
        }

        .sheet tfoot tr.grade-row td { font-size: 11px; }

        .sheet tfoot tr.avg-row td {
            font-weight: 700;
            color: #111827;
        }

        .sheet tfoot tr td:first-child {
            text-align: left;
            font-weight: 700;
            font-size: 10.5px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--pts-grey);
            position: sticky;
            left: 0;
            z-index: 3;
            border-right: 1px solid var(--pts-grey-light);
        }

        .grade-chip {
            display: inline-block;
            padding: 1px 6px;
            border-radius: 4px;
            font-weight: 700;
            font-size: 10.5px;
            margin: 0 1px;
        }

        .grade-chip.a { background: #DCFCE7; color: #166534; }
        .grade-chip.b { background: #DBEAFE; color: #1E40AF; }
        .grade-chip.c { background: #FEF3C7; color: #92400E; }
        .grade-chip.d { background: #FFE4E6; color: #9F1239; }
        .grade-chip.e,
        .grade-chip.f { background: #FEE2E2; color: #991B1B; }

        /* ---------- Empty state ---------- */
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

        /* ---------- Print-only header + signatures ---------- */
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
            letter-spacing: 0.02em;
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

        .print-signatures {
            display: none;
            margin-top: 30px;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
        }
        .print-signatures__block {
            text-align: center;
            font-size: 11px;
        }
        .print-signatures__line {
            border-top: 1px solid #333;
            margin-bottom: 6px;
            height: 30px;
        }
        .print-signatures__label {
            font-weight: 700;
            color: #333;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-size: 10px;
        }

        /* ---------- Print ---------- */
        @media print {
            .pts-sidebar,
            .pts-topbar,
            .pts-footer,
            .bs-controls,
            .pts-flash { display: none !important; }

            .pts-main { margin-left: 0 !important; }
            .pts-content { padding: 0 !important; }

            .print-header { display: block !important; }
            .print-signatures { display: grid !important; }

            .sheet-wrap {
                max-height: none !important;
                border: none !important;
                border-radius: 0 !important;
                overflow: visible !important;
            }

            .sheet {
                font-size: 11px;
                page-break-inside: auto;
            }

            .sheet thead {
                display: table-header-group;
            }

            .sheet tfoot {
                display: table-footer-group;
            }

            .sheet thead th,
            .sheet tbody td,
            .sheet tfoot td {
                padding: 6px 8px !important;
                font-size: 10.5px !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .sheet tbody tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }

            .mark {
                padding: 3px 6px !important;
                font-size: 10.5px !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .sheet tbody tr:hover td,
            .sheet tbody tr:hover td:first-child {
                background: transparent !important;
            }

            body {
                background: #fff !important;
            }

            @page {
                size: A4 landscape;
                margin: 10mm;
            }
        }
    </style>
@endsection

@section('content')

    {{-- Print-only header (hidden on screen) --}}
    <div class="print-header">
        <div class="print-header__school">{{ $school->name ?? 'School' }}</div>
        <div class="print-header__title">
            Broadsheet — {{ $selectedClass->name ?? '' }}
        </div>
        <div class="print-header__meta">
            {{ $currentTerm->name ?? 'Term' }}
            @if ($currentYear) · {{ $currentYear->label ?? '' }} @endif
            · Generated {{ now()->format('d M Y H:i') }}
        </div>
    </div>

    @if ($classes->isEmpty())
        <div class="empty-state">
            <div class="empty-state__icon">
                <i class="fa-solid fa-table-cells"></i>
            </div>
            <div class="empty-state__title">No classes yet</div>
            <div class="empty-state__desc">Create a class and add students to see the broadsheet.</div>
        </div>
    @else

            {{-- Controls --}}
            <div class="bs-controls">

                {{-- Term selector --}}
                @if (isset($allTerms) && $allTerms->count() > 0)
                    <form method="GET" action="{{ route('reports.broadsheet') }}" id="termForm">
                        <input type="hidden" name="class_id" value="{{ $selectedClassId }}">
                        <input type="hidden" name="sort" value="{{ $sort ?? 'position' }}">
                        <input type="hidden" name="dir" value="{{ $sortDir ?? 'asc' }}">
                        <select name="term" class="bs-select" onchange="document.getElementById('termForm').submit()">
                            @foreach ($allTerms as $t)
                                <option value="{{ $t->id }}" @selected(($currentTermId ?? 0) == $t->id)>
                                    {{ $t->name }} · {{ $t->academicYear?->label ?? '' }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                @endif

                {{-- Class pills --}}
                @foreach ($classes as $class)
                            <a href="{{ route('reports.broadsheet', [
                        'class_id' => $class->id,
                        'term' => $currentTermId ?? null,
                        'sort' => $sort ?? 'position',
                        'dir' => $sortDir ?? 'asc',
                    ]) }}"
                               class="bs-pill {{ $selectedClassId == $class->id ? 'active' : '' }}">
                                {{ $class->name }}
                            </a>
                @endforeach

                <div class="bs-spacer"></div>

                {{-- Actions --}}
                <div class="bs-actions">
                    <a href="{{ route('reports.broadsheet.export', [
            'class_id' => $selectedClassId,
            'term' => $currentTermId ?? null,
        ]) }}" class="bs-btn">
                        <i class="fa-solid fa-file-excel"></i> Excel
                    </a>
                    <button type="button" onclick="window.print()" class="bs-btn primary">
                        <i class="fa-solid fa-print"></i> Print
                    </button>
                </div>
            </div>

            {{-- Sheet --}}
            @if (empty($rows))
                <div class="empty-state">
                    <div class="empty-state__icon">
                        <i class="fa-solid fa-inbox"></i>
                    </div>
                    <div class="empty-state__title">No marks yet</div>
                    <div class="empty-state__desc">
                        @if ($selectedClass)
                            No subject results have been entered for <strong>{{ $selectedClass->name }}</strong>
                            in <strong>{{ $currentTerm->name ?? 'this term' }}</strong>.
                        @else
                            Select a class above.
                        @endif
                    </div>
                </div>
            @else
                <div class="sheet-wrap">
                    <table class="sheet">
                        <thead>
                            <tr>
                                <th>
                                    <a href="{{ route('reports.broadsheet', array_merge(request()->query(), ['sort' => 'name', 'dir' => 'asc'])) }}">
                                        Student @if (($sort ?? '') === 'name')<i class="fa-solid fa-arrow-down-long"></i>@endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ route('reports.broadsheet', array_merge(request()->query(), ['sort' => 'position', 'dir' => 'asc'])) }}">
                                        Pos @if (($sort ?? '') === 'position')<i class="fa-solid fa-arrow-down-long"></i>@endif
                                    </a>
                                </th>
                                @foreach ($subjects as $subject)
                                    <th>
                                        <a href="{{ route('reports.broadsheet', array_merge(request()->query(), ['sort' => $subject->id, 'dir' => 'desc'])) }}">
                                            {{ $subject->name }}
                                            @if (($sort ?? '') == $subject->id)<i class="fa-solid fa-arrow-down-long"></i>@endif
                                        </a>
                                    </th>
                                @endforeach
                                <th>
                                    <a href="{{ route('reports.broadsheet', array_merge(request()->query(), ['sort' => 'total', 'dir' => 'desc'])) }}">
                                        Total @if (($sort ?? '') === 'total')<i class="fa-solid fa-arrow-down-long"></i>@endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ route('reports.broadsheet', array_merge(request()->query(), ['sort' => 'average', 'dir' => 'desc'])) }}">
                                        Average @if (($sort ?? '') === 'average')<i class="fa-solid fa-arrow-down-long"></i>@endif
                                    </a>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rows as $row)
                                <tr class="{{ ($row['failed_count'] ?? 0) >= 2 ? 'row-fail' : '' }}">
                                    <td>{{ $row['student']->full_name }}</td>
                                    <td class="rank-cell">
                                        @if ($row['position'])
                                            <span class="pos {{ $row['position'] <= 3 ? 'top-3' : '' }}">
                                                {{ $row['position'] }}
                                            </span>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    @foreach ($subjects as $subject)
                                        @php $cell = $row['subjects'][$subject->id] ?? null; @endphp
                                        <td>
                                            @if ($cell)
                                                @php
                                                    $gradeClass = match ($cell['grade']) {
                                                        'A' => 'pass-a',
                                                        'B' => 'pass-b',
                                                        'C' => 'pass-c',
                                                        'D' => 'pass-d',
                                                        'E' => 'pass-e',
                                                        default => 'fail',
                                                    };
                                                    $delta = $cell['delta'] ?? null;
                                                    $deltaClass = $delta === null
                                                        ? 'flat'
                                                        : ($delta > 0 ? 'up' : ($delta < 0 ? 'down' : 'flat'));
                                                @endphp
                                                <span class="cell-stack">
                                                    <span class="mark {{ $gradeClass }}">
                                                        {{ number_format($cell['percentage'], 0) }}
                                                    </span>
                                                    @if ($delta !== null)
                                                        <span class="delta {{ $deltaClass }}">
                                                            @if ($delta > 0)
                                                                ▲ {{ number_format(abs($delta), 1) }}
                                                            @elseif ($delta < 0)
                                                                ▼ {{ number_format(abs($delta), 1) }}
                                                            @else
                                                                — 0
                                                            @endif
                                                        </span>
                                                    @endif
                                                    @if (($cell['above_avg'] ?? null) !== null)
                                                        <span class="cell-meta {{ $cell['above_avg'] ? 'above' : 'below' }}">
                                                            @if ($cell['above_avg'])
                                                                <i class="fa-solid fa-arrow-up"></i> avg
                                                            @else
                                                                <i class="fa-solid fa-arrow-down"></i> avg
                                                            @endif
                                                        </span>
                                                    @endif
                                                </span>
                                            @else
                                                <span style="color: var(--pts-grey-light);">—</span>
                                            @endif
                                        </td>
                                    @endforeach
                                    <td>{{ number_format($row['total'], 0) }}</td>
                                    <td class="avg">{{ number_format($row['average'], 1) }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                        @if (!empty($gradeSummary) && !empty($classAverages))
                            <tfoot>
                                <tr class="grade-row">
                                    <td>Grade distribution</td>
                                    <td></td>
                                    @foreach ($subjects as $subject)
                                        @php $g = $gradeSummary[$subject->id] ?? null; @endphp
                                        <td>
                                            @if ($g)
                                                @foreach (['A', 'B', 'C', 'D', 'E', 'F'] as $letter)
                                                    @if (($g[$letter] ?? 0) > 0)
                                                        <span class="grade-chip {{ strtolower($letter) }}">
                                                            {{ $letter }}:{{ $g[$letter] }}
                                                        </span>
                                                    @endif
                                                @endforeach
                                            @else
                                                —
                                            @endif
                                        </td>
                                    @endforeach
                                    <td colspan="2"></td>
                                </tr>
                                <tr class="avg-row">
                                    <td>Class average</td>
                                    <td></td>
                                    @foreach ($subjects as $subject)
                                                        <td>
                                                            {{ $classAverages[$subject->id] !== null
                                        ? number_format($classAverages[$subject->id], 1) . '%'
                                        : '—' }}
                                                        </td>
                                    @endforeach
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            @endif

    @endif

    {{-- Print-only signatures (hidden on screen) --}}
    <div class="print-signatures">
        <div class="print-signatures__block">
            <div class="print-signatures__line"></div>
            <div class="print-signatures__label">Class Teacher</div>
        </div>
        <div class="print-signatures__block">
            <div class="print-signatures__line"></div>
            <div class="print-signatures__label">Head Teacher</div>
        </div>
    </div>

@endsection