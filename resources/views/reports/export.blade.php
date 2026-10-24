@extends('layouts.app')

@section('title', 'Export Center')
@section('page-title', 'Export Center')

@section('styles')
    <style>
        .ec-intro {
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 14px;
            padding: 24px;
            margin-bottom: 20px;
        }

        .ec-intro__title {
            font-size: 20px;
            font-weight: 800;
            color: #111827;
            margin-bottom: 8px;
        }

        .ec-intro__sub {
            font-size: 14px;
            color: var(--pts-grey);
            line-height: 1.6;
            max-width: 640px;
        }

        .ec-controls {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }

        .ec-select {
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

        .ec-select:focus { border-color: var(--pts-green); }

        /* Export grid */
        .ec-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 18px;
            margin-bottom: 24px;
        }

        .ec-card {
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 14px;
            padding: 24px;
            display: flex;
            flex-direction: column;
            transition: all 0.15s;
        }

        .ec-card:hover {
            border-color: var(--pts-green);
            box-shadow: 0 4px 16px rgba(122, 193, 66, 0.08);
        }

        .ec-card__icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-bottom: 16px;
            flex-shrink: 0;
        }

        .ec-card__icon.green {
            background: var(--pts-green-light);
            color: var(--pts-green-dark);
        }

        .ec-card__icon.red {
            background: #FEE2E2;
            color: #991B1B;
        }

        .ec-card__icon.blue {
            background: #DBEAFE;
            color: #1E40AF;
        }

        .ec-card__icon.amber {
            background: #FEF3C7;
            color: #92400E;
        }

        .ec-card__title {
            font-size: 16px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 6px;
        }

        .ec-card__desc {
            font-size: 13px;
            color: var(--pts-grey);
            line-height: 1.55;
            flex: 1;
            margin-bottom: 16px;
        }

        .ec-card__meta {
            font-size: 11.5px;
            color: var(--pts-grey);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .ec-card__meta__item {
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .ec-card__meta__item i {
            font-size: 10px;
            color: var(--pts-green-dark);
        }

        .ec-btn {
            padding: 11px 18px;
            background: var(--pts-black);
            color: #fff;
            border: none;
            border-radius: 9px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
            font-family: inherit;
            transition: background 0.15s;
            align-self: flex-start;
        }

        .ec-btn:hover {
            background: var(--pts-green-dark);
        }

        .ec-btn.disabled {
            background: var(--pts-grey-light);
            color: var(--pts-grey);
            cursor: not-allowed;
            pointer-events: none;
        }

        .ec-btn i { font-size: 12px; }

        /* Info block */
        .ec-info {
            background: var(--pts-bg);
            border-radius: 12px;
            padding: 20px 22px;
            font-size: 13px;
            color: #374151;
            line-height: 1.7;
        }

        .ec-info__title {
            font-size: 13px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .ec-info__title i {
            color: var(--pts-green-dark);
        }

        .ec-info ul {
            padding-left: 20px;
            margin: 0;
        }

        .ec-info li {
            margin-bottom: 4px;
        }

        .ec-info li:last-child { margin-bottom: 0; }

        @media (max-width: 900px) {
            .ec-grid { grid-template-columns: 1fr; }
        }
    </style>
@endsection

@section('content')

    {{-- Intro --}}
    <div class="ec-intro">
        <div class="ec-intro__title">Export Center</div>
        <div class="ec-intro__sub">
            Download results, report cards, and broadsheets for offline use, archiving, or sharing with staff. Every export respects your school's data isolation.
        </div>
    </div>

    {{-- Term + class controls --}}
    <div class="ec-controls">
        @if ($allTerms->count() > 0)
            <form method="GET" action="{{ route('reports.export') }}" id="ecTermForm">
                <select name="term" class="ec-select" onchange="document.getElementById('ecTermForm').submit()">
                    @foreach ($allTerms as $t)
                        <option value="{{ $t->id }}" @selected($currentTermId == $t->id)>
                            {{ $t->name }} · {{ $t->academicYear?->label ?? '' }}
                        </option>
                    @endforeach
                </select>
            </form>
        @endif

        <span style="font-size: 12.5px; color: var(--pts-grey);">
            <i class="fa-solid fa-circle-info"></i>
            Select a term. Each export below uses this term.
        </span>
    </div>

    {{-- Export cards --}}
    <div class="ec-grid">

        {{-- Broadsheet XLSX --}}
        @php $firstClass = $classes->first(); @endphp
        <div class="ec-card">
            <div class="ec-card__icon green">
                <i class="fa-solid fa-table-cells"></i>
            </div>
            <div class="ec-card__title">Broadsheet (Excel)</div>
            <div class="ec-card__desc">
                Styled .xlsx workbook for one class — students × subjects grid with marks, averages, and class totals.
            </div>
            <div class="ec-card__meta">
                <span class="ec-card__meta__item">
                    <i class="fa-solid fa-check"></i> Formatted
                </span>
                <span class="ec-card__meta__item">
                    <i class="fa-solid fa-check"></i> Print-ready
                </span>
            </div>
            @if ($firstClass)
                <a href="{{ route('reports.broadsheet.export', ['class_id' => $firstClass->id, 'term' => $currentTermId]) }}"
                   class="ec-btn">
                    <i class="fa-solid fa-download"></i> Download Broadsheet
                </a>
            @else
                <span class="ec-btn disabled">No classes yet</span>
            @endif
        </div>

        {{-- Full Results XLSX --}}
        <div class="ec-card">
            <div class="ec-card__icon blue">
                <i class="fa-solid fa-table-list"></i>
            </div>
            <div class="ec-card__title">Full Results (Excel)</div>
            <div class="ec-card__desc">
                Every student, every subject, every mark in the school for the selected term. One workbook, all data.
            </div>
            <div class="ec-card__meta">
                <span class="ec-card__meta__item">
                    <i class="fa-solid fa-check"></i> School-wide
                </span>
                <span class="ec-card__meta__item">
                    <i class="fa-solid fa-check"></i> Sortable
                </span>
            </div>
            <a href="{{ route('reports.export.full-results-xlsx', ['term' => $currentTermId]) }}" class="ec-btn">
                <i class="fa-solid fa-download"></i> Download Full Results
            </a>
        </div>

        {{-- Class Results CSV --}}
        <div class="ec-card">
            <div class="ec-card__icon amber">
                <i class="fa-solid fa-file-csv"></i>
            </div>
            <div class="ec-card__title">Class Results (CSV)</div>
            <div class="ec-card__desc">
                Simple CSV of one class's results — student, marks, grades, position. Opens in any spreadsheet app.
            </div>
            <div class="ec-card__meta">
                <span class="ec-card__meta__item">
                    <i class="fa-solid fa-check"></i> Excel-friendly
                </span>
                <span class="ec-card__meta__item">
                    <i class="fa-solid fa-check"></i> Universal
                </span>
            </div>
            @if ($firstClass)
                <a href="{{ route('reports.export.results-csv', ['class_id' => $firstClass->id, 'term' => $currentTermId]) }}"
                   class="ec-btn">
                    <i class="fa-solid fa-download"></i> Download CSV
                </a>
            @else
                <span class="ec-btn disabled">No classes yet</span>
            @endif
        </div>

        {{-- Report Cards ZIP --}}
        <div class="ec-card">
            <div class="ec-card__icon red">
                <i class="fa-solid fa-file-pdf"></i>
            </div>
            <div class="ec-card__title">Bulk Report Cards (ZIP)</div>
            <div class="ec-card__desc">
                All PDF report cards for one class bundled into a single ZIP file. Ready to print or email.
            </div>
            <div class="ec-card__meta">
                <span class="ec-card__meta__item">
                    <i class="fa-solid fa-clock"></i> May take 30–60s
                </span>
            </div>
            @if ($firstClass)
                <a href="{{ route('reports.export.report-cards-zip', ['class_id' => $firstClass->id, 'term' => $currentTermId]) }}"
                   class="ec-btn"
                   data-confirm="This will generate a PDF for every student in the class. On large classes, this may take up to a minute. Continue?">
                    <i class="fa-solid fa-download"></i> Download ZIP
                </a>
            @else
                <span class="ec-btn disabled">No classes yet</span>
            @endif
        </div>

    </div>

    {{-- Info panel --}}
    <div class="ec-info">
        <div class="ec-info__title">
            <i class="fa-solid fa-circle-info"></i> About these exports
        </div>
        <ul>
            <li><strong>Excel files (.xlsx)</strong> — formatted with colors, column widths, and formulas. Best for analysis and printing.</li>
            <li><strong>CSV files (.csv)</strong> — plain text, opens in any tool. Best for transferring to another system.</li>
            <li><strong>ZIP of PDFs</strong> — bulk report cards. Best for printing a class or emailing to parents.</li>
            <li><strong>Broadsheet export</strong> — opens the styled broadsheet for the first class in your school. Change the class on the broadsheet page first if you need a different one.</li>
            <li>All exports use the <strong>term currently selected above</strong>. Switch terms before exporting if needed.</li>
        </ul>
    </div>

@endsection