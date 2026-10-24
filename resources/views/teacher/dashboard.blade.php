@extends('layouts.app')

@section('title', 'My Classes')
@section('page-title', 'Teacher Dashboard')

@section('styles')
    <style>
        .teacher-welcome {
            margin-bottom: 20px;
        }

        .teacher-welcome__title {
            font-size: 19px;
            font-weight: 800;
            color: #111827;
        }

        .teacher-welcome__sub {
            font-size: 13.5px;
            color: var(--pts-grey);
            margin-top: 4px;
        }

        /* ---------- Class selector tabs ---------- */
        .class-tabs {
            display: flex;
            gap: 8px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .class-tab {
            padding: 10px 18px;
            border: 1.5px solid var(--pts-grey-light);
            border-radius: 9px;
            background: #fff;
            font-size: 13px;
            font-weight: 700;
            color: #374151;
            cursor: pointer;
        }

        .class-tab.active {
            background: var(--pts-black);
            border-color: var(--pts-black);
            color: #fff;
        }

        /* ---------- Stat row ---------- */
        .t-stat-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 20px;
        }

        .t-stat {
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 14px;
            padding: 18px;
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .t-stat__icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
        }

        .t-stat__icon.green {
            background: var(--pts-green-light);
            color: var(--pts-green-dark);
        }

        .t-stat__icon.amber {
            background: #FFFBEB;
            color: #D97706;
        }

        .t-stat__icon.red {
            background: #FEF2F2;
            color: #DC2626;
        }

        .t-stat__icon.black {
            background: #F3F4F6;
            color: var(--pts-black);
        }

        .t-stat__value {
            font-size: 21px;
            font-weight: 800;
            color: #111827;
            line-height: 1;
        }

        .t-stat__label {
            font-size: 12px;
            color: var(--pts-grey);
            margin-top: 3px;
        }

        /* ---------- Two column ---------- */
        .t-grid {
            display: grid;
            grid-template-columns: 1.4fr 1fr;
            gap: 18px;
        }

        .panel {
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 14px;
            padding: 22px;
            margin-bottom: 18px;
        }

        .panel__title {
            font-size: 15px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 4px;
        }

        .panel__sub {
            font-size: 12.5px;
            color: var(--pts-grey);
            margin-bottom: 18px;
        }

        /* ---------- Instructional insight card ---------- */
        .insight-card {
            background: var(--pts-black);
            border-radius: 12px;
            padding: 20px;
            color: #fff;
            margin-bottom: 14px;
        }

        .insight-card:last-child {
            margin-bottom: 0;
        }

        .insight-card__label {
            font-size: 10.5px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #9CA3AF;
            margin-bottom: 8px;
        }

        .insight-card__topic {
            font-size: 15px;
            font-weight: 700;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .insight-card__topic i {
            color: var(--pts-green);
        }

        .insight-card__mastery {
            font-size: 12.5px;
            color: #C9CBCF;
            margin-bottom: 12px;
        }

        .insight-card__recommendation {
            font-size: 12.5px;
            color: #E5E7EB;
            line-height: 1.6;
            padding: 12px 14px;
            background: rgba(122, 193, 66, 0.12);
            border-left: 3px solid var(--pts-green);
            border-radius: 6px;
        }

        /* ---------- Topic performance bars ---------- */
        .topic-row {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 13px;
        }

        .topic-row:last-child {
            margin-bottom: 0;
        }

        .topic-row__label {
            width: 110px;
            font-size: 12.5px;
            font-weight: 600;
            color: #111827;
            flex-shrink: 0;
        }

        .topic-row__track {
            flex: 1;
            height: 10px;
            background: var(--pts-bg);
            border-radius: 6px;
            overflow: hidden;
        }

        .topic-row__fill {
            height: 100%;
            border-radius: 6px;
        }

        .topic-row__pct {
            width: 40px;
            text-align: right;
            font-size: 12.5px;
            font-weight: 700;
            color: #111827;
            flex-shrink: 0;
        }

        /* ---------- Students needing attention ---------- */
        .attn-student {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 0;
            border-bottom: 1px solid var(--pts-grey-light);
        }

        .attn-student:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .attn-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: var(--pts-black);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .attn-info__name {
            font-size: 13px;
            font-weight: 600;
            color: #111827;
        }

        .attn-info__note {
            font-size: 11.5px;
            color: var(--pts-grey);
            margin-top: 1px;
        }

        .attn-action-btn {
            margin-left: auto;
            padding: 6px 12px;
            border: 1.5px solid var(--pts-grey-light);
            border-radius: 7px;
            background: #fff;
            font-size: 11.5px;
            font-weight: 700;
            color: #374151;
            cursor: pointer;
            white-space: nowrap;
        }

        .attn-action-btn:hover {
            border-color: var(--pts-green);
            color: var(--pts-green-dark);
        }

        /* ---------- Excelling students ---------- */
        .excel-pill-list {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .excel-pill {
            display: flex;
            align-items: center;
            gap: 7px;
            padding: 7px 12px;
            background: var(--pts-green-light);
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            color: var(--pts-green-dark);
        }

        @media (max-width: 1100px) {
            .t-stat-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .t-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')

    @php
        $insightTopics = $insightTopics ?? [
            ['Algebra', 78],
            ['Geometry', 71],
            ['Statistics', 65],
            ['Functions', 54],
            ['Probability', 38],
        ];
        $needsAttention = $needsAttention ?? [
            ['John Banda', 'Below pass threshold, 2 topics weak'],
            ['Chikondi Mvula', 'Declining trend, -11% this term'],
            ['Thandiwe Zulu', '3 missed assessments'],
        ];
        $excelling = $excelling ?? ['Grace Phiri', 'Mercy Chirwa', 'Dan Kamanga', 'Faith Nkhoma'];
    @endphp

    <div class="teacher-welcome">
        <div class="teacher-welcome__title">Good morning, {{ auth()->user()->name ?? 'Mr. Phiri' }}</div>
        <div class="teacher-welcome__sub">Here's how your classes are performing this term.</div>
    </div>

    <div class="class-tabs">
        <button class="class-tab active">Form 3 Mathematics</button>
        <button class="class-tab">Form 2 Mathematics</button>
        <button class="class-tab">Form 4 Additional Maths</button>
    </div>

    <div class="t-stat-grid">
        <div class="t-stat">
            <div class="t-stat__icon black"><i class="fa-solid fa-chart-simple"></i></div>
            <div>
                <div class="t-stat__value">64.8%</div>
                <div class="t-stat__label">Class Average</div>
            </div>
        </div>
        <div class="t-stat">
            <div class="t-stat__icon green"><i class="fa-solid fa-arrow-trend-up"></i></div>
            <div>
                <div class="t-stat__value">+8%</div>
                <div class="t-stat__label">Improvement</div>
            </div>
        </div>
        <div class="t-stat">
            <div class="t-stat__icon red"><i class="fa-solid fa-triangle-exclamation"></i></div>
            <div>
                <div class="t-stat__value">9</div>
                <div class="t-stat__label">Students at Risk</div>
            </div>
        </div>
        <div class="t-stat">
            <div class="t-stat__icon amber"><i class="fa-solid fa-star"></i></div>
            <div>
                <div class="t-stat__value">14</div>
                <div class="t-stat__label">Students Excelling</div>
            </div>
        </div>
    </div>

    <div class="t-grid">
        {{-- Left --}}
        <div>
            <div class="panel">
                <div class="panel__title">Topic Performance</div>
                <div class="panel__sub">Class-wide mastery by topic, based on recent assessments.</div>
                @foreach ($insightTopics as [$topic, $score])
                    <div class="topic-row">
                        <div class="topic-row__label">{{ $topic }}</div>
                        <div class="topic-row__track">
                            <div class="topic-row__fill"
                                style="width: {{ $score }}%; background: {{ $score >= 65 ? 'var(--pts-green)' : ($score >= 50 ? '#D97706' : '#DC2626') }};">
                            </div>
                        </div>
                        <div class="topic-row__pct">{{ $score }}%</div>
                    </div>
                @endforeach
            </div>

            <div class="panel">
                <div class="panel__title">Instructional Insight</div>
                <div class="panel__sub">Automatically generated from topic mastery data, not a ranking.</div>

                @if ($insight)
                    <div class="insight-card">
                        <div class="insight-card__label">Lowest Mastery Topic</div>
                        <div class="insight-card__topic"><i class="fa-solid fa-triangle-exclamation"></i>
                            {{ $insight['topic'] }} - {{ $insight['mastery'] }}% mastery
                        </div>
                        <div class="insight-card__mastery">{{ $insight['gap_note'] }}</div>
                        <div class="insight-card__recommendation">
                            <strong>Recommendation:</strong> {{ $insight['recommendation'] }}
                        </div>
                    </div>
                @else
                    <div class="insight-card">
                        <div class="insight-card__label">Status</div>
                        <div class="insight-card__topic"><i class="fa-solid fa-circle-check"
                                style="color:var(--pts-green);"></i> No significant gaps detected</div>
                        <div class="insight-card__mastery">Topic performance is fairly even across this class. No single topic
                            is significantly behind the others.</div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Right --}}
        <div>
            <div class="panel">
                <div class="panel__title">Students Needing Attention</div>
                @foreach ($needsAttention as [$name, $note])
                    <div class="attn-student">
                        <div class="attn-avatar">{{ strtoupper(substr($name, 0, 1)) }}</div>
                        <div>
                            <div class="attn-info__name">{{ $name }}</div>
                            <div class="attn-info__note">{{ $note }}</div>
                        </div>
                        <button type="button" class="attn-action-btn">View</button>
                    </div>
                @endforeach
            </div>

            <div class="panel" style="margin-bottom:0;">
                <div class="panel__title">Students Excelling</div>
                <div class="panel__sub">Recognized here, not ranked against peers.</div>
                <div class="excel-pill-list">
                    @foreach ($excelling as $name)
                        <div class="excel-pill"><i class="fa-solid fa-star"></i> {{ $name }}</div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

@endsection