@extends('layouts.app')

@section('title', 'Question Analytics')
@section('page-title', 'Question Performance')

@section('styles')
    <style>
        .analytics-toolbar {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 18px;
            flex-wrap: wrap;
        }

        .filter-select {
            padding: 10px 14px;
            border: 1.5px solid var(--pts-grey-light);
            border-radius: 9px;
            font-size: 13px;
            color: #374151;
            background: #fff;
            outline: none;
            cursor: pointer;
        }

        .toolbar-spacer {
            flex: 1;
        }

        .exam-context-pill {
            font-size: 12.5px;
            font-weight: 600;
            color: var(--pts-grey);
            padding: 9px 14px;
            background: var(--pts-bg);
            border-radius: 9px;
        }

        .exam-context-pill strong {
            color: #111827;
        }

        /* ---------- Summary cards ---------- */
        .analytics-stat-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 20px;
        }

        .analytics-stat {
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 14px;
            padding: 18px;
        }

        .analytics-stat__value {
            font-size: 24px;
            font-weight: 800;
            color: #111827;
        }

        .analytics-stat__label {
            font-size: 12.5px;
            color: var(--pts-grey);
            margin-top: 4px;
        }

        .analytics-stat.flagged {
            background: #FEF2F2;
            border-color: #FECACA;
        }

        .analytics-stat.flagged .analytics-stat__value {
            color: #DC2626;
        }

        /* ---------- Question performance table ---------- */
        .qp-panel {
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 14px;
            padding: 24px;
            margin-bottom: 18px;
        }

        .qp-panel__title {
            font-size: 15px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 4px;
        }

        .qp-panel__sub {
            font-size: 12.5px;
            color: var(--pts-grey);
            margin-bottom: 20px;
        }

        .qp-row {
            display: grid;
            grid-template-columns: 46px 1fr 140px 90px 90px;
            gap: 16px;
            align-items: center;
            padding: 14px 0;
            border-bottom: 1px solid var(--pts-grey-light);
        }

        .qp-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .qp-row.header {
            font-size: 10.5px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--pts-grey);
            font-weight: 700;
            border-bottom: 2px solid var(--pts-grey-light);
        }

        .qp-num {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            background: var(--pts-bg);
            color: #374151;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 800;
        }

        .qp-row.flagged .qp-num {
            background: #FEF2F2;
            color: #DC2626;
        }

        .qp-topic-name {
            font-size: 13.5px;
            font-weight: 700;
            color: #111827;
        }

        .qp-topic-meta {
            font-size: 11.5px;
            color: var(--pts-grey);
            margin-top: 2px;
        }

        .qp-bar-track {
            height: 9px;
            background: var(--pts-bg);
            border-radius: 6px;
            overflow: hidden;
            margin-bottom: 4px;
        }

        .qp-bar-fill {
            height: 100%;
            border-radius: 6px;
        }

        .qp-success-value {
            font-size: 11.5px;
            font-weight: 700;
            color: #374151;
        }

        .qp-difficulty-pill {
            text-align: center;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 20px;
            display: inline-block;
        }

        .qp-difficulty-pill.easy {
            background: var(--pts-green-light);
            color: var(--pts-green-dark);
        }

        .qp-difficulty-pill.medium {
            background: #FFFBEB;
            color: #D97706;
        }

        .qp-difficulty-pill.high {
            background: #FEF2F2;
            color: #DC2626;
        }

        .qp-review-badge {
            font-size: 10.5px;
            font-weight: 800;
            color: #DC2626;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* ---------- Topic mastery panel ---------- */
        .topic-mastery-row {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 14px;
        }

        .topic-mastery-row:last-child {
            margin-bottom: 0;
        }

        .topic-mastery-row__label {
            width: 130px;
            font-size: 12.5px;
            font-weight: 600;
            color: #111827;
            flex-shrink: 0;
        }

        .topic-mastery-row__track {
            flex: 1;
            height: 12px;
            background: var(--pts-bg);
            border-radius: 6px;
            overflow: hidden;
        }

        .topic-mastery-row__fill {
            height: 100%;
            border-radius: 6px;
        }

        .topic-mastery-row__pct {
            width: 42px;
            text-align: right;
            font-size: 12.5px;
            font-weight: 700;
            color: #111827;
            flex-shrink: 0;
        }

        /* ---------- Investigate CTA ---------- */
        .review-cta {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 16px 18px;
            background: #FEF2F2;
            border: 1.5px solid #FECACA;
            border-radius: 10px;
            margin-top: 4px;
        }

        .review-cta i {
            color: #DC2626;
            font-size: 18px;
            flex-shrink: 0;
        }

        .review-cta__text {
            font-size: 13px;
            color: #7F1D1D;
            flex: 1;
        }

        .review-cta__text strong {
            display: block;
            font-size: 13.5px;
            margin-bottom: 2px;
        }

        .review-cta__btn {
            padding: 9px 16px;
            background: #DC2626;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 12.5px;
            font-weight: 700;
            cursor: pointer;
            white-space: nowrap;
        }

        @media (max-width: 1100px) {
            .analytics-stat-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .qp-row,
            .qp-row.header {
                grid-template-columns: 40px 1fr 100px 80px;
            }

            .qp-row .qp-difficulty-col {
                display: none;
            }
        }
    </style>
@endsection

@section('content')

    <div class="analytics-toolbar">
        <select class="filter-select">
            <option>{{ $assessment->name }}</option>
        </select>
        <select class="filter-select">
            <option>{{ $assessment->subject->name }}</option>
        </select>
        <div class="toolbar-spacer"></div>
        <div class="exam-context-pill"><strong>{{ $studentCount }}</strong> students · <strong>{{ $classAverage }}%</strong>
            class average</div>
    </div>

    <div class="analytics-stat-grid">
        <div class="analytics-stat">
            <div class="analytics-stat__value">{{ count($questions) }}</div>
            <div class="analytics-stat__label">Total Questions</div>
        </div>
        <div class="analytics-stat">
            <div class="analytics-stat__value">{{ round(collect($questions)->avg('success')) }}%</div>
            <div class="analytics-stat__label">Average Success Rate</div>
        </div>
        <div class="analytics-stat">
            <div class="analytics-stat__value">{{ collect($questions)->max('success') }}%</div>
            <div class="analytics-stat__label">Highest Performing</div>
        </div>
        <div class="analytics-stat flagged">
            <div class="analytics-stat__value">{{ $flaggedQuestions->count() }}</div>
            <div class="analytics-stat__label">Questions Flagged for Review</div>
        </div>
    </div>

    <div class="qp-panel">
        <div class="qp-panel__title">Question Performance Index</div>
        <div class="qp-panel__sub">Success rate per question, with automatic flagging for questions under 40%.</div>

        <div class="qp-row header">
            <div>Q#</div>
            <div>Topic & Objective</div>
            <div>Success Rate</div>
            <div class="qp-difficulty-col">Difficulty</div>
            <div></div>
        </div>

        @foreach ($questions as $q)
            <div class="qp-row {{ $q['success'] < 40 ? 'flagged' : '' }}">
                <div class="qp-num">{{ $q['no'] }}</div>
                <div>
                    <div class="qp-topic-name">{{ $q['topic'] }}</div>
                    <div class="qp-topic-meta">{{ $q['objective'] }}</div>
                </div>
                <div>
                    <div class="qp-bar-track">
                        <div class="qp-bar-fill"
                            style="width: {{ $q['success'] }}%; background: {{ $q['success'] >= 65 ? 'var(--pts-green)' : ($q['success'] >= 40 ? '#D97706' : '#DC2626') }};">
                        </div>
                    </div>
                    <div class="qp-success-value">{{ $q['success'] }}% success</div>
                </div>
                <div class="qp-difficulty-col">
                    <span class="qp-difficulty-pill {{ $q['difficulty'] }}">{{ ucfirst($q['difficulty']) }}</span>
                </div>
                <div>
                    @if ($q['success'] < 40)
                        <div class="qp-review-badge"><i class="fa-solid fa-triangle-exclamation"></i> Review</div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    @if ($flaggedQuestions->isNotEmpty())
        <div class="qp-panel">
            <div class="review-cta">
                <i class="fa-solid fa-circle-exclamation"></i>
                <div class="review-cta__text">
                    <strong>{{ $flaggedQuestions->count() }} question(s) require review</strong>
                    Success rate below 40% may indicate low topic mastery, question difficulty, or unclear wording. Investigate
                    before reusing this question.
                </div>
                <button type="button" class="review-cta__btn">Investigate</button>
            </div>
        </div>
    @endif

    <div class="qp-panel">
        <div class="qp-panel__title">Topic Mastery Breakdown</div>
        <div class="qp-panel__sub">Aggregated performance by topic across all questions in this examination.</div>

        @foreach ($topics as [$topic, $score])
            <div class="topic-mastery-row">
                <div class="topic-mastery-row__label">{{ $topic }}</div>
                <div class="topic-mastery-row__track">
                    <div class="topic-mastery-row__fill"
                        style="width: {{ $score }}%; background: {{ $score >= 65 ? 'var(--pts-green)' : ($score >= 50 ? '#D97706' : '#DC2626') }};">
                    </div>
                </div>
                <div class="topic-mastery-row__pct">{{ $score }}%</div>
            </div>
        @endforeach
    </div>

@endsection