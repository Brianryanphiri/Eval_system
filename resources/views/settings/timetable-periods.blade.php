@extends('layouts.app')

@section('title', 'Timetable Periods')
@section('page-title', 'Timetable Periods')

@section('styles')
    <style>
        .page-head {
            margin-bottom: 20px;
        }

        .page-head h1 {
            font-size: 20px;
            font-weight: 700;
            color: #111827;
        }

        .page-head p {
            font-size: 13px;
            color: var(--pts-grey);
            margin-top: 4px;
        }

        .panel {
            background: #fff;
            border: 1px solid var(--pts-grey-light);
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 20px;
        }

        .panel-head {
            padding: 16px 20px;
            border-bottom: 1px solid var(--pts-grey-light);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .panel-head h2 {
            font-size: 14px;
            font-weight: 700;
            color: #111827;
        }

        .panel-head .hint {
            font-size: 12px;
            color: var(--pts-grey);
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th {
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--pts-grey);
            font-weight: 700;
            padding: 10px 20px;
            border-bottom: 1px solid var(--pts-grey-light);
            background: #FAFBFC;
        }

        .table td {
            padding: 12px 20px;
            font-size: 13px;
            color: #374151;
            border-bottom: 1px solid var(--pts-grey-light);
            vertical-align: middle;
        }

        .table tr:last-child td {
            border-bottom: none;
        }

        .table tr.break-row td {
            background: #F9FAFB;
            color: var(--pts-grey);
            font-style: italic;
        }

        .period-name {
            font-weight: 700;
            color: #111827;
        }

        .time-chip {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            background: var(--pts-green-light);
            color: var(--pts-green-dark);
            font-family: 'SFMono-Regular', Consolas, monospace;
        }

        .break-row .time-chip {
            background: #F3F4F6;
            color: #6B7280;
        }

        .row-actions {
            display: flex;
            gap: 8px;
            justify-content: flex-end;
        }

        .icon-btn {
            width: 32px;
            height: 32px;
            border-radius: 7px;
            border: 1px solid var(--pts-grey-light);
            background: #fff;
            cursor: pointer;
            color: var(--pts-grey);
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-family: inherit;
        }

        .icon-btn:hover {
            border-color: var(--pts-green);
            color: var(--pts-green-dark);
        }

        .icon-btn.danger:hover {
            border-color: #FECACA;
            color: #DC2626;
            background: #FEF2F2;
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

        .empty {
            padding: 40px 20px;
            text-align: center;
            color: var(--pts-grey);
        }

        .empty i {
            font-size: 32px;
            color: var(--pts-grey-light);
            margin-bottom: 12px;
            display: block;
        }

        .empty p {
            font-size: 13px;
            margin-bottom: 16px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr auto;
            gap: 12px;
            align-items: end;
            padding: 20px;
            border-bottom: 1px solid var(--pts-grey-light);
            background: #FAFBFC;
        }

        @media (max-width: 900px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
        }

        .field label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--pts-grey);
            margin-bottom: 6px;
        }

        .field input[type="text"],
        .field input[type="time"] {
            width: 100%;
            padding: 9px 12px;
            border: 1.5px solid var(--pts-grey-light);
            border-radius: 8px;
            font-size: 13px;
            font-family: inherit;
            outline: none;
            background: #fff;
        }

        .field input:focus {
            border-color: var(--pts-green);
            box-shadow: 0 0 0 3px rgba(122, 193, 66, 0.15);
        }

        .check {
            display: flex;
            align-items: center;
            gap: 8px;
            padding-bottom: 10px;
        }

        .check input {
            width: 16px;
            height: 16px;
            accent-color: var(--pts-green);
        }

        .check label {
            font-size: 13px;
            color: #374151;
            text-transform: none;
            letter-spacing: 0;
            font-weight: 500;
            margin: 0;
        }

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

        .flash-error {
            background: #FEF2F2;
            color: #991B1B;
            border: 1px solid #FECACA;
            padding: 11px 14px;
            border-radius: 9px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 18px;
        }
    </style>
@endsection

@section('content')

    <div class="page-head">
        <h1>Timetable Periods</h1>
        <p>Define your school's daily bell schedule. These periods are used to build class timetables.</p>
    </div>

    @if (session('success'))
        <div class="flash">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="flash-error">{{ $errors->first() }}</div>
    @endif

    @if ($periods->isEmpty())
        <div class="panel">
            <div class="empty">
                <i class="fa-regular fa-clock"></i>
                <p>No periods defined yet. Start with a standard bell schedule, then adjust it.</p>
                <form method="POST" action="{{ route('settings.timetable-periods.seed') }}">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-wand-magic-sparkles"></i>
                        Add default schedule
                    </button>
                </form>
            </div>
        </div>
    @else
        <div class="panel">
            <div class="panel-head">
                <h2>Bell schedule</h2>
                <span class="hint">{{ $periods->count() }} {{ Str::plural('period', $periods->count()) }}</span>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 60px;">Order</th>
                        <th>Name</th>
                        <th style="width: 180px;">Time</th>
                        <th style="width: 100px;">Type</th>
                        <th style="width: 140px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($periods as $period)
                        <tr class="{{ $period->is_break ? 'break-row' : '' }}">
                            <td>{{ str_pad((string) $period->sort_order, 2, '0', STR_PAD_LEFT) }}</td>
                            <td class="period-name">{{ $period->name }}</td>
                            <td>
                                <span class="time-chip">{{ $period->time_label }}</span>
                            </td>
                            <td>
                                {{ $period->is_break ? 'Break' : 'Teaching' }}
                            </td>
                            <td>
                                <div class="row-actions">
                                    <button type="button" class="icon-btn" title="Edit" onclick="openEditModal(
                                                        {{ $period->id }},
                                                        @js($period->name),
                                                        '{{ substr($period->start_time, 0, 5) }}',
                                                        '{{ substr($period->end_time, 0, 5) }}',
                                                        {{ $period->is_break ? 'true' : 'false' }}
                                                    )">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    <form method="POST" action="{{ route('settings.timetable-periods.destroy', $period->id) }}"
                                        data-confirm="Remove &quot;{{ $period->name }}&quot;? This cannot be undone.">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="icon-btn danger" title="Delete">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <form method="POST" action="{{ route('settings.timetable-periods.store') }}" class="form-grid">
                @csrf
                <div class="field">
                    <label for="new-name">Name</label>
                    <input type="text" id="new-name" name="name" placeholder="e.g. Period 1" required>
                </div>
                <div class="field">
                    <label for="new-start">Start</label>
                    <input type="time" id="new-start" name="start_time" required>
                </div>
                <div class="field">
                    <label for="new-end">End</label>
                    <input type="time" id="new-end" name="end_time" required>
                </div>
                <div class="field">
                    <label>&nbsp;</label>
                    <div class="check">
                        <input type="checkbox" id="new-break" name="is_break" value="1">
                        <label for="new-break">Break period</label>
                    </div>
                </div>
                <div class="field">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-plus"></i> Add
                    </button>
                </div>
            </form>
        </div>
    @endif

    {{-- Edit modal --}}
    <div id="editModal" class="modal-overlay"
        style="display:none; position:fixed; inset:0; background:rgba(15,15,15,0.55); z-index:100; align-items:center; justify-content:center; padding:20px;"
        onclick="if (event.target === this) closeEditModal()">
        <div
            style="background:#fff; border-radius:14px; padding:26px; max-width:480px; width:100%; box-shadow:0 20px 60px rgba(0,0,0,0.25);">
            <h2 style="font-size:16px; font-weight:800; color:#111827; margin-bottom:18px;">Edit period</h2>

            <form method="POST" id="editForm">
                @csrf
                @method('PUT')

                <div class="field" style="margin-bottom:14px;">
                    <label for="edit-name">Name</label>
                    <input type="text" id="edit-name" name="name" required>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:14px;">
                    <div class="field">
                        <label for="edit-start">Start</label>
                        <input type="time" id="edit-start" name="start_time" required>
                    </div>
                    <div class="field">
                        <label for="edit-end">End</label>
                        <input type="time" id="edit-end" name="end_time" required>
                    </div>
                </div>

                <div class="check" style="margin-bottom:18px;">
                    <input type="checkbox" id="edit-break" name="is_break" value="1">
                    <label for="edit-break">Break period</label>
                </div>

                <div style="display:flex; gap:10px; justify-content:flex-end;">
                    <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(id, name, start, end, isBreak) {
            const modal = document.getElementById('editModal');
            const form = document.getElementById('editForm');

            form.action = '{{ url('settings/timetable-periods') }}/' + id;

            document.getElementById('edit-name').value = name;
            document.getElementById('edit-start').value = start;
            document.getElementById('edit-end').value = end;
            document.getElementById('edit-break').checked = !!isBreak;

            modal.style.display = 'flex';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeEditModal();
        });
    </script>

@endsection