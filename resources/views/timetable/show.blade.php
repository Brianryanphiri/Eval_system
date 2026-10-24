@extends('layouts.app')

@section('title', $classroom->name . ' Timetable')
@section('page-title', 'Timetable')

@section('styles')
    <style>
        .tt-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 18px;
        }

        .tt-head__left {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
        }

        .tt-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--pts-grey);
            text-decoration: none;
            font-weight: 600;
        }

        .tt-back:hover {
            color: var(--pts-green-dark);
        }

        .tt-class-name {
            font-size: 18px;
            font-weight: 800;
            color: #111827;
        }

        .tt-term-picker {
            padding: 9px 12px;
            border: 1.5px solid var(--pts-grey-light);
            border-radius: 8px;
            font-size: 13px;
            font-family: inherit;
            background: #fff;
        }

        .tt-actions {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 9px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            border: none;
            font-family: inherit;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-primary {
            background: var(--pts-black);
            color: #fff;
        }

        .btn-primary:hover {
            background: var(--pts-green-dark);
        }

        .btn-secondary {
            background: #fff;
            color: #374151;
            border: 1.5px solid var(--pts-grey-light);
        }

        .btn-secondary:hover {
            border-color: var(--pts-green);
            color: var(--pts-green-dark);
        }

        .btn-ghost {
            background: transparent;
            color: var(--pts-grey);
            border: 1.5px solid transparent;
        }

        .btn-ghost:hover {
            color: var(--pts-green-dark);
            border-color: var(--pts-green);
        }

        .grid-wrap {
            overflow-x: auto;
            background: #fff;
            border: 1px solid var(--pts-grey-light);
            border-radius: 12px;
        }

        table.tt {
            width: 100%;
            border-collapse: collapse;
            min-width: 900px;
        }

        table.tt th,
        table.tt td {
            border: 1px solid var(--pts-grey-light);
            padding: 0;
            vertical-align: top;
        }

        table.tt thead th {
            background: var(--pts-black);
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            padding: 12px 10px;
            text-align: center;
        }

        table.tt th.period-col {
            width: 140px;
            text-align: left;
        }

        .period-cell {
            background: #FAFBFC;
            padding: 10px 12px;
        }

        .period-cell__name {
            font-size: 12.5px;
            font-weight: 700;
            color: #111827;
        }

        .period-cell__time {
            font-size: 11px;
            color: var(--pts-grey);
            margin-top: 2px;
            font-family: 'SFMono-Regular', Consolas, monospace;
        }

        .break-row td {
            background: #F5F6F8;
        }

        .break-row .period-cell {
            background: #EFF1F4;
        }

        .break-row .period-cell__name {
            color: var(--pts-grey);
        }

        .slot {
            min-height: 68px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 8px 6px;
            gap: 3px;
        }

        .slot--empty {
            color: #C1C5CC;
            font-size: 12px;
        }

        .slot__subject {
            font-size: 12.5px;
            font-weight: 700;
            color: #111827;
            line-height: 1.25;
        }

        .slot__teacher {
            font-size: 11px;
            color: var(--pts-grey);
            line-height: 1.2;
        }

        .slot__warn {
            font-size: 10px;
            color: #DC2626;
            font-weight: 700;
            margin-top: 2px;
        }

        .slot.conflict {
            background: #FEF2F2;
        }

        .slot-edit {
            padding: 6px 5px;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .slot-edit select {
            width: 100%;
            font-size: 11px;
            font-family: inherit;
            padding: 5px 4px;
            border: 1px solid var(--pts-grey-light);
            border-radius: 5px;
            background: #fff;
            outline: none;
        }

        .slot-edit select:focus {
            border-color: var(--pts-green);
        }

        .slot-edit.conflict {
            background: #FEF2F2;
        }

        .empty-state {
            padding: 40px 20px;
            text-align: center;
            color: var(--pts-grey);
            font-size: 13.5px;
        }

        .empty-state a {
            color: var(--pts-green-dark);
            font-weight: 700;
            text-decoration: none;
        }

        .flash {
            background: var(--pts-green-light);
            color: var(--pts-green-dark);
            border: 1px solid var(--pts-green);
            padding: 11px 14px;
            border-radius: 9px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 16px;
        }

        .flash-error {
            background: #FEF2F2;
            color: #991B1B;
            border: 1px solid #FECACA;
            padding: 11px 14px;
            border-radius: 9px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 16px;
        }

        /* ---------- Email modal ---------- */
        .email-modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 15, 15, 0.55);
            z-index: 100;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .email-modal.open {
            display: flex;
        }

        .email-modal__card {
            background: #fff;
            border-radius: 14px;
            padding: 26px;
            max-width: 520px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
        }

        .email-modal__title {
            font-size: 16px;
            font-weight: 800;
            color: #111827;
            margin-bottom: 6px;
        }

        .email-modal__sub {
            font-size: 12.5px;
            color: var(--pts-grey);
            margin-bottom: 18px;
        }

        .email-modal__label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }

        .email-modal__hint {
            font-size: 11.5px;
            color: var(--pts-grey);
            margin-bottom: 10px;
            line-height: 1.5;
        }

        .email-modal__hint code {
            background: var(--pts-bg);
            padding: 1px 5px;
            border-radius: 4px;
            font-size: 11px;
            color: var(--pts-green-dark);
        }

        .email-modal__textarea {
            width: 100%;
            padding: 12px 14px;
            border: 1.5px solid var(--pts-grey-light);
            border-radius: 9px;
            font-size: 13px;
            font-family: inherit;
            outline: none;
            resize: vertical;
            min-height: 180px;
            line-height: 1.5;
        }

        .email-modal__textarea:focus {
            border-color: var(--pts-green);
            box-shadow: 0 0 0 3px rgba(122, 193, 66, 0.15);
        }

        .email-modal__actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 18px;
        }

        .email-modal__cancel {
            padding: 10px 16px;
            border: 1.5px solid var(--pts-grey-light);
            background: #fff;
            border-radius: 9px;
            font-size: 13px;
            font-weight: 700;
            color: #374151;
            cursor: pointer;
            font-family: inherit;
        }

        .email-modal__cancel:hover {
            border-color: var(--pts-grey);
        }

        /* ---------- Sent confirmation toast ---------- */
        .tt-toast {
            position: fixed;
            bottom: 24px;
            right: 24px;
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 16px 22px 16px 16px;
            background: #fff;
            border: 1px solid var(--pts-grey-light);
            border-radius: 14px;
            box-shadow: 0 12px 40px rgba(15, 15, 15, 0.14), 0 2px 8px rgba(15, 15, 15, 0.06);
            z-index: 200;
            transform: translateY(120px) scale(0.85);
            opacity: 0;
            animation: tt-toast-in 0.55s cubic-bezier(0.22, 1, 0.36, 1) 0.15s forwards;
        }

        .tt-toast.dismissing {
            animation: tt-toast-out 0.35s cubic-bezier(0.4, 0, 0.6, 1) forwards;
        }

        @keyframes tt-toast-in {
            0% {
                transform: translateY(120px) scale(0.85);
                opacity: 0;
            }

            60% {
                transform: translateY(-6px) scale(1.02);
                opacity: 1;
            }

            100% {
                transform: translateY(0) scale(1);
                opacity: 1;
            }
        }

        @keyframes tt-toast-out {
            0% {
                transform: translateY(0) scale(1);
                opacity: 1;
            }

            100% {
                transform: translateY(40px) scale(0.9);
                opacity: 0;
            }
        }

        .tt-toast__ring {
            width: 42px;
            height: 42px;
            flex-shrink: 0;
        }

        .tt-toast__svg {
            width: 100%;
            height: 100%;
            display: block;
        }

        .tt-toast__circle-bg {
            fill: none;
            stroke: var(--pts-green-light);
            stroke-width: 3;
        }

        .tt-toast__circle {
            fill: none;
            stroke: var(--pts-green);
            stroke-width: 3;
            stroke-linecap: round;
            stroke-dasharray: 107;
            stroke-dashoffset: 107;
            transform-origin: 20px 20px;
            transform: rotate(-90deg);
            animation: tt-draw-circle 0.5s cubic-bezier(0.65, 0, 0.35, 1) 0.45s forwards;
        }

        .tt-toast__check {
            fill: none;
            stroke: var(--pts-green-dark);
            stroke-width: 3;
            stroke-linecap: round;
            stroke-linejoin: round;
            stroke-dasharray: 24;
            stroke-dashoffset: 24;
            animation: tt-draw-check 0.35s cubic-bezier(0.65, 0, 0.35, 1) 0.85s forwards;
        }

        @keyframes tt-draw-circle {
            to {
                stroke-dashoffset: 0;
            }
        }

        @keyframes tt-draw-check {
            to {
                stroke-dashoffset: 0;
            }
        }

        .tt-toast__body {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .tt-toast__title {
            font-size: 13.5px;
            font-weight: 800;
            color: #111827;
            letter-spacing: -0.005em;
        }

        .tt-toast__sub {
            font-size: 12px;
            color: var(--pts-grey);
        }

        /* ---------- Print ---------- */
        @media print {
            @page {
                size: A4 landscape;
                margin: 10mm;
            }

            .pts-sidebar,
            .pts-topbar,
            .pts-footer,
            .pts-toasts,
            .pts-confirm,
            .pts-sidebar__overlay,
            .tt-head,
            .flash,
            .flash-error,
            .pts-flash,
            .edit-mode,
            #saveBar,
            .email-modal,
            .tt-toast {
                display: none !important;
            }

            .pts-main {
                margin-left: 0 !important;
                min-height: 0 !important;
                display: block !important;
            }

            .pts-content {
                padding: 0 !important;
            }

            body {
                background: #fff !important;
            }

            .grid-wrap {
                border: none !important;
                border-radius: 0 !important;
                overflow: visible !important;
            }

            table.tt {
                min-width: 0 !important;
                width: 100% !important;
            }

            table.tt thead th {
                background: #fff !important;
                color: #111827 !important;
                border: 1px solid #9CA3AF !important;
                font-size: 10px !important;
                padding: 6px 4px !important;
            }

            table.tt td {
                border: 1px solid #9CA3AF !important;
            }

            .period-cell {
                background: #fff !important;
                padding: 5px 6px !important;
            }

            .period-cell__name {
                font-size: 10px !important;
            }

            .period-cell__time {
                font-size: 8.5px !important;
            }

            .slot {
                min-height: 0 !important;
                padding: 4px 3px !important;
            }

            .slot__subject {
                font-size: 10px !important;
            }

            .slot__teacher {
                font-size: 8.5px !important;
            }

            .slot__warn {
                display: none !important;
            }

            .slot.conflict {
                background: #fff !important;
            }

            .break-row td,
            .break-row .period-cell {
                background: #F3F4F6 !important;
            }
        }
    </style>
@endsection

@section('content')

    @if (session('success') && !session('timetable_sent_count'))
        <div class="flash">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="flash-error">{{ $errors->first() }}</div>
    @endif

    <div class="tt-head">
        <div class="tt-head__left">
            <a href="{{ route('timetable.index') }}" class="tt-back">
                <i class="fa-solid fa-arrow-left"></i> All classes
            </a>
            <div class="tt-class-name">{{ $classroom->name }}</div>

            <form method="GET" action="{{ route('timetable.show', $classroom->id) }}">
                <select name="term" class="tt-term-picker" onchange="this.form.submit()">
                    @foreach ($terms as $term)
                        <option value="{{ $term->id }}" {{ $selectedTerm?->id === $term->id ? 'selected' : '' }}>
                            {{ $term->name }} · {{ $term->academicYear->label ?? '' }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        <div class="tt-actions">
            @if (!$periods->isEmpty())
                <button type="button" class="btn btn-secondary" onclick="printTimetable()">
                    <i class="fa-solid fa-print"></i> Print
                </button>
                <a href="{{ route('timetable.pdf', ['classroom' => $classroom->id, 'term' => $selectedTerm?->id]) }}"
                    class="btn btn-secondary">
                    <i class="fa-solid fa-file-arrow-down"></i> PDF
                </a>
                <a href="{{ route('timetable.xlsx', ['classroom' => $classroom->id, 'term' => $selectedTerm?->id]) }}"
                    class="btn btn-secondary">
                    <i class="fa-solid fa-file-excel"></i> Excel
                </a>
            @endif

            @if ($canEdit && !$periods->isEmpty())
                <button type="button" class="btn btn-secondary" onclick="openEmailModal()">
                    <i class="fa-solid fa-envelope"></i> Email
                </button>
                <button type="button" class="btn btn-primary" id="editToggle" onclick="toggleEdit()">
                    <i class="fa-solid fa-pen"></i> Edit
                </button>
            @endif
        </div>
    </div>

    @if ($periods->isEmpty())
        <div class="empty-state">
            No periods defined yet for this school.
            @if ($canEdit)
                <br>Set up the daily bell schedule first:
                <a href="{{ route('settings.timetable-periods') }}">Manage periods</a>.
            @endif
        </div>
    @else
        <form method="POST" action="{{ route('timetable.save', $classroom->id) }}" id="ttForm">
            @csrf
            <input type="hidden" name="term_id" value="{{ $selectedTerm?->id }}">

            <div class="grid-wrap" id="printArea">
                <table class="tt" id="ttTable">
                    <thead>
                        <tr>
                            <th class="period-col">Period</th>
                            @foreach (\App\Models\ClassTimetable::days() as $dayNum => $dayName)
                                <th>{{ $dayName }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($periods as $period)
                            <tr class="{{ $period->is_break ? 'break-row' : '' }}">
                                <td class="period-cell">
                                    <div class="period-cell__name">{{ $period->name }}</div>
                                    <div class="period-cell__time">{{ $period->time_label }}</div>
                                </td>

                                @foreach (\App\Models\ClassTimetable::days() as $dayNum => $dayName)
                                    @php
                                        $slot = $grid[$dayNum][$period->id] ?? null;
                                        $isBreak = $period->is_break;
                                        $subjectId = $slot?->subject_id;
                                        $teacherId = $slot?->teacher_id;
                                        $conflictKey = $teacherId ? "{$teacherId}:{$dayNum}:{$period->id}" : null;
                                        $isConflict = $conflictKey && isset($conflicts[$conflictKey]);
                                    @endphp

                                    <td data-day="{{ $dayNum }}" data-period="{{ $period->id }}">
                                        @if ($isBreak)
                                            <div class="slot slot--empty">
                                                {{ $period->name }}
                                            </div>
                                        @else
                                            <div class="slot {{ $isConflict ? 'conflict' : '' }} view-mode">
                                                @if ($slot && $slot->subject)
                                                    <div class="slot__subject">{{ $slot->subject->name }}</div>
                                                    @if ($slot->teacher)
                                                        <div class="slot__teacher">{{ $slot->teacher->name }}</div>
                                                    @endif
                                                    @if ($isConflict)
                                                        <div class="slot__warn">
                                                            <i class="fa-solid fa-triangle-exclamation"></i> Clash
                                                        </div>
                                                    @endif
                                                @else
                                                    <div class="slot--empty">-</div>
                                                @endif
                                            </div>

                                            @if ($canEdit)
                                                <div class="slot-edit edit-mode {{ $isConflict ? 'conflict' : '' }}"
                                                    style="display:none;">
                                                    <select name="slots[{{ $dayNum }}_{{ $period->id }}][subject_id]"
                                                        onchange="onSubjectChange(this)">
                                                        <option value="">- Subject -</option>
                                                        @foreach ($classSubjects as $cs)
                                                            <option value="{{ $cs->id }}"
                                                                data-teachers="{{ implode(',', $teachersBySubject[$cs->id] ?? []) }}"
                                                                {{ $subjectId === $cs->id ? 'selected' : '' }}>
                                                                {{ $cs->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>

                                                    <select name="slots[{{ $dayNum }}_{{ $period->id }}][teacher_id]"
                                                        class="teacher-select">
                                                        <option value="">- Teacher -</option>
                                                        @foreach ($allTeachers as $t)
                                                            <option value="{{ $t->id }}"
                                                                {{ $teacherId === $t->id ? 'selected' : '' }}>
                                                                {{ $t->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>

                                                    <input type="hidden"
                                                        name="slots[{{ $dayNum }}_{{ $period->id }}][day]"
                                                        value="{{ $dayNum }}">
                                                    <input type="hidden"
                                                        name="slots[{{ $dayNum }}_{{ $period->id }}][period_id]"
                                                        value="{{ $period->id }}">
                                                </div>
                                            @endif
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($canEdit)
                <div style="display:none; margin-top:16px; gap:10px;" id="saveBar">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-check"></i> Save timetable
                    </button>
                    <button type="button" class="btn btn-ghost" onclick="cancelEdit()">
                        Cancel
                    </button>
                </div>
            @endif
        </form>
    @endif

    <div id="emailModal" class="email-modal" onclick="if (event.target === this) closeEmailModal()">
        <div class="email-modal__card">
            <div class="email-modal__title">Email timetable to parents</div>
            <div class="email-modal__sub">
                The PDF will be sent to every guardian of students currently in {{ $classroom->name }}.
            </div>

            <form method="POST" action="{{ route('timetable.email', $classroom->id) }}">
                @csrf
                <input type="hidden" name="term_id" value="{{ $selectedTerm?->id }}">

                <label class="email-modal__label">Message</label>
                <div class="email-modal__hint">
                    This is the body of the email. Edit it as you like. Placeholders like
                    <code>@{{guardian_name}}</code> and <code>@{{class_name}}</code> are filled in per recipient.
                </div>
                <textarea name="message" rows="10" class="email-modal__textarea">{{ $defaultMessage }}</textarea>

                <div class="email-modal__actions">
                    <button type="button" class="email-modal__cancel" onclick="closeEmailModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-paper-plane"></i> Send
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if (session('timetable_sent_count'))
        <div class="tt-toast" id="ttToast">
            <div class="tt-toast__ring">
                <svg viewBox="0 0 40 40" class="tt-toast__svg">
                    <circle class="tt-toast__circle-bg" cx="20" cy="20" r="17" />
                    <circle class="tt-toast__circle" cx="20" cy="20" r="17" />
                    <path class="tt-toast__check" d="M 12 20 L 18 26 L 28 14" />
                </svg>
            </div>
            <div class="tt-toast__body">
                <div class="tt-toast__title">Timetable sent</div>
                <div class="tt-toast__sub">
                    Queued to {{ session('timetable_sent_count') }}
                    {{ \Illuminate\Support\Str::plural('guardian', session('timetable_sent_count')) }}
                </div>
            </div>
        </div>
    @endif

    <script>
        function toggleEdit() {
            document.querySelectorAll('.view-mode').forEach(function (el) { el.style.display = 'none'; });
            document.querySelectorAll('.edit-mode').forEach(function (el) { el.style.display = 'flex'; });
            document.getElementById('editToggle').style.display = 'none';
            document.getElementById('saveBar').style.display = 'flex';
        }

        function cancelEdit() {
            window.location.reload();
        }

        function onSubjectChange(selectEl) {
            var cell = selectEl.closest('.slot-edit');
            var teacherSelect = cell.querySelector('.teacher-select');
            var opt = selectEl.options[selectEl.selectedIndex];
            var teacherIds = opt.getAttribute('data-teachers');

            var allowed = teacherIds ? teacherIds.split(',').filter(Boolean) : [];

            Array.prototype.forEach.call(teacherSelect.options, function (o, i) {
                if (i === 0) return;
                var id = String(o.value);
                var show = allowed.length === 0 || allowed.indexOf(id) !== -1;
                o.hidden = !show;
                o.disabled = !show;
            });
        }

        function openEmailModal() {
            document.getElementById('emailModal').classList.add('open');
        }

        function closeEmailModal() {
            document.getElementById('emailModal').classList.remove('open');
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeEmailModal();
        });

        function printTimetable() {
            var table = document.getElementById('ttTable');
            if (!table) {
                window.print();
                return;
            }

            var printWidth = 1047;
            var printHeight = 718;

            table.style.transform = '';
            table.style.transformOrigin = '';

            var rect = table.getBoundingClientRect();

            var scaleW = printWidth / rect.width;
            var scaleH = printHeight / rect.height;
            var scale = Math.min(1, scaleW, scaleH);

            if (scale < 0.7) {
                scale = 0.7;
            }

            if (scale < 1) {
                table.style.transformOrigin = 'top left';
                table.style.transform = 'scale(' + scale + ')';

                var wrap = document.getElementById('printArea');
                wrap.style.width = (rect.width * scale) + 'px';
                wrap.style.height = (rect.height * scale) + 'px';
            }

            window.print();

            setTimeout(function () {
                table.style.transform = '';
                table.style.transformOrigin = '';
                var wrap = document.getElementById('printArea');
                wrap.style.width = '';
                wrap.style.height = '';
            }, 500);
        }

        window.addEventListener('afterprint', function () {
            var table = document.getElementById('ttTable');
            if (table) {
                table.style.transform = '';
                table.style.transformOrigin = '';
            }
            var wrap = document.getElementById('printArea');
            if (wrap) {
                wrap.style.width = '';
                wrap.style.height = '';
            }
        });

        (function () {
            var toast = document.getElementById('ttToast');
            if (!toast) return;

            setTimeout(function () {
                toast.classList.add('dismissing');
                setTimeout(function () { toast.remove(); }, 400);
            }, 4000);
        })();
    </script>

@endsection