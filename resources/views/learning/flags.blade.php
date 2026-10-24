@extends('layouts.app')

@section('title', 'Learning Flags')
@section('page-title', 'Learning Flags')

@section('styles')
    <style>
        /* ---------- Controls ---------- */
        .lf-controls {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 18px;
            flex-wrap: wrap;
        }

        .lf-select {
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

        .lf-select:focus {
            border-color: var(--pts-green);
        }

        .lf-pill {
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

        .lf-pill:hover {
            border-color: var(--pts-green);
            color: var(--pts-green-dark);
        }

        .lf-pill.active {
            background: var(--pts-green);
            color: #fff;
            border-color: var(--pts-green);
        }

        .lf-spacer {
            flex: 1;
        }

        .lf-actions {
            display: flex;
            gap: 8px;
        }

        .lf-btn {
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

        .lf-btn:hover {
            border-color: var(--pts-green);
            color: var(--pts-green-dark);
        }

        .lf-btn.primary {
            background: var(--pts-black);
            color: #fff;
            border-color: var(--pts-black);
        }

        .lf-btn.primary:hover {
            background: var(--pts-green-dark);
            border-color: var(--pts-green-dark);
            color: #fff;
        }

        .lf-btn.warn {
            background: var(--pts-amber);
            color: #fff;
            border-color: var(--pts-amber);
        }

        .lf-btn.warn:hover {
            background: #B45309;
            border-color: #B45309;
            color: #fff;
        }

        /* ---------- Summary ---------- */
        .lf-summary {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }

        .lf-summary__card {
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 12px;
            padding: 18px;
            position: relative;
            overflow: hidden;
        }

        .lf-summary__card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
        }

        .lf-summary__card.total::before {
            background: #6B7280;
        }

        .lf-summary__card.critical::before {
            background: #DC2626;
        }

        .lf-summary__card.watch::before {
            background: #D97706;
        }

        .lf-summary__card.improving::before {
            background: #16A34A;
        }

        .lf-summary__card.excellent::before {
            background: #2563EB;
        }

        .lf-summary__label {
            font-size: 10.5px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--pts-grey);
            font-weight: 700;
            margin-bottom: 8px;
        }

        .lf-summary__value {
            font-size: 24px;
            font-weight: 800;
            color: #111827;
            line-height: 1;
        }

        .lf-summary__sub {
            font-size: 11.5px;
            color: var(--pts-grey);
            margin-top: 4px;
        }

        /* ---------- Flag tabs ---------- */
        .lf-tabs {
            display: flex;
            gap: 4px;
            margin-bottom: 18px;
            flex-wrap: wrap;
            border-bottom: 1px solid var(--pts-grey-light);
        }

        .lf-tab {
            padding: 10px 16px;
            font-size: 13px;
            font-weight: 600;
            color: var(--pts-grey);
            text-decoration: none;
            border-bottom: 2px solid transparent;
            margin-bottom: -1px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.15s;
        }

        .lf-tab:hover {
            color: #111827;
        }

        .lf-tab.active {
            color: var(--pts-green-dark);
            border-bottom-color: var(--pts-green);
        }

        .lf-tab__count {
            font-size: 11px;
            font-weight: 800;
            padding: 1px 7px;
            border-radius: 10px;
            background: var(--pts-bg);
            color: var(--pts-grey);
        }

        .lf-tab.active .lf-tab__count {
            background: var(--pts-green-light);
            color: var(--pts-green-dark);
        }

        /* ---------- Table ---------- */
        .lf-table-wrap {
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 14px;
            overflow: hidden;
        }

        .lf-table {
            width: 100%;
            border-collapse: collapse;
        }

        .lf-table thead th {
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

        .lf-table thead th.center {
            text-align: center;
        }

        .lf-table thead th.right {
            text-align: right;
        }

        .lf-table tbody td {
            padding: 14px 20px;
            font-size: 13.5px;
            color: #374151;
            border-bottom: 1px solid var(--pts-grey-light);
        }

        .lf-table tbody tr:last-child td {
            border-bottom: none;
        }

        .lf-table tbody tr:hover {
            background: #FAFBFC;
        }

        /* Flag row left-border colors */
        .lf-table tbody tr.flag-at_risk td:first-child {
            border-left: 4px solid #DC2626;
            padding-left: 12px;
        }

        .lf-table tbody tr.flag-needs_support td:first-child {
            border-left: 4px solid #D97706;
            padding-left: 12px;
        }

        .lf-table tbody tr.flag-watch td:first-child {
            border-left: 4px solid #F59E0B;
            padding-left: 12px;
        }

        .lf-table tbody tr.flag-uneven td:first-child {
            border-left: 4px solid #7C3AED;
            padding-left: 12px;
        }

        .lf-table tbody tr.flag-improving td:first-child {
            border-left: 4px solid #16A34A;
            padding-left: 12px;
        }

        .lf-table tbody tr.flag-high_achiever td:first-child {
            border-left: 4px solid #2563EB;
            padding-left: 12px;
        }

        .lf-table tbody tr.flag-on_track td:first-child {
            border-left: 4px solid #D1D5DB;
            padding-left: 12px;
        }

        .lf-student-cell {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: inherit;
        }

        .lf-avatar {
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

        .lf-name {
            font-size: 13.5px;
            font-weight: 600;
            color: #111827;
        }

        .lf-meta {
            font-size: 11.5px;
            color: var(--pts-grey);
            margin-top: 1px;
        }

        /* Flag badge */
        .lf-flag {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .lf-flag.red {
            background: #FEE2E2;
            color: #991B1B;
        }

        .lf-flag.amber {
            background: #FEF3C7;
            color: #92400E;
        }

        .lf-flag.green {
            background: #DCFCE7;
            color: #166534;
        }

        .lf-flag.grey {
            background: #F3F4F6;
            color: #6B7280;
        }

        /* Score cells */
        .lf-score {
            font-size: 14px;
            font-weight: 800;
            color: #111827;
        }

        .lf-score.low {
            color: #DC2626;
        }

        .lf-score.mid {
            color: #D97706;
        }

        .lf-score.high {
            color: #16A34A;
        }

        .lf-delta {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
        }

        .lf-delta.up {
            background: #DCFCE7;
            color: #166534;
        }

        .lf-delta.down {
            background: #FEE2E2;
            color: #991B1B;
        }

        .lf-delta.flat {
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
            max-width: 480px;
            margin: 0 auto;
        }

        /* Pagination */
        .lf-pagination {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px;
            border-top: 1px solid var(--pts-grey-light);
        }

        .lf-pagination__count {
            font-size: 12.5px;
            color: var(--pts-grey);
        }

        .lf-pagination__links {
            display: flex;
            gap: 6px;
        }

        .lf-pagination__links a,
        .lf-pagination__links span {
            min-width: 32px;
            height: 32px;
            padding: 0 8px;
            border-radius: 7px;
            border: 1px solid var(--pts-grey-light);
            background: #fff;
            color: #374151;
            font-size: 12.5px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .lf-pagination__links a:hover {
            border-color: var(--pts-green);
            color: var(--pts-green-dark);
        }

        .lf-pagination__links .active {
            background: var(--pts-black);
            color: #fff;
            border-color: var(--pts-black);
        }

        .lf-pagination__links .disabled {
            opacity: 0.4;
            pointer-events: none;
        }

        @media (max-width: 900px) {
            .lf-summary {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
@endsection

@section('content')

    {{-- Controls --}}
    <div class="lf-controls">

        {{-- Term selector --}}
        @if ($allTerms->count() > 0)
            <form method="GET" action="{{ route('learning.flags') }}" id="lfTermForm">
                <input type="hidden" name="class_id" value="{{ $selectedClassId }}">
                <input type="hidden" name="flag" value="{{ $flagFilter }}">
                <input type="hidden" name="sort" value="{{ $sort }}">
                <select name="term" class="lf-select" onchange="document.getElementById('lfTermForm').submit()">
                    @foreach ($allTerms as $t)
                        <option value="{{ $t->id }}" @selected($currentTermId == $t->id)>
                            {{ $t->name }} · {{ $t->academicYear?->label ?? '' }}
                        </option>
                    @endforeach
                </select>
            </form>
        @endif

        {{-- Class pills --}}
        <a href="{{ route('learning.flags', ['class_id' => 'all', 'term' => $currentTermId, 'flag' => $flagFilter, 'sort' => $sort]) }}"
            class="lf-pill {{ $selectedClassId === 'all' ? 'active' : '' }}">
            All Classes
        </a>
        @foreach ($classes as $class)
            <a href="{{ route('learning.flags', ['class_id' => $class->id, 'term' => $currentTermId, 'flag' => $flagFilter, 'sort' => $sort]) }}"
                class="lf-pill {{ $selectedClassId == $class->id ? 'active' : '' }}">
                {{ $class->name }}
            </a>
        @endforeach

        <div class="lf-spacer"></div>

        <div class="lf-actions">
            <form method="POST" action="{{ route('learning.recompute') }}" style="display:inline;">
                @csrf
                <input type="hidden" name="term_id" value="{{ $currentTermId }}">
                <button type="submit" class="lf-btn warn"
                    data-confirm="This will recalculate learning profiles for every student in the current term. Existing profiles will be overwritten. Continue?">
                    <i class="fa-solid fa-rotate"></i> Recompute
                </button>
            </form>
        </div>
    </div>

    {{-- Summary cards --}}
    <div class="lf-summary">
        <div class="lf-summary__card total">
            <div class="lf-summary__label">Total Flagged</div>
            <div class="lf-summary__value">{{ $totalFlagged }}</div>
            <div class="lf-summary__sub">Students analysed</div>
        </div>
        <div class="lf-summary__card critical">
            <div class="lf-summary__label">Critical</div>
            <div class="lf-summary__value">{{ $criticalCount }}</div>
            <div class="lf-summary__sub">At risk + needs support</div>
        </div>
        <div class="lf-summary__card watch">
            <div class="lf-summary__label">Watch</div>
            <div class="lf-summary__value">{{ ($flagCounts['watch'] ?? 0) + ($flagCounts['uneven'] ?? 0) }}</div>
            <div class="lf-summary__sub">Monitor closely</div>
        </div>
        <div class="lf-summary__card improving">
            <div class="lf-summary__label">Improving</div>
            <div class="lf-summary__value">{{ $flagCounts['improving'] ?? 0 }}</div>
            <div class="lf-summary__sub">Meaningful gains</div>
        </div>
        <div class="lf-summary__card excellent">
            <div class="lf-summary__label">High Achievers</div>
            <div class="lf-summary__value">{{ $flagCounts['high_achiever'] ?? 0 }}</div>
            <div class="lf-summary__sub">Well above peers</div>
        </div>
    </div>

    {{-- Flag filter tabs --}}
    <div class="lf-tabs">
        <a href="{{ route('learning.flags', array_merge(request()->query(), ['flag' => 'all'])) }}"
            class="lf-tab {{ $flagFilter === 'all' ? 'active' : '' }}">
            All
            <span class="lf-tab__count">{{ $totalFlagged }}</span>
        </a>
        @foreach (['at_risk', 'needs_support', 'watch', 'uneven', 'improving', 'high_achiever', 'on_track'] as $flagKey)
            @php
                $count = $flagCounts[$flagKey] ?? 0;
                $meta = $flagMeta[$flagKey] ?? ['label' => ucfirst($flagKey)];
            @endphp
            @if ($count > 0 || $flagFilter === $flagKey)
                <a href="{{ route('learning.flags', array_merge(request()->query(), ['flag' => $flagKey])) }}"
                    class="lf-tab {{ $flagFilter === $flagKey ? 'active' : '' }}">
                    {{ $meta['label'] }}
                    <span class="lf-tab__count">{{ $count }}</span>
                </a>
            @endif
        @endforeach
    </div>

    {{-- Table --}}
    @if ($profiles->isEmpty())
        <div class="empty-state">
            <div class="empty-state__icon"><i class="fa-solid fa-brain"></i></div>
            <div class="empty-state__title">
                @if ($totalFlagged === 0)
                    No learning profiles yet
                @else
                    No students match this filter
                @endif
            </div>
            <div class="empty-state__desc">
                @if ($totalFlagged === 0)
                    Learning profiles are computed from term results. Click <strong>Recompute</strong> above to generate them for
                    the current term.
                @else
                    Try changing the class, term, or flag filter.
                @endif
            </div>
        </div>
    @else
        <div class="lf-table-wrap">
            <table class="lf-table">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th class="center" style="width: 130px;">Flag</th>
                        <th class="center" style="width: 120px;">Average</th>
                        <th class="center" style="width: 120px;">Change</th>
                        <th class="center" style="width: 120px;">Peer avg</th>
                        <th class="center" style="width: 120px;">Spread</th>
                        <th class="right" style="width: 100px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($profiles as $profile)
                        @php
                            $student = $profile->student;
                            if (!$student)
                                continue;

                            $flagColor = $profile->flag_color;
                            $initial = strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1));

                            $avgClass = 'mid';
                            if ($profile->overall_score >= 70)
                                $avgClass = 'high';
                            elseif ($profile->overall_score < 50)
                                $avgClass = 'low';

                            $delta = $profile->overall_delta;
                            $deltaClass = $delta === null ? 'flat' : ($delta > 1 ? 'up' : ($delta < -1 ? 'down' : 'flat'));
                        @endphp
                        <tr class="flag-{{ $profile->flag }}">
                            <td>
                                <a href="{{ route('learning.flags.student', $student->id) }}" class="lf-student-cell">
                                    <div class="lf-avatar">{{ $initial }}</div>
                                    <div>
                                        <div class="lf-name">{{ $student->full_name }}</div>
                                        <div class="lf-meta">{{ $student->student_number }}</div>
                                    </div>
                                </a>
                            </td>
                            <td class="center">
                                <span class="lf-flag {{ $flagColor }}">
                                    {{ $profile->flag_label }}
                                </span>
                            </td>
                            <td class="center">
                                <span class="lf-score {{ $avgClass }}">{{ number_format($profile->overall_score, 1) }}%</span>
                            </td>
                            <td class="center">
                                @if ($delta !== null)
                                    <span class="lf-delta {{ $deltaClass }}">
                                        @if ($delta > 1)
                                            ▲ {{ number_format($delta, 1) }}
                                        @elseif ($delta < -1)
                                            ▼ {{ number_format(abs($delta), 1) }}
                                        @else
                                            — 0
                                        @endif
                                    </span>
                                @else
                                    <span class="lf-delta flat">—</span>
                                @endif
                            </td>
                            <td class="center">
                                {{ number_format($profile->peer_average, 1) }}%
                            </td>
                            <td class="center">
                                {{ number_format($profile->spread, 1) }}
                            </td>
                            <td class="right">
                                <a href="{{ route('learning.flags.student', $student->id) }}" class="lf-btn"
                                    style="padding: 6px 12px; font-size: 12px;">
                                    View <i class="fa-solid fa-arrow-right"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Pagination --}}
            <div class="lf-pagination">
                <div class="lf-pagination__count">
                    Showing {{ $profiles->firstItem() }}–{{ $profiles->lastItem() }} of {{ $profiles->total() }}
                </div>
                <div class="lf-pagination__links">
                    @if ($profiles->onFirstPage())
                        <span class="disabled"><i class="fa-solid fa-chevron-left"></i></span>
                    @else
                        <a href="{{ $profiles->previousPageUrl() }}"><i class="fa-solid fa-chevron-left"></i></a>
                    @endif

                    @foreach ($profiles->getUrlRange(max(1, $profiles->currentPage() - 2), min($profiles->lastPage(), $profiles->currentPage() + 2)) as $page => $url)
                        @if ($page == $profiles->currentPage())
                            <span class="active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if ($profiles->hasMorePages())
                        <a href="{{ $profiles->nextPageUrl() }}"><i class="fa-solid fa-chevron-right"></i></a>
                    @else
                        <span class="disabled"><i class="fa-solid fa-chevron-right"></i></span>
                    @endif
                </div>
            </div>
        </div>
    @endif

@endsection