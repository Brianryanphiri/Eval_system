@extends('layouts.app')

@section('title', 'Create Intervention')
@section('page-title', 'Create Intervention')

@section('styles')
    <style>
        .form-wrap { max-width: 760px; }

        .form-header__back { display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--pts-grey); text-decoration: none; margin-bottom: 10px; }
        .form-header__back:hover { color: var(--pts-green-dark); }
        .form-header__title { font-size: 20px; font-weight: 800; color: #111827; margin-bottom: 20px; }
        .form-header__sub { font-size: 13.5px; color: var(--pts-grey); margin-top: 4px; }

        .panel { background: var(--pts-white); border: 1px solid var(--pts-grey-light); border-radius: 14px; padding: 28px; margin-bottom: 18px; }
        .panel__section-title {
            font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em;
            color: var(--pts-green-dark); margin-bottom: 18px; display: flex; align-items: center; gap: 8px;
        }

        .form-group { margin-bottom: 18px; }
        .form-group:last-child { margin-bottom: 0; }
        .form-label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 7px; }
        .form-label .req { color: #DC2626; }
        .form-input, .form-select, .form-textarea {
            width: 100%; padding: 11px 14px; border: 1.5px solid var(--pts-grey-light); border-radius: 9px;
            font-size: 13.5px; color: #111827; outline: none; background: #fff; font-family: 'Inter', sans-serif;
        }
        .form-input:focus, .form-select:focus, .form-textarea:focus { border-color: var(--pts-green); box-shadow: 0 0 0 3px rgba(122,193,66,0.15); }
        .form-textarea { resize: vertical; min-height: 70px; }
        .form-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

        /* ---------- Student search ---------- */
        .student-search-wrap { position: relative; }
        .student-search-wrap i.search-icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--pts-grey); font-size: 13px; }
        .student-search-wrap input { padding-left: 40px; }
        .selected-student {
            display: none; align-items: center; gap: 10px; padding: 10px 14px; background: var(--pts-green-light);
            border: 1.5px solid var(--pts-green); border-radius: 9px; margin-top: 8px;
        }
        .selected-student.show { display: flex; }
        .selected-student__avatar {
            width: 30px; height: 30px; border-radius: 50%; background: var(--pts-black); color: #fff;
            display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700;
        }
        .selected-student__name { font-size: 13px; font-weight: 700; color: #111827; }
        .selected-student__meta { font-size: 11.5px; color: var(--pts-green-dark); }
        .selected-student__remove { margin-left: auto; background: none; border: none; color: #6B7280; cursor: pointer; font-size: 13px; }

        /* ---------- Target preview ---------- */
        .target-preview {
            display: flex; align-items: center; gap: 12px; margin-top: 8px; padding: 12px 16px;
            background: var(--pts-bg); border-radius: 9px; font-size: 12.5px; color: #374151;
        }
        .target-preview strong { color: var(--pts-green-dark); }
        .target-preview .arrow { color: var(--pts-grey); }

        .form-footer { display: flex; align-items: center; justify-content: flex-end; gap: 10px; }
        .btn-secondary {
            padding: 12px 22px; background: #fff; border: 1.5px solid var(--pts-grey-light); border-radius: 9px;
            font-size: 13.5px; font-weight: 700; color: #374151; cursor: pointer;
        }
        .btn-primary {
            padding: 12px 24px; background: var(--pts-black); color: #fff; border: none; border-radius: 9px;
            font-size: 13.5px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 8px;
        }
        .btn-primary:hover { background: var(--pts-green-dark); }

        @media (max-width: 700px) { .form-grid-2 { grid-template-columns: 1fr; } }
    </style>
@endsection

@section('content')

    <div class="form-wrap">
        <a href="{{ route('interventions.index') }}" class="form-header__back"><i class="fa-solid fa-arrow-left"></i> Back to Interventions</a>
        <div class="form-header__title">Create Intervention</div>

        <form method="POST" action="{{ route('interventions.store') }}">
            @csrf

            <div class="panel">
                <div class="panel__section-title"><i class="fa-solid fa-user"></i> Student</div>
                <div class="form-group">
                    <label class="form-label">Search Student <span class="req">*</span></label>
                    <div class="student-search-wrap">
                        <i class="fa-solid fa-magnifying-glass search-icon"></i>
                        <input type="text" id="studentSearch" class="form-input" placeholder="Type student name...">
                    </div>
                    <input type="hidden" name="student_id" id="studentIdInput">
                    <div class="selected-student" id="selectedStudentBox">
                        <div class="selected-student__avatar" id="selectedAvatar"></div>
                        <div>
                            <div class="selected-student__name" id="selectedName"></div>
                            <div class="selected-student__meta" id="selectedMeta"></div>
                        </div>
                        <button type="button" class="selected-student__remove" id="clearStudentBtn"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                </div>
            </div>

            <div class="panel">
                <div class="panel__section-title"><i class="fa-solid fa-magnifying-glass-chart"></i> Problem Identification</div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label">Subject / Area <span class="req">*</span></label>
                        <input type="text" name="area" class="form-input" placeholder="e.g. Algebra" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Trigger</label>
                        <select name="trigger" class="form-select">
                            <option value="declining_performance">Declining Performance</option>
                            <option value="low_completion">Low Assessment Completion</option>
                            <option value="repeated_failures">Repeated Subject Failures</option>
                            <option value="low_attendance">Low Attendance</option>
                            <option value="performance_gap">Large Performance Gap</option>
                            <option value="manual">Manually Flagged</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Problem Description <span class="req">*</span></label>
                    <textarea name="problem" class="form-textarea" placeholder="e.g. Low mastery in simultaneous equations, scoring below class average on recent assessments" required></textarea>
                </div>
            </div>

            <div class="panel">
                <div class="panel__section-title"><i class="fa-solid fa-hand-holding-heart"></i> Intervention Plan</div>

                <div class="form-group">
                    <label class="form-label">Intervention Type <span class="req">*</span></label>
                    <select name="intervention_type" class="form-select" required>
                        <option value="" disabled selected>Select intervention</option>
                        <option value="remedial_lessons">Remedial Lessons</option>
                        <option value="one_on_one">One-on-One Tutoring</option>
                        <option value="peer_support">Peer Support / Study Group</option>
                        <option value="extra_practice">Extra Practice Exercises</option>
                        <option value="counseling_referral">Counseling Referral</option>
                        <option value="parent_meeting">Parent/Guardian Meeting</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label">Assigned Teacher <span class="req">*</span></label>
                        <select name="teacher_id" class="form-select" required>
                            <option value="" disabled selected>Select teacher</option>
                            <option value="1">Mr. Phiri</option>
                            <option value="2">Mrs. Zulu</option>
                            <option value="3">Mr. Kamanga</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Priority</label>
                        <select name="priority" class="form-select">
                            <option value="low">Low</option>
                            <option value="moderate" selected>Moderate</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label">Start Date <span class="req">*</span></label>
                        <input type="date" name="start_date" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Review Date <span class="req">*</span></label>
                        <input type="date" name="review_date" class="form-input" required>
                    </div>
                </div>
            </div>

            <div class="panel">
                <div class="panel__section-title"><i class="fa-solid fa-bullseye"></i> Target Outcome</div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label">Current Score <span class="req">*</span></label>
                        <input type="number" name="current_score" id="currentScore" class="form-input" placeholder="42" min="0" max="100" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Target Score <span class="req">*</span></label>
                        <input type="number" name="target_score" id="targetScore" class="form-input" placeholder="60" min="0" max="100" required>
                    </div>
                </div>

                <div class="target-preview" id="targetPreview">
                    <i class="fa-solid fa-bullseye" style="color:var(--pts-green-dark);"></i>
                    Increase score from <strong>—%</strong> <span class="arrow">→</span> <strong>—%</strong>
                </div>
            </div>

            <div class="panel">
                <div class="form-footer">
                    <button type="button" class="btn-secondary" onclick="history.back()">Cancel</button>
                    <button type="submit" class="btn-primary"><i class="fa-solid fa-check"></i> Create Intervention</button>
                </div>
            </div>
        </form>
    </div>

    <script>
        // Placeholder student list — replace with an AJAX lookup against Student::search() later
        const mockStudents = [
            { id: 1, name: 'John Banda', class: 'Form 2A' },
            { id: 2, name: 'Grace Phiri', class: 'Form 3B' },
            { id: 3, name: 'Chikondi Mvula', class: 'Form 1C' },
        ];

        const searchInput = document.getElementById('studentSearch');
        searchInput.addEventListener('change', () => {
            const match = mockStudents.find(s => s.name.toLowerCase() === searchInput.value.toLowerCase());
            if (match) selectStudent(match);
        });

        function selectStudent(s) {
            document.getElementById('studentIdInput').value = s.id;
            document.getElementById('selectedAvatar').textContent = s.name.charAt(0).toUpperCase();
            document.getElementById('selectedName').textContent = s.name;
            document.getElementById('selectedMeta').textContent = s.class;
            document.getElementById('selectedStudentBox').classList.add('show');
            searchInput.style.display = 'none';
        }

        document.getElementById('clearStudentBtn').addEventListener('click', () => {
            document.getElementById('studentIdInput').value = '';
            document.getElementById('selectedStudentBox').classList.remove('show');
            searchInput.value = '';
            searchInput.style.display = 'block';
        });

        function updateTargetPreview() {
            const current = document.getElementById('currentScore').value || '—';
            const target = document.getElementById('targetScore').value || '—';
            document.getElementById('targetPreview').innerHTML =
                `<i class="fa-solid fa-bullseye" style="color:var(--pts-green-dark);"></i> Increase score from <strong>${current}%</strong> <span class="arrow">→</span> <strong>${target}%</strong>`;
        }
        document.getElementById('currentScore').addEventListener('input', updateTargetPreview);
        document.getElementById('targetScore').addEventListener('input', updateTargetPreview);
    </script>

@endsection