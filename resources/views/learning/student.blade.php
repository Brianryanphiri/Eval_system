@extends('layouts.app')

@section('title', 'Learning Profile — ' . $student->full_name)
@section('page-title', $student->full_name)

@section('breadcrumbs')
    <a href="{{ route('learning.flags') }}">Learning Flags</a>
    <span class="sep">/</span>
    <span>{{ $student->full_name }}</span>
@endsection

@section('styles')
    <style>
        /* Back */
        .lp-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 500;
            color: var(--pts-grey);
            text-decoration: none;
            margin-bottom: 16px;
        }

        .lp-back:hover {
            color: var(--pts-green-dark);
        }

        /* Student header */
        .lp-header {
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 14px;
            padding: 22px;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .lp-avatar {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: var(--pts-black);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            font-weight: 800;
            flex-shrink: 0;
        }

        .lp-info {
            flex: 1;
            min-width: 0;
        }

        .lp-name {
            font-size: 20px;
            font-weight: 800;
            color: #111827;
            margin-bottom: 4px;
        }

        .lp-meta {
            font-size: 13px;
            color: var(--pts-grey);
        }

        .lp-meta strong {
            color: #374151;
            font-weight: 700;
        }

        /* Current flag chip */
        .lp-current-flag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            flex-shrink: 0;
        }

        .lp-current-flag.red {
            background: #FEE2E2;
            color: #991B1B;
        }

        .lp-current-flag.amber {
            background: #FEF3C7;
            color: #92400E;
        }

        .lp-current-flag.green {
            background: #DCFCE7;
            color: #166534;
        }

        .lp-current-flag.grey {
            background: #F3F4F6;
            color: #6B7280;
        }

        /* Two-column grid */
        .lp-grid {
            display: grid;
            grid-template-columns: 1.4fr 1fr;
            gap: 18px;
            margin-bottom: 18px;
        }

        .lp-panel {
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 14px;
            padding: 22px;
        }

        .lp-panel__title {
            font-size: 15px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .lp-panel__title i {
            color: var(--pts-green-dark);
            font-size: 14px;
        }

        /* Metric grid */
        .lp-metrics {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
        }

        .lp-metric {
            padding: 14px;
            background: var(--pts-bg);
            border-radius: 10px;
            text-align: center;
        }

        .lp-metric__label {
            font-size: 10.5px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--pts-grey);
            font-weight: 700;
            margin-bottom: 6px;
        }

        .lp-metric__value {
            font-size: 20px;
            font-weight: 800;
            color: #111827;
            line-height: 1;
        }

        .lp-metric__value.green {
            color: #16A34A;
        }

        .lp-metric__value.red {
            color: #DC2626;
        }

        .lp-metric__value.blue {
            color: #2563EB;
        }

        /* Subject breakdown */
        .lp-subject {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid var(--pts-grey-light);
        }

        .lp-subject:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .lp-subject__name {
            font-size: 13.5px;
            font-weight: 600;
            color: #111827;
        }

        .lp-subject__delta {
            font-size: 11.5px;
            font-weight: 700;
            margin-left: 8px;
        }

        .lp-subject__delta.up {
            color: #16A34A;
        }

        .lp-subject__delta.down {
            color: #DC2626;
        }

        .lp-subject__delta.flat {
            color: var(--pts-grey);
        }

        .lp-subject__right {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .lp-subject__score {
            font-size: 14px;
            font-weight: 800;
            color: #111827;
        }

        .lp-subject__grade {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 26px;
            height: 26px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 800;
        }

        .lp-subject__grade.a {
            background: #DCFCE7;
            color: #166534;
        }

        .lp-subject__grade.b {
            background: #DBEAFE;
            color: #1E40AF;
        }

        .lp-subject__grade.c {
            background: #FEF3C7;
            color: #92400E;
        }

        .lp-subject__grade.d {
            background: #FFE4E6;
            color: #9F1239;
        }

        .lp-subject__grade.e,
        .lp-subject__grade.f {
            background: #FEE2E2;
            color: #991B1B;
        }

        /* History */
        .lp-history {
            display: flex;
            align-items: center;
            padding: 14px 0;
            border-bottom: 1px solid var(--pts-grey-light);
            gap: 14px;
        }

        .lp-history:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .lp-history__term {
            width: 110px;
            font-size: 13px;
            font-weight: 700;
            color: #111827;
            flex-shrink: 0;
        }

        .lp-history__flag {
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 10.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            flex-shrink: 0;
        }

        .lp-history__flag.red {
            background: #FEE2E2;
            color: #991B1B;
        }

        .lp-history__flag.amber {
            background: #FEF3C7;
            color: #92400E;
        }

        .lp-history__flag.green {
            background: #DCFCE7;
            color: #166534;
        }

        .lp-history__flag.grey {
            background: #F3F4F6;
            color: #6B7280;
        }

        .lp-history__score {
            margin-left: auto;
            font-size: 14px;
            font-weight: 800;
            color: #111827;
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

        @media (max-width: 900px) {
            .lp-grid {
                grid-template-columns: 1fr;
            }

            .lp-metrics {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
@endsection

@section('content')

    <a href="{{ route('learning.flags') }}" class="lp-back">
        <i class="fa-solid fa-arrow-left"></i> Back to Learning Flags
    </a>

    {{-- Student header --}}
    <div class="lp-header">
        <div class="lp-avatar">
            {{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}
        </div>
        <div class="lp-info">
            <div class="lp-name">{{ $student->full_name }}</div>
            <div class="lp-meta">
                <strong>{{ $student->student_number }}</strong>
                @if ($student->currentClassroom())
                    · {{ $student->currentClassroom()->name }}
                @endif
                @if ($latest?->term)
                    · {{ $latest->term->name }}
                @endif
            </div>
        </div>
        @if ($latest)
            <div class="lp-current-flag {{ $latest->flag_color }}">
                {{ $latest->flag_label }}
            </div>
        @endif
    </div>

    @if (!$latest)
        <div class="empty-state">
            <div class="empty-state__icon"><i class="fa-solid fa-brain"></i></div>
            <div class="empty-state__title">No learning profile yet</div>
            <div class="empty-state__desc">
                This student doesn't have a learning profile for the current term. Recompute from the Learning Flags page.
            </div>
        </div>
    @else

        <div class="lp-grid">

            {{-- Left: Metrics + subject breakdown --}}
            <div>
                <div class="lp-panel" style="margin-bottom: 18px;">
                    <div class="lp-panel__title">
                        <i class="fa-solid fa-chart-simple"></i> Current Metrics
                    </div>

                    <div class="lp-metrics">
                        <div class="lp-metric">
                            <div class="lp-metric__label">Overall</div>
                            <div class="lp-metric__value">{{ number_format($latest->overall_score, 1) }}%</div>
                        </div>
                        <div class="lp-metric">
                            <div class="lp-metric__label">Peer avg</div>
                            <div class="lp-metric__value blue">{{ number_format($latest->peer_average, 1) }}%</div>
                        </div>
                        <div class="lp-metric">
                            <div class="lp-metric__label">Z-score</div>
                            <div class="lp-metric__value">{{ number_format($latest->z_score, 2) }}</div>
                        </div>
                        <div class="lp-metric">
                            <div class="lp-metric__label">Spread</div>
                            <div class="lp-metric__value">{{ number_format($latest->spread, 1) }}</div>
                        </div>
                        <div class="lp-metric">
                            <div class="lp-metric__label">Volatility</div>
                            <div class="lp-metric__value">{{ number_format($latest->volatility, 1) }}</div>
                        </div>
                        <div class="lp-metric">
                            <div class="lp-metric__label">Change</div>
                            <div
                                class="lp-metric__value {{ $latest->overall_delta > 1 ? 'green' : ($latest->overall_delta < -1 ? 'red' : '') }}">
                                @if ($latest->overall_delta === null)
                                    —
                                @else
                                    {{ $latest->overall_delta > 0 ? '+' : '' }}{{ number_format($latest->overall_delta, 1) }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lp-panel">
                    <div class="lp-panel__title">
                        <i class="fa-solid fa-book-open"></i> Subject Breakdown
                    </div>

                    @if ($subjectBreakdown->isEmpty())
                        <div style="text-align: center; padding: 30px; color: var(--pts-grey); font-size: 13px;">
                            No subject-level data available.
                        </div>
                    @else
                        @foreach ($subjectBreakdown as $item)
                            @php
                                $gradeLower = strtolower($item['grade'] ?? 'f');
                                $delta = $item['delta'];
                                $deltaClass = $delta === null ? 'flat' : ($delta > 1 ? 'up' : ($delta < -1 ? 'down' : 'flat'));
                            @endphp
                            <div class="lp-subject">
                                <div>
                                    <span class="lp-subject__name">{{ $item['subject'] }}</span>
                                    @if ($delta !== null)
                                        <span class="lp-subject__delta {{ $deltaClass }}">
                                            @if ($delta > 1)
                                                ▲ {{ number_format($delta, 1) }}
                                            @elseif ($delta < -1)
                                                ▼ {{ number_format(abs($delta), 1) }}
                                            @else
                                                — 0
                                            @endif
                                        </span>
                                    @endif
                                </div>
                                <div class="lp-subject__right">
                                    <span
                                        class="lp-subject__score">{{ $item['score'] !== null ? number_format($item['score'], 1) . '%' : '—' }}</span>
                                    @if ($item['grade'])
                                        <span class="lp-subject__grade {{ $gradeLower }}">{{ $item['grade'] }}</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            {{-- Right: History + Recommendation --}}
            <div>
                <div class="lp-panel" style="margin-bottom: 18px;">
                    <div class="lp-panel__title">
                        <i class="fa-solid fa-clock-rotate-left"></i> History
                    </div>

                    @if ($profiles->isEmpty())
                        <div style="text-align: center; padding: 20px; color: var(--pts-grey); font-size: 13px;">
                            No history yet.
                        </div>
                    @else
                        @foreach ($profiles as $p)
                            <div class="lp-history">
                                <div class="lp-history__term">
                                    {{ $p->term?->name ?? 'Term' }}
                                    <div style="font-size: 11px; color: var(--pts-grey); font-weight: 500;">
                                        {{ $p->term?->academicYear?->label ?? '' }}
                                    </div>
                                </div>
                                <span class="lp-history__flag {{ $p->flag_color }}">
                                    {{ $p->flag_label }}
                                </span>
                                <span class="lp-history__score">{{ number_format($p->overall_score, 1) }}%</span>
                            </div>
                        @endforeach
                    @endif
                </div>

                <div class="lp-panel">
                    <div class="lp-panel__title">
                        <i class="fa-solid fa-lightbulb"></i> What This Means
                    </div>

                    @php
                        $meaning = match ($latest->flag) {
                            'at_risk' => 'This student is failing multiple subjects or has dropped sharply. Immediate intervention is recommended — speak to their teachers, contact guardians, and consider remedial support.',
                            'needs_support' => 'This student is below the pass mark or struggling in one specific subject. A targeted intervention in that area would help.',
                            'watch' => 'This student has declined slightly and is worth monitoring. Check in over the next few assessments before it becomes a bigger issue.',
                            'uneven' => 'This student performs very differently across subjects. They are strong in some areas and weak in others — subject-specific support may help close the gap.',
                            'improving' => 'This student has shown meaningful improvement this term. Consider acknowledging this progress to reinforce it.',
                            'high_achiever' => 'This student is performing well above their peers. Consider enrichment opportunities or participation in competitions.',
                            'on_track' => 'This student is performing as expected. No action needed — continue monitoring at the next assessment.',
                            default => 'Review the metrics above to decide on next steps.',
                        };
                    @endphp

                    <p style="font-size: 13.5px; color: #374151; line-height: 1.6; margin: 0;">
                        {{ $meaning }}
                    </p>
                </div>
            </div>

        </div>

    @endif

@endsection