@extends('layouts.app')

@section('title', 'Absence Reasons')
@section('page-title', 'Absence Reasons')

@section('breadcrumbs')
    <a href="{{ route('attendance.index') }}">Attendance</a>
    <span class="sep">/</span>
    <span>Absence Reasons</span>
@endsection

@section('styles')
    <style>
        /* Intro panel */
        .ar-intro {
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 14px;
            padding: 24px;
            margin-bottom: 20px;
        }

        .ar-intro__title {
            font-size: 20px;
            font-weight: 800;
            color: #111827;
            margin-bottom: 8px;
        }

        .ar-intro__sub {
            font-size: 14px;
            color: var(--pts-grey);
            line-height: 1.6;
            max-width: 640px;
        }

        /* Grid */
        .ar-grid {
            display: grid;
            grid-template-columns: 1.4fr 1fr;
            gap: 18px;
            align-items: start;
        }

        .ar-panel {
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 14px;
            padding: 22px;
        }

        .ar-panel__title {
            font-size: 15px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .ar-panel__title i {
            color: var(--pts-green-dark);
            font-size: 14px;
        }

        /* Add form */
        .ar-form {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .ar-field {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .ar-field__label {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--pts-grey);
        }

        .ar-input {
            padding: 11px 14px;
            border: 1.5px solid var(--pts-grey-light);
            border-radius: 9px;
            font-size: 13.5px;
            color: #374151;
            background: #fff;
            outline: none;
            font-family: inherit;
            width: 100%;
        }

        .ar-input:focus {
            border-color: var(--pts-green);
        }

        .ar-checkbox {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            color: #374151;
            cursor: pointer;
            user-select: none;
        }

        .ar-checkbox input {
            width: 18px;
            height: 18px;
            accent-color: var(--pts-green);
            cursor: pointer;
        }

        .ar-btn {
            padding: 11px 18px;
            background: var(--pts-green);
            color: #fff;
            border: none;
            border-radius: 9px;
            font-size: 13.5px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-family: inherit;
            transition: background 0.15s;
        }

        .ar-btn:hover {
            background: var(--pts-green-dark);
        }

        .ar-btn i {
            font-size: 12px;
        }

        /* Existing reasons list */
        .ar-list {
            display: flex;
            flex-direction: column;
            gap: 1px;
        }

        .ar-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 16px;
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 10px;
            margin-bottom: 8px;
            transition: border-color 0.15s;
        }

        .ar-item:hover {
            border-color: var(--pts-green);
        }

        .ar-item:last-child {
            margin-bottom: 0;
        }

        .ar-item__icon {
            width: 36px;
            height: 36px;
            border-radius: 9px;
            background: var(--pts-green-light);
            color: var(--pts-green-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
        }

        .ar-item__body {
            flex: 1;
            min-width: 0;
        }

        .ar-item__label {
            font-size: 13.5px;
            font-weight: 600;
            color: #111827;
        }

        .ar-item__note {
            font-size: 11.5px;
            color: var(--pts-grey);
            margin-top: 2px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .ar-item__note i {
            font-size: 10px;
            color: var(--pts-amber);
        }

        .ar-item__delete {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: 1px solid var(--pts-grey-light);
            background: #fff;
            color: var(--pts-grey);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            flex-shrink: 0;
            transition: all 0.15s;
        }

        .ar-item__delete:hover {
            border-color: var(--pts-red);
            color: var(--pts-red);
            background: #FEF2F2;
        }

        /* Empty state */
        .empty-mini {
            text-align: center;
            padding: 40px 20px;
            color: var(--pts-grey);
            font-size: 13px;
        }

        .empty-mini__icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            background: var(--pts-bg);
            color: var(--pts-grey);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            margin-bottom: 14px;
        }

        .empty-mini__title {
            font-size: 14px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 4px;
        }

        /* Default suggestions */
        .ar-suggestions {
            margin-top: 20px;
            padding: 16px;
            background: var(--pts-bg);
            border-radius: 12px;
        }

        .ar-suggestions__title {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            font-weight: 700;
            color: var(--pts-grey);
            margin-bottom: 10px;
        }

        .ar-suggestions__list {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .ar-suggestion {
            padding: 6px 12px;
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            color: #374151;
            cursor: pointer;
            font-family: inherit;
            transition: all 0.15s;
        }

        .ar-suggestion:hover {
            border-color: var(--pts-green);
            color: var(--pts-green-dark);
        }

        @media (max-width: 900px) {
            .ar-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')

    {{-- Intro --}}
    <div class="ar-intro">
        <div class="ar-intro__title">Absence Reasons</div>
        <div class="ar-intro__sub">
            Configure the reasons teachers can pick when a student is absent. These appear in the reason dropdown when
            marking attendance. Schools usually start with a few standard reasons and add their own as needed.
        </div>
    </div>

    <div class="ar-grid">

        {{-- Left: existing reasons list --}}
        <div class="ar-panel">
            <div class="ar-panel__title">
                <i class="fa-solid fa-list-check"></i> Existing Reasons
                <span style="font-weight:500; color: var(--pts-grey); font-size: 13px;">
                    · {{ $reasons->count() }}
                </span>
            </div>

            @if ($reasons->isEmpty())
                <div class="empty-mini">
                    <div class="empty-mini__icon"><i class="fa-solid fa-list"></i></div>
                    <div class="empty-mini__title">No reasons configured yet</div>
                    <div>Add your first reason using the form on the right.</div>
                </div>
            @else
                <div class="ar-list">
                    @foreach ($reasons as $reason)
                        <div class="ar-item">
                            <div class="ar-item__icon">
                                <i class="fa-solid fa-circle-info"></i>
                            </div>
                            <div class="ar-item__body">
                                <div class="ar-item__label">{{ $reason->label }}</div>
                                @if ($reason->requires_note)
                                    <div class="ar-item__note">
                                        <i class="fa-solid fa-triangle-exclamation"></i>
                                        Requires a note from guardian
                                    </div>
                                @endif
                            </div>
                            <form method="POST" action="{{ route('attendance.reasons.destroy', $reason->id) }}"
                                data-confirm="Remove '{{ $reason->label }}' from your absence reasons?" style="margin: 0;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="ar-item__delete" title="Delete">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Right: add form + suggestions --}}
        <div class="ar-panel">
            <div class="ar-panel__title">
                <i class="fa-solid fa-circle-plus"></i> Add New Reason
            </div>

            <form method="POST" action="{{ route('attendance.reasons.store') }}" class="ar-form">
                @csrf

                <div class="ar-field">
                    <label for="label" class="ar-field__label">Label</label>
                    <input type="text" id="label" name="label" class="ar-input" placeholder="e.g. Family emergency"
                        maxlength="255" required value="{{ old('label') }}">
                    @error('label')
                        <div style="font-size:12px; color: var(--pts-red); margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>

                <label class="ar-checkbox">
                    <input type="checkbox" name="requires_note" value="1" @checked(old('requires_note'))>
                    Requires a note from guardian
                </label>

                <button type="submit" class="ar-btn">
                    <i class="fa-solid fa-plus"></i> Add Reason
                </button>
            </form>

            {{-- Default suggestions --}}
            @php
                $existingLabels = $reasons->pluck('label')->map(fn($l) => strtolower($l))->toArray();
                $suggestions = [
                    'Family emergency',
                    'Medical appointment',
                    'Illness',
                    'Bereavement',
                    'Religious observance',
                    'Transport issues',
                    'School activity',
                    'Approved by head teacher',
                ];
                $available = array_filter($suggestions, fn($s) => !in_array(strtolower($s), $existingLabels));
            @endphp

            @if (!empty($available))
                <div class="ar-suggestions">
                    <div class="ar-suggestions__title">Common reasons</div>
                    <div class="ar-suggestions__list">
                        @foreach ($available as $suggestion)
                            <button type="button" class="ar-suggestion"
                                onclick="document.getElementById('label').value = '{{ $suggestion }}'; document.getElementById('label').focus();">
                                {{ $suggestion }}
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

    </div>

@endsection