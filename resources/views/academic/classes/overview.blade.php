@extends('layouts.app')

@section('title', 'Classes Overview')
@section('page-title', 'Classes Overview')

@section('styles')
    <style>
        .page-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            gap: 16px;
            flex-wrap: wrap;
        }

        .page-head__title {
            font-size: 15px;
            color: var(--pts-grey);
        }

        .page-head__title strong {
            color: #111827;
            font-weight: 700;
        }

        .btn-add {
            padding: 11px 18px;
            background: var(--pts-black);
            color: #fff;
            border: none;
            border-radius: 9px;
            font-size: 13.5px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: background 0.15s;
        }

        .btn-add:hover {
            background: var(--pts-green-dark);
        }

        .btn-add i {
            font-size: 12px;
        }

        /* Table */
        .table-panel {
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 14px;
            overflow: hidden;
        }

        .classes-table {
            width: 100%;
            border-collapse: collapse;
        }

        .classes-table thead th {
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

        .classes-table thead th.text-right {
            text-align: right;
        }

        .classes-table tbody td {
            padding: 14px 20px;
            font-size: 13.5px;
            color: #374151;
            border-bottom: 1px solid var(--pts-grey-light);
            vertical-align: middle;
        }

        .classes-table tbody tr:last-child td {
            border-bottom: none;
        }

        .classes-table tbody tr:hover {
            background: #FAFBFC;
        }

        .class-name {
            font-size: 13.5px;
            font-weight: 600;
            color: #111827;
        }

        .class-grade {
            font-size: 12px;
            color: var(--pts-grey);
            margin-top: 2px;
        }

        .student-count {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 600;
            color: #111827;
            padding: 4px 10px;
            background: var(--pts-green-light);
            color: var(--pts-green-dark);
            border-radius: 20px;
        }

        .student-count i {
            font-size: 11px;
        }

        .student-count.empty {
            background: var(--pts-bg);
            color: var(--pts-grey);
        }

        .row-actions {
            display: flex;
            gap: 6px;
            justify-content: flex-end;
        }

        .row-action-btn {
            width: 32px;
            height: 32px;
            border-radius: 7px;
            border: 1px solid var(--pts-grey-light);
            background: #fff;
            color: var(--pts-grey);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            text-decoration: none;
            transition: all 0.15s;
        }

        .row-action-btn:hover {
            border-color: var(--pts-green);
            color: var(--pts-green-dark);
            background: var(--pts-green-light);
        }

        /* Empty state */
        .empty-state {
            padding: 72px 24px;
            text-align: center;
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
            margin-bottom: 20px;
            max-width: 420px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.5;
        }
    </style>
@endsection

@section('content')

    <div class="page-head">
        <div class="page-head__title">
            <strong>{{ $classes->count() }}</strong>
            {{ Str::plural('class', $classes->count()) }}
            @if ($classes->count() > 0)
                · {{ $classes->sum('students_count') }} {{ Str::plural('student', $classes->sum('students_count')) }} total
            @endif
        </div>
        <a href="{{ route('classes.create') }}" class="btn-add">
            <i class="fa-solid fa-plus"></i> New Class
        </a>
    </div>

    <div class="table-panel">

        @if ($classes->isEmpty())
            <div class="empty-state">
                <div class="empty-state__icon">
                    <i class="fa-solid fa-chalkboard"></i>
                </div>
                <div class="empty-state__title">No classes yet</div>
                <div class="empty-state__desc">
                    Classes are the foundation of your evaluation system. Create your first class to start adding students and
                    assessments.
                </div>
                <a href="{{ route('classes.create') }}" class="btn-add">
                    <i class="fa-solid fa-plus"></i> Create First Class
                </a>
            </div>
        @else
            <table class="classes-table">
                <thead>
                    <tr>
                        <th>Class</th>
                        <th>Students</th>
                        <th class="text-right"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($classes as $class)
                        <tr>
                            <td>
                                <div class="class-name">{{ $class->name }}</div>
                                @if ($class->grade_level)
                                    <div class="class-grade">{{ $class->grade_level }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="student-count {{ $class->students_count == 0 ? 'empty' : '' }}">
                                    <i class="fa-solid fa-user-group"></i>
                                    {{ $class->students_count ?? 0 }}
                                </span>
                            </td>
                            <td>
                                <div class="row-actions">
                                    <a href="{{ route('classes.edit', $class) }}" class="row-action-btn" title="Edit">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

    </div>

@endsection