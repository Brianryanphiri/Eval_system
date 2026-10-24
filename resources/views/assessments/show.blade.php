@extends('layouts.app')

@section('title', $assessment->name)
@section('page-title', 'Assessment Overview')

@section('styles')
    <style>
        .assess-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 14px;
        }

        .assess-header__back {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--pts-grey);
            text-decoration: none;
            margin-bottom: 10px;
        }

        .assess-header__back:hover {
            color: var(--pts-green-dark);
        }

        .assess-header__title {
            font-size: 21px;
            font-weight: 800;
            color: #111827;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .assess-header__meta {
            font-size: 13.5px;
            color: var(--pts-grey);
            margin-top: 6px;
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
        }

        .assess-header__meta span {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .status-badge {
            font-size: 11.5px;
            font-weight: 700;
            padding: 5px 14px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .status-badge.draft {
            background: var(--pts-bg);
            color: var(--pts-grey);
        }

        .status-badge.submitted,
        .status-badge.under_review,
        .status-badge.moderation {
            background: #FFFBEB;
            color: #D97706;
        }

        .status-badge.approved,
        .status-badge.published {
            background: #EFF6FF;
            color: #2563EB;
        }

        .status-badge.locked {
            background: var(--pts-black);
            color: #fff;
        }

        .status-badge.amended {
            background: #FEF2F2;
            color: #DC2626;
        }

        .header-actions {
            display: flex;
            gap: 10px;
        }

        .btn-secondary {
            padding: 10px 18px;
            background: #fff;
            border: 1.5px solid var(--pts-grey-light);
            border-radius: 9px;
            font-size: 13px;
            font-weight: 700;
            color: #374151;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-secondary:hover {
            border-color: var(--pts-green);
            color: var(--pts-green-dark);
        }

        .btn-primary {
            padding: 10px 18px;
            background: var(--pts-black);
            color: #fff;
            border: none;
            border-radius: 9px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-primary:hover {
            background: var(--pts-green-dark);
        }

        .btn-danger {
            background: #fff;
            border: 1.5px solid #FECACA;
            color: #DC2626;
        }

        .btn-danger:hover {
            background: #FEF2F2;
        }

        /* ---------- Stat row ---------- */
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 14px;
            padding: 18px;
        }

        .stat-card__value {
            font-size: 22px;
            font-weight: 800;
            color: #111827;
        }

        .stat-card__label {
            font-size: 12px;
            color: var(--pts-grey);
            margin-top: 4px;
        }

        /* ---------- Action cards ---------- */
        .action-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 20px;
        }

        .action-card {
            background: var(--pts-white);
            border: 1.5px solid var(--pts-grey-light);
            border-radius: 14px;
            padding: 20px;
            text-decoration: none;
            display: block;
            transition: border-color 0.15s;
        }

        .action-card:hover {
            border-color: var(--pts-green);
        }

        .action-card__icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: var(--pts-green-light);
            color: var(--pts-green-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            margin-bottom: 14px;
        }

        .action-card__title {
            font-size: 14.5px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 4px;
        }

        .action-card__desc {
            font-size: 12.5px;
            color: var(--pts-grey);
            line-height: 1.4;
        }

        .action-card__arrow {
            margin-top: 12px;
            font-size: 12px;
            color: var(--pts-green-dark);
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* ---------- Questions table ---------- */
        .panel {
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 14px;
            padding: 24px;
            margin-bottom: 18px;
        }

        .panel__head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
        }

        .panel__title {
            font-size: 15px;
            font-weight: 700;
            color: #111827;
        }

        .panel__link {
            font-size: 12.5px;
            font-weight: 600;
            color: var(--pts-green-dark);
            text-decoration: none;
        }

        .panel__link:hover {
            text-decoration: underline;
        }

        .q-row {
            display: grid;
            grid-template-columns: 40px 1fr 100px 90px;
            gap: 14px;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid var(--pts-grey-light);
        }

        .q-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .q-row.header {
            font-size: 10.5px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--pts-grey);
            font-weight: 700;
            border-bottom: 2px solid var(--pts-grey-light);
        }

        .q-num {
            width: 30px;
            height: 30px;
            border-radius: 7px;
            background: var(--pts-bg);
            color: #374151;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 800;
        }

        .q-topic {
            font-size: 13.5px;
            font-weight: 600;
            color: #111827;
        }

        .q-meta {
            font-size: 11.5px;
            color: var(--pts-grey);
            margin-top: 2px;
        }

        .q-difficulty-pill {
            text-align: center;
            font-size: 10.5px;
            font-weight: 700;
            padding: 3px 9px;
            border-radius: 20px;
            display: inline-block;
        }

        .q-difficulty-pill.easy {
            background: var(--pts-green-light);
            color: var(--pts-green-dark);
        }

        .q-difficulty-pill.medium {
            background: #FFFBEB;
            color: #D97706;
        }

        .q-difficulty-pill.hard {
            background: #FEF2F2;
            color: #DC2626;
        }

        .empty-state {
            text-align: center;
            padding: 30px 20px;
            color: var(--pts-grey);
        }

        .empty-state i {
            font-size: 24px;
            margin-bottom: 10px;
            display: block;
        }

        .lock-warning {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 16px;
            background: #FEF2F2;
            border: 1.5px solid #FECACA;
            border-radius: 10px;
            margin-bottom: 18px;
            font-size: 13px;
            color: #7F1D1D;
        }

        .lock-warning i {
            color: #DC2626;
            font-size: 16px;
        }

        @media (max-width: 1100px) {

            .stat-grid,
            .action-grid {
                grid-template-columns: 1fr 1fr;
            }
        }
    </style>
@endsection

@section('content')

    @php
        $totalMarks = $assessment->allocatedMarks();
        $resultsCount = $assessment->results()->count();
        $classSize = $assessment->classroom->currentStudents()->count();
        $classAverage = $assessment->results()->avg('percentage');
    @endphp

    <div class="assess-header">
        <div>
            <a href="{{ route('assessments.index') }}" class="assess-header__back"><i class="fa-solid fa-arrow-left"></i>
                Back to Assessments</a>
            <div class="assess-header__title">
                {{ $assessment->name }}
                <span class="status-badge {{ $assessment->status }}">{{ str_replace('_', ' ', $assessment->status) }}</span>
            </div>
            <div class="assess-header__meta">
                <span><i class="fa-solid fa-book"></i> {{ $assessment->subject->name }}</span>
                <span><i class="fa-solid fa-chalkboard"></i> {{ $assessment->classroom->name }}</span>
                <span><i class="fa-solid fa-clock"></i> {{ $assessment->duration_minutes }} min</span>
                <span><i class="fa-solid fa-bullseye"></i> Pass mark: {{ $assessment->pass_mark ?? '—' }}%</span>
            </div>
        </div>

        <div class="header-actions">
            @if (!$assessment->isLocked())
                @can('assessment.edit')
                    <a href="{{ route('assessments.edit', $assessment) }}" class="btn-secondary"><i class="fa-solid fa-pen"></i>
                        Edit</a>
                @endcan
                @can('results.enter')
                    <form method="POST" action="{{ route('marking.lock', $assessment) }}"
                        onsubmit="return confirm('Lock this assessment? Marks cannot be edited after locking without a correction entry.');">
                        @csrf
                        <button type="submit" class="btn-primary"><i class="fa-solid fa-lock"></i> Lock Assessment</button>
                    </form>
                @endcan
            @else
                <span class="btn-secondary" style="cursor:default;"><i class="fa-solid fa-lock"></i> Locked
                    {{ $assessment->locked_at?->format('d M Y') }}</span>
            @endif
        </div>
    </div>

    @if ($assessment->questions->isNotEmpty() && $assessment->allocatedMarks() != $assessment->total_marks)
        <div class="lock-warning">
            <i class="fa-solid fa-triangle-exclamation"></i>
            Questions total {{ $totalMarks }} marks, but this assessment is set to {{ $assessment->total_marks }} total marks.
            Review before locking.
        </div>
    @endif

    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-card__value">{{ $assessment->total_marks }}</div>
            <div class="stat-card__label">Total Marks</div>
        </div>
        <div class="stat-card">
            <div class="stat-card__value">{{ $assessment->questions->count() }}</div>
            <div class="stat-card__label">Questions</div>
        </div>
        <div class="stat-card">
            <div class="stat-card__value">{{ $resultsCount }} / {{ $classSize }}</div>
            <div class="stat-card__label">Students Marked</div>
        </div>
        <div class="stat-card">
            <div class="stat-card__value">{{ $classAverage ? round($classAverage, 1) . '%' : '—' }}</div>
            <div class="stat-card__label">Class Average</div>
        </div>
    </div>

    <div class="action-grid">
        @can('assessment.edit')
            <a href="{{ route('questions.create', $assessment) }}" class="action-card">
                <div class="action-card__icon"><i class="fa-solid fa-list-check"></i></div>
                <div class="action-card__title">Question Bank</div>
                <div class="action-card__desc">Add or edit questions with topic, difficulty, and cognitive level tagging.</div>
                <div class="action-card__arrow">Manage Questions <i class="fa-solid fa-arrow-right"></i></div>
            </a>
        @endcan

        @can('results.enter')
            <a href="{{ route('marking.edit', $assessment) }}" class="action-card">
                <div class="action-card__icon"><i class="fa-solid fa-pen-to-square"></i></div>
                <div class="action-card__title">Enter Marks</div>
                <div class="action-card__desc">Open the marking grid to record or update student scores per question.</div>
                <div class="action-card__arrow">Go to Marking <i class="fa-solid fa-arrow-right"></i></div>
            </a>
        @endcan

        <a href="{{ route('analytics.question-performance', $assessment) }}" class="action-card">
            <div class="action-card__icon"><i class="fa-solid fa-chart-simple"></i></div>
            <div class="action-card__title">Question Analytics</div>
            <div class="action-card__desc">See success rates per question and topic mastery once marking is complete.</div>
            <div class="action-card__arrow">View Analytics <i class="fa-solid fa-arrow-right"></i></div>
        </a>
    </div>

    <div class="panel">
        <div class="panel__head">
            <div class="panel__title">Questions</div>
            @if (!$assessment->isLocked())
                @can('assessment.edit')
                    <a href="{{ route('questions.create', $assessment) }}" class="panel__link">Edit Questions</a>
                @endcan
            @endif
        </div>

        @if ($assessment->questions->isEmpty())
            <div class="empty-state">
                <i class="fa-regular fa-file-lines"></i>
                No questions added yet.
                @can('assessment.edit')
                    <div style="margin-top:10px;">
                        <a href="{{ route('questions.create', $assessment) }}" class="btn-primary" style="display:inline-flex;">Add
                            Questions</a>
                    </div>
                @endcan
            </div>
        @else
            <div class="q-row header">
                <div>Q#</div>
                <div>Topic & Objective</div>
                <div>Difficulty</div>
                <div>Marks</div>
            </div>
            @foreach ($assessment->questions as $q)
                <div class="q-row">
                    <div class="q-num">{{ $q->question_number }}</div>
                    <div>
                        <div class="q-topic">{{ $q->topic ?? 'Untitled topic' }}</div>
                        <div class="q-meta">{{ $q->learning_objective ?? $q->question_text }}</div>
                    </div>
                    <div><span class="q-difficulty-pill {{ $q->difficulty }}">{{ ucfirst($q->difficulty) }}</span></div>
                    <div style="font-size:13px; font-weight:700; color:#111827;">{{ $q->max_marks }}</div>
                </div>
            @endforeach
        @endif
    </div>

@endsection