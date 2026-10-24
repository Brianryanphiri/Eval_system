@extends('layouts.app')

@section('title', 'Student Profile')
@section('page-title', 'Student Profile')

@section('styles')
    <style>
        /* ---------- Flash ---------- */
        .flash {
            background: var(--pts-green-light);
            color: var(--pts-green-dark);
            border: 1px solid #BBE29E;
            padding: 11px 14px;
            border-radius: 9px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 18px;
        }

        /* ---------- Profile header ---------- */
        .profile-header {
            background: var(--pts-black);
            border-radius: 16px;
            padding: 28px 32px;
            display: flex;
            align-items: center;
            gap: 22px;
            margin-bottom: 22px;
            color: #fff;
            position: relative;
        }

        .profile-avatar {
            width: 74px;
            height: 74px;
            border-radius: 50%;
            background: var(--pts-green);
            color: var(--pts-black);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            font-weight: 800;
            flex-shrink: 0;
        }

        .profile-name {
            font-size: 21px;
            font-weight: 800;
            margin-bottom: 4px;
        }

        .profile-meta {
            font-size: 13px;
            color: #9CA3AF;
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
        }

        .profile-meta span {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .profile-header-stats {
            margin-left: auto;
            display: flex;
            gap: 28px;
        }

        .header-stat {
            text-align: right;
        }

        .header-stat__value {
            font-size: 22px;
            font-weight: 800;
        }

        .header-stat__label {
            font-size: 11px;
            color: #9CA3AF;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-top: 2px;
        }

        /* ---------- Report card CTA ---------- */
        .btn-report {
            padding: 10px 16px;
            background: var(--pts-green);
            color: var(--pts-black);
            border-radius: 9px;
            font-size: 12.5px;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            border: none;
            cursor: pointer;
        }

        .btn-report:hover {
            background: #8FD050;
        }

        /* ---------- Kebab menu ---------- */
        .kebab-wrap {
            position: relative;
        }

        .kebab-btn {
            background: rgba(255, 255, 255, 0.08);
            border: none;
            width: 38px;
            height: 38px;
            border-radius: 9px;
            color: #fff;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .kebab-btn:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        .kebab-menu {
            position: absolute;
            top: 46px;
            right: 0;
            background: #fff;
            border: 1px solid var(--pts-grey-light);
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
            min-width: 210px;
            padding: 6px;
            z-index: 30;
            display: none;
        }

        .kebab-menu.open {
            display: block;
        }

        .kebab-menu a,
        .kebab-menu button {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            padding: 9px 12px;
            border: none;
            background: none;
            text-align: left;
            font-size: 13px;
            color: #374151;
            text-decoration: none;
            border-radius: 7px;
            cursor: pointer;
            font-family: inherit;
        }

        .kebab-menu a:hover,
        .kebab-menu button:hover {
            background: var(--pts-bg);
            color: #111827;
        }

        .kebab-menu i {
            width: 16px;
            text-align: center;
            color: var(--pts-grey);
        }

        /* ---------- Risk banner ---------- */
        .risk-banner {
            border-radius: 12px;
            padding: 14px 18px;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .risk-banner__icon {
            font-size: 18px;
            flex-shrink: 0;
        }

        .risk-banner__body {
            flex: 1;
        }

        .risk-banner__label {
            font-size: 13.5px;
            font-weight: 700;
        }

        .risk-banner__reasons {
            font-size: 12.5px;
            margin-top: 2px;
        }

        .risk-banner__cta {
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
            color: #fff;
            white-space: nowrap;
        }

        /* ---------- Grid + panels ---------- */
        .profile-grid {
            display: grid;
            grid-template-columns: 1.5fr 1fr;
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

        /* ---------- Subjects ---------- */
        .subject-row {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 13px 0;
            border-bottom: 1px solid var(--pts-grey-light);
        }

        .subject-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .subject-row__name {
            width: 130px;
            font-size: 13.5px;
            font-weight: 600;
            color: #111827;
            flex-shrink: 0;
        }

        .subject-row__bar-track {
            flex: 1;
            height: 10px;
            background: var(--pts-bg);
            border-radius: 6px;
            overflow: hidden;
        }

        .subject-row__bar-fill {
            height: 100%;
            border-radius: 6px;
        }

        .subject-row__value {
            width: 46px;
            text-align: right;
            font-size: 13px;
            font-weight: 700;
            color: #111827;
            flex-shrink: 0;
        }

        .subject-row__trend {
            width: 60px;
            font-size: 11.5px;
            font-weight: 700;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 3px;
        }

        .subject-row__trend.up {
            color: var(--pts-green-dark);
        }

        .subject-row__trend.down {
            color: #DC2626;
        }

        /* ---------- Strengths / weaknesses ---------- */
        .sw-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .sw-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 9px;
            font-size: 13px;
            font-weight: 600;
        }

        .sw-item.strength {
            background: var(--pts-green-light);
            color: var(--pts-green-dark);
        }

        .sw-item.weakness {
            background: #FEF2F2;
            color: #DC2626;
        }

        .sw-empty {
            font-size: 12.5px;
            color: var(--pts-grey);
            padding: 8px 0;
        }

        /* ---------- Achievement / narrative / attendance ---------- */
        .achievement-box {
            text-align: center;
            padding: 20px;
            background: var(--pts-bg);
            border-radius: 12px;
        }

        .achievement-box__level {
            font-size: 20px;
            font-weight: 800;
            color: var(--pts-green-dark);
            margin-bottom: 4px;
        }

        .achievement-box__desc {
            font-size: 12.5px;
            color: var(--pts-grey);
        }

        .narrative-box {
            background: var(--pts-green-light);
            border-left: 4px solid var(--pts-green);
            border-radius: 8px;
            padding: 16px 18px;
            font-size: 13.5px;
            color: #1F2937;
            line-height: 1.6;
        }

        .attendance-row {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .attendance-ring {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: conic-gradient(var(--pts-green) 0% 94%, var(--pts-grey-light) 94% 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .attendance-ring__inner {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 800;
            color: #111827;
        }

        .attendance-label {
            font-size: 13px;
            color: var(--pts-grey);
        }

        .attendance-label strong {
            display: block;
            color: #111827;
            font-size: 14px;
        }

        /* ---------- Interventions ---------- */
        .iv-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border: 1px solid var(--pts-grey-light);
            border-radius: 9px;
            text-decoration: none;
            color: inherit;
            font-size: 12.5px;
            margin-bottom: 8px;
        }

        .iv-item:last-child {
            margin-bottom: 0;
        }

        .iv-item:hover {
            border-color: var(--pts-green);
        }

        .iv-item__body {
            flex: 1;
        }

        .iv-item__title {
            font-weight: 600;
            color: #111827;
        }

        .iv-item__meta {
            color: var(--pts-grey);
            font-size: 11.5px;
            margin-top: 1px;
        }

        .iv-status {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .iv-status.active {
            background: #D97706;
        }

        .iv-status.completed {
            background: var(--pts-green);
        }

        .iv-status.overdue {
            background: #DC2626;
        }

        .iv-status.cancelled {
            background: var(--pts-grey);
        }

        /* ---------- Guardians ---------- */
        .guardian-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px 0;
            border-bottom: 1px solid var(--pts-grey-light);
        }

        .guardian-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .guardian-info__name {
            font-size: 13px;
            font-weight: 600;
            color: #111827;
        }

        .guardian-info__meta {
            font-size: 11.5px;
            color: var(--pts-grey);
            margin-top: 1px;
        }

        .guardian-pill {
            font-size: 10px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 20px;
            background: var(--pts-green-light);
            color: var(--pts-green-dark);
            margin-left: 6px;
        }

        .guardian-btn {
            margin-left: auto;
            padding: 6px 12px;
            border: 1.5px solid var(--pts-grey-light);
            border-radius: 7px;
            background: #fff;
            font-size: 11.5px;
            font-weight: 700;
            color: #374151;
            cursor: pointer;
        }

        .guardian-btn:hover {
            border-color: var(--pts-green);
            color: var(--pts-green-dark);
        }

        /* ---------- Add guardian form toggle ---------- */
        #addGuardianForm {
            display: none;
        }

        #addGuardianForm.show {
            display: block;
        }

        /* ---------- Missing assessments ---------- */
        .missing-panel {
            border-color: #FECACA;
        }

        .missing-panel .panel__title {
            color: #991B1B;
        }

        .missing-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            background: #FEF2F2;
            border-radius: 9px;
            font-size: 12.5px;
            font-weight: 600;
            color: #991B1B;
            margin-bottom: 8px;
        }

        .missing-item:last-child {
            margin-bottom: 0;
        }

        .missing-item a {
            margin-left: auto;
            font-size: 11.5px;
            color: #991B1B;
            text-decoration: underline;
        }

        @media (max-width: 1100px) {
            .profile-grid {
                grid-template-columns: 1fr;
            }

            .profile-header {
                flex-wrap: wrap;
            }

            .profile-header-stats {
                margin-left: 0;
                width: 100%;
                justify-content: space-between;
                margin-top: 14px;
            }
        }
    </style>
@endsection

@section('content')

    @if (session('success'))
        <div class="flash">{{ session('success') }}</div>
    @endif

    <div class="profile-header">
        @can('students.edit')
            <form action="{{ route('students.photo.update', $student->id) }}" method="POST" enctype="multipart/form-data"
                id="photoForm">
                @csrf
                @if ($student->photo_url)
                    <img src="{{ $student->photo_url }}" class="profile-avatar" style="object-fit:cover;"
                        alt="{{ $student->name }}">
                @else
                    <div class="profile-avatar">{{ strtoupper(substr($student->name, 0, 1)) }}</div>
                @endif
                <input type="file" name="photo" id="photoInput" accept="image/*" capture="environment" style="display:none;"
                    onchange="document.getElementById('photoForm').submit();">
                <label for="photoInput"
                    style="cursor:pointer; font-size:10px; color:var(--pts-green); text-align:center; display:block; margin-top:4px;">
                    Change photo
                </label>
            </form>
        @else
            @if ($student->photo_url)
                <img src="{{ $student->photo_url }}" class="profile-avatar" style="object-fit:cover;" alt="{{ $student->name }}">
            @else
                <div class="profile-avatar">{{ strtoupper(substr($student->name, 0, 1)) }}</div>
            @endif
        @endcan

        <div>
            <div class="profile-name">{{ $student->name }}</div>
            <div class="profile-meta">
                <span><i class="fa-solid fa-id-badge"></i> {{ $student->student_number }}</span>
                <span><i class="fa-solid fa-chalkboard"></i> {{ $student->class }}</span>
            </div>
        </div>

        <div class="profile-header-stats">
            <div class="header-stat">
                <div class="header-stat__value">{{ $student->average }}%</div>
                <div class="header-stat__label">Overall</div>
            </div>
            @if ($student->position)
                <div class="header-stat">
                    <div class="header-stat__value">{{ $student->position }}</div>
                    <div class="header-stat__label">Position</div>
                </div>
            @endif
            <div class="header-stat">
                <div class="header-stat__value">{{ $student->attendance }}%</div>
                <div class="header-stat__label">Attendance</div>
            </div>
        </div>

        {{-- Report card CTA + kebab --}}
        <div style="display:flex; align-items:center; gap:10px; margin-left:18px;">
            @can('reports.generate')
                <a href="{{ route('reports.report-card', $student->id) }}" target="_blank" class="btn-report">
                    <i class="fa-solid fa-file-arrow-down"></i> Report Card
                </a>
            @endcan

            @can('students.edit')
                <div class="kebab-wrap">
                    <button type="button" class="kebab-btn"
                        onclick="document.getElementById('kebabMenu').classList.toggle('open')">
                        <i class="fa-solid fa-ellipsis-vertical"></i>
                    </button>
                    <div class="kebab-menu" id="kebabMenu">
                        <a href="{{ route('students.edit', $student->id) }}">
                            <i class="fa-solid fa-pen"></i> Edit student
                        </a>
                        <a href="{{ route('academic.index') }}">
                            <i class="fa-solid fa-chalkboard-user"></i> Manage enrolment
                        </a>
                        <a href="{{ route('interventions.create', ['student_id' => $student->id]) }}">
                            <i class="fa-solid fa-hand-holding-heart"></i> New intervention
                        </a>
                        <form method="POST" action="{{ route('students.destroy', $student->id) }}"
                            onsubmit="return confirm('Mark this student as inactive?');">
                            @csrf @method('DELETE')
                            <button type="submit">
                                <i class="fa-solid fa-user-slash"></i> Mark inactive
                            </button>
                        </form>
                    </div>
                </div>
            @endcan
        </div>
    </div>

    {{-- Risk banner --}}
    @if ($risk['level'] !== 'ok')
        @php
            $isHigh = $risk['level'] === 'at_risk';
            $bg = $isHigh ? '#FEF2F2' : '#FFFBEB';
            $border = $isHigh ? '#FECACA' : '#FDE68A';
            $color = $isHigh ? '#991B1B' : '#92400E';
            $icon = $isHigh ? 'fa-triangle-exclamation' : 'fa-circle-info';
            $label = $isHigh ? 'At Risk' : 'Watch';
        @endphp
        <div class="risk-banner" style="background:{{ $bg }}; border:1.5px solid {{ $border }}; color:{{ $color }};">
            <i class="fa-solid {{ $icon }} risk-banner__icon"></i>
            <div class="risk-banner__body">
                <div class="risk-banner__label">{{ $label }}</div>
                <div class="risk-banner__reasons">{{ implode(' · ', $risk['reasons']) }}</div>
            </div>
            @can('students.edit')
                <a href="{{ route('interventions.create', ['student_id' => $student->id]) }}" class="risk-banner__cta"
                    style="background:{{ $color }};">
                    Create intervention
                </a>
            @endcan
        </div>
    @endif

    <div class="profile-grid">
        {{-- Left column --}}
        <div>
            <div class="panel">
                <div class="panel__title">Subject Performance &amp; Trend</div>
                @forelse ($subjects as [$name, $score, $delta, $dir])
                    <div class="subject-row">
                        <div class="subject-row__name">{{ $name }}</div>
                        <div class="subject-row__bar-track">
                            <div class="subject-row__bar-fill"
                                style="width: {{ $score }}%; background: {{ $score >= 65 ? 'var(--pts-green)' : ($score >= 50 ? '#D97706' : '#DC2626') }};">
                            </div>
                        </div>
                        <div class="subject-row__value">{{ $score }}%</div>
                        <div class="subject-row__trend {{ $dir }}">
                            <i class="fa-solid fa-arrow-{{ $dir }}"></i> {{ $delta }}%
                        </div>
                    </div>
                @empty
                    <div class="sw-empty">No subject results recorded for the current term yet.</div>
                @endforelse
            </div>

            <div class="panel">
                <div class="panel__title">Learning Report</div>
                <div class="narrative-box">{{ $learningReport }}</div>
            </div>

            @if ($missingAssessments->isNotEmpty())
                <div class="panel missing-panel">
                    <div class="panel__title"><i class="fa-solid fa-circle-exclamation"></i> Missing assessments</div>
                    @foreach ($missingAssessments as $m)
                        <div class="missing-item">
                            {{ $m->subject?->name ?? 'Assessment' }} - {{ $m->name ?? ($m->title ?? 'Untitled') }}
                            @can('results.enter')
                                <a href="{{ route('marking.edit', $m->id) }}">Mark now</a>
                            @endcan
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Right column --}}
        <div>
            <div class="panel">
                <div class="panel__title">Achievement Level</div>
                <div class="achievement-box">
                    <div class="achievement-box__level">{{ $student->achievement_level }}</div>
                    <div class="achievement-box__desc">Based on {{ $student->average }}% overall average</div>
                </div>
            </div>

            <div class="panel">
                <div class="panel__title">Strengths</div>
                <div class="sw-list">
                    @forelse ($strengths as $s)
                        <div class="sw-item strength"><i class="fa-solid fa-circle-check"></i> {{ $s }}</div>
                    @empty
                        <div class="sw-empty">No standout subjects yet this term.</div>
                    @endforelse
                </div>
            </div>

            <div class="panel">
                <div class="panel__title">Areas Requiring Attention</div>
                <div class="sw-list">
                    @forelse ($weaknesses as $w)
                        <div class="sw-item weakness"><i class="fa-solid fa-triangle-exclamation"></i> {{ $w }}</div>
                    @empty
                        <div class="sw-empty">No subjects currently below the support threshold.</div>
                    @endforelse
                </div>
            </div>

            {{-- Interventions --}}
            <div class="panel">
                <div class="panel__title">Intervention History</div>
                @forelse ($interventions as $iv)
                    <a href="{{ route('interventions.show', $iv['id']) }}" class="iv-item">
                        <span class="iv-status {{ $iv['status'] }}"></span>
                        <div class="iv-item__body">
                            <div class="iv-item__title">{{ $iv['area'] }}</div>
                            <div class="iv-item__meta">
                                {{ ucfirst($iv['type']) }} · Started {{ $iv['started'] }} · {{ ucfirst($iv['status']) }}
                                · {{ $iv['score_note'] }}
                            </div>
                        </div>
                        <i class="fa-solid fa-chevron-right" style="color:var(--pts-grey); font-size:11px;"></i>
                    </a>
                @empty
                    <div class="sw-empty">No interventions recorded for this student yet.</div>
                @endforelse
            </div>

            {{-- Attendance --}}
            <div class="panel" style="margin-bottom:18px;">
                <div class="panel__title">Attendance</div>
                <div class="attendance-row">
                    <div class="attendance-ring">
                        <div class="attendance-ring__inner">{{ $student->attendance }}%</div>
                    </div>
                    <div class="attendance-label">
                        <strong>Regular attendance</strong>
                        Present {{ $student->attendance }}% of school days this term
                    </div>
                </div>
            </div>

            {{-- Guardians (add/remove) --}}
            <div class="panel" style="margin-bottom:18px;">
                <div class="panel__title">Guardians</div>

                @forelse ($guardians as $guardian)
                    <div
                        style="display:flex; align-items:center; gap:10px; padding:9px 0; border-bottom:1px solid var(--pts-grey-light);">
                        <div
                            style="width:30px; height:30px; border-radius:50%; background:var(--pts-black); color:#fff; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:700; flex-shrink:0;">
                            {{ strtoupper(substr($guardian->name, 0, 1)) }}
                        </div>
                        <div style="flex:1;">
                            <div style="font-size:12.5px; font-weight:600; color:#111827;">{{ $guardian->name }}</div>
                            <div style="font-size:11px; color:var(--pts-grey);">
                                {{ $guardian->pivot->relationship ?? 'Guardian' }} &middot; {{ $guardian->email }}
                            </div>
                        </div>
                        @can('students.edit')
                            <button type="button" class="guardian-btn"
                                onclick="document.getElementById('guardianModal-{{ $guardian->id }}').style.display='flex';">
                                Send update
                            </button>
                            <form method="POST" action="{{ route('students.guardians.destroy', [$studentModelId, $guardian->id]) }}"
                                onsubmit="return confirm('Remove this guardian?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    style="background:none; border:none; color:#DC2626; cursor:pointer; font-size:12px;"><i
                                        class="fa-solid fa-xmark"></i></button>
                            </form>
                        @endcan
                    </div>
                @empty
                    <div style="font-size:12.5px; color:var(--pts-grey); padding:8px 0;">No guardians linked yet.</div>
                @endforelse

                @can('students.edit')
                    <div style="font-size:12px; font-weight:700; color:var(--pts-green-dark); cursor:pointer; margin-top:12px; display:inline-flex; align-items:center; gap:6px;"
                        onclick="document.getElementById('addGuardianForm').classList.toggle('show')">
                        <i class="fa-solid fa-plus"></i> Add Guardian
                    </div>

                    <form method="POST" action="{{ route('students.guardians.store', $studentModelId) }}" id="addGuardianForm"
                        style="margin-top:12px; padding:14px; background:var(--pts-bg); border-radius:10px;">
                        @csrf
                        <div style="margin-bottom:10px;">
                            <input type="text" name="name" placeholder="Full name" required
                                style="width:100%; padding:9px 11px; border:1.5px solid var(--pts-grey-light); border-radius:7px; font-size:12.5px; margin-bottom:8px;">
                            <input type="email" name="email" placeholder="Email address" required
                                style="width:100%; padding:9px 11px; border:1.5px solid var(--pts-grey-light); border-radius:7px; font-size:12.5px; margin-bottom:8px;">
                            <input type="text" name="relationship" placeholder="Relationship, e.g. Mother"
                                style="width:100%; padding:9px 11px; border:1.5px solid var(--pts-grey-light); border-radius:7px; font-size:12.5px;">
                        </div>
                        <button type="submit"
                            style="padding:9px 14px; background:var(--pts-black); color:#fff; border:none; border-radius:7px; font-size:12px; font-weight:700; cursor:pointer;">Send
                            Invite</button>
                    </form>
                @endcan
            </div>

            {{-- Student Portal --}}
            <div class="panel" style="margin-bottom:0;">
                <div class="panel__title">Student Portal</div>
                @if ($student->has_portal_access)
                    <div style="display:flex; align-items:center; gap:10px; font-size:12.5px; color:#374151;">
                        <i class="fa-solid fa-circle-check" style="color:var(--pts-green-dark);"></i>
                        This student has portal access.
                    </div>
                    @can('students.edit')
                        <form method="POST" action="{{ route('students.portal.destroy', $studentModelId) }}"
                            onsubmit="return confirm('Remove portal access for this student?');" style="margin-top:10px;">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                style="padding:9px 14px; background:#fff; border:1.5px solid #FECACA; color:#DC2626; border-radius:7px; font-size:12px; font-weight:700; cursor:pointer;">Remove
                                Access</button>
                        </form>
                    @endcan
                @else
                    <div style="font-size:12.5px; color:var(--pts-grey); margin-bottom:10px;">This student does not have a login
                        yet.</div>
                    @can('students.edit')
                        <form method="POST" action="{{ route('students.portal.store', $studentModelId) }}">
                            @csrf
                            <input type="email" name="email" placeholder="Student's email address" required
                                style="width:100%; padding:9px 11px; border:1.5px solid var(--pts-grey-light); border-radius:7px; font-size:12.5px; margin-bottom:8px;">
                            <button type="submit"
                                style="padding:9px 14px; background:var(--pts-black); color:#fff; border:none; border-radius:7px; font-size:12px; font-weight:700; cursor:pointer;">Enable
                                Portal Access</button>
                        </form>
                    @endcan
                @endif
            </div>
        </div>
    </div>

    {{-- Guardian email modals --}}
    @foreach ($guardians as $g)
        @can('students.edit')
            <div id="guardianModal-{{ $g->id }}" style="display:none; position:fixed; inset:0; background:rgba(15,15,15,0.55); z-index:100;
                                                    align-items:center; justify-content:center; padding:20px;">
                <div style="background:#fff; border-radius:14px; padding:26px; max-width:460px; width:100%;
                                                        box-shadow:0 20px 60px rgba(0,0,0,0.25);">
                    <div style="font-size:16px; font-weight:800; color:#111827; margin-bottom:6px;">
                        Send progress update
                    </div>
                    <div style="font-size:12.5px; color:var(--pts-grey); margin-bottom:18px;">
                        To: {{ $g->name }} &lt;{{ $g->email }}&gt;
                    </div>
                    <form method="POST" action="{{ route('students.email-guardian', $studentModelId) }}">
                        @csrf
                        <input type="hidden" name="guardian_id" value="{{ $g->id }}">
                        <label style="display:block; font-size:12px; font-weight:600; color:#374151; margin-bottom:6px;">
                            Optional message
                        </label>
                        <textarea name="message" rows="4"
                            style="width:100%; padding:10px 12px; border:1.5px solid var(--pts-grey-light);
                                                               border-radius:9px; font-size:13px; font-family:inherit; outline:none; resize:vertical;"
                            placeholder="Add a short note to accompany the automated report..."></textarea>
                        <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:18px;">
                            <button type="button"
                                onclick="document.getElementById('guardianModal-{{ $g->id }}').style.display='none';" style="padding:10px 16px; border:1.5px solid var(--pts-grey-light);
                                                                   background:#fff; border-radius:9px; font-size:13px; font-weight:700;
                                                                   color:#374151; cursor:pointer;">
                                Cancel
                            </button>
                            <button type="submit"
                                style="padding:10px 16px; border:none; background:var(--pts-black); color:#fff;
                                                                   border-radius:9px; font-size:13px; font-weight:700; cursor:pointer;">
                                Send update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endcan
    @endforeach

    <script>
        // Close kebab menu when clicking outside
        document.addEventListener('click', function (e) {
            const menu = document.getElementById('kebabMenu');
            const wrap = menu?.parentElement;
            if (!wrap || !menu) return;
            if (!wrap.contains(e.target)) menu.classList.remove('open');
        });

        // Close guardian modals when clicking outside the inner card
        document.querySelectorAll('[id^="guardianModal-"]').forEach(modal => {
            modal.addEventListener('click', function (e) {
                if (e.target === modal) modal.style.display = 'none';
            });
        });
    </script>

@endsection