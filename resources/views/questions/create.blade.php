@extends('layouts.app')

@section('title', 'Question Bank')
@section('page-title', 'Add Questions')

@section('styles')
    <style>
        .form-wrap {
            max-width: 100%;
        }

        .form-header__back {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--pts-grey);
            text-decoration: none;
            margin-bottom: 10px;
        }

        .form-header__back:hover {
            color: var(--pts-green-dark);
        }

        .form-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .form-header__title {
            font-size: 20px;
            font-weight: 800;
            color: #111827;
        }

        .form-header__sub {
            font-size: 13.5px;
            color: var(--pts-grey);
            margin-top: 4px;
        }

        /* ---------- Exam context bar ---------- */
        .exam-context {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 16px 22px;
            background: var(--pts-black);
            border-radius: 12px;
            margin-bottom: 22px;
            color: #fff;
            flex-wrap: wrap;
        }

        .exam-context__item {
            font-size: 13px;
            color: #C9CBCF;
            display: flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
        }

        .exam-context__item i {
            color: var(--pts-green);
            font-size: 13px;
        }

        .exam-context__item strong {
            color: #fff;
            font-weight: 700;
        }

        .exam-context__marks {
            margin-left: auto;
            text-align: right;
            white-space: nowrap;
        }

        .exam-context__marks strong {
            font-size: 20px;
            font-weight: 800;
            color: var(--pts-green);
        }

        .exam-context__marks span {
            font-size: 12.5px;
            color: #C9CBCF;
        }

        /* ---------- Question cards ---------- */
        .question-card {
            background: var(--pts-white);
            border: 1.5px solid var(--pts-grey-light);
            border-radius: 14px;
            padding: 24px 28px;
            margin-bottom: 16px;
            position: relative;
        }

        .question-card__head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
        }

        .question-card__number {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 15px;
            font-weight: 800;
            color: #111827;
        }

        .question-card__number span {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            background: var(--pts-green-light);
            color: var(--pts-green-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 800;
        }

        .question-card__remove {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: 1px solid var(--pts-grey-light);
            background: #fff;
            color: #DC2626;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
        }

        .question-card__remove:hover {
            background: #FEF2F2;
            border-color: #FECACA;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group:last-child {
            margin-bottom: 0;
        }

        .form-label {
            display: block;
            font-size: 12.5px;
            font-weight: 700;
            color: #374151;
            margin-bottom: 6px;
        }

        .form-input,
        .form-select,
        .form-textarea {
            width: 100%;
            padding: 10px 13px;
            border: 1.5px solid var(--pts-grey-light);
            border-radius: 8px;
            font-size: 13.5px;
            color: #111827;
            outline: none;
            background: #fff;
            font-family: 'Inter', sans-serif;
        }

        .form-input:focus,
        .form-select:focus,
        .form-textarea:focus {
            border-color: var(--pts-green);
            box-shadow: 0 0 0 3px rgba(122, 193, 66, 0.15);
        }

        .form-textarea {
            resize: vertical;
            min-height: 80px;
        }

        .meta-grid {
            display: grid;
            grid-template-columns: 2fr 2fr 1.4fr 1fr;
            gap: 14px;
        }

        /* ---------- Cognitive level pills ---------- */
        .cog-pills {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 6px;
        }

        .cog-pill {
            position: relative;
        }

        .cog-pill input {
            position: absolute;
            opacity: 0;
            inset: 0;
            cursor: pointer;
        }

        .cog-pill label {
            display: block;
            padding: 7px 14px;
            border: 1.5px solid var(--pts-grey-light);
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            color: #374151;
            cursor: pointer;
            transition: all 0.12s;
        }

        .cog-pill label:hover {
            border-color: var(--pts-green);
        }

        .cog-pill input:checked+label {
            background: var(--pts-green);
            border-color: var(--pts-green);
            color: var(--pts-black);
        }

        /* ---------- Difficulty buttons ---------- */
        .difficulty-select-wrap {
            display: flex;
            gap: 8px;
        }

        .diff-btn {
            position: relative;
            flex: 1;
        }

        .diff-btn input {
            position: absolute;
            opacity: 0;
            inset: 0;
            cursor: pointer;
        }

        .diff-btn label {
            display: block;
            text-align: center;
            padding: 9px;
            border: 1.5px solid var(--pts-grey-light);
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
            color: #374151;
            cursor: pointer;
            transition: all 0.12s;
        }

        .diff-btn input:checked+label.easy {
            background: var(--pts-green-light);
            border-color: var(--pts-green);
            color: var(--pts-green-dark);
        }

        .diff-btn input:checked+label.medium {
            background: #FFFBEB;
            border-color: #D97706;
            color: #D97706;
        }

        .diff-btn input:checked+label.hard {
            background: #FEF2F2;
            border-color: #DC2626;
            color: #DC2626;
        }

        /* ---------- Add question button ---------- */
        .add-question-btn {
            width: 100%;
            padding: 16px;
            border: 1.5px dashed var(--pts-grey-light);
            border-radius: 12px;
            background: transparent;
            color: var(--pts-grey);
            font-size: 13.5px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 20px;
            transition: all 0.15s;
        }

        .add-question-btn:hover {
            border-color: var(--pts-green);
            color: var(--pts-green-dark);
            background: var(--pts-green-light);
        }

        /* ---------- Marks summary ---------- */
        .marks-summary {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 22px;
            background: var(--pts-bg);
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 13.5px;
            color: #374151;
            font-weight: 600;
        }

        .marks-summary strong {
            font-size: 17px;
            font-weight: 800;
        }

        .marks-summary strong.ok {
            color: var(--pts-green-dark);
        }

        .marks-summary strong.bad {
            color: #DC2626;
        }

        /* ---------- Footer ---------- */
        .form-footer {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            padding-bottom: 40px;
        }

        .btn-secondary {
            padding: 12px 22px;
            background: #fff;
            border: 1.5px solid var(--pts-grey-light);
            border-radius: 9px;
            font-size: 13.5px;
            font-weight: 700;
            color: #374151;
            cursor: pointer;
        }

        .btn-secondary:hover {
            border-color: var(--pts-grey);
        }

        .btn-primary {
            padding: 12px 24px;
            background: var(--pts-black);
            color: #fff;
            border: none;
            border-radius: 9px;
            font-size: 13.5px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary:hover {
            background: var(--pts-green-dark);
        }

        @media (max-width: 900px) {
            .meta-grid {
                grid-template-columns: 1fr 1fr;
            }
        }
    </style>
@endsection

@section('content')

    <div class="form-wrap">
        <a href="{{ route('assessments.show', $assessment->id) }}" class="form-header__back">
            <i class="fa-solid fa-arrow-left"></i> Back to Assessment
        </a>

        <div class="form-header">
            <div>
                <div class="form-header__title">Add Questions</div>
                <div class="form-header__sub">
                    Build the question set with topic, difficulty and cognitive level tagging for deeper analytics later.
                </div>
            </div>
        </div>

        <div class="exam-context">
            <div class="exam-context__item">
                <i class="fa-solid fa-file-lines"></i>
                <strong>{{ $assessment->name }}</strong>
            </div>
            <div class="exam-context__item">
                <i class="fa-solid fa-book"></i>
                {{ $assessment->subject?->name ?? '—' }}
            </div>
            <div class="exam-context__item">
                <i class="fa-solid fa-chalkboard"></i>
                {{ $assessment->classroom?->name ?? '—' }}
            </div>
            @if ($assessment->term)
                <div class="exam-context__item">
                    <i class="fa-regular fa-calendar"></i>
                    {{ $assessment->term->name }}
                </div>
            @endif
            <div class="exam-context__marks">
                <strong id="totalAllocated">0</strong>
                <span>/ {{ $assessment->total_marks }} marks allocated</span>
            </div>
        </div>

        <form method="POST" action="{{ route('questions.store', $assessment->id) }}">
            @csrf

            <div id="questionsContainer"></div>

            <button type="button" class="add-question-btn" id="addQuestionBtn">
                <i class="fa-solid fa-plus"></i> Add Question
            </button>

            <div class="marks-summary">
                <span>Total marks allocated across all questions</span>
                <strong id="marksSummaryValue">0 / {{ $assessment->total_marks }}</strong>
            </div>

            <div class="form-footer">
                <button type="button" class="btn-secondary">Save as Draft</button>
                <button type="submit" class="btn-primary">
                    Save Questions <i class="fa-solid fa-check"></i>
                </button>
            </div>
        </form>
    </div>

    <script>
        const TOTAL_MARKS = {{ $assessment->total_marks }};
        const container = document.getElementById('questionsContainer');
        let qCount = 0;

        function addQuestion() {
            qCount++;
            const card = document.createElement('div');
            card.className = 'question-card';
            card.innerHTML = `
                    <div class="question-card__head">
                        <div class="question-card__number"><span>${qCount}</span> Question ${qCount}</div>
                        <button type="button" class="question-card__remove" onclick="removeQuestion(this)">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Question Text</label>
                        <textarea class="form-textarea" name="questions[${qCount}][text]" placeholder="Enter the question..." required></textarea>
                    </div>

                    <div class="meta-grid">
                        <div class="form-group">
                            <label class="form-label">Topic</label>
                            <input type="text" class="form-input" name="questions[${qCount}][topic]" placeholder="e.g. Algebra" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Learning Objective</label>
                            <input type="text" class="form-input" name="questions[${qCount}][objective]" placeholder="e.g. Solve simultaneous equations">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Question Type</label>
                            <select class="form-select" name="questions[${qCount}][type]">
                                <option value="short_answer">Short Answer</option>
                                <option value="essay">Essay</option>
                                <option value="mcq">Multiple Choice</option>
                                <option value="calculation">Calculation</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Marks</label>
                            <input type="number" class="form-input marks-input" name="questions[${qCount}][marks]" placeholder="10" min="1" required>
                        </div>
                    </div>

                    <div class="form-group" style="margin-top:16px;">
                        <label class="form-label">Difficulty</label>
                        <div class="difficulty-select-wrap">
                            <div class="diff-btn">
                                <input type="radio" name="questions[${qCount}][difficulty]" id="diff${qCount}_easy" value="easy">
                                <label for="diff${qCount}_easy" class="easy">Easy</label>
                            </div>
                            <div class="diff-btn">
                                <input type="radio" name="questions[${qCount}][difficulty]" id="diff${qCount}_medium" value="medium" checked>
                                <label for="diff${qCount}_medium" class="medium">Medium</label>
                            </div>
                            <div class="diff-btn">
                                <input type="radio" name="questions[${qCount}][difficulty]" id="diff${qCount}_hard" value="hard">
                                <label for="diff${qCount}_hard" class="hard">Hard</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Cognitive Level</label>
                        <div class="cog-pills">
                            ${['Remember', 'Understand', 'Apply', 'Analyse', 'Evaluate', 'Create'].map((level, i) => `
                                <div class="cog-pill">
                                    <input type="radio" name="questions[${qCount}][cognitive_level]" id="cog${qCount}_${i}" value="${level.toLowerCase()}" ${i === 2 ? 'checked' : ''}>
                                    <label for="cog${qCount}_${i}">${level}</label>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                `;
            container.appendChild(card);
            card.querySelector('.marks-input').addEventListener('input', updateMarksTotal);
            updateMarksTotal();
        }

        function removeQuestion(btn) {
            btn.closest('.question-card').remove();
            updateMarksTotal();
        }

        function updateMarksTotal() {
            let sum = 0;
            document.querySelectorAll('.marks-input').forEach(i => sum += Number(i.value) || 0);
            document.getElementById('totalAllocated').textContent = sum;
            const summaryEl = document.getElementById('marksSummaryValue');
            summaryEl.textContent = sum + ' / ' + TOTAL_MARKS;
            summaryEl.className = sum === TOTAL_MARKS ? 'ok' : (sum > TOTAL_MARKS ? 'bad' : '');
        }

        document.getElementById('addQuestionBtn').addEventListener('click', addQuestion);
        addQuestion(); // start with one question
    </script>

@endsection