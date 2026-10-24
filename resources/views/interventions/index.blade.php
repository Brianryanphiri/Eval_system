@extends('layouts.app')

@section('title', 'Interventions')
@section('page-title', 'Intervention Management')

@section('styles')
    <style>
        .page-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .page-head__sub {
            font-size: 13.5px;
            color: var(--pts-grey);
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
        }

        .btn-add:hover {
            background: var(--pts-green-dark);
        }

        /* ---------- Status tabs ---------- */
        .status-tabs {
            display: flex;
            gap: 8px;
            margin-bottom: 18px;
        }

        .status-tab {
            padding: 9px 16px;
            border: 1.5px solid var(--pts-grey-light);
            border-radius: 20px;
            background: #fff;
            font-size: 12.5px;
            font-weight: 700;
            color: #374151;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .status-tab.active {
            background: var(--pts-black);
            border-color: var(--pts-black);
            color: #fff;
        }

        .status-tab__count {
            background: var(--pts-bg);
            color: var(--pts-grey);
            font-size: 10.5px;
            font-weight: 800;
            padding: 1px 7px;
            border-radius: 10px;
        }

        .status-tab.active .status-tab__count {
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
        }

        /* ---------- Intervention cards ---------- */
        .intervention-card {
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 14px;
            padding: 20px 22px;
            margin-bottom: 14px;
        }

        .ic-top {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 14px;
        }

        .ic-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--pts-black);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .ic-name {
            font-size: 14.5px;
            font-weight: 700;
            color: #111827;
        }

        .ic-meta {
            font-size: 12px;
            color: var(--pts-grey);
            margin-top: 1px;
        }

        .ic-status-badge {
            margin-left: auto;
            font-size: 11px;
            font-weight: 700;
            padding: 5px 12px;
            border-radius: 20px;
            white-space: nowrap;
        }

        .ic-status-badge.active {
            background: #FFFBEB;
            color: #D97706;
        }

        .ic-status-badge.completed {
            background: var(--pts-green-light);
            color: var(--pts-green-dark);
        }

        .ic-status-badge.overdue {
            background: #FEF2F2;
            color: #DC2626;
        }

        .ic-body {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 16px;
            margin-bottom: 16px;
        }

        .ic-field__label {
            font-size: 10.5px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--pts-grey);
            margin-bottom: 3px;
        }

        .ic-field__value {
            font-size: 13px;
            font-weight: 600;
            color: #111827;
        }

        .ic-progress {
            margin-bottom: 14px;
        }

        .ic-progress__labels {
            display: flex;
            justify-content: space-between;
            font-size: 11.5px;
            color: var(--pts-grey);
            margin-bottom: 6px;
        }

        .ic-progress__labels strong {
            color: #111827;
        }

        .ic-progress__track {
            height: 8px;
            background: var(--pts-bg);
            border-radius: 5px;
            overflow: hidden;
            position: relative;
        }

        .ic-progress__target-marker {
            position: absolute;
            top: -3px;
            width: 2px;
            height: 14px;
            background: var(--pts-black);
        }

        .ic-progress__fill {
            height: 100%;
            background: var(--pts-green);
            border-radius: 5px;
        }

        .ic-comparison {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 16px;
            background: var(--pts-green-light);
            border-radius: 9px;
            font-size: 13px;
            color: #1F2937;
        }

        .ic-comparison strong {
            color: var(--pts-green-dark);
        }

        .ic-comparison i {
            color: var(--pts-green-dark);
        }

        .ic-footer {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            margin-top: 14px;
        }

        .ic-btn {
            padding: 8px 14px;
            border: 1.5px solid var(--pts-grey-light);
            border-radius: 8px;
            background: #fff;
            font-size: 12px;
            font-weight: 700;
            color: #374151;
            cursor: pointer;
        }

        .ic-btn:hover {
            border-color: var(--pts-green);
            color: var(--pts-green-dark);
        }

        .ic-btn.primary {
            background: var(--pts-black);
            border-color: var(--pts-black);
            color: #fff;
        }

        .ic-btn.primary:hover {
            background: var(--pts-green-dark);
            border-color: var(--pts-green-dark);
            color: #fff;
        }

        @media (max-width: 800px) {
            .ic-body {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')

    @php
        $interventions = $interventions ?? [
            [
                'student' => 'John Banda',
                'class' => 'Form 2A',
                'area' => 'Algebra',
                'problem' => 'Low mastery in simultaneous equations',
                'intervention' => 'Remedial lessons',
                'teacher' => 'Mr. Phiri',
                'start' => '10 Sep 2026',
                'review' => '24 Sep 2026',
                'before' => 42,
                'target' => 60,
                'current' => 51,
                'status' => 'active',
            ],
            [
                'student' => 'Chikondi Mvula',
                'class' => 'Form 1C',
                'area' => 'Reading Comprehension',
                'problem' => 'Below pass threshold, 3 missed assessments',
                'intervention' => 'One-on-one tutoring',
                'teacher' => 'Mrs. Zulu',
                'start' => '2 Sep 2026',
                'review' => '16 Sep 2026',
                'before' => 38,
                'target' => 55,
                'current' => 55,
                'status' => 'completed',
                'after' => 67,
            ],
            [
                'student' => 'Blessings Nyirenda',
                'class' => 'Form 2A',
                'area' => 'Scientific Application',
                'problem' => 'Strong theory, weak practical translation',
                'intervention' => 'Extra lab sessions',
                'teacher' => 'Mr. Kamanga',
                'start' => '28 Aug 2026',
                'review' => '11 Sep 2026',
                'before' => 44,
                'target' => 60,
                'current' => 46,
                'status' => 'overdue',
            ],
        ];
    @endphp

    <div class="page-head">
        <div class="page-head__sub">Track and measure academic interventions across your classes</div>
        <button type="button" class="btn-add"><i class="fa-solid fa-plus"></i> Create Intervention</button>
    </div>

    <div class="status-tabs">
        <div class="status-tab active">All <span class="status-tab__count">{{ count($interventions) }}</span></div>
        <div class="status-tab">Active <span
                class="status-tab__count">{{ collect($interventions)->where('status', 'active')->count() }}</span></div>
        <div class="status-tab">Completed <span
                class="status-tab__count">{{ collect($interventions)->where('status', 'completed')->count() }}</span></div>
        <div class="status-tab">Overdue <span
                class="status-tab__count">{{ collect($interventions)->where('status', 'overdue')->count() }}</span></div>
    </div>

    @foreach ($interventions as $iv)
        @php
            $progressPct = $iv['target'] > $iv['before']
                ? min(100, max(0, (($iv['current'] - $iv['before']) / ($iv['target'] - $iv['before'])) * 100))
                : 0;
            $targetMarkerPct = 100; // target line sits at end of track (100% = target reached)
        @endphp
        <div class="intervention-card">
            <div class="ic-top">
                <div class="ic-avatar">{{ strtoupper(substr($iv['student'], 0, 1)) }}</div>
                <div>
                    <div class="ic-name">{{ $iv['student'] }}</div>
                    <div class="ic-meta">{{ $iv['class'] }} · {{ $iv['area'] }}</div>
                </div>
                <div class="ic-status-badge {{ $iv['status'] }}">
                    {{ ucfirst($iv['status']) }}
                </div>
            </div>

            <div class="ic-body">
                <div>
                    <div class="ic-field__label">Problem</div>
                    <div class="ic-field__value">{{ $iv['problem'] }}</div>
                </div>
                <div>
                    <div class="ic-field__label">Intervention</div>
                    <div class="ic-field__value">{{ $iv['intervention'] }} ({{ $iv['teacher'] }})</div>
                </div>
                <div>
                    <div class="ic-field__label">Timeline</div>
                    <div class="ic-field__value">{{ $iv['start'] }} → {{ $iv['review'] }}</div>
                </div>
            </div>

            @if ($iv['status'] === 'completed')
                <div class="ic-comparison">
                    <i class="fa-solid fa-arrow-trend-up"></i>
                    Before intervention: <strong>{{ $iv['before'] }}%</strong> · After: <strong>{{ $iv['after'] }}%</strong>
                    · Improvement: <strong>+{{ $iv['after'] - $iv['before'] }}%</strong>
                </div>
            @else
                <div class="ic-progress">
                    <div class="ic-progress__labels">
                        <span>Before: <strong>{{ $iv['before'] }}%</strong></span>
                        <span>Current: <strong>{{ $iv['current'] }}%</strong></span>
                        <span>Target: <strong>{{ $iv['target'] }}%</strong></span>
                    </div>
                    <div class="ic-progress__track">
                        <div class="ic-progress__fill" style="width: {{ $progressPct }}%;"></div>
                    </div>
                </div>
            @endif

            <div class="ic-footer">
                <button type="button" class="ic-btn">View Details</button>
                @if ($iv['status'] !== 'completed')
                    <button type="button" class="ic-btn primary">Update Progress</button>
                @endif
            </div>
        </div>
    @endforeach

@endsection