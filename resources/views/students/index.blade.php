@extends('layouts.app')

@section('title', 'Students')
@section('page-title', 'Students')

@section('styles')
    <style>
        .page-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
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
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-add:hover {
            background: var(--pts-green-dark);
        }

        .toolbar {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 18px;
            flex-wrap: wrap;
        }

        .search-box {
            position: relative;
            flex: 1;
            min-width: 220px;
            max-width: 340px;
        }

        .search-box i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--pts-grey);
            font-size: 13px;
        }

        .search-box input {
            width: 100%;
            padding: 10px 14px 10px 38px;
            border: 1.5px solid var(--pts-grey-light);
            border-radius: 9px;
            font-size: 13.5px;
            outline: none;
            background: #fff;
        }

        .search-box input:focus {
            border-color: var(--pts-green);
            box-shadow: 0 0 0 3px rgba(122, 193, 66, 0.15);
        }

        .filter-select {
            padding: 10px 14px;
            border: 1.5px solid var(--pts-grey-light);
            border-radius: 9px;
            font-size: 13.5px;
            color: #374151;
            background: #fff;
            outline: none;
            cursor: pointer;
        }

        .filter-select:focus {
            border-color: var(--pts-green);
        }

        .toolbar-spacer {
            flex: 1;
        }

        .view-toggle {
            display: flex;
            border: 1.5px solid var(--pts-grey-light);
            border-radius: 9px;
            overflow: hidden;
        }

        .view-toggle button {
            padding: 9px 12px;
            background: #fff;
            border: none;
            color: var(--pts-grey);
            cursor: pointer;
            font-size: 13px;
        }

        .view-toggle button.active {
            background: var(--pts-black);
            color: #fff;
        }

        .table-panel {
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 14px;
            overflow: hidden;
        }

        .students-table {
            width: 100%;
            border-collapse: collapse;
        }

        .students-table thead th {
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

        .students-table tbody td {
            padding: 14px 20px;
            font-size: 13.5px;
            color: #374151;
            border-bottom: 1px solid var(--pts-grey-light);
        }

        .students-table tbody tr:last-child td {
            border-bottom: none;
        }

        .students-table tbody tr:hover {
            background: #FAFBFC;
        }

        .student-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .student-avatar {
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

        .student-name {
            font-size: 13.5px;
            font-weight: 600;
            color: #111827;
        }

        .student-id {
            font-size: 11.5px;
            color: var(--pts-grey);
        }

        .avg-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 12.5px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 20px;
        }

        .avg-pill.high {
            background: var(--pts-green-light);
            color: var(--pts-green-dark);
        }

        .avg-pill.mid {
            background: #FFFBEB;
            color: #D97706;
        }

        .avg-pill.low {
            background: #FEF2F2;
            color: #DC2626;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 12px;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 20px;
            background: var(--pts-bg);
            color: #374151;
        }

        .status-pill.risk {
            background: #FEF2F2;
            color: #DC2626;
        }

        .status-pill.good {
            background: var(--pts-green-light);
            color: var(--pts-green-dark);
        }

        .row-actions {
            display: flex;
            gap: 6px;
        }

        .row-action-btn {
            width: 30px;
            height: 30px;
            border-radius: 7px;
            border: 1px solid var(--pts-grey-light);
            background: #fff;
            color: var(--pts-grey);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            text-decoration: none;
        }

        .row-action-btn:hover {
            border-color: var(--pts-green);
            color: var(--pts-green-dark);
        }

        .empty-state {
            padding: 60px 20px;
            text-align: center;
        }

        .empty-state i {
            font-size: 36px;
            color: var(--pts-grey-light);
            margin-bottom: 14px;
        }

        .empty-state__title {
            font-size: 15px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 6px;
        }

        .empty-state__desc {
            font-size: 13px;
            color: var(--pts-grey);
        }

        .table-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px;
            border-top: 1px solid var(--pts-grey-light);
        }

        .table-footer__count {
            font-size: 12.5px;
            color: var(--pts-grey);
        }

        .pagination {
            display: flex;
            gap: 6px;
            align-items: center;
        }

        .pagination a,
        .pagination span {
            min-width: 32px;
            height: 32px;
            padding: 0 8px;
            border-radius: 7px;
            border: 1px solid var(--pts-grey-light);
            background: #fff;
            color: #374151;
            cursor: pointer;
            font-size: 12.5px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .pagination a:hover {
            border-color: var(--pts-green);
            color: var(--pts-green-dark);
        }

        .pagination .active {
            background: var(--pts-black);
            color: #fff;
            border-color: var(--pts-black);
        }

        .pagination .disabled {
            opacity: 0.4;
            cursor: not-allowed;
            pointer-events: none;
        }
    </style>
@endsection

@section('content')

    <div class="page-head">
        <div class="page-head__title"><strong>{{ $students->total() }}</strong> students
            {{ request('search') || request('class') || request('status') ? 'matching filters' : 'enrolled' }}
        </div>
        @can('students.edit')
            <a href="{{ route('students.create') }}" class="btn-add"><i class="fa-solid fa-plus"></i> Add Student</a>
        @endcan
    </div>

    <form method="GET" action="{{ route('students.index') }}" class="toolbar">
        <div class="search-box">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" name="search" placeholder="Search by name or ID..." value="{{ request('search') }}"
                autocomplete="off">
        </div>

        <select class="filter-select" name="class" onchange="this.form.submit()">
            <option value="">All Classes</option>
            @foreach (($classrooms ?? collect()) as $c)
                <option value="{{ $c->id }}" @selected(request('class') == $c->id)>{{ $c->name }}</option>
            @endforeach
        </select>

        <select class="filter-select" name="status" onchange="this.form.submit()">
            <option value="">All Statuses</option>
            <option value="good" @selected(request('status') === 'good')>Good Standing</option>
            <option value="risk" @selected(request('status') === 'risk')>At Risk</option>
        </select>

        @if (request('search') || request('class') || request('status'))
            <a href="{{ route('students.index') }}"
                style="font-size:12.5px; color:var(--pts-grey); text-decoration:none; padding:10px 8px;">
                Clear filters
            </a>
        @endif

        <div class="toolbar-spacer"></div>

        <div class="view-toggle">
            <button type="button" class="active"><i class="fa-solid fa-table-list"></i></button>
            <button type="button"><i class="fa-solid fa-grip"></i></button>
        </div>
    </form>

    <div class="table-panel">
        @if ($students->isEmpty())
            <div class="empty-state">
                <i class="fa-solid fa-users"></i>
                <div class="empty-state__title">No students found</div>
                <div class="empty-state__desc">
                    @if (request('search') || request('class') || request('status'))
                        Try adjusting your filters or <a href="{{ route('students.index') }}"
                            style="color:var(--pts-green-dark); font-weight:600;">clear them</a>.
                    @else
                        Add your first student to get started.
                    @endif
                </div>
            </div>
        @else
            <table class="students-table">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Class</th>
                        <th>Overall Average</th>
                        <th>Position</th>
                        <th>Attendance</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($students as $s)
                        <tr>
                            <td>
                                <div class="student-cell">
                                    <div class="student-avatar">{{ strtoupper(substr($s->name, 0, 1)) }}</div>
                                    <div>
                                        <div class="student-name">{{ $s->name }}</div>
                                        <div class="student-id">{{ $s->student_number }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $s->class }}</td>
                            <td>
                                <span class="avg-pill {{ $s->average >= 65 ? 'high' : ($s->average >= 50 ? 'mid' : 'low') }}">
                                    {{ $s->average }}%
                                </span>
                            </td>
                            <td>{{ $s->position }}</td>
                            <td>{{ $s->attendance }}</td>
                            <td>
                                <span class="status-pill {{ $s->status }}">
                                    <i
                                        class="fa-solid {{ $s->status === 'risk' ? 'fa-triangle-exclamation' : 'fa-circle-check' }}"></i>
                                    {{ $s->status === 'risk' ? 'At Risk' : 'Good Standing' }}
                                </span>
                            </td>
                            <td>
                                <div class="row-actions">
                                    <a href="{{ route('students.show', $s->id) }}" class="row-action-btn" title="View"><i
                                            class="fa-solid fa-eye"></i></a>
                                    @can('students.edit')
                                        <a href="{{ route('students.edit', $s->id) }}" class="row-action-btn" title="Edit"><i
                                                class="fa-solid fa-pen"></i></a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="table-footer">
                <div class="table-footer__count">
                    Showing {{ $students->firstItem() }}–{{ $students->lastItem() }} of {{ $students->total() }}
                </div>
                <div class="pagination">
                    @if ($students->onFirstPage())
                        <span class="disabled"><i class="fa-solid fa-chevron-left"></i></span>
                    @else
                        <a href="{{ $students->previousPageUrl() }}"><i class="fa-solid fa-chevron-left"></i></a>
                    @endif

                    @foreach ($students->getUrlRange(max(1, $students->currentPage() - 2), min($students->lastPage(), $students->currentPage() + 2)) as $page => $url)
                        @if ($page == $students->currentPage())
                            <span class="active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if ($students->hasMorePages())
                        <a href="{{ $students->nextPageUrl() }}"><i class="fa-solid fa-chevron-right"></i></a>
                    @else
                        <span class="disabled"><i class="fa-solid fa-chevron-right"></i></span>
                    @endif
                </div>
            </div>
        @endif
    </div>

@endsection