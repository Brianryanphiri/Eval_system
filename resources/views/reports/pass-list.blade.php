@extends('layouts.app')

@section('title', 'Pass List')
@section('page-title', 'Pass List')

@section('styles')
    <style>
        .pl-controls {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 18px;
            flex-wrap: wrap;
        }

        .pl-select {
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

        .pl-select:focus {
            border-color: var(--pts-green);
        }

        .pl-pill {
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

        .pl-pill:hover {
            border-color: var(--pts-green);
            color: var(--pts-green-dark);
        }

        .pl-pill.active {
            background: var(--pts-green);
            color: #fff;
            border-color: var(--pts-green);
        }

        .pl-spacer {
            flex: 1;
        }

        .pl-actions {
            display: flex;
            gap: 8px;
        }

        .pl-btn {
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

        .pl-btn:hover {
            border-color: var(--pts-green);
            color: var(--pts-green-dark);
        }

        .pl-btn.primary {
            background: var(--pts-black);
            color: #fff;
            border-color: var(--pts-black);
        }

        .pl-btn.primary:hover {
            background: var(--pts-green-dark);
            border-color: var(--pts-green-dark);
            color: #fff;
        }

        /* Summary cards */
        .pl-summary {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }

        .pl-summary__card {
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 12px;
            padding: 18px;
            position: relative;
            overflow: hidden;
        }

        .pl-summary__card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
        }

        .pl-summary__card.total::before {
            background: #6B7280;
        }

        .pl-summary__card.passed::before {
            background: #16A34A;
        }

        .pl-summary__card.failed::before {
            background: #DC2626;
        }

        .pl-summary__card.rate::before {
            background: #2563EB;
        }

        .pl-summary__label {
            font-size: 10.5px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--pts-grey);
            font-weight: 700;
            margin-bottom: 8px;
        }

        .pl-summary__value {
            font-size: 24px;
            font-weight: 800;
            color: #111827;
            line-height: 1;
        }

        .pl-summary__sub {
            font-size: 12px;
            color: var(--pts-grey);
            margin-top: 4px;
        }

        /* Grade section */
        .pl-section {
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 14px;
            overflow: hidden;
            margin-bottom: 16px;
        }

        .pl-section__head {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 16px 22px;
            border-bottom: 1px solid var(--pts-grey-light);
            background: var(--pts-bg);
        }

        .pl-section__badge {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: 800;
        }

        .pl-section__badge.a {
            background: #DCFCE7;
            color: #166534;
        }

        .pl-section__badge.b {
            background: #DBEAFE;
            color: #1E40AF;
        }

        .pl-section__badge.c {
            background: #FEF3C7;
            color: #92400E;
        }

        .pl-section__badge.d {
            background: #FFE4E6;
            color: #9F1239;
        }

        .pl-section__badge.e {
            background: #FEE2E2;
            color: #991B1B;
        }

        .pl-section__badge.f {
            background: #FEE2E2;
            color: #7F1D1D;
        }

        .pl-section__title {
            font-size: 15px;
            font-weight: 700;
            color: #111827;
        }

        .pl-section__sub {
            font-size: 12px;
            color: var(--pts-grey);
            margin-top: 2px;
        }

        .pl-section__count {
            margin-left: auto;
            padding: 6px 14px;
            background: #fff;
            border: 1px solid var(--pts-grey-light);
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            color: #374151;
        }

        /* Table */
        .pl-table {
            width: 100%;
            border-collapse: collapse;
        }

        .pl-table thead th {
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--pts-grey);
            font-weight: 700;
            padding: 12px 20px;
            background: #FBFCFD;
            border-bottom: 1px solid var(--pts-grey-light);
        }

        .pl-table thead th.center {
            text-align: center;
        }

        .pl-table thead th.right {
            text-align: right;
        }

        .pl-table tbody td {
            padding: 12px 20px;
            font-size: 13.5px;
            color: #374151;
            border-bottom: 1px solid var(--pts-grey-light);
        }

        .pl-table tbody tr:last-child td {
            border-bottom: none;
        }

        .pl-table tbody tr:hover {
            background: #FAFBFC;
        }

        .pl-table .student-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .pl-avatar {
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

        .pl-name {
            font-size: 13.5px;
            font-weight: 600;
            color: #111827;
        }

        .pl-meta {
            font-size: 11.5px;
            color: var(--pts-grey);
            margin-top: 1px;
        }

        .pl-average {
            font-size: 14px;
            font-weight: 800;
            color: #111827;
        }

        .pl-promo {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .pl-promo.promoted {
            background: #DCFCE7;
            color: #166534;
        }

        .pl-promo.conditional {
            background: #FEF3C7;
            color: #92400E;
        }

        .pl-promo.not_promoted {
            background: #FEE2E2;
            color: #991B1B;
        }

        .pl-promo.pending {
            background: #F3F4F6;
            color: #6B7280;
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

        @media (max-width: 900px) {
            .pl-summary {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        /* Print */
        @media print {

            .pts-sidebar,
            .pts-topbar,
            .pts-footer,
            .pl-controls,
            .pl-actions,
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

            .pl-summary__card,
            .pl-section {
                box-shadow: none !important;
                border: 1px solid #333 !important;
                page-break-inside: avoid;
            }

            .pl-section__badge,
            .pl-avatar,
            .pl-promo {
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
            Pass List — {{ $selectedClass?->name ?? 'All Classes' }}
        </div>
        <div class="print-header__meta">
            {{ $currentTerm->name ?? 'Term' }}
            @if ($currentYear) · {{ $currentYear->label ?? '' }} @endif
            · Pass threshold: {{ $passThreshold }}%
            · Generated {{ now()->format('d M Y H:i') }}
        </div>
    </div>

    @if ($grouped->isEmpty())
        <div class="empty-state">
            <div class="empty-state__icon"><i class="fa-solid fa-circle-check"></i></div>
            <div class="empty-state__title">No pass data yet</div>
            <div class="empty-state__desc">
                No term results have been computed for the current term. Enter marks first, then check back.
            </div>
        </div>
    @else

        {{-- Controls --}}
        <div class="pl-controls">
            @if ($allTerms->count() > 0)
                <form method="GET" action="{{ route('reports.pass-list') }}" id="termForm">
                    <input type="hidden" name="class_id" value="{{ $selectedClassId }}">
                    <input type="hidden" name="pass" value="{{ $passThreshold }}">
                    <select name="term" class="pl-select" onchange="document.getElementById('termForm').submit()">
                        @foreach ($allTerms as $t)
                            <option value="{{ $t->id }}" @selected($currentTermId == $t->id)>
                                {{ $t->name }} · {{ $t->academicYear?->label ?? '' }}
                            </option>
                        @endforeach
                    </select>
                </form>
            @endif

            <a href="{{ route('reports.pass-list', ['class_id' => 'all', 'term' => $currentTermId, 'pass' => $passThreshold]) }}"
                class="pl-pill {{ $selectedClassId === 'all' ? 'active' : '' }}">
                All Classes
            </a>

            @foreach ($classes as $class)
                <a href="{{ route('reports.pass-list', ['class_id' => $class->id, 'term' => $currentTermId, 'pass' => $passThreshold]) }}"
                    class="pl-pill {{ $selectedClassId == $class->id ? 'active' : '' }}">
                    {{ $class->name }}
                </a>
            @endforeach

            <div class="pl-spacer"></div>

            {{-- Threshold selector --}}
            <form method="GET" action="{{ route('reports.pass-list') }}" id="passForm">
                <input type="hidden" name="class_id" value="{{ $selectedClassId }}">
                <input type="hidden" name="term" value="{{ $currentTermId }}">
                <select name="pass" class="pl-select" onchange="document.getElementById('passForm').submit()">
                    <option value="40" @selected($passThreshold == 40)>Pass ≥ 40%</option>
                    <option value="50" @selected($passThreshold == 50)>Pass ≥ 50%</option>
                    <option value="60" @selected($passThreshold == 60)>Pass ≥ 60%</option>
                    <option value="70" @selected($passThreshold == 70)>Pass ≥ 70%</option>
                </select>
            </form>

            <div class="pl-actions">
                <button type="button" onclick="window.print()" class="pl-btn primary">
                    <i class="fa-solid fa-print"></i> Print
                </button>
            </div>
        </div>

        {{-- Summary --}}
        <div class="pl-summary">
            <div class="pl-summary__card total">
                <div class="pl-summary__label">Total Students</div>
                <div class="pl-summary__value">{{ $summary['total'] }}</div>
                <div class="pl-summary__sub">{{ $selectedClass?->name ?? 'All classes' }}</div>
            </div>
            <div class="pl-summary__card passed">
                <div class="pl-summary__label">Passed</div>
                <div class="pl-summary__value">{{ $summary['passed'] }}</div>
                <div class="pl-summary__sub">Threshold {{ $passThreshold }}%</div>
            </div>
            <div class="pl-summary__card failed">
                <div class="pl-summary__label">Failed</div>
                <div class="pl-summary__value">{{ $summary['failed'] }}</div>
                <div class="pl-summary__sub">Below threshold</div>
            </div>
            <div class="pl-summary__card rate">
                <div class="pl-summary__label">Pass Rate</div>
                <div class="pl-summary__value">{{ $summary['pass_rate'] }}%</div>
                <div class="pl-summary__sub">{{ $currentTerm->name ?? '' }}</div>
            </div>
        </div>

        {{-- Grade sections --}}
        @foreach ($grouped as $letter => $students)
            @php
                $band = $bands[$letter] ?? ['min' => 0, 'label' => 'Other', 'color' => 'f'];
            @endphp
            <div class="pl-section">
                <div class="pl-section__head">
                    <div class="pl-section__badge {{ $band['color'] }}">{{ $letter }}</div>
                    <div>
                        <div class="pl-section__title">Grade {{ $letter }} — {{ $band['label'] }}</div>
                        <div class="pl-section__sub">
                            {{ count($students) }} {{ Str::plural('student', count($students)) }} ·
                            {{ $letter === 'F' ? 'Below' : 'From' }} {{ $band['min'] }}%
                        </div>
                    </div>
                    <div class="pl-section__count">{{ count($students) }}</div>
                </div>
                <table class="pl-table">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th class="center" style="width: 140px;">Class</th>
                            <th class="center" style="width: 130px;">Position</th>
                            <th class="center" style="width: 130px;">Subjects</th>
                            <th class="center" style="width: 160px;">Promotion</th>
                            <th class="right" style="width: 120px;">Average</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($students as $row)
                            <tr>
                                <td>
                                    <div class="student-cell">
                                        <div class="pl-avatar">
                                            {{ strtoupper(substr($row['student']->first_name, 0, 1) . substr($row['student']->last_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="pl-name">{{ $row['student']->full_name }}</div>
                                            <div class="pl-meta">{{ $row['student']->student_number }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="center">{{ $row['class_name'] }}</td>
                                <td class="center">
                                    @if ($row['position'] && $row['class_size'])
                                        <strong>{{ $row['position'] }}</strong> / {{ $row['class_size'] }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="center">{{ $row['subjects_passed'] }} / {{ $row['subjects_taken'] }}</td>
                                <td class="center">
                                    <span class="pl-promo {{ $row['promotion_status'] }}">
                                        {{ str_replace('_', ' ', $row['promotion_status']) }}
                                    </span>
                                </td>
                                <td class="right">
                                    <span class="pl-average">{{ number_format($row['average'], 1) }}%</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach

    @endif

@endsection