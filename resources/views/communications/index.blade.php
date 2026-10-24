@extends('layouts.app')

@section('title', 'Communications')
@section('page-title', 'Communications')

@section('styles')
    <style>
        .comms-wrap {
            width: 100%;
        }

        .comms-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 20px;
        }

        .comms-head h1 {
            font-size: 20px;
            font-weight: 800;
            color: #111827;
        }

        .comms-head p {
            font-size: 13px;
            color: var(--pts-grey);
            margin-top: 4px;
        }

        .panel {
            background: #fff;
            border: 1px solid var(--pts-grey-light);
            border-radius: 14px;
            padding: 24px;
            margin-bottom: 18px;
        }

        .panel__title {
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--pts-grey);
            margin-bottom: 14px;
        }

        .field {
            margin-bottom: 18px;
        }

        .field label {
            display: block;
            font-size: 12.5px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }

        .field input[type="text"],
        .field textarea,
        .field select {
            width: 100%;
            padding: 10px 12px;
            border: 1.5px solid var(--pts-grey-light);
            border-radius: 9px;
            font-size: 13px;
            font-family: inherit;
            outline: none;
            background: #fff;
        }

        .field input:focus,
        .field textarea:focus,
        .field select:focus {
            border-color: var(--pts-green);
            box-shadow: 0 0 0 3px rgba(122, 193, 66, 0.15);
        }

        .field textarea {
            resize: vertical;
            min-height: 260px;
            line-height: 1.55;
        }

        .field-hint {
            font-size: 11.5px;
            color: var(--pts-grey);
            margin-top: 6px;
        }

        .group-tabs {
            display: flex;
            gap: 4px;
            margin-bottom: 14px;
            border-bottom: 1.5px solid var(--pts-grey-light);
        }

        .group-tab {
            padding: 10px 16px;
            font-size: 13px;
            font-weight: 600;
            color: var(--pts-grey);
            cursor: pointer;
            border: none;
            background: none;
            border-bottom: 2px solid transparent;
            margin-bottom: -1.5px;
            font-family: inherit;
        }

        .group-tab.active {
            color: var(--pts-green-dark);
            border-bottom-color: var(--pts-green);
            font-weight: 700;
        }

        .recipient-summary {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 16px;
            background: var(--pts-green-light);
            border-radius: 10px;
            font-size: 13px;
            color: var(--pts-green-dark);
            font-weight: 600;
            margin-bottom: 12px;
        }

        .recipient-summary i {
            font-size: 16px;
        }

        .recipient-preview {
            font-size: 12px;
            color: var(--pts-grey);
            margin-top: 8px;
            line-height: 1.6;
        }

        .actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 8px;
        }

        .btn {
            padding: 11px 22px;
            border-radius: 9px;
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

        /* ---------- Sent confirmation toast ---------- */
        .comms-toast {
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
            animation: comms-toast-in 0.55s cubic-bezier(0.22, 1, 0.36, 1) 0.15s forwards;
        }

        .comms-toast.dismissing {
            animation: comms-toast-out 0.35s cubic-bezier(0.4, 0, 0.6, 1) forwards;
        }

        @keyframes comms-toast-in {
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

        @keyframes comms-toast-out {
            0% {
                transform: translateY(0) scale(1);
                opacity: 1;
            }

            100% {
                transform: translateY(40px) scale(0.9);
                opacity: 0;
            }
        }

        .comms-toast__ring {
            width: 42px;
            height: 42px;
            flex-shrink: 0;
        }

        .comms-toast__svg {
            width: 100%;
            height: 100%;
            display: block;
        }

        .comms-toast__circle-bg {
            fill: none;
            stroke: var(--pts-green-light);
            stroke-width: 3;
        }

        .comms-toast__circle {
            fill: none;
            stroke: var(--pts-green);
            stroke-width: 3;
            stroke-linecap: round;
            stroke-dasharray: 107;
            stroke-dashoffset: 107;
            transform-origin: 20px 20px;
            transform: rotate(-90deg);
            animation: comms-draw-circle 0.5s cubic-bezier(0.65, 0, 0.35, 1) 0.45s forwards;
        }

        .comms-toast__check {
            fill: none;
            stroke: var(--pts-green-dark);
            stroke-width: 3;
            stroke-linecap: round;
            stroke-linejoin: round;
            stroke-dasharray: 24;
            stroke-dashoffset: 24;
            animation: comms-draw-check 0.35s cubic-bezier(0.65, 0, 0.35, 1) 0.85s forwards;
        }

        @keyframes comms-draw-circle {
            to {
                stroke-dashoffset: 0;
            }
        }

        @keyframes comms-draw-check {
            to {
                stroke-dashoffset: 0;
            }
        }

        .comms-toast__body {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .comms-toast__title {
            font-size: 13.5px;
            font-weight: 800;
            color: #111827;
            letter-spacing: -0.005em;
        }

        .comms-toast__sub {
            font-size: 12px;
            color: var(--pts-grey);
        }
    </style>
@endsection

@section('content')

    <div class="comms-wrap">

        <div class="comms-head">
            <div>
                <h1>Send a message to guardians</h1>
                <p>Choose a group of recipients, write your message, and send it. Emails are queued and logged.</p>
            </div>
            <a href="{{ route('communications.history') }}" class="btn btn-secondary">
                <i class="fa-solid fa-clock-rotate-left"></i> History
            </a>
        </div>

        <form method="POST" action="{{ route('communications.send') }}" id="commsForm">
            @csrf

            <div class="panel">
                <div class="panel__title">Recipients</div>

                <div class="group-tabs">
                    <button type="button" class="group-tab active" data-group="class">By class</button>
                    <button type="button" class="group-tab" data-group="year">By year group</button>
                    <button type="button" class="group-tab" data-group="school">Whole school</button>
                    <button type="button" class="group-tab" data-group="guardian">One guardian</button>
                </div>

                <input type="hidden" name="group" id="groupInput" value="class">

                <div class="field" id="classField">
                    <label for="class_id">Class</label>
                    <select name="class_id" id="class_id">
                        @foreach ($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="field" id="yearField" style="display:none;">
                    <label for="grade_level">Year group</label>
                    <select name="grade_level" id="grade_level">
                        @foreach ($gradeLevels as $level)
                            <option value="{{ $level }}">{{ $level }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="field" id="guardianField" style="display:none;">
                    <label for="guardian_id">Guardian</label>
                    <input type="text" id="guardianSearch" placeholder="Type a name or email…" autocomplete="off">
                    <input type="hidden" name="guardian_id" id="guardian_id">
                    <div id="guardianResults" class="recipient-preview"></div>
                </div>

                <div id="recipientSummary" class="recipient-summary" style="display:none;">
                    <i class="fa-solid fa-users"></i>
                    <span id="recipientSummaryText">Calculating…</span>
                </div>
                <div id="recipientPreview" class="recipient-preview"></div>
            </div>

            <div class="panel">
                <div class="panel__title">Message</div>

                <div class="field">
                    <label for="template_key">Start from a template (optional)</label>
                    <select id="template_key">
                        <option value="">- Blank -</option>
                        @foreach ($templates as $template)
                            <option value="{{ $template['key'] }}" data-subject="{{ $template['subject'] }}"
                                data-body="{{ $template['body'] }}">
                                {{ $template['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label for="subject">Subject</label>
                    <input type="text" name="subject" id="subject" required maxlength="255">
                </div>

                <div class="field">
                    <label for="body">Message</label>
                    <textarea name="body" id="body" required minlength="10" maxlength="10000"></textarea>
                    <div class="field-hint">
                        Placeholders like <code>@{{guardian_name}}</code> and <code>@{{school_name}}</code>
                        are filled in per recipient when the email sends.
                    </div>
                </div>
            </div>

            <div class="actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-paper-plane"></i> Send
                </button>
            </div>
        </form>

    </div>

    @if (session('bulk_sent_count'))
        <div class="comms-toast" id="commsToast">
            <div class="comms-toast__ring">
                <svg viewBox="0 0 40 40" class="comms-toast__svg">
                    <circle class="comms-toast__circle-bg" cx="20" cy="20" r="17" />
                    <circle class="comms-toast__circle" cx="20" cy="20" r="17" />
                    <path class="comms-toast__check" d="M 12 20 L 18 26 L 28 14" />
                </svg>
            </div>
            <div class="comms-toast__body">
                <div class="comms-toast__title">Message queued</div>
                <div class="comms-toast__sub">
                    {{ session('bulk_sent_count') }}
                    {{ \Illuminate\Support\Str::plural('guardian', session('bulk_sent_count')) }} will receive this email.
                </div>
            </div>
        </div>

        <script>
            (function () {
                var toast = document.getElementById('commsToast');
                if (!toast) return;
                setTimeout(function () {
                    toast.classList.add('dismissing');
                    setTimeout(function () { toast.remove(); }, 400);
                }, 4500);
            })();
        </script>
    @endif

    <script>
        (function () {
            const form = document.getElementById('commsForm');
            const groupInput = document.getElementById('groupInput');
            const classField = document.getElementById('classField');
            const yearField = document.getElementById('yearField');
            const guardianField = document.getElementById('guardianField');
            const classSelect = document.getElementById('class_id');
            const yearSelect = document.getElementById('grade_level');
            const guardianSearch = document.getElementById('guardianSearch');
            const guardianId = document.getElementById('guardian_id');
            const guardianResults = document.getElementById('guardianResults');
            const summary = document.getElementById('recipientSummary');
            const summaryText = document.getElementById('recipientSummaryText');
            const preview = document.getElementById('recipientPreview');

            const previewUrl = '{{ route('communications.preview') }}';

            document.querySelectorAll('.group-tab').forEach(function (tab) {
                tab.addEventListener('click', function () {
                    document.querySelectorAll('.group-tab').forEach(t => t.classList.remove('active'));
                    tab.classList.add('active');
                    const group = tab.dataset.group;
                    groupInput.value = group;

                    classField.style.display = group === 'class' ? 'block' : 'none';
                    yearField.style.display = group === 'year' ? 'block' : 'none';
                    guardianField.style.display = group === 'guardian' ? 'block' : 'none';
                    guardianResults.innerHTML = '';

                    if (group !== 'guardian') {
                        loadCount();
                    } else {
                        summary.style.display = 'none';
                        preview.textContent = '';
                    }
                });
            });

            function loadCount() {
                const group = groupInput.value;
                const params = new URLSearchParams({ group });

                if (group === 'class') params.set('class_id', classSelect.value);
                if (group === 'year') params.set('grade_level', yearSelect.value);
                if (group === 'guardian' && guardianId.value) params.set('guardian_id', guardianId.value);

                summary.style.display = 'flex';
                summaryText.textContent = 'Calculating…';
                preview.textContent = '';

                fetch(previewUrl + '?' + params.toString(), {
                    headers: { 'Accept': 'application/json' },
                })
                    .then(r => r.json())
                    .then(data => {
                        summaryText.textContent = data.count + ' recipient' + (data.count === 1 ? '' : 's') + ' will receive this email';
                        if (data.preview && data.preview.length) {
                            preview.textContent = 'Example: ' + data.preview.map(p => p.name + ' <' + p.email + '>').join(', ');
                            if (data.count > data.preview.length) {
                                preview.textContent += ', and ' + (data.count - data.preview.length) + ' more';
                            }
                        } else {
                            preview.textContent = 'No recipients match this selection.';
                        }
                    })
                    .catch(() => {
                        summaryText.textContent = 'Could not calculate recipients.';
                    });
            }

            classSelect?.addEventListener('change', loadCount);
            yearSelect?.addEventListener('change', loadCount);

            let guardianTimer = null;
            guardianSearch?.addEventListener('input', function () {
                clearTimeout(guardianTimer);
                const q = guardianSearch.value.trim();
                if (q.length < 2) {
                    guardianResults.innerHTML = '';
                    return;
                }
                guardianTimer = setTimeout(function () {
                    fetch('{{ route('communications.searchGuardians') }}?q=' + encodeURIComponent(q), {
                        headers: { 'Accept': 'application/json' },
                    })
                        .then(r => r.json())
                        .then(list => {
                            guardianResults.innerHTML = '';
                            if (!list.length) {
                                guardianResults.textContent = 'No guardians found.';
                                return;
                            }
                            list.forEach(function (g) {
                                const btn = document.createElement('button');
                                btn.type = 'button';
                                btn.style.cssText = 'display:block; width:100%; text-align:left; padding:8px 10px; margin-bottom:4px; background:#fff; border:1px solid var(--pts-grey-light); border-radius:6px; cursor:pointer; font-size:12.5px; font-family:inherit;';
                                btn.textContent = g.name + ' (' + g.email + ')';
                                btn.addEventListener('click', function () {
                                    guardianId.value = g.id;
                                    guardianSearch.value = g.name;
                                    guardianResults.innerHTML = '';
                                    loadCount();
                                });
                                guardianResults.appendChild(btn);
                            });
                        });
                }, 250);
            });

            document.getElementById('template_key')?.addEventListener('change', function () {
                const opt = this.options[this.selectedIndex];
                const subj = opt.getAttribute('data-subject') || '';
                const body = opt.getAttribute('data-body') || '';
                document.getElementById('subject').value = subj;
                document.getElementById('body').value = body;
            });

            loadCount();
        })();
    </script>

@endsection