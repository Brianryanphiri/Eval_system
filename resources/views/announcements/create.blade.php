@extends('layouts.app')

@section('title', 'New Announcement')

@section('page-title', 'Post Announcement')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}">Dashboard</a>
    <span class="sep">/</span>
    <a href="{{ route('announcements.index') }}">Announcements</a>
    <span class="sep">/</span>
    <span>New</span>
@endsection

@section('page-actions')
    <a href="{{ route('announcements.index') }}"
       style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;background:var(--pts-bg);color:#111827;border-radius:8px;font-size:12.5px;font-weight:600;text-decoration:none;">
        <i class="fa-solid fa-arrow-left" style="font-size:11px;"></i>
        Back
    </a>
@endsection

@section('styles')
    <style>
        .ann-compose {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 340px;
            gap: 20px;
            align-items: start;
        }

        @media (max-width: 1024px) {
            .ann-compose { grid-template-columns: 1fr; }
            .ann-preview { position: static !important; }
        }

        .ann-form {
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 12px;
            padding: 22px 24px;
        }

        .ann-form__section {
            padding-bottom: 20px;
            margin-bottom: 20px;
            border-bottom: 1px solid var(--pts-grey-light);
        }

        .ann-form__section:last-of-type {
            border-bottom: none;
            padding-bottom: 0;
            margin-bottom: 0;
        }

        .ann-form__label {
            display: block;
            font-size: 12.5px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 6px;
        }

        .ann-form__hint {
            font-size: 11.5px;
            color: var(--pts-grey);
            margin-top: 5px;
        }

        .ann-form__input,
        .ann-form__textarea,
        .ann-form__select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--pts-grey-light);
            border-radius: 8px;
            font-family: inherit;
            font-size: 13.5px;
            color: #111827;
            background: #fff;
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s;
        }

        .ann-form__input:focus,
        .ann-form__textarea:focus,
        .ann-form__select:focus {
            border-color: var(--pts-green);
            box-shadow: 0 0 0 3px rgba(122, 193, 66, 0.15);
        }

        .ann-form__textarea {
            resize: vertical;
            min-height: 160px;
            line-height: 1.55;
        }

        .ann-form__char-count {
            font-size: 11px;
            color: var(--pts-grey);
            text-align: right;
            margin-top: 4px;
        }

        .ann-form__char-count.warn { color: var(--pts-amber); }
        .ann-form__char-count.over { color: var(--pts-red); font-weight: 700; }

        /* ---------- Radio cards (audience + priority) ---------- */
        .ann-radio-grid {
            display: grid;
            gap: 8px;
        }

        .ann-radio-grid.audience { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .ann-radio-grid.priority { grid-template-columns: repeat(4, minmax(0, 1fr)); }

        @media (max-width: 640px) {
            .ann-radio-grid.audience { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .ann-radio-grid.priority { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        .ann-radio {
            position: relative;
        }

        .ann-radio input[type="radio"] {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .ann-radio label {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 12px;
            border: 1px solid var(--pts-grey-light);
            border-radius: 9px;
            cursor: pointer;
            font-size: 12.5px;
            font-weight: 600;
            color: #374151;
            transition: all 0.15s;
            background: #fff;
        }

        .ann-radio label:hover {
            border-color: var(--pts-green);
            background: var(--pts-green-light);
        }

        .ann-radio input[type="radio"]:checked + label {
            border-color: var(--pts-green);
            background: var(--pts-green-light);
            color: var(--pts-green-dark);
            box-shadow: 0 0 0 2px rgba(122, 193, 66, 0.15);
        }

        .ann-radio label i {
            font-size: 12px;
            color: var(--pts-grey);
        }

        .ann-radio input[type="radio"]:checked + label i {
            color: var(--pts-green-dark);
        }

        /* Priority-specific colors when checked */
        .ann-radio.priority-urgent input[type="radio"]:checked + label {
            border-color: var(--pts-red);
            background: var(--pts-red-light);
            color: var(--pts-red);
            box-shadow: 0 0 0 2px rgba(220, 38, 38, 0.12);
        }
        .ann-radio.priority-urgent input[type="radio"]:checked + label i { color: var(--pts-red); }

        .ann-radio.priority-high input[type="radio"]:checked + label {
            border-color: var(--pts-amber);
            background: var(--pts-amber-light);
            color: var(--pts-amber);
            box-shadow: 0 0 0 2px rgba(217, 119, 6, 0.12);
        }
        .ann-radio.priority-high input[type="radio"]:checked + label i { color: var(--pts-amber); }

        /* ---------- Schedule toggle ---------- */
        .ann-toggle-row {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            background: var(--pts-bg);
            border-radius: 9px;
            margin-bottom: 10px;
        }

        .ann-switch {
            position: relative;
            display: inline-block;
            width: 38px;
            height: 22px;
            flex-shrink: 0;
        }

        .ann-switch input { opacity: 0; width: 0; height: 0; }

        .ann-switch__slider {
            position: absolute;
            cursor: pointer;
            inset: 0;
            background: #CBD5E1;
            border-radius: 22px;
            transition: background 0.2s;
        }

        .ann-switch__slider::before {
            content: '';
            position: absolute;
            height: 16px;
            width: 16px;
            left: 3px;
            top: 3px;
            background: #fff;
            border-radius: 50%;
            transition: transform 0.2s;
        }

        .ann-switch input:checked + .ann-switch__slider {
            background: var(--pts-green);
        }

        .ann-switch input:checked + .ann-switch__slider::before {
            transform: translateX(16px);
        }

        .ann-toggle-row__label {
            font-size: 12.5px;
            font-weight: 600;
            color: #111827;
        }

        .ann-toggle-row__hint {
            font-size: 11.5px;
            color: var(--pts-grey);
            margin-top: 1px;
        }

        /* ---------- Class picker (hidden by default) ---------- */
        .ann-conditional {
            display: none;
            margin-top: 12px;
        }

        .ann-conditional.visible { display: block; }

        /* ---------- Preview ---------- */
        .ann-preview {
            position: sticky;
            top: 92px;
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 12px;
            overflow: hidden;
        }

        .ann-preview__head {
            padding: 12px 16px;
            border-bottom: 1px solid var(--pts-grey-light);
            font-size: 11px;
            font-weight: 700;
            color: var(--pts-grey);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            background: var(--pts-bg);
        }

        .ann-preview__body {
            padding: 16px;
        }

        .ann-preview__card {
            border-left: 4px solid var(--pts-blue);
            padding: 12px 14px;
            background: var(--pts-bg);
            border-radius: 8px;
        }

        .ann-preview__card.priority-urgent { border-left-color: var(--pts-red); }
        .ann-preview__card.priority-high   { border-left-color: var(--pts-amber); }
        .ann-preview__card.priority-normal { border-left-color: var(--pts-blue); }
        .ann-preview__card.priority-low    { border-left-color: var(--pts-grey); }

        .ann-preview__title {
            font-size: 14px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 4px;
            word-wrap: break-word;
        }

        .ann-preview__meta {
            font-size: 11px;
            color: var(--pts-grey);
            margin-bottom: 8px;
        }

        .ann-preview__text {
            font-size: 12.5px;
            color: #374151;
            line-height: 1.5;
            white-space: pre-wrap;
            word-wrap: break-word;
            max-height: 200px;
            overflow: hidden;
        }

        .ann-preview__badges {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            margin-top: 10px;
        }

        /* ---------- Submit bar ---------- */
        .ann-submit-bar {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 22px;
            padding-top: 20px;
            border-top: 1px solid var(--pts-grey-light);
        }

        .ann-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 10px 18px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            font-family: inherit;
            text-decoration: none;
            transition: all 0.15s;
        }

        .ann-btn.primary {
            background: var(--pts-green);
            color: #fff;
        }

        .ann-btn.primary:hover { background: var(--pts-green-dark); }

        .ann-btn.ghost {
            background: transparent;
            color: var(--pts-grey);
        }

        .ann-btn.ghost:hover { color: #111827; }

        .ann-error {
            font-size: 12px;
            color: var(--pts-red);
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
    </style>
@endsection

@section('content')

    <form method="POST" action="{{ route('announcements.store') }}" id="annForm">
        @csrf

        <div class="ann-compose">

            {{-- ---------- Left: form ---------- --}}
            <div class="ann-form">

                {{-- Title --}}
                <div class="ann-form__section">
                    <label for="title" class="ann-form__label">Title</label>
                    <input type="text"
                           id="title"
                           name="title"
                           class="ann-form__input"
                           value="{{ old('title') }}"
                           placeholder="e.g. Mid-term exams start Monday"
                           maxlength="255"
                           required
                           autofocus>
                    @error('title')
                        <div class="ann-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div>
                    @enderror
                </div>

                {{-- Body --}}
                <div class="ann-form__section">
                    <label for="body" class="ann-form__label">Message</label>
                    <textarea id="body"
                              name="body"
                              class="ann-form__textarea"
                              placeholder="Write the announcement here…"
                              maxlength="5000"
                              required>{{ old('body') }}</textarea>
                    <div class="ann-form__char-count" id="bodyCount">0 / 5000</div>
                    @error('body')
                        <div class="ann-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div>
                    @enderror
                </div>

                {{-- Audience --}}
                <div class="ann-form__section">
                    <label class="ann-form__label">Audience</label>
                    <div class="ann-radio-grid audience">
                        @php
                            $audiences = [
                                ['all', 'fa-globe', 'Everyone'],
                                ['staff', 'fa-user-tie', 'Staff'],
                                ['teachers', 'fa-chalkboard-user', 'Teachers'],
                                ['students', 'fa-user-graduate', 'Students'],
                                ['guardians', 'fa-people-roof', 'Guardians'],
                                ['class', 'fa-chalkboard', 'Specific class'],
                            ];
                            $selectedAudience = old('audience', 'all');
                        @endphp
                        @foreach ($audiences as [$value, $icon, $label])
                            <div class="ann-radio">
                                <input type="radio"
                                       id="aud_{{ $value }}"
                                       name="audience"
                                       value="{{ $value }}"
                                       {{ $selectedAudience === $value ? 'checked' : '' }}>
                                <label for="aud_{{ $value }}">
                                    <i class="fa-solid {{ $icon }}"></i>
                                    {{ $label }}
                                </label>
                            </div>
                        @endforeach
                    </div>

                    {{-- Class picker (only when audience = class) --}}
                    <div class="ann-conditional {{ $selectedAudience === 'class' ? 'visible' : '' }}" id="classPicker">
                        <label for="audience_class_id" class="ann-form__label">Select class</label>
                        <select id="audience_class_id" name="audience_class_id" class="ann-form__select">
                            <option value="">— Pick a class —</option>
                            @foreach ($classes as $class)
                                <option value="{{ $class->id }}"
                                    {{ (int) old('audience_class_id') === $class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('audience_class_id')
                            <div class="ann-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    @error('audience')
                        <div class="ann-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div>
                    @enderror
                </div>

                {{-- Priority --}}
                <div class="ann-form__section">
                    <label class="ann-form__label">Priority</label>
                    <div class="ann-radio-grid priority">
                        @php
                            $priorities = [
                                ['low', 'fa-arrow-down', 'Low'],
                                ['normal', 'fa-minus', 'Normal'],
                                ['high', 'fa-arrow-up', 'High'],
                                ['urgent', 'fa-triangle-exclamation', 'Urgent'],
                            ];
                            $selectedPriority = old('priority', 'normal');
                        @endphp
                        @foreach ($priorities as [$value, $icon, $label])
                            <div class="ann-radio priority-{{ $value }}">
                                <input type="radio"
                                       id="pri_{{ $value }}"
                                       name="priority"
                                       value="{{ $value }}"
                                       {{ $selectedPriority === $value ? 'checked' : '' }}>
                                <label for="pri_{{ $value }}">
                                    <i class="fa-solid {{ $icon }}"></i>
                                    {{ $label }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                    @error('priority')
                        <div class="ann-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div>
                    @enderror
                </div>

                {{-- Schedule --}}
                <div class="ann-form__section">
                    <label class="ann-form__label">Publishing</label>

                    <div class="ann-toggle-row">
                        <label class="ann-switch">
                            <input type="checkbox" id="scheduleToggle" name="schedule" value="1"
                                   {{ old('publish_at') ? 'checked' : '' }}>
                            <span class="ann-switch__slider"></span>
                        </label>
                        <div>
                            <div class="ann-toggle-row__label">Schedule for later</div>
                            <div class="ann-toggle-row__hint">Off = publish immediately</div>
                        </div>
                    </div>

                    <div class="ann-conditional {{ old('publish_at') ? 'visible' : '' }}" id="publishAtPicker">
                        <label for="publish_at" class="ann-form__label">Publish at</label>
                        <input type="datetime-local"
                               id="publish_at"
                               name="publish_at"
                               class="ann-form__input"
                               value="{{ old('publish_at') }}">
                        @error('publish_at')
                            <div class="ann-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Hidden field that store() reads --}}
                    <input type="hidden" name="publish_now" id="publishNowField" value="{{ old('publish_at') ? 0 : 1 }}">
                </div>

                {{-- Expiry --}}
                <div class="ann-form__section">
                    <label for="expires_at" class="ann-form__label">
                        Expires at <span style="color:var(--pts-grey);font-weight:500;">(optional)</span>
                    </label>
                    <input type="datetime-local"
                           id="expires_at"
                           name="expires_at"
                           class="ann-form__input"
                           value="{{ old('expires_at') }}">
                    <div class="ann-form__hint">
                        Leave blank to keep it visible until manually removed.
                    </div>
                    @error('expires_at')
                        <div class="ann-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div>
                    @enderror
                </div>

                {{-- Submit --}}
                <div class="ann-submit-bar">
                    <button type="submit" class="ann-btn primary" id="submitBtn">
                        <i class="fa-solid fa-paper-plane"></i>
                        <span id="submitLabel">Post announcement</span>
                    </button>
                    <a href="{{ route('announcements.index') }}" class="ann-btn ghost">Cancel</a>
                </div>

            </div>

            {{-- ---------- Right: preview ---------- --}}
            <aside class="ann-preview">
                <div class="ann-preview__head">
                    <i class="fa-regular fa-eye" style="margin-right:5px;"></i> Live preview
                </div>
                <div class="ann-preview__body">
                    <div class="ann-preview__card priority-normal" id="previewCard">
                        <div class="ann-preview__title" id="previewTitle">Your title will appear here</div>
                        <div class="ann-preview__meta" id="previewMeta">Everyone · Normal priority · just now</div>
                        <div class="ann-preview__text" id="previewText">
                            Your message preview will appear here as you type.
                        </div>
                        <div class="ann-preview__badges" id="previewBadges">
                            <span class="ann-badge audience" style="background:var(--pts-green-light);color:var(--pts-green-dark);font-size:10.5px;font-weight:700;padding:2px 8px;border-radius:12px;text-transform:uppercase;letter-spacing:.04em;">
                                Everyone
                            </span>
                            <span class="ann-badge priority-normal" style="background:var(--pts-blue-light);color:var(--pts-blue);font-size:10.5px;font-weight:700;padding:2px 8px;border-radius:12px;text-transform:uppercase;letter-spacing:.04em;">
                                Normal
                            </span>
                        </div>
                    </div>
                </div>
            </aside>

        </div>
    </form>

@endsection

@section('scripts')
    <script>
    (function () {
        // ---------- Character counter ----------
        const body = document.getElementById('body');
        const count = document.getElementById('bodyCount');
        const updateCount = function () {
            const len = body.value.length;
            count.textContent = len + ' / 5000';
            count.classList.remove('warn', 'over');
            if (len > 4500) count.classList.add('warn');
            if (len > 5000) count.classList.add('over');
        };
        body.addEventListener('input', updateCount);
        updateCount();

        // ---------- Class picker visibility ----------
        const classPicker = document.getElementById('classPicker');
        const audienceInputs = document.querySelectorAll('input[name="audience"]');
        audienceInputs.forEach(function (input) {
            input.addEventListener('change', function () {
                classPicker.classList.toggle('visible', input.value === 'class');
                updatePreview();
            });
        });

        // ---------- Schedule toggle ----------
        const scheduleToggle = document.getElementById('scheduleToggle');
        const publishAtPicker = document.getElementById('publishAtPicker');
        const publishNowField = document.getElementById('publishNowField');
        const submitLabel = document.getElementById('submitLabel');

        scheduleToggle.addEventListener('change', function () {
            const on = scheduleToggle.checked;
            publishAtPicker.classList.toggle('visible', on);
            publishNowField.value = on ? '0' : '1';
            submitLabel.textContent = on ? 'Schedule announcement' : 'Post announcement';

            // Default the datetime to now + 1 hour when first switched on
            const input = document.getElementById('publish_at');
            if (on && !input.value) {
                const d = new Date();
                d.setHours(d.getHours() + 1);
                d.setMinutes(d.getMinutes() - d.getTimezoneOffset());
                input.value = d.toISOString().slice(0, 16);
            }
            updatePreview();
        });

        // ---------- Live preview ----------
        const titleEl = document.getElementById('previewTitle');
        const textEl = document.getElementById('previewText');
        const metaEl = document.getElementById('previewMeta');
        const cardEl = document.getElementById('previewCard');
        const badgesEl = document.getElementById('previewBadges');

        const AUDIENCE_LABELS = {
            all: 'Everyone',
            staff: 'Staff',
            teachers: 'Teachers',
            students: 'Students',
            guardians: 'Guardians',
            class: 'Class'
        };

        const PRIORITY_LABELS = {
            low: 'Low', normal: 'Normal', high: 'High', urgent: 'Urgent'
        };

        const PRIORITY_COLORS = {
            low:    { bg: 'var(--pts-bg)',          color: 'var(--pts-grey)' },
            normal: { bg: 'var(--pts-blue-light)',  color: 'var(--pts-blue)' },
            high:   { bg: 'var(--pts-amber-light)', color: 'var(--pts-amber)' },
            urgent: { bg: 'var(--pts-red-light)',   color: 'var(--pts-red)' }
        };

        function updatePreview() {
            const title = document.getElementById('title').value.trim();
            const text = body.value.trim();
            const audience = document.querySelector('input[name="audience"]:checked')?.value || 'all';
            const priority = document.querySelector('input[name="priority"]:checked')?.value || 'normal';
            const scheduled = scheduleToggle.checked;
            const publishAt = document.getElementById('publish_at').value;

            titleEl.textContent = title || 'Your title will appear here';
            textEl.textContent = text || 'Your message preview will appear here as you type.';

            let audienceLabel = AUDIENCE_LABELS[audience] || 'Everyone';
            if (audience === 'class') {
                const sel = document.getElementById('audience_class_id');
                const className = sel.options[sel.selectedIndex]?.text || '';
                if (sel.value && className && !className.startsWith('—')) {
                    audienceLabel = 'Class: ' + className;
                }
            }

            let timingText = 'just now';
            if (scheduled && publishAt) {
                timingText = 'scheduled for ' + new Date(publishAt).toLocaleString();
            } else if (scheduled) {
                timingText = 'scheduled';
            }

            metaEl.textContent = audienceLabel + ' · ' + PRIORITY_LABELS[priority] + ' priority · ' + timingText;

            // Card stripe
            cardEl.classList.remove('priority-low', 'priority-normal', 'priority-high', 'priority-urgent');
            cardEl.classList.add('priority-' + priority);

            // Badges
            const pc = PRIORITY_COLORS[priority];
            badgesEl.innerHTML =
                '<span style="background:var(--pts-green-light);color:var(--pts-green-dark);font-size:10.5px;font-weight:700;padding:2px 8px;border-radius:12px;text-transform:uppercase;letter-spacing:.04em;">' +
                    audienceLabel +
                '</span>' +
                '<span style="background:' + pc.bg + ';color:' + pc.color + ';font-size:10.5px;font-weight:700;padding:2px 8px;border-radius:12px;text-transform:uppercase;letter-spacing:.04em;">' +
                    PRIORITY_LABELS[priority] +
                '</span>';
        }

        document.getElementById('title').addEventListener('input', updatePreview);
        body.addEventListener('input', updatePreview);
        document.querySelectorAll('input[name="priority"]').forEach(function (i) {
            i.addEventListener('change', updatePreview);
        });
        document.getElementById('audience_class_id').addEventListener('change', updatePreview);
        document.getElementById('publish_at').addEventListener('input', updatePreview);

        // Initialize preview with old() values
        updatePreview();
    })();
    </script>
@endsection