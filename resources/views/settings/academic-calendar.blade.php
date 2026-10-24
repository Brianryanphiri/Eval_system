@extends('layouts.app')

@section('title', 'Academic Calendar')
@section('page-title', 'Settings')

@section('styles')
    <style>
        .settings-tabs {
            display: flex;
            gap: 4px;
            margin-bottom: 22px;
            border-bottom: 1.5px solid var(--pts-grey-light);
        }

        .settings-tab {
            padding: 12px 18px;
            font-size: 13.5px;
            font-weight: 600;
            color: var(--pts-grey);
            cursor: pointer;
            border-bottom: 2px solid transparent;
            margin-bottom: -1.5px;
            text-decoration: none;
        }

        .settings-tab.active {
            color: var(--pts-black);
            border-bottom-color: var(--pts-green);
            font-weight: 700;
        }

        .settings-tab.disabled {
            color: #C1C5CC;
            cursor: not-allowed;
        }

        .page-sub {
            font-size: 13.5px;
            color: var(--pts-grey);
            margin-bottom: 20px;
        }

        .layout-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
        }

        .panel {
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 14px;
            padding: 24px;
            margin-bottom: 18px;
        }

        .panel__title {
            font-size: 15px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 16px;
        }

        .year-card {
            border: 1.5px solid var(--pts-grey-light);
            border-radius: 12px;
            padding: 18px;
            margin-bottom: 14px;
        }

        .year-card:last-child {
            margin-bottom: 0;
        }

        .year-card.is-current {
            border-color: var(--pts-green);
            background: var(--pts-green-light);
        }

        .year-card__head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .year-card__name {
            font-size: 16px;
            font-weight: 800;
            color: #111827;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .current-badge {
            font-size: 10.5px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 20px;
            background: var(--pts-green);
            color: var(--pts-black);
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .year-card__dates {
            font-size: 12px;
            color: var(--pts-grey);
        }

        .btn-set-current {
            padding: 7px 12px;
            border: 1.5px solid var(--pts-grey-light);
            border-radius: 7px;
            background: #fff;
            font-size: 11.5px;
            font-weight: 700;
            color: #374151;
            cursor: pointer;
        }

        .btn-set-current:hover {
            border-color: var(--pts-green);
            color: var(--pts-green-dark);
        }

        .term-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: 9px;
            background: var(--pts-bg);
            margin-bottom: 8px;
        }

        .term-row:last-child {
            margin-bottom: 0;
        }

        .term-row.is-current {
            background: #fff;
            border: 1.5px solid var(--pts-green);
        }

        .term-row__name {
            font-size: 13px;
            font-weight: 700;
            color: #111827;
        }

        .term-row__dates {
            font-size: 11.5px;
            color: var(--pts-grey);
            margin-left: auto;
            margin-right: 10px;
        }

        .add-term-toggle {
            font-size: 12px;
            font-weight: 700;
            color: var(--pts-green-dark);
            cursor: pointer;
            margin-top: 10px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .add-term-form {
            display: none;
            margin-top: 12px;
            padding: 14px;
            background: var(--pts-bg);
            border-radius: 10px;
        }

        .add-term-form.show {
            display: block;
        }

        .form-grid-3 {
            display: grid;
            grid-template-columns: 1fr 90px 1fr 1fr;
            gap: 10px;
            align-items: end;
        }

        .form-group {
            margin-bottom: 0;
        }

        .form-label {
            display: block;
            font-size: 11.5px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 5px;
        }

        .form-input {
            width: 100%;
            padding: 9px 11px;
            border: 1.5px solid var(--pts-grey-light);
            border-radius: 7px;
            font-size: 12.5px;
            color: #111827;
            outline: none;
            background: #fff;
        }

        .form-input:focus {
            border-color: var(--pts-green);
        }

        .btn-mini {
            padding: 9px 14px;
            background: var(--pts-black);
            color: #fff;
            border: none;
            border-radius: 7px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
        }

        .btn-mini:hover {
            background: var(--pts-green-dark);
        }

        .form-error {
            background: #FEF2F2;
            color: #B91C1C;
            border: 1px solid #FECACA;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 18px;
        }

        .sidebar-form .form-group {
            margin-bottom: 14px;
        }

        .sidebar-form .form-label {
            font-size: 12.5px;
        }

        .sidebar-form .form-input {
            padding: 10px 12px;
            font-size: 13px;
        }

        .btn-primary-full {
            width: 100%;
            padding: 11px;
            background: var(--pts-black);
            color: #fff;
            border: none;
            border-radius: 9px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
        }

        .btn-primary-full:hover {
            background: var(--pts-green-dark);
        }

        .empty-note {
            text-align: center;
            padding: 30px 20px;
            color: var(--pts-grey);
            font-size: 13.5px;
        }

        @media (max-width: 1000px) {
            .layout-grid {
                grid-template-columns: 1fr;
            }

            .form-grid-3 {
                grid-template-columns: 1fr 1fr;
            }
        }
    </style>
@endsection

@section('content')

    <div class="settings-tabs">
        <span class="settings-tab disabled" title="Not built yet">Institution Profile</span>
        <span class="settings-tab disabled" title="Not built yet">Assessment Policy</span>
        <span class="settings-tab disabled" title="Not built yet">Grading Scales</span>
        <a href="{{ route('settings.roles') }}" class="settings-tab">Roles &amp; Permissions</a>
        <a href="{{ route('settings.calendar') }}" class="settings-tab active">Academic Calendar</a>
    </div>

    <div class="page-sub">Manage academic years and terms, and set which one is currently active.</div>

    @if (session('success'))
        <div
            style="background: var(--pts-green-light); color: var(--pts-green-dark); border: 1px solid var(--pts-green); padding: 12px 16px; border-radius: 9px; font-size: 13px; margin-bottom: 18px;">
            {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="form-error">{{ $errors->first() }}</div>
    @endif

    <div class="layout-grid">
        <div>
            <div class="panel">
                <div class="panel__title">Academic Years</div>

                @forelse ($years as $year)
                    <div class="year-card {{ $year->is_current ? 'is-current' : '' }}">
                        <div class="year-card__head">
                            <div class="year-card__name">
                                {{ $year->label }}
                                @if ($year->is_current)
                                    <span class="current-badge">Current</span>
                                @endif
                            </div>
                            @unless ($year->is_current)
                                <form method="POST" action="{{ route('settings.calendar.years.setCurrent', $year) }}"
                                    onsubmit="return confirm('Make {{ $year->label }} the current academic year?');">
                                    @csrf
                                    <button type="submit" class="btn-set-current">Set as Current</button>
                                </form>
                            @endunless
                        </div>

                        @if ($year->start_date || $year->end_date)
                            <div class="year-card__dates">
                                {{ $year->start_date?->format('d M Y') ?? '—' }} to {{ $year->end_date?->format('d M Y') ?? '—' }}
                            </div>
                        @endif

                        <div style="margin-top:14px;">
                            @forelse ($year->terms->sortBy('sequence') as $term)
                                <div class="term-row {{ $term->is_current ? 'is-current' : '' }}">
                                    <div class="term-row__name">{{ $term->name }}</div>
                                    @if ($term->start_date || $term->end_date)
                                        <div class="term-row__dates">{{ $term->start_date?->format('d M') ?? '—' }} to
                                            {{ $term->end_date?->format('d M') ?? '—' }}</div>
                                    @endif
                                    @if ($term->is_current)
                                        <span class="current-badge">Current</span>
                                    @else
                                        <form method="POST" action="{{ route('settings.calendar.terms.setCurrent', $term) }}"
                                            onsubmit="return confirm('Make {{ $term->name }} the current term?');">
                                            @csrf
                                            <button type="submit" class="btn-set-current">Set Current</button>
                                        </form>
                                    @endif
                                </div>
                            @empty
                                <div style="font-size:12px; color:var(--pts-grey);">No terms added yet for {{ $year->label }}.</div>
                            @endforelse
                        </div>

                        <div class="add-term-toggle"
                            onclick="document.getElementById('addTermForm{{ $year->id }}').classList.toggle('show')">
                            <i class="fa-solid fa-plus"></i> Add Term
                        </div>

                        <form method="POST" action="{{ route('settings.calendar.terms.store', $year) }}" class="add-term-form"
                            id="addTermForm{{ $year->id }}">
                            @csrf
                            <div class="form-grid-3">
                                <div class="form-group">
                                    <label class="form-label">Term Name</label>
                                    <input type="text" name="name" class="form-input" placeholder="Term 3" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Order</label>
                                    <input type="number" name="sequence" class="form-input" placeholder="3" min="1" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Start Date</label>
                                    <input type="date" name="start_date" class="form-input">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">End Date</label>
                                    <input type="date" name="end_date" class="form-input">
                                </div>
                            </div>
                            <button type="submit" class="btn-mini" style="margin-top:10px;">Add Term</button>
                        </form>
                    </div>
                @empty
                    <div class="empty-note">No academic years yet. Add one using the form on the right.</div>
                @endforelse
            </div>
        </div>

        <div>
            <div class="panel">
                <div class="panel__title">Add Academic Year</div>
                <form method="POST" action="{{ route('settings.calendar.years.store') }}" class="sidebar-form">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Year Label</label>
                        <input type="text" name="label" class="form-input" placeholder="2027" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" class="form-input">
                    </div>
                    <button type="submit" class="btn-primary-full">Add Academic Year</button>
                </form>
            </div>
        </div>
    </div>

@endsection