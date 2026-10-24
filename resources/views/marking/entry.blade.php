@extends('layouts.app')

@section('title', 'Marking')
@section('page-title', 'Enter Marks')

@section('styles')
    <style>
        .marking-wrap {
            max-width: 100%;
        }

        .marking-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .marking-header__back {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--pts-grey);
            text-decoration: none;
        }

        .marking-header__back:hover {
            color: var(--pts-green-dark);
        }

        .marking-header__title {
            font-size: 19px;
            font-weight: 800;
            color: #111827;
        }

        .marking-header__sub {
            font-size: 13px;
            color: var(--pts-grey);
            margin-top: 3px;
        }

        .marking-mode-toggle {
            display: flex;
            border: 1.5px solid var(--pts-grey-light);
            border-radius: 9px;
            overflow: hidden;
        }

        .marking-mode-toggle button {
            padding: 9px 16px;
            background: #fff;
            border: none;
            color: var(--pts-grey);
            cursor: pointer;
            font-size: 12.5px;
            font-weight: 600;
        }

        .marking-mode-toggle button.active {
            background: var(--pts-black);
            color: #fff;
        }

        .marking-toolbar {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 14px;
        }

        .search-box {
            position: relative;
            max-width: 260px;
            flex: 1;
        }

        .search-box i {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--pts-grey);
            font-size: 12.5px;
        }

        .search-box input {
            width: 100%;
            padding: 9px 13px 9px 34px;
            border: 1.5px solid var(--pts-grey-light);
            border-radius: 8px;
            font-size: 13px;
            outline: none;
            background: #fff;
        }

        .marking-toolbar-spacer {
            flex: 1;
        }

        .btn-upload {
            padding: 9px 16px;
            background: #fff;
            border: 1.5px solid var(--pts-grey-light);
            border-radius: 8px;
            font-size: 12.5px;
            font-weight: 700;
            color: #374151;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .btn-upload:hover {
            border-color: var(--pts-green);
            color: var(--pts-green-dark);
        }

        /* ---------- Grid ---------- */
        .marking-grid-wrap {
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 14px;
            overflow-x: auto;
            margin-bottom: 18px;
        }

        .marking-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 720px;
        }

        .marking-table thead th {
            position: sticky;
            top: 0;
            background: var(--pts-bg);
            z-index: 2;
            text-align: center;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--pts-grey);
            font-weight: 700;
            padding: 12px 10px;
            border-bottom: 1px solid var(--pts-grey-light);
            white-space: nowrap;
        }

        .marking-table thead th.student-col {
            text-align: left;
            position: sticky;
            left: 0;
            z-index: 3;
            min-width: 200px;
        }

        .marking-table thead th.total-col {
            background: var(--pts-black);
            color: #fff;
        }

        .marking-table tbody td {
            padding: 8px 10px;
            border-bottom: 1px solid var(--pts-grey-light);
            text-align: center;
        }

        .marking-table tbody tr:last-child td {
            border-bottom: none;
        }

        .marking-table tbody td.student-col {
            text-align: left;
            position: sticky;
            left: 0;
            background: #fff;
            z-index: 1;
            border-right: 1px solid var(--pts-grey-light);
        }

        .marking-table tbody tr:hover td.student-col {
            background: #FAFBFC;
        }

        .student-cell {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .student-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: var(--pts-black);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .student-name {
            font-size: 13px;
            font-weight: 600;
            color: #111827;
        }

        .mark-input {
            width: 52px;
            padding: 7px 4px;
            border: 1.5px solid var(--pts-grey-light);
            border-radius: 7px;
            font-size: 13px;
            font-weight: 700;
            text-align: center;
            outline: none;
            color: #111827;
        }

        .mark-input:focus {
            border-color: var(--pts-green);
            box-shadow: 0 0 0 3px rgba(122, 193, 66, 0.15);
        }

        .mark-input.over-limit {
            border-color: #DC2626;
            background: #FEF2F2;
        }

        .row-total {
            font-size: 14px;
            font-weight: 800;
            color: #111827;
            background: var(--pts-bg);
            border-radius: 7px;
            padding: 7px 10px;
            display: inline-block;
            min-width: 44px;
        }

        .row-total.high {
            color: var(--pts-green-dark);
            background: var(--pts-green-light);
        }

        .row-total.low {
            color: #DC2626;
            background: #FEF2F2;
        }

        .q-max {
            display: block;
            font-size: 9.5px;
            color: var(--pts-grey);
            font-weight: 500;
            margin-top: 2px;
            text-transform: none;
            letter-spacing: 0;
        }

        /* ---------- Footer bar ---------- */
        .marking-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 14px;
            padding: 16px 22px;
        }

        .marking-footer__stats {
            display: flex;
            gap: 26px;
            font-size: 12.5px;
            color: var(--pts-grey);
        }

        .marking-footer__stats strong {
            color: #111827;
            font-size: 14px;
            display: block;
        }

        .footer-actions {
            display: flex;
            gap: 10px;
        }

        .btn-secondary {
            padding: 11px 20px;
            background: #fff;
            border: 1.5px solid var(--pts-grey-light);
            border-radius: 9px;
            font-size: 13px;
            font-weight: 700;
            color: #374151;
            cursor: pointer;
        }

        .btn-primary {
            padding: 11px 22px;
            background: var(--pts-black);
            color: #fff;
            border: none;
            border-radius: 9px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary:hover {
            background: var(--pts-green-dark);
        }
    </style>
@endsection

@section('content')

    @php
        $questions = $questions ?? [
            ['no' => 1, 'max' => 10],
            ['no' => 2, 'max' => 15],
            ['no' => 3, 'max' => 10],
            ['no' => 4, 'max' => 20],
            ['no' => 5, 'max' => 15],
        ];
        $studentsList = $studentsList ?? [
            ['id' => 1, 'name' => 'John Banda'],
            ['id' => 2, 'name' => 'Grace Phiri'],
            ['id' => 3, 'name' => 'Chikondi Mvula'],
            ['id' => 4, 'name' => 'Blessings Nyirenda'],
        ];
        $totalMax = array_sum(array_column($questions, 'max'));
    @endphp

    <div class="marking-wrap">
        <div class="marking-header">
            <div>
                <a href="{{ route('assessments.show', $assessment->id ?? 1) }}" class="marking-header__back"><i
                        class="fa-solid fa-arrow-left"></i> Back</a>
                <div class="marking-header__title">{{ $assessment->name ?? 'Mid-Term Examination' }} —
                    {{ $assessment->subject ?? 'Mathematics' }}</div>
                <div class="marking-header__sub">{{ $assessment->class ?? 'Form 2A' }} · {{ count($studentsList) }} students
                    · {{ $totalMax }} total marks</div>
            </div>
            <div class="marking-mode-toggle">
                <button class="active">Question-Level</button>
                <button>Bulk Total</button>
                <button>Rubric</button>
            </div>
        </div>

        <div class="marking-toolbar">
            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" placeholder="Search student...">
            </div>
            <div class="marking-toolbar-spacer"></div>
            <button type="button" class="btn-upload"><i class="fa-solid fa-file-arrow-up"></i> Upload CSV</button>
        </div>

        <form method="POST" action="{{ route('marking.store') }}" id="markingForm">
            @csrf
            <div class="marking-grid-wrap">
                <table class="marking-table">
                    <thead>
                        <tr>
                            <th class="student-col">Student</th>
                            @foreach ($questions as $q)
                                <th>Q{{ $q['no'] }} <span class="q-max">/{{ $q['max'] }}</span></th>
                            @endforeach
                            <th class="total-col">Total <span class="q-max" style="color:#9CA3AF;">/{{ $totalMax }}</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($studentsList as $s)
                            <tr data-student-row>
                                <td class="student-col">
                                    <div class="student-cell">
                                        <div class="student-avatar">{{ strtoupper(substr($s['name'], 0, 1)) }}</div>
                                        <div class="student-name">{{ $s['name'] }}</div>
                                    </div>
                                </td>
                                @foreach ($questions as $q)
                                    <td>
                                        <input type="number" class="mark-input q-mark" min="0" max="{{ $q['max'] }}"
                                            name="marks[{{ $s['id'] }}][{{ $q['no'] }}]" data-max="{{ $q['max'] }}" placeholder="0">
                                    </td>
                                @endforeach
                                <td><span class="row-total" data-row-total>0</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="marking-footer">
                <div class="marking-footer__stats">
                    <div><strong id="markedCount">0</strong> Marked</div>
                    <div><strong>{{ count($studentsList) }}</strong> Total Students</div>
                    <div><strong id="classAvg">0%</strong> Running Average</div>
                </div>
                <div class="footer-actions">
                    <button type="button" class="btn-secondary">Save Draft</button>
                    <button type="submit" class="btn-primary">Submit Marks <i class="fa-solid fa-check"></i></button>
                </div>
            </div>
        </form>
    </div>

    <script>
        const TOTAL_MAX = {{ $totalMax }};

        function recalcRow(row) {
            let sum = 0, filled = 0;
            row.querySelectorAll('.q-mark').forEach(input => {
                const val = Number(input.value);
                const max = Number(input.dataset.max);
                input.classList.toggle('over-limit', val > max);
                if (input.value !== '') { sum += val; filled++; }
            });
            const totalEl = row.querySelector('[data-row-total]');
            totalEl.textContent = sum;
            const pct = TOTAL_MAX ? (sum / TOTAL_MAX) * 100 : 0;
            totalEl.className = 'row-total ' + (pct >= 65 ? 'high' : (pct < 40 ? 'low' : ''));
            return filled > 0;
        }

        function recalcAll() {
            const rows = document.querySelectorAll('[data-student-row]');
            let markedRows = 0, sumOfTotals = 0;
            rows.forEach(row => {
                const hasEntry = recalcRow(row);
                if (hasEntry) markedRows++;
                sumOfTotals += Number(row.querySelector('[data-row-total]').textContent);
            });
            document.getElementById('markedCount').textContent = markedRows;
            const avgPct = rows.length && TOTAL_MAX ? (sumOfTotals / rows.length / TOTAL_MAX) * 100 : 0;
            document.getElementById('classAvg').textContent = avgPct.toFixed(1) + '%';
        }

        document.querySelectorAll('.q-mark').forEach(input => {
            input.addEventListener('input', recalcAll);
        });
    </script>

@endsection