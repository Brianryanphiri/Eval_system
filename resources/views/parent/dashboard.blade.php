@extends('layouts.app')

@section('title', 'Parent Portal')
@section('page-title', 'My Children')

@section('styles')
    <style>
        .parent-welcome {
            margin-bottom: 22px;
        }

        .parent-welcome__title {
            font-size: 19px;
            font-weight: 800;
            color: #111827;
        }

        .parent-welcome__sub {
            font-size: 13.5px;
            color: var(--pts-grey);
            margin-top: 4px;
        }

        /* ---------- Child selector ---------- */
        .child-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 22px;
            flex-wrap: wrap;
        }

        .child-tab {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 16px 10px 10px;
            border: 1.5px solid var(--pts-grey-light);
            border-radius: 12px;
            background: #fff;
            cursor: pointer;
        }

        .child-tab.active {
            border-color: var(--pts-green);
            background: var(--pts-green-light);
        }

        .child-tab__avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--pts-black);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
        }

        .child-tab__name {
            font-size: 13px;
            font-weight: 700;
            color: #111827;
        }

        .child-tab__class {
            font-size: 11px;
            color: var(--pts-grey);
        }

        /* ---------- Summary hero ---------- */
        .parent-hero {
            background: var(--pts-black);
            border-radius: 16px;
            padding: 26px 30px;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 30px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .hero-stat {
            text-align: center;
        }

        .hero-stat__value {
            font-size: 26px;
            font-weight: 800;
        }

        .hero-stat__label {
            font-size: 11px;
            color: #9CA3AF;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-top: 4px;
        }

        .hero-divider {
            width: 1px;
            height: 44px;
            background: rgba(255, 255, 255, 0.15);
        }

        .hero-standing {
            margin-left: auto;
            text-align: right;
        }

        .hero-standing__badge {
            display: inline-block;
            padding: 6px 16px;
            background: var(--pts-green);
            color: var(--pts-black);
            border-radius: 20px;
            font-size: 13px;
            font-weight: 800;
            margin-bottom: 6px;
        }

        .hero-standing__note {
            font-size: 12px;
            color: #9CA3AF;
        }

        /* ---------- Two column ---------- */
        .p-grid {
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
            margin-bottom: 18px;
        }

        /* ---------- Subject bars ---------- */
        .subj-row {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 0;
            border-bottom: 1px solid var(--pts-grey-light);
        }

        .subj-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .subj-row__name {
            width: 120px;
            font-size: 13px;
            font-weight: 600;
            color: #111827;
            flex-shrink: 0;
        }

        .subj-row__track {
            flex: 1;
            height: 10px;
            background: var(--pts-bg);
            border-radius: 6px;
            overflow: hidden;
        }

        .subj-row__fill {
            height: 100%;
            border-radius: 6px;
        }

        .subj-row__value {
            width: 44px;
            text-align: right;
            font-size: 12.5px;
            font-weight: 700;
            color: #111827;
        }

        /* ---------- Strength/support pills ---------- */
        .pill-group-title {
            font-size: 12px;
            font-weight: 700;
            color: #374151;
            margin: 16px 0 10px;
        }

        .pill-group-title:first-child {
            margin-top: 0;
        }

        .pill-row {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .pill {
            padding: 7px 13px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .pill.strong {
            background: var(--pts-green-light);
            color: var(--pts-green-dark);
        }

        .pill.support {
            background: #FEF2F2;
            color: #DC2626;
        }

        /* ---------- Teacher comment ---------- */
        .comment-box {
            background: var(--pts-bg);
            border-radius: 10px;
            padding: 16px 18px;
            font-size: 13px;
            color: #374151;
            line-height: 1.6;
        }

        .comment-box__meta {
            font-size: 11.5px;
            color: var(--pts-grey);
            margin-top: 10px;
        }

        /* ---------- Notifications ---------- */
        .notif-item {
            display: flex;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid var(--pts-grey-light);
        }

        .notif-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .notif-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12.5px;
            flex-shrink: 0;
        }

        .notif-icon.green {
            background: var(--pts-green-light);
            color: var(--pts-green-dark);
        }

        .notif-icon.amber {
            background: #FFFBEB;
            color: #D97706;
        }

        .notif-text {
            font-size: 12.5px;
            color: #374151;
            line-height: 1.5;
        }

        .notif-time {
            font-size: 11px;
            color: var(--pts-grey);
            margin-top: 2px;
        }

        .btn-report {
            width: 100%;
            padding: 12px;
            background: var(--pts-black);
            color: #fff;
            border: none;
            border-radius: 9px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 4px;
        }

        .btn-report:hover {
            background: var(--pts-green-dark);
        }

        @media (max-width: 1100px) {
            .p-grid {
                grid-template-columns: 1fr;
            }

            .parent-hero {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }

            .hero-standing {
                margin-left: 0;
                text-align: left;
            }
        }
    </style>
@endsection

@section('content')

    @php
        $subjects = $subjects ?? [
            ['English', 78, 'up'],
            ['Social Studies', 82, 'up'],
            ['Mathematics', 61, 'down'],
            ['Science', 55, 'down'],
        ];
        $strengths = $strengths ?? ['English', 'Social Studies', 'Project Work'];
        $support = $support ?? ['Mathematics', 'Science'];
    @endphp

    <div class="parent-welcome">
        <div class="parent-welcome__title">Welcome back, {{ auth()->user()->name ?? 'Mrs. Banda' }}</div>
        <div class="parent-welcome__sub">Here's how your child is progressing this term.</div>
    </div>

    <div class="child-tabs">
        <div class="child-tab active">
            <div class="child-tab__avatar">J</div>
            <div>
                <div class="child-tab__name">John Banda</div>
                <div class="child-tab__class">Form 2A</div>
            </div>
        </div>
        <div class="child-tab">
            <div class="child-tab__avatar">M</div>
            <div>
                <div class="child-tab__name">Mercy Banda</div>
                <div class="child-tab__class">Form 4B</div>
            </div>
        </div>
    </div>

    <div class="parent-hero">
        <div class="hero-stat">
            <div class="hero-stat__value">68%</div>
            <div class="hero-stat__label">Overall Achievement</div>
        </div>
        <div class="hero-divider"></div>
        <div class="hero-stat">
            <div class="hero-stat__value" style="color:var(--pts-green);">+9%</div>
            <div class="hero-stat__label">Progress This Term</div>
        </div>
        <div class="hero-divider"></div>
        <div class="hero-stat">
            <div class="hero-stat__value">94%</div>
            <div class="hero-stat__label">Attendance</div>
        </div>
        <div class="hero-standing">
            <div class="hero-standing__badge">GOOD STANDING</div>
            <div class="hero-standing__note">Academic Term 2, 2026</div>
        </div>
    </div>

    <div class="p-grid">
        {{-- Left --}}
        <div>
            <div class="panel">
                <div class="panel__title">Subject Performance</div>
                @foreach ($subjects as [$name, $score, $dir])
                    <div class="subj-row">
                        <div class="subj-row__name">{{ $name }}</div>
                        <div class="subj-row__track">
                            <div class="subj-row__fill"
                                style="width: {{ $score }}%; background: {{ $score >= 65 ? 'var(--pts-green)' : ($score >= 50 ? '#D97706' : '#DC2626') }};">
                            </div>
                        </div>
                        <div class="subj-row__value">{{ $score }}%</div>
                    </div>
                @endforeach
            </div>

            <div class="panel">
                <div class="panel__title">Teacher's Comment</div>
                <div class="comment-box">
                    "John has demonstrated significant improvement during the term, particularly in English and Social
                    Studies. His written communication continues to be a real strength. We'd recommend some additional
                    support at home with Mathematics revision — happy to discuss ideas at the next parent meeting."
                    <div class="comment-box__meta">— Mr. Phiri, Class Teacher · 5 Sep 2026</div>
                </div>
            </div>
        </div>

        {{-- Right --}}
        <div>
            <div class="panel">
                <div class="panel__title">Strengths & Support Areas</div>
                <div class="pill-group-title">Strongest Areas</div>
                <div class="pill-row">
                    @foreach ($strengths as $s)
                        <div class="pill strong"><i class="fa-solid fa-circle-check"></i> {{ $s }}</div>
                    @endforeach
                </div>
                <div class="pill-group-title">Needs Support</div>
                <div class="pill-row">
                    @foreach ($support as $s)
                        <div class="pill support"><i class="fa-solid fa-hand-holding-heart"></i> {{ $s }}</div>
                    @endforeach
                </div>
            </div>

            <div class="panel">
                <div class="panel__title">Recent Updates</div>
                <div class="notif-item">
                    <div class="notif-icon green"><i class="fa-solid fa-file-lines"></i></div>
                    <div>
                        <div class="notif-text">Term 2 report card has been published.</div>
                        <div class="notif-time">2 days ago</div>
                    </div>
                </div>
                <div class="notif-item">
                    <div class="notif-icon amber"><i class="fa-solid fa-hand-holding-heart"></i></div>
                    <div>
                        <div class="notif-text">A Mathematics support plan has been started for John.</div>
                        <div class="notif-time">4 days ago</div>
                    </div>
                </div>
                <div class="notif-item">
                    <div class="notif-icon green"><i class="fa-solid fa-calendar-check"></i></div>
                    <div>
                        <div class="notif-text">Mid-Term results are now available to view.</div>
                        <div class="notif-time">1 week ago</div>
                    </div>
                </div>
            </div>

            <div class="panel" style="margin-bottom:0;">
                <button type="button" class="btn-report" onclick="location.href='{{ route('reports.report-card', 1) }}'">
                    <i class="fa-solid fa-file-arrow-down"></i> View Full Report Card
                </button>
            </div>
        </div>
    </div>

@endsection