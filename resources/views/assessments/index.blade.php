@extends('layouts.app')

@section('title', 'Examinations')
@section('page-title', 'Examinations')

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
            display: inline-flex;
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

        .table-panel {
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 14px;
            overflow: hidden;
        }

        .assessments-table {
            width: 100%;
            border-collapse: collapse;
        }

        .assessments-table thead th {
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

        .assessments-table tbody td {
            padding: 14px 20px;
            font-size: 13.5px;
            color: #374151;
            border-bottom: 1px solid var(--pts-grey-light);
        }

        .assessments-table tbody tr:last-child td {
            border-bottom: none;
        }

        .assessments-table tbody tr:hover {
            background: #FAFBFC;
        }

        .assess-name {
            font-size: 13.5px;
            font-weight: 600;
            color: #111827;
        }

        .assess-meta {
            font-size: 11.5px;
            color: var(--pts-grey);
            margin-top: 1px;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11.5px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 20px;
            text-transform: capitalize;
        }

        .status-pill.draft {
            background: #F3F4F6;
            color: #374151;
        }

        .status-pill.submitted {
            background: #FFFBEB;
            color: #D97706;
        }

        .status-pill.locked {
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
            display: inline-flex;
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
            margin-bottom: 18px;
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
        <div class="page-head__title">
            <strong>{{ $assessments->total() }}</strong>
            {{ Str::plural('assessment', $assessments->total()) }}
        </div>
        @can('assessment.create')
            <a href="{{ route('assessments.create') }}" class="btn-add">
                <i class="fa-solid fa-plus"></i> New Assessment
            </a>
        @endcan
    </div>

    <form method="GET" action="{{ route('assessments.index') }}" class="toolbar">
        <div class="search-box">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" name="search" placeholder="Search by name..."
                value="{{ request('search') }}" autocomplete="off">
        </div>

        <select class="filter-select" name="class" onchange="this.form.submit()">
            <option value="">All Classes</option>
            @foreach (($classrooms ?? collect()) as $c)
                <option value="{{ $c->id }}" @selected(request('class') == $c->id)>{{ $c->name }}</option>
            @endforeach
        </select>

        <select class="filter-select" name="subject" onchange="this.form.submit()">
            <option value="">All Subjects</option>
            @foreach (($subjects ?? collect()) as $s)
                <option value="{{ $s->id }}" @selected(request('subject') == $s->id)>{{ $s->name }}</option>
            @endforeach
        </select>

        <select class="filter-select" name="status" onchange="this.form.submit()">
            <option value="">All Statuses</option>
            <option value="draft" @selected(request('status') === 'draft')>Draft</option>
            <option value="submitted" @selected(request('status') === 'submitted')>Submitted</option>
            <option value="locked" @selected(request('status') === 'locked')>Locked</option>
        </select>

        @if (request('search') || request('class') || request('subject') || request('status'))
            <a href="{{ route('assessments.index') }}"
                style="font-size:12.5px; color:var(--pts-grey); text-decoration:none; padding:10px 8px;">
                Clear filters
            </a>
        @endif
    </form>

    <div class="table-panel">
        @if ($assessments->isEmpty())
            <div class="empty-state">
                <i class="fa-solid fa-file-pen"></i>
                <div class="empty-state__title">No assessments found</div>
                <div class="empty-state__desc">
                    @if (request('search') || request('class') || request('subject') || request('status'))
                        Try adjusting your filters or
                        <a href="{{ route('assessments.index') }}"
                            style="color:var(--pts-green-dark); font-weight:600;">clear them</a>.
                    @else
                        Create your first assessment to get started.
                    @endif
                </div>
                @can('assessment.create')
                    <a href="{{ route('assessments.create') }}" class="btn-add">
                        <i class="fa-solid fa-plus"></i> New Assessment
                    </a>
                @endcan
            </div>
        @else
            <table class="assessments-table">
                <thead>
                    <tr>
                        <th>Assessment</th>
                        <th>Class</th>
                        <th>Subject</th>
                        <th>Term</th>
                        <th>Marks</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($assessments as $a)
                        <tr>
                            <td>
                                <div class="assess-name">{{ $a->name }}</div>
                                <div class="assess-meta">
                                    {{ $a->assessmentType?->name ?? 'Assessment' }}
                                    @if ($a->duration_minutes) · {{ $a->duration_minutes }} min @endif
                                </div>
                            </td>
                            <td>{{ $a->classroom?->name ?? '—' }}</td>
                            <td>{{ $a->subject?->name ?? '—' }}</td>
                            <td>{{ $a->term?->name ?? '—' }}</td>
                            <td>{{ $a->total_marks }}</td>
                            <td>
                                <span class="status-pill {{ $a->status }}">
                                    @if ($a->status === 'locked')
                                        <i class="fa-solid fa-lock"></i>
                                    @elseif ($a->status === 'submitted')
                                        <i class="fa-solid fa-paper-plane"></i>
                                    @else
                                        <i class="fa-solid fa-pencil"></i>
                                    @endif
                                    {{ ucfirst($a->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="row-actions">
                                    <a href="{{ route('assessments.show', $a->id) }}" class="row-action-btn" title="View">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    @can('results.enter')
                                        <a href="{{ route('marking.edit', $a->id) }}" class="row-action-btn" title="Mark">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="table-footer">
                <div class="table-footer__count">
                    Showing {{ $assessments->firstItem() }}–{{ $assessments->lastItem() }} of {{ $assessments->total() }}
                </div>
                <div class="pagination">
                    @if ($assessments->onFirstPage())
                        <span class="disabled"><i class="fa-solid fa-chevron-left"></i></span>
                    @else
                        <a href="{{ $assessments->previousPageUrl() }}"><i class="fa-solid fa-chevron-left"></i></a>
                    @endif

                    @foreach ($assessments->getUrlRange(max(1, $assessments->currentPage() - 2), min($assessments->lastPage(), $assessments->currentPage() + 2)) as $page => $url)
                        @if ($page == $assessments->currentPage())
                            <span class="active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if ($assessments->hasMorePages())
                        <a href="{{ $assessments->nextPageUrl() }}"><i class="fa-solid fa-chevron-right"></i></a>
                    @else
                        <span class="disabled"><i class="fa-solid fa-chevron-right"></i></span>
                    @endif
                </div>
            </div>
        @endif
    </div>

@endsection