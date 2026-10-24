@extends('layouts.app')

@section('title', 'Merit List')
@section('page-title', 'Merit List')

@section('styles')
    <style>
        .ml-controls {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 18px;
            flex-wrap: wrap;
        }

        .ml-select {
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

        .ml-select:focus { border-color: var(--pts-green); }

        .ml-pill {
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

        .ml-pill:hover {
            border-color: var(--pts-green);
            color: var(--pts-green-dark);
        }

        .ml-pill.active {
            background: var(--pts-green);
            color: #fff;
            border-color: var(--pts-green);
        }

        .ml-spacer { flex: 1; }

        .ml-actions { display: flex; gap: 8px; }

        .ml-btn {
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

        .ml-btn:hover {
            border-color: var(--pts-green);
            color: var(--pts-green-dark);
        }

        .ml-btn.primary {
            background: var(--pts-black);
            color: #fff;
            border-color: var(--pts-black);
        }

        .ml-btn.primary:hover {
            background: var(--pts-green-dark);
            border-color: var(--pts-green-dark);
            color: #fff;
        }

        /* Podium */
        .ml-podium {
            display: grid;
            grid-template-columns: 1fr 1.2fr 1fr;
            gap: 16px;
            margin-bottom: 24px;
            align-items: end;
        }

        .ml-podium__place {
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 14px;
            padding: 24px 20px;
            text-align: center;
            position: relative;
            overflow: hidden;
            transition: transform 0.15s;
        }

        .ml-podium__place:hover {
            transform: translateY(-3px);
        }

        .ml-podium__place::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
        }

        .ml-podium__place.gold { padding-top: 42px; padding-bottom: 34px; }
        .ml-podium__place.gold::before { background: linear-gradient(90deg, #D97706, #F59E0B); }
        .ml-podium__place.silver::before { background: linear-gradient(90deg, #6B7280, #9CA3AF); }
        .ml-podium__place.bronze::before { background: linear-gradient(90deg, #C2410C, #EA580C); }

        .ml-podium__rank {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            font-size: 18px;
            font-weight: 800;
            margin-bottom: 12px;
        }

        .ml-podium__place.gold .ml-podium__rank {
            background: linear-gradient(135deg, #FCD34D, #D97706);
            color: #fff;
            width: 52px;
            height: 52px;
            font-size: 22px;
            box-shadow: 0 4px 14px rgba(217, 119, 6, 0.3);
        }

        .ml-podium__place.silver .ml-podium__rank {
            background: linear-gradient(135deg, #D1D5DB, #6B7280);
            color: #fff;
        }

        .ml-podium__place.bronze .ml-podium__rank {
            background: linear-gradient(135deg, #FED7AA, #C2410C);
            color: #fff;
        }

        .ml-podium__avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: var(--pts-black);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 800;
            margin: 0 auto 12px;
        }

        .ml-podium__place.gold .ml-podium__avatar {
            width: 72px;
            height: 72px;
            font-size: 24px;
            background: #D97706;
        }

        .ml-podium__place.silver .ml-podium__avatar { background: #6B7280; }
        .ml-podium__place.bronze .ml-podium__avatar { background: #C2410C; }

        .ml-podium__name {
            font-size: 14px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .ml-podium__place.gold .ml-podium__name { font-size: 16px; }

        .ml-podium__class {
            font-size: 11.5px;
            color: var(--pts-grey);
            margin-bottom: 10px;
        }

        .ml-podium__score {
            display: inline-block;
            padding: 5px 14px;
            background: var(--pts-green-light);
            color: var(--pts-green-dark);
            border-radius: 20px;
            font-size: 13px;
            font-weight: 800;
        }

        .ml-podium__place.gold .ml-podium__score {
            font-size: 15px;
            padding: 6px 18px;
        }

        /* Table */
        .ml-table-wrap {
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 14px;
            overflow: hidden;
        }

        .ml-table-head {
            padding: 16px 22px;
            border-bottom: 1px solid var(--pts-grey-light);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .ml-table-title {
            font-size: 15px;
            font-weight: 700;
            color: #111827;
        }

        .ml-table-title span {
            font-weight: 500;
            color: var(--pts-grey);
        }

        .ml-table {
            width: 100%;
            border-collapse: collapse;
        }

        .ml-table thead th {
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

        .ml-table thead th.center { text-align: center; }
        .ml-table thead th.right { text-align: right; }

        .ml-table tbody td {
            padding: 14px 20px;
            font-size: 13.5px;
            color: #374151;
            border-bottom: 1px solid var(--pts-grey-light);
        }

        .ml-table tbody tr:last-child td { border-bottom: none; }
        .ml-table tbody tr:hover { background: #FAFBFC; }

        .ml-table tbody tr.top-1 {
            background: linear-gradient(90deg, #FFFBEB 0%, #fff 30%);
        }
        .ml-table tbody tr.top-2 {
            background: linear-gradient(90deg, #F3F4F6 0%, #fff 30%);
        }
        .ml-table tbody tr.top-3 {
            background: linear-gradient(90deg, #FEF3C7 0%, #fff 30%);
        }

        .ml-table .student-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .ml-avatar {
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

        .ml-avatar.gold { background: #D97706; }
        .ml-avatar.silver { background: #6B7280; }
        .ml-avatar.bronze { background: #C2410C; }

        .ml-name {
            font-size: 13.5px;
            font-weight: 600;
            color: #111827;
        }

        .ml-meta {
            font-size: 11.5px;
            color: var(--pts-grey);
            margin-top: 1px;
        }

        .ml-pos {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 30px;
            height: 30px;
            padding: 0 8px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 800;
            background: var(--pts-bg);
            color: #374151;
        }

        .ml-pos.gold { background: #FEF3C7; color: #92400E; }
        .ml-pos.silver { background: #E5E7EB; color: #374151; }
        .ml-pos.bronze { background: #FED7AA; color: #9A3412; }

        .ml-score {
            font-size: 15px;
            font-weight: 800;
            color: #111827;
        }

        .ml-level {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
        }

        .ml-level.advanced { background: #DCFCE7; color: #166534; }
        .ml-level.proficient { background: #DBEAFE; color: #1E40AF; }
        .ml-level.developing { background: #FEF3C7; color: #92400E; }
        .ml-level.beginning { background: #FEE2E2; color: #991B1B; }

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
            .ml-podium { grid-template-columns: 1fr; }
        }

        /* Print */
        @media print {
            .pts-sidebar,
            .pts-topbar,
            .pts-footer,
            .ml-controls,
            .ml-actions,
            .pts-flash { display: none !important; }

            .pts-main { margin-left: 0 !important; }
            .pts-content { padding: 0 !important; }

            .print-header { display: block !important; }

            .ml-podium__place,
            .ml-table-wrap {
                box-shadow: none !important;
                border: 1px solid #333 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .ml-podium__rank,
            .ml-podium__avatar,
            .ml-avatar,
            .ml-pos,
            .ml-level,
            .ml-score {
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
            Merit List — Top {{ $limit }} — {{ $selectedClass?->name ?? 'All Classes' }}
        </div>
        <div class="print-header__meta">
            {{ $currentTerm->name ?? 'Term' }}
            @if ($currentYear) · {{ $currentYear->label ?? '' }} @endif
            · Generated {{ now()->format('d M Y H:i') }}
        </div>
    </div>

    @if (empty($rows))
        <div class="empty-state">
            <div class="empty-state__icon"><i class="fa-solid fa-award"></i></div>
            <div class="empty-state__title">No merit data yet</div>
            <div class="empty-state__desc">
                No subject results have been computed for the current term. Enter marks first, then check back.
            </div>
        </div>
    @else

        {{-- Controls --}}
        <div class="ml-controls">
            @if ($allTerms->count() > 0)
                <form method="GET" action="{{ route('reports.merit') }}" id="termForm">
                    <input type="hidden" name="class_id" value="{{ $selectedClassId }}">
                    <input type="hidden" name="limit" value="{{ $limit }}">
                    <select name="term" class="ml-select" onchange="document.getElementById('termForm').submit()">
                        @foreach ($allTerms as $t)
                            <option value="{{ $t->id }}" @selected($currentTermId == $t->id)>
                                {{ $t->name }} · {{ $t->academicYear?->label ?? '' }}
                            </option>
                        @endforeach
                    </select>
                </form>
            @endif

            <a href="{{ route('reports.merit', ['class_id' => 'all', 'term' => $currentTermId, 'limit' => $limit]) }}"
               class="ml-pill {{ $selectedClassId === 'all' ? 'active' : '' }}">
                All Classes
            </a>

            @foreach ($classes as $class)
                <a href="{{ route('reports.merit', ['class_id' => $class->id, 'term' => $currentTermId, 'limit' => $limit]) }}"
                   class="ml-pill {{ $selectedClassId == $class->id ? 'active' : '' }}">
                    {{ $class->name }}
                </a>
            @endforeach

            <div class="ml-spacer"></div>

            {{-- Limit selector --}}
            <form method="GET" action="{{ route('reports.merit') }}" id="limitForm">
                <input type="hidden" name="class_id" value="{{ $selectedClassId }}">
                <input type="hidden" name="term" value="{{ $currentTermId }}">
                <select name="limit" class="ml-select" onchange="document.getElementById('limitForm').submit()">
                    <option value="5" @selected($limit == 5)>Top 5</option>
                    <option value="10" @selected($limit == 10)>Top 10</option>
                    <option value="20" @selected($limit == 20)>Top 20</option>
                    <option value="50" @selected($limit == 50)>Top 50</option>
                </select>
            </form>

            <div class="ml-actions">
                <button type="button" onclick="window.print()" class="ml-btn primary">
                    <i class="fa-solid fa-print"></i> Print
                </button>
            </div>
        </div>

        {{-- Podium (top 3) --}}
        @if (count($rows) >= 3)
            @php
                $first = $rows[0];
                $second = $rows[1];
                $third = $rows[2];
            @endphp
            <div class="ml-podium">
                {{-- 2nd --}}
                <div class="ml-podium__place silver">
                    <div class="ml-podium__rank">2</div>
                    <div class="ml-podium__avatar">
                        {{ strtoupper(substr($second['student']->first_name, 0, 1) . substr($second['student']->last_name, 0, 1)) }}
                    </div>
                    <div class="ml-podium__name">{{ $second['student']->full_name }}</div>
                    <div class="ml-podium__class">{{ $second['class_name'] }}</div>
                    <div class="ml-podium__score">{{ number_format($second['average'], 1) }}%</div>
                </div>

                {{-- 1st --}}
                <div class="ml-podium__place gold">
                    <div class="ml-podium__rank">1</div>
                    <div class="ml-podium__avatar">
                        {{ strtoupper(substr($first['student']->first_name, 0, 1) . substr($first['student']->last_name, 0, 1)) }}
                    </div>
                    <div class="ml-podium__name">{{ $first['student']->full_name }}</div>
                    <div class="ml-podium__class">{{ $first['class_name'] }}</div>
                    <div class="ml-podium__score">{{ number_format($first['average'], 1) }}%</div>
                </div>

                {{-- 3rd --}}
                <div class="ml-podium__place bronze">
                    <div class="ml-podium__rank">3</div>
                    <div class="ml-podium__avatar">
                        {{ strtoupper(substr($third['student']->first_name, 0, 1) . substr($third['student']->last_name, 0, 1)) }}
                    </div>
                    <div class="ml-podium__name">{{ $third['student']->full_name }}</div>
                    <div class="ml-podium__class">{{ $third['class_name'] }}</div>
                    <div class="ml-podium__score">{{ number_format($third['average'], 1) }}%</div>
                </div>
            </div>
        @endif

        {{-- Table --}}
        <div class="ml-table-wrap">
            <div class="ml-table-head">
                <div class="ml-table-title">
                    Top {{ count($rows) }} students
                    <span>· {{ $selectedClass?->name ?? 'All classes' }} · {{ $currentTerm->name ?? '' }}</span>
                </div>
            </div>
            <table class="ml-table">
                <thead>
                    <tr>
                        <th style="width: 80px;">Position</th>
                        <th>Student</th>
                        <th class="center" style="width: 120px;">Class</th>
                        <th class="center" style="width: 130px;">Subjects</th>
                        <th class="center" style="width: 130px;">Level</th>
                        <th class="right" style="width: 110px;">Average</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $i => $row)
                        @php
                            $pos = $i + 1;
                            $rowClass = '';
                            $posClass = '';
                            $avatarClass = '';
                            if ($pos === 1) { $rowClass = 'top-1'; $posClass = 'gold'; $avatarClass = 'gold'; }
                            elseif ($pos === 2) { $rowClass = 'top-2'; $posClass = 'silver'; $avatarClass = 'silver'; }
                            elseif ($pos === 3) { $rowClass = 'top-3'; $posClass = 'bronze'; $avatarClass = 'bronze'; }

                            $levelClass = strtolower($row['achievement_level'] ?? '');
                            $levelClass = in_array($levelClass, ['advanced','proficient','developing','beginning'])
                                ? $levelClass
                                : 'developing';
                        @endphp
                        <tr class="{{ $rowClass }}">
                            <td>
                                <span class="ml-pos {{ $posClass }}">{{ $pos }}</span>
                            </td>
                            <td>
                                <div class="student-cell">
                                    <div class="ml-avatar {{ $avatarClass }}">
                                        {{ strtoupper(substr($row['student']->first_name, 0, 1) . substr($row['student']->last_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="ml-name">{{ $row['student']->full_name }}</div>
                                        <div class="ml-meta">{{ $row['student']->student_number }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="center">{{ $row['class_name'] }}</td>
                            <td class="center">{{ $row['subjects_passed'] }} / {{ $row['subjects_taken'] }}</td>
                            <td class="center">
                                <span class="ml-level {{ $levelClass }}">{{ $row['achievement_level'] }}</span>
                            </td>
                            <td class="right">
                                <span class="ml-score">{{ number_format($row['average'], 1) }}%</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    @endif

@endsection