@extends('layouts.app')

@section('title', 'Classes & Subjects')
@section('page-title', 'Academic Structure')

@section('styles')
    <style>
        .page-tabs {
            display: flex;
            gap: 4px;
            margin-bottom: 20px;
            border-bottom: 1.5px solid var(--pts-grey-light);
        }

        .page-tab {
            padding: 12px 18px;
            font-size: 13.5px;
            font-weight: 600;
            color: var(--pts-grey);
            cursor: pointer;
            border-bottom: 2px solid transparent;
            margin-bottom: -1.5px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .page-tab.active {
            color: var(--pts-black);
            border-bottom-color: var(--pts-green);
            font-weight: 700;
        }

        .page-tab__count {
            background: var(--pts-bg);
            color: var(--pts-grey);
            font-size: 10.5px;
            font-weight: 800;
            padding: 1px 8px;
            border-radius: 10px;
        }

        .page-tab.active .page-tab__count {
            background: var(--pts-green-light);
            color: var(--pts-green-dark);
        }

        .toolbar {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 18px;
        }

        .search-box {
            position: relative;
            max-width: 280px;
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
            padding: 10px 13px 10px 36px;
            border: 1.5px solid var(--pts-grey-light);
            border-radius: 9px;
            font-size: 13px;
            outline: none;
        }

        .toolbar-spacer {
            flex: 1;
        }

        .btn-add {
            padding: 10px 16px;
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

        .btn-add:hover {
            background: var(--pts-green-dark);
        }

        /* ---------- Class cards grid ---------- */
        .class-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }

        .class-card {
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 14px;
            padding: 20px;
        }

        .class-card__top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
        }

        .class-card__icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: var(--pts-green-light);
            color: var(--pts-green-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        .class-card__menu {
            color: var(--pts-grey);
            cursor: pointer;
            padding: 6px;
        }

        .class-card__name {
            font-size: 15.5px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 3px;
        }

        .class-card__meta {
            font-size: 12px;
            color: var(--pts-grey);
            margin-bottom: 16px;
        }

        .class-card__stats {
            display: flex;
            gap: 20px;
            padding-top: 14px;
            border-top: 1px solid var(--pts-grey-light);
        }

        .cc-stat__value {
            font-size: 15px;
            font-weight: 800;
            color: #111827;
        }

        .cc-stat__label {
            font-size: 10.5px;
            color: var(--pts-grey);
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-top: 2px;
        }

        .class-card__footer {
            display: flex;
            gap: 8px;
            margin-top: 14px;
        }

        .cc-btn {
            flex: 1;
            padding: 8px;
            border: 1.5px solid var(--pts-grey-light);
            border-radius: 8px;
            background: #fff;
            font-size: 12px;
            font-weight: 700;
            color: #374151;
            cursor: pointer;
        }

        .cc-btn:hover {
            border-color: var(--pts-green);
            color: var(--pts-green-dark);
        }

        /* ---------- Subjects table ---------- */
        .table-panel {
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 14px;
            overflow: hidden;
        }

        .subjects-table {
            width: 100%;
            border-collapse: collapse;
        }

        .subjects-table thead th {
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--pts-grey);
            font-weight: 700;
            padding: 14px 20px;
            background: var(--pts-bg);
            border-bottom: 1px solid var(--pts-grey-light);
        }

        .subjects-table tbody td {
            padding: 14px 20px;
            font-size: 13.5px;
            color: #374151;
            border-bottom: 1px solid var(--pts-grey-light);
        }

        .subjects-table tbody tr:last-child td {
            border-bottom: none;
        }

        .subject-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .subject-icon {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            background: var(--pts-bg);
            color: var(--pts-grey);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
        }

        .subject-name {
            font-size: 13.5px;
            font-weight: 600;
            color: #111827;
        }

        .subject-code {
            font-size: 11px;
            color: var(--pts-grey);
            font-family: 'Courier New', monospace;
        }

        .weight-tags {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }

        .weight-tag {
            font-size: 11px;
            font-weight: 700;
            padding: 3px 9px;
            border-radius: 6px;
            background: var(--pts-bg);
            color: #374151;
        }

        .compulsory-pill {
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 20px;
        }

        .compulsory-pill.yes {
            background: var(--pts-green-light);
            color: var(--pts-green-dark);
        }

        .compulsory-pill.no {
            background: var(--pts-bg);
            color: var(--pts-grey);
        }

        .row-actions {
            display: flex;
            gap: 6px;
        }

        .row-action-btn {
            width: 30px;
            height: 30px;
            border-radius: 7px;
            border: 1px solid var(--pts-grey-light);
            background: #fff;
            color: var(--pts-grey);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }

        .row-action-btn:hover {
            border-color: var(--pts-green);
            color: var(--pts-green-dark);
        }

        .row-action-btn.danger:hover {
            border-color: #FECACA;
            color: #DC2626;
            background: #FEF2F2;
        }

        #subjectsTab {
            display: none;
        }

        @media (max-width: 1000px) {
            .class-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 700px) {
            .class-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')

    @php
        $classes = $classes ?? [
            ['name' => 'Form 1A', 'meta' => 'Secondary · Homeroom: Mrs. Zulu', 'students' => 42, 'subjects' => 9, 'avg' => 68],
            ['name' => 'Form 1B', 'meta' => 'Secondary · Homeroom: Mr. Kamanga', 'students' => 39, 'subjects' => 9, 'avg' => 61],
            ['name' => 'Form 2A', 'meta' => 'Secondary · Homeroom: Mr. Phiri', 'students' => 44, 'subjects' => 10, 'avg' => 73],
            ['name' => 'Form 2B', 'meta' => 'Secondary · Homeroom: Mrs. Nkhoma', 'students' => 41, 'subjects' => 10, 'avg' => 57],
            ['name' => 'Form 3A', 'meta' => 'Secondary · Homeroom: Mr. Banda', 'students' => 38, 'subjects' => 11, 'avg' => 66],
            ['name' => 'Form 4A', 'meta' => 'Secondary · Homeroom: Mrs. Chirwa', 'students' => 35, 'subjects' => 8, 'avg' => 71],
        ];
        $subjects = $subjects ?? [
            ['name' => 'Mathematics', 'code' => 'MATH101', 'icon' => 'fa-square-root-variable', 'weights' => ['CA 30%', 'Exam 70%'], 'compulsory' => true],
            ['name' => 'English', 'code' => 'ENG101', 'icon' => 'fa-book-open', 'weights' => ['CA 40%', 'Exam 60%'], 'compulsory' => true],
            ['name' => 'Physical Education', 'code' => 'PE101', 'icon' => 'fa-person-running', 'weights' => ['Practical 70%', 'Theory 30%'], 'compulsory' => false],
            ['name' => 'Computer Science', 'code' => 'CS101', 'icon' => 'fa-laptop-code', 'weights' => ['Projects 40%', 'Practical 30%', 'Theory 30%'], 'compulsory' => false],
            ['name' => 'Art', 'code' => 'ART101', 'icon' => 'fa-palette', 'weights' => ['Portfolio 50%', 'Practical 30%', 'Theory 20%'], 'compulsory' => false],
        ];
    @endphp

    <div class="page-tabs">
        <div class="page-tab active" onclick="switchTab('classes', this)">Classes <span
                class="page-tab__count">{{ count($classes) }}</span></div>
        <div class="page-tab" onclick="switchTab('subjects', this)">Subjects <span
                class="page-tab__count">{{ count($subjects) }}</span></div>
    </div>

    {{-- CLASSES TAB --}}
    <div id="classesTab">
        <div class="toolbar">
            <div class="search-box"><i class="fa-solid fa-magnifying-glass"></i><input type="text"
                    placeholder="Search classes..."></div>
            <div class="toolbar-spacer"></div>
            <button type="button" class="btn-add" onclick="location.href='{{ route('classes.create') }}'"><i
                    class="fa-solid fa-plus"></i> Add Class</button>
        </div>

        <div class="class-grid">
            @foreach ($classes as $c)
                <div class="class-card">
                    <div class="class-card__top">
                        <div class="class-card__icon"><i class="fa-solid fa-chalkboard"></i></div>
                        <div class="class-card__menu"><i class="fa-solid fa-ellipsis"></i></div>
                    </div>
                    <div class="class-card__name">{{ $c['name'] }}</div>
                    <div class="class-card__meta">{{ $c['meta'] }}</div>
                    <div class="class-card__stats">
                        <div>
                            <div class="cc-stat__value">{{ $c['students'] }}</div>
                            <div class="cc-stat__label">Students</div>
                        </div>
                        <div>
                            <div class="cc-stat__value">{{ $c['subjects'] }}</div>
                            <div class="cc-stat__label">Subjects</div>
                        </div>
                        <div>
                            <div class="cc-stat__value">{{ $c['avg'] }}%</div>
                            <div class="cc-stat__label">Average</div>
                        </div>
                    </div>
                    <div class="class-card__footer">
                        <button type="button" class="cc-btn"><i class="fa-solid fa-eye"></i> View</button>
                        <button type="button" class="cc-btn"><i class="fa-solid fa-pen"></i> Edit</button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- SUBJECTS TAB --}}
    <div id="subjectsTab">
        <div class="toolbar">
            <div class="search-box"><i class="fa-solid fa-magnifying-glass"></i><input type="text"
                    placeholder="Search subjects..."></div>
            <div class="toolbar-spacer"></div>
            <button type="button" class="btn-add" onclick="location.href='{{ route('subjects.create') }}'"><i
                    class="fa-solid fa-plus"></i> Add Subject</button>
        </div>

        <div class="table-panel">
            <table class="subjects-table">
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Weighting</th>
                        <th>Compulsory</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($subjects as $s)
                        <tr>
                            <td>
                                <div class="subject-cell">
                                    <div class="subject-icon"><i class="fa-solid {{ $s['icon'] }}"></i></div>
                                    <div>
                                        <div class="subject-name">{{ $s['name'] }}</div>
                                        <div class="subject-code">{{ $s['code'] }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="weight-tags">
                                    @foreach ($s['weights'] as $w)
                                        <span class="weight-tag">{{ $w }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td>
                                <span class="compulsory-pill {{ $s['compulsory'] ? 'yes' : 'no' }}">
                                    {{ $s['compulsory'] ? 'Compulsory' : 'Elective' }}
                                </span>
                            </td>
                            <td>
                                <div class="row-actions">
                                    <button type="button" class="row-action-btn"><i class="fa-solid fa-pen"></i></button>
                                    <button type="button" class="row-action-btn danger"><i
                                            class="fa-solid fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function switchTab(tab, el) {
            document.querySelectorAll('.page-tab').forEach(t => t.classList.remove('active'));
            el.classList.add('active');
            document.getElementById('classesTab').style.display = tab === 'classes' ? 'block' : 'none';
            document.getElementById('subjectsTab').style.display = tab === 'subjects' ? 'block' : 'none';
        }
    </script>

@endsection