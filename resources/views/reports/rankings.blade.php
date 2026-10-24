@extends('layouts.app')

@section('title', 'Rankings')
@section('page-title', 'Rankings')

@section('styles')
    <style>
        .rk-controls {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 18px;
            flex-wrap: wrap;
        }

        .rk-select {
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

        .rk-select:focus { border-color: var(--pts-green); }

        .rk-pill {
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

        .rk-pill:hover {
            border-color: var(--pts-green);
            color: var(--pts-green-dark);
        }

        .rk-pill.active {
            background: var(--pts-green);
            color: #fff;
            border-color: var(--pts-green);
        }

        .rk-spacer { flex: 1; }

        .rk-actions { display: flex; gap: 8px; }

        .rk-btn {
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

        .rk-btn:hover {
            border-color: var(--pts-green);
            color: var(--pts-green-dark);
        }

        .rk-btn.primary {
            background: var(--pts-black);
            color: #fff;
            border-color: var(--pts-black);
        }

        .rk-btn.primary:hover {
            background: var(--pts-green-dark);
            border-color: var(--pts-green-dark);
            color: #fff;
        }

        /* Table */
        .rk-wrap {
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 14px;
            overflow: hidden;
        }

        .rk-table {
            width: 100%;
            border-collapse: collapse;
        }

        .rk-table thead th {
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

        .rk-table thead th.center { text-align: center; }
        .rk-table thead th.right { text-align: right; }

        .rk-table tbody td {
            padding: 14px 20px;
            font-size: 13.5px;
            color: #374151;
            border-bottom: 1px solid var(--pts-grey-light);
        }

        .rk-table tbody tr:last-child td { border-bottom: none; }
        .rk-table tbody tr:hover { background: #FAFBFC; }

        /* Top 3 highlight */
        .rk-table tbody tr.top-1 {
            background: linear-gradient(90deg, #FFFBEB 0%, #fff 30%);
        }
        .rk-table tbody tr.top-2 {
            background: linear-gradient(90deg, #F3F4F6 0%, #fff 30%);
        }
        .rk-table tbody tr.top-3 {
            background: linear-gradient(90deg, #FEF3C7 0%, #fff 30%);
        }
        .rk-table tbody tr.top-1:hover,
        .rk-table tbody tr.top-2:hover,
        .rk-table tbody tr.top-3:hover {
            background: linear-gradient(90deg, #F3F4F6 0%, #FAFBFC 30%);
        }

        /* Position badge */
        .pos-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 34px;
            height: 34px;
            padding: 0 8px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 800;
            background: var(--pts-bg);
            color: #374151;
        }

        .pos-badge.gold {
            background: #FEF3C7;
            color: #92400E;
        }

        .pos-badge.silver {
            background: #E5E7EB;
            color: #374151;
        }

        .pos-badge.bronze {
            background: #FED7AA;
            color: #9A3412;
        }

        /* Student cell */
        .rk-student {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .rk-avatar {
            width: 36px;
            height: 36px;
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

        .rk-avatar.gold { background: #D97706; }
        .rk-avatar.silver { background: #6B7280; }
        .rk-avatar.bronze { background: #C2410C; }

        .rk-name {
            font-size: 13.5px;
            font-weight: 600;
            color: #111827;
        }

        .rk-meta {
            font-size: 11.5px;
            color: var(--pts-grey);
            margin-top: 1px;
        }

        /* Score */
        .rk-score {
            font-size: 15px;
            font-weight: 800;
            color: #111827;
        }

        /* Grade chip */
        .rk-level {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
        }

        .rk-level.advanced { background: #DCFCE7; color: #166534; }
        .rk-level.proficient { background: #DBEAFE; color: #1E40AF; }
        .rk-level.developing { background: #FEF3C7; color: #92400E; }
        .rk-level.beginning { background: #FEE2E2; color: #991B1B; }

        /* Promotion */
        .rk-promo {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .rk-promo.promoted { background: #DCFCE7; color: #166534; }
        .rk-promo.conditional { background: #FEF3C7; color: #92400E; }
        .rk-promo.not_promoted { background: #FEE2E2; color: #991B1B; }
        .rk-promo.pending { background: #F3F4F6; color: #6B7280; }

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

        /* Print-only header */
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

        /* Print */
        @media print {
            .pts-sidebar,
            .pts-topbar,
            .pts-footer,
            .rk-controls,
            .pts-flash { display: none !important; }

            .pts-main { margin-left: 0 !important; }
            .pts-content { padding: 0 !important; }

            .print-header { display: block !important; }

            .rk-wrap {
                border: none !important;
                border-radius: 0 !important;
            }

            .rk-table { font-size: 11px; }

            .rk-table thead {
                display: table-header-group;
            }

            .rk-table tbody tr {
                page-break-inside: avoid;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .rk-table thead th,
            .rk-table tbody td {
                padding: 6px 10px !important;
                font-size: 10.5px !important;
            }

            .pos-badge,
            .rk-avatar,
            .rk-level,
            .rk-promo {
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

    {{-- Print-only header --}}
    <div class="print-header">
        <div class="print-header__school">{{ $school->name ?? 'School' }}</div>
        <div class="print-header__title">
            Rankings — {{ $selectedClass->name ?? '' }}
        </div>
        <div class="print-header__meta">
            {{ $currentTerm->name ?? 'Term' }}
            @if ($currentYear) · {{ $currentYear->label ?? '' }} @endif
            · Generated {{ now()->format('d M Y H:i') }}
        </div>
    </div>

    @if ($classes->isEmpty())
        <div class="empty-state">
            <div class="empty-state__icon"><i class="fa-solid fa-ranking-star"></i></div>
            <div class="empty-state__title">No classes yet</div>
            <div class="empty-state__desc">Create a class and add students to see rankings.</div>
        </div>
    @else

        {{-- Controls --}}
        <div class="rk-controls">

            {{-- Term selector --}}
            @if (isset($allTerms) && $allTerms->count() > 0)
                <form method="GET" action="{{ route('reports.rankings') }}" id="termForm">
                    <input type="hidden" name="class_id" value="{{ $selectedClassId }}">
                    <select name="term" class="rk-select" onchange="document.getElementById('termForm').submit()">
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
                        <a href="{{ route('reports.rankings', [
                    'class_id' => $class->id,
                    'term' => $currentTermId ?? null,
                ]) }}"
                           class="rk-pill {{ $selectedClassId == $class->id ? 'active' : '' }}">
                            {{ $class->name }}
                        </a>
            @endforeach

            <div class="rk-spacer"></div>

            {{-- Actions --}}
            <div class="rk-actions">
                <button type="button" onclick="window.print()" class="rk-btn primary">
                    <i class="fa-solid fa-print"></i> Print
                </button>
            </div>
        </div>

        {{-- Table --}}
        @if (empty($rows))
            <div class="empty-state">
                <div class="empty-state__icon"><i class="fa-solid fa-inbox"></i></div>
                <div class="empty-state__title">No rankings yet</div>
                <div class="empty-state__desc">
                    @if ($selectedClass)
                        No results have been computed for <strong>{{ $selectedClass->name }}</strong>
                        in <strong>{{ $currentTerm->name ?? 'this term' }}</strong>.
                    @else
                        Select a class above.
                    @endif
                </div>
            </div>
        @else
            <div class="rk-wrap">
                <table class="rk-table">
                    <thead>
                        <tr>
                            <th style="width:80px;">Position</th>
                            <th>Student</th>
                            <th class="center" style="width:130px;">Subjects</th>
                            <th class="center" style="width:140px;">Level</th>
                            <th class="center" style="width:140px;">Promotion</th>
                            <th class="right" style="width:110px;">Average</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $row)
                            @php
                                $pos = $row['position'];
                                $rowClass = '';
                                $badgeClass = '';
                                $avatarClass = '';
                                if ($pos === 1) {
                                    $rowClass = 'top-1';
                                    $badgeClass = 'gold';
                                    $avatarClass = 'gold';
                                } elseif ($pos === 2) {
                                    $rowClass = 'top-2';
                                    $badgeClass = 'silver';
                                    $avatarClass = 'silver';
                                } elseif ($pos === 3) {
                                    $rowClass = 'top-3';
                                    $badgeClass = 'bronze';
                                    $avatarClass = 'bronze';
                                }

                                $levelClass = strtolower($row['achievement_level'] ?? '');
                                $levelClass = in_array($levelClass, ['advanced', 'proficient', 'developing', 'beginning'])
                                    ? $levelClass
                                    : 'developing';

                                $promoClass = $row['promotion_status'] ?? 'pending';
                            @endphp
                            <tr class="{{ $rowClass }}">
                                <td>
                                    @if ($pos)
                                        <span class="pos-badge {{ $badgeClass }}">{{ $pos }}</span>
                                    @else
                                        <span class="pos-badge">—</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="rk-student">
                                        <div class="rk-avatar {{ $avatarClass }}">
                                            {{ strtoupper(substr($row['student']->first_name, 0, 1) . substr($row['student']->last_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="rk-name">{{ $row['student']->full_name }}</div>
                                            <div class="rk-meta">{{ $row['student']->student_number }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="center">
                                    {{ $row['subjects_passed'] }} / {{ $row['subjects_taken'] }}
                                </td>
                                <td class="center">
                                    <span class="rk-level {{ $levelClass }}">{{ $row['achievement_level'] }}</span>
                                </td>
                                <td class="center">
                                    <span class="rk-promo {{ $promoClass }}">
                                        {{ str_replace('_', ' ', $row['promotion_status']) }}
                                    </span>
                                </td>
                                <td class="right">
                                    <span class="rk-score">{{ number_format($row['average'], 1) }}%</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

    @endif

@endsection