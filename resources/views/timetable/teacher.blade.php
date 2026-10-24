@extends('layouts.app')

@section('title', $teacher->name . ' Timetable')
@section('page-title', 'Timetable')

@section('styles')
    <style>
        .tt-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 18px;
        }

        .tt-head__left {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
        }

        .tt-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--pts-grey);
            text-decoration: none;
            font-weight: 600;
        }

        .tt-back:hover {
            color: var(--pts-green-dark);
        }

        .tt-teacher-name {
            font-size: 18px;
            font-weight: 800;
            color: #111827;
        }

        .tt-pickers {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .tt-picker {
            padding: 9px 12px;
            border: 1.5px solid var(--pts-grey-light);
            border-radius: 8px;
            font-size: 13px;
            font-family: inherit;
            background: #fff;
        }

        .tt-actions {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 9px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            border: none;
            font-family: inherit;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-primary {
            background: var(--pts-black);
            color: #fff;
        }

        .btn-primary:hover {
            background: var(--pts-green-dark);
        }

        .btn-secondary {
            background: #fff;
            color: #374151;
            border: 1.5px solid var(--pts-grey-light);
        }

        .btn-secondary:hover {
            border-color: var(--pts-green);
            color: var(--pts-green-dark);
        }

        .grid-wrap {
            overflow-x: auto;
            background: #fff;
            border: 1px solid var(--pts-grey-light);
            border-radius: 12px;
        }

        table.tt {
            width: 100%;
            border-collapse: collapse;
            min-width: 900px;
        }

        table.tt th,
        table.tt td {
            border: 1px solid var(--pts-grey-light);
            padding: 0;
            vertical-align: top;
        }

        table.tt thead th {
            background: var(--pts-black);
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            padding: 12px 10px;
            text-align: center;
        }

        table.tt th.period-col {
            width: 140px;
            text-align: left;
        }

        .period-cell {
            background: #FAFBFC;
            padding: 10px 12px;
        }

        .period-cell__name {
            font-size: 12.5px;
            font-weight: 700;
            color: #111827;
        }

        .period-cell__time {
            font-size: 11px;
            color: var(--pts-grey);
            margin-top: 2px;
            font-family: 'SFMono-Regular', Consolas, monospace;
        }

        .break-row td {
            background: #F5F6F8;
        }

        .break-row .period-cell {
            background: #EFF1F4;
        }

        .break-row .period-cell__name {
            color: var(--pts-grey);
        }

        .slot {
            min-height: 68px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 8px 6px;
            gap: 3px;
        }

        .slot--empty {
            color: #C1C5CC;
            font-size: 12px;
        }

        .slot__class {
            font-size: 12.5px;
            font-weight: 800;
            color: #111827;
            line-height: 1.25;
        }

        .slot__subject {
            font-size: 11px;
            color: var(--pts-grey);
            line-height: 1.2;
        }

        .empty-state {
            padding: 60px 20px;
            text-align: center;
            color: var(--pts-grey);
            font-size: 13.5px;
        }

        .empty-state i {
            display: block;
            font-size: 32px;
            color: var(--pts-grey-light);
            margin-bottom: 12px;
        }

        /* ---------- Print ---------- */
        @media print {
            @page {
                size: A4 landscape;
                margin: 10mm;
            }

            .pts-sidebar,
            .pts-topbar,
            .pts-footer,
            .pts-toasts,
            .pts-confirm,
            .pts-sidebar__overlay,
            .tt-head,
            .pts-flash {
                display: none !important;
            }

            .pts-main {
                margin-left: 0 !important;
                min-height: 0 !important;
                display: block !important;
            }

            .pts-content {
                padding: 0 !important;
            }

            body {
                background: #fff !important;
            }

            .grid-wrap {
                border: none !important;
                border-radius: 0 !important;
                overflow: visible !important;
            }

            table.tt {
                min-width: 0 !important;
                width: 100% !important;
            }

            table.tt thead th {
                background: #fff !important;
                color: #111827 !important;
                border: 1px solid #9CA3AF !important;
                font-size: 10px !important;
                padding: 6px 4px !important;
            }

            table.tt td {
                border: 1px solid #9CA3AF !important;
            }

            .period-cell {
                background: #fff !important;
                padding: 5px 6px !important;
            }

            .period-cell__name {
                font-size: 10px !important;
            }

            .period-cell__time {
                font-size: 8.5px !important;
            }

            .slot {
                min-height: 0 !important;
                padding: 4px 3px !important;
            }

            .slot__class {
                font-size: 10px !important;
            }

            .slot__subject {
                font-size: 8.5px !important;
            }

            .break-row td,
            .break-row .period-cell {
                background: #F3F4F6 !important;
            }
        }
    </style>
@endsection

@section('content')

    <div class="tt-head">
        <div class="tt-head__left">
            <a href="{{ route('timetable.index') }}" class="tt-back">
                <i class="fa-solid fa-arrow-left"></i> All classes
            </a>
            <div class="tt-teacher-name">{{ $teacher->name }}</div>

            <div class="tt-pickers">
                @if ($isAdmin && $teachers->count() > 1)
                    <form method="GET" action="{{ route('timetable.teacher') }}">
                        <input type="hidden" name="term" value="{{ $selectedTerm?->id }}">
                        <select name="teacher_id" class="tt-picker" onchange="this.form.submit()">
                            @foreach ($teachers as $t)
                                <option value="{{ $t->id }}" {{ $teacher->id === $t->id ? 'selected' : '' }}>
                                    {{ $t->name }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                @endif

                <form method="GET" action="{{ route('timetable.teacher') }}">
                    <input type="hidden" name="teacher_id" value="{{ $teacher->id }}">
                    <select name="term" class="tt-picker" onchange="this.form.submit()">
                        @foreach ($terms as $term)
                            <option value="{{ $term->id }}" {{ $selectedTerm?->id === $term->id ? 'selected' : '' }}>
                                {{ $term->name }} · {{ $term->academicYear->label ?? '' }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>

        <div class="tt-actions">
            <button type="button" class="btn btn-secondary" onclick="window.print()">
                <i class="fa-solid fa-print"></i> Print
            </button>
            <a href="{{ route('timetable.teacher.pdf', ['teacher' => $teacher->id, 'term' => $selectedTerm?->id]) }}"
                class="btn btn-secondary">
                <i class="fa-solid fa-file-arrow-down"></i> PDF
            </a>
        </div>
    </div>

    @if ($periods->isEmpty())
        <div class="empty-state">
            No periods defined yet for this school.
        </div>
    @elseif (!$hasEntries)
        <div class="empty-state">
            <i class="fa-regular fa-calendar-xmark"></i>
            No timetable entries for {{ $teacher->name }} in {{ $selectedTerm?->name ?? 'the selected term' }}.
        </div>
    @else
        <div class="grid-wrap">
            <table class="tt">
                <thead>
                    <tr>
                        <th class="period-col">Period</th>
                        @foreach (\App\Models\ClassTimetable::days() as $dayNum => $dayName)
                            <th>{{ $dayName }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($periods as $period)
                        <tr class="{{ $period->is_break ? 'break-row' : '' }}">
                            <td class="period-cell">
                                <div class="period-cell__name">{{ $period->name }}</div>
                                <div class="period-cell__time">{{ $period->time_label }}</div>
                            </td>

                            @foreach (\App\Models\ClassTimetable::days() as $dayNum => $dayName)
                                @php
            $slot = $grid[$dayNum][$period->id] ?? null;
                                @endphp

                                <td>
                                    @if ($period->is_break)
                                        <div class="slot slot--empty">
                                            {{ $period->name }}
                                        </div>
                                    @elseif ($slot)
                                        <div class="slot">
                                            <div class="slot__class">{{ $slot->class_name }}</div>
                                            <div class="slot__subject">{{ $slot->subject_name }}</div>
                                        </div>
                                    @else
                                        <div class="slot slot--empty">-</div>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

@endsection