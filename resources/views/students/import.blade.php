@extends('layouts.app')

@section('title', 'Import Students')
@section('page-title', 'Import Students')

@section('styles')
    <style>
        /* Back link */
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 500;
            color: var(--pts-grey);
            text-decoration: none;
            margin-bottom: 16px;
            transition: color 0.15s;
        }

        .back-link:hover {
            color: var(--pts-green-dark);
        }

        .back-link i {
            font-size: 11px;
        }

        /* Page header */
        .page-intro {
            margin-bottom: 24px;
        }

        .page-intro__title {
            font-size: 22px;
            font-weight: 800;
            color: #111827;
            letter-spacing: -0.01em;
            margin-bottom: 6px;
        }

        .page-intro__sub {
            font-size: 13.5px;
            color: var(--pts-grey);
            line-height: 1.5;
            max-width: 640px;
        }

        /* Two-column layout: form left, help right */
        .import-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.6fr) minmax(0, 1fr);
            gap: 20px;
            align-items: start;
        }

        /* Panel */
        .panel {
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 14px;
            padding: 28px;
        }

        /* Template callout */
        .template-cta {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 16px 18px;
            background: var(--pts-green-light);
            border: 1px solid rgba(122, 193, 66, 0.25);
            border-radius: 12px;
            margin-bottom: 26px;
        }

        .template-cta__icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--pts-green-dark);
            font-size: 17px;
            flex-shrink: 0;
        }

        .template-cta__body {
            flex: 1;
            min-width: 0;
        }

        .template-cta__title {
            font-size: 13.5px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 2px;
        }

        .template-cta__desc {
            font-size: 12.5px;
            color: #4B5563;
        }

        .template-cta__link {
            font-size: 12.5px;
            font-weight: 700;
            color: var(--pts-green-dark);
            text-decoration: none;
            padding: 9px 14px;
            border-radius: 8px;
            background: #fff;
            border: 1px solid rgba(122, 193, 66, 0.3);
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            transition: all 0.15s;
        }

        .template-cta__link:hover {
            background: var(--pts-green-dark);
            color: #fff;
            border-color: var(--pts-green-dark);
        }

        .template-cta__link i {
            font-size: 11px;
        }

        /* Dropzone */
        .dropzone {
            position: relative;
            border: 2px dashed var(--pts-grey-light);
            border-radius: 12px;
            padding: 64px 24px;
            text-align: center;
            cursor: pointer;
            transition: border-color 0.15s, background 0.15s;
            background: #FAFBFC;
        }

        .dropzone:hover,
        .dropzone.dragover {
            border-color: var(--pts-green);
            background: var(--pts-green-light);
        }

        .dropzone__icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            background: #fff;
            border: 1px solid var(--pts-grey-light);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 18px;
            font-size: 24px;
            color: var(--pts-grey);
            transition: all 0.15s;
        }

        .dropzone:hover .dropzone__icon,
        .dropzone.dragover .dropzone__icon {
            background: var(--pts-green);
            border-color: var(--pts-green);
            color: #fff;
        }

        .dropzone__title {
            font-size: 15px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 5px;
        }

        .dropzone__sub {
            font-size: 12.5px;
            color: var(--pts-grey);
        }

        .dropzone input {
            display: none;
        }

        /* File selected state */
        .file-selected {
            display: none;
            align-items: center;
            gap: 12px;
            padding: 16px 18px;
            background: var(--pts-green-light);
            border: 1.5px solid var(--pts-green);
            border-radius: 12px;
            margin-top: 14px;
        }

        .file-selected.show {
            display: flex;
        }

        .file-selected__icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--pts-green-dark);
            font-size: 16px;
            flex-shrink: 0;
        }

        .file-selected__body {
            flex: 1;
            min-width: 0;
        }

        .file-selected__name {
            font-size: 13.5px;
            font-weight: 700;
            color: #111827;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .file-selected__size {
            font-size: 11.5px;
            color: #4B5563;
            margin-top: 2px;
        }

        .file-selected__remove {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            background: #fff;
            border: 1px solid rgba(220, 38, 38, 0.2);
            color: #DC2626;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            flex-shrink: 0;
            transition: all 0.15s;
        }

        .file-selected__remove:hover {
            background: #DC2626;
            color: #fff;
            border-color: #DC2626;
        }

        /* Submit button */
        .btn-primary {
            width: 100%;
            padding: 14px;
            background: var(--pts-black);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 22px;
            transition: all 0.15s;
            font-family: inherit;
        }

        .btn-primary:hover:not(:disabled) {
            background: var(--pts-green-dark);
        }

        .btn-primary:disabled {
            background: var(--pts-grey-light);
            color: var(--pts-grey);
            cursor: not-allowed;
        }

        .btn-primary i {
            font-size: 12px;
        }

        /* Error list */
        .error-list {
            margin-top: 20px;
            padding: 18px 20px;
            background: var(--pts-red-light);
            border: 1.5px solid #FECACA;
            border-radius: 12px;
        }

        .error-list__head {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .error-list__head i {
            color: #DC2626;
            font-size: 15px;
        }

        .error-list__title {
            font-size: 13px;
            font-weight: 700;
            color: #DC2626;
        }

        .error-list ul {
            padding-left: 26px;
            margin: 0;
        }

        .error-list li {
            font-size: 12.5px;
            color: #7F1D1D;
            margin-bottom: 5px;
            line-height: 1.5;
        }

        .error-list li:last-child {
            margin-bottom: 0;
        }

        /* ---------- Help sidebar ---------- */
        .help-panel {
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 14px;
            padding: 24px;
            position: sticky;
            top: calc(var(--topbar-height) + 24px);
        }

        .help-panel__head {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 18px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--pts-grey-light);
        }

        .help-panel__head i {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: var(--pts-green-light);
            color: var(--pts-green-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
        }

        .help-panel__title {
            font-size: 14px;
            font-weight: 700;
            color: #111827;
        }

        .help-block {
            padding: 14px 0;
            border-bottom: 1px solid var(--pts-grey-light);
        }

        .help-block:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .help-block:first-of-type {
            padding-top: 0;
        }

        .help-block__label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--pts-grey);
            margin-bottom: 10px;
        }

        .help-block__item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 12.5px;
            color: #374151;
            line-height: 1.5;
            margin-bottom: 8px;
        }

        .help-block__item:last-child {
            margin-bottom: 0;
        }

        .help-block__item i {
            color: var(--pts-green-dark);
            font-size: 11px;
            margin-top: 4px;
            flex-shrink: 0;
            width: 14px;
            text-align: center;
        }

        .help-block__item strong {
            color: #111827;
            font-weight: 700;
        }

        .help-block__item code {
            background: var(--pts-bg);
            padding: 2px 7px;
            border-radius: 5px;
            font-family: 'SF Mono', 'Monaco', 'Courier New', monospace;
            font-size: 11.5px;
            color: var(--pts-green-dark);
            border: 1px solid var(--pts-grey-light);
            font-weight: 600;
        }

        @media (max-width: 1024px) {
            .import-grid {
                grid-template-columns: 1fr;
            }

            .help-panel {
                position: static;
            }
        }

        @media (max-width: 640px) {

            .panel,
            .help-panel {
                padding: 20px;
            }

            .template-cta {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }

            .template-cta__link {
                width: 100%;
                justify-content: center;
            }

            .dropzone {
                padding: 40px 16px;
            }
        }
    </style>
@endsection

@section('content')

    <a href="{{ route('students.index') }}" class="back-link">
        <i class="fa-solid fa-arrow-left"></i> Back to Students
    </a>

    <div class="page-intro">
        <h1 class="page-intro__title">Import Students</h1>
        <p class="page-intro__sub">
            Add multiple students at once by uploading a spreadsheet. Useful when onboarding a school's existing student
            records.
        </p>
    </div>

    <div class="import-grid">

        {{-- LEFT: Form --}}
        <div class="panel">

            {{-- Template download --}}
            <div class="template-cta">
                <div class="template-cta__icon">
                    <i class="fa-solid fa-file-arrow-down"></i>
                </div>
                <div class="template-cta__body">
                    <div class="template-cta__title">Need a template?</div>
                    <div class="template-cta__desc">Download a sample CSV with the correct columns and format.</div>
                </div>
                <a href="{{ route('students.import.template') }}" class="template-cta__link">
                    <i class="fa-solid fa-download"></i> Download Template
                </a>
            </div>

            <form method="POST" action="{{ route('students.import.store') }}" enctype="multipart/form-data" id="importForm">
                @csrf

                <label class="dropzone" id="dropzone">
                    <div class="dropzone__icon">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                    </div>
                    <div class="dropzone__title">Click to upload or drag and drop</div>
                    <div class="dropzone__sub">CSV file, up to 5 MB</div>
                    <input type="file" name="file" id="fileInput" accept=".csv,.txt" required>
                </label>

                <div class="file-selected" id="fileSelected">
                    <div class="file-selected__icon">
                        <i class="fa-solid fa-file-csv"></i>
                    </div>
                    <div class="file-selected__body">
                        <div class="file-selected__name" id="fileName"></div>
                        <div class="file-selected__size" id="fileSize"></div>
                    </div>
                    <button type="button" class="file-selected__remove" id="removeFile" title="Remove file">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <button type="submit" class="btn-primary" id="submitBtn" disabled>
                    <i class="fa-solid fa-upload"></i> Import Students
                </button>
            </form>

            @if (session('import_errors') && count(session('import_errors')) > 0)
                <div class="error-list">
                    <div class="error-list__head">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <div class="error-list__title">
                            {{ count(session('import_errors')) }} {{ Str::plural('row', count(session('import_errors'))) }} had
                            issues and were skipped
                        </div>
                    </div>
                    <ul>
                        @foreach (session('import_errors') as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

        </div>

        {{-- RIGHT: Help sidebar --}}
        <aside class="help-panel">

            <div class="help-panel__head">
                <i class="fa-solid fa-circle-info"></i>
                <div class="help-panel__title">File Requirements</div>
            </div>

            <div class="help-block">
                <div class="help-block__label">Required Columns</div>
                <div class="help-block__item">
                    <i class="fa-solid fa-check"></i>
                    <div><code>first_name</code> and <code>last_name</code></div>
                </div>
                <div class="help-block__item">
                    <i class="fa-solid fa-check"></i>
                    <div><code>class_name</code> matching an existing class exactly, e.g. <strong>Form 2A</strong></div>
                </div>
            </div>

            <div class="help-block">
                <div class="help-block__label">Optional Columns</div>
                <div class="help-block__item">
                    <i class="fa-solid fa-circle"></i>
                    <div><code>date_of_birth</code> in YYYY-MM-DD format</div>
                </div>
                <div class="help-block__item">
                    <i class="fa-solid fa-circle"></i>
                    <div><code>gender</code> as Male or Female</div>
                </div>
            </div>

            <div class="help-block">
                <div class="help-block__label">File Rules</div>
                <div class="help-block__item">
                    <i class="fa-solid fa-circle"></i>
                    <div>Must be a <strong>.csv</strong> file, up to 5 MB</div>
                </div>
                <div class="help-block__item">
                    <i class="fa-solid fa-circle"></i>
                    <div>First row must contain column headers</div>
                </div>
                <div class="help-block__item">
                    <i class="fa-solid fa-circle"></i>
                    <div>Invalid rows are skipped and reported below the form</div>
                </div>
            </div>

        </aside>

    </div>

    <script>
        const dropzone = document.getElementById('dropzone');
        const fileInput = document.getElementById('fileInput');
        const fileSelected = document.getElementById('fileSelected');
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');
        const submitBtn = document.getElementById('submitBtn');
        const removeFile = document.getElementById('removeFile');

        function formatSize(bytes) {
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
            return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
        }

        function showFile(file) {
            fileName.textContent = file.name;
            fileSize.textContent = formatSize(file.size);
            fileSelected.classList.add('show');
            dropzone.style.display = 'none';
            submitBtn.disabled = false;
        }

        fileInput.addEventListener('change', () => {
            if (fileInput.files.length) showFile(fileInput.files[0]);
        });

        ['dragover', 'dragenter'].forEach(evt => {
            dropzone.addEventListener(evt, (e) => {
                e.preventDefault();
                dropzone.classList.add('dragover');
            });
        });
        ['dragleave', 'drop'].forEach(evt => {
            dropzone.addEventListener(evt, (e) => {
                e.preventDefault();
                dropzone.classList.remove('dragover');
            });
        });
        dropzone.addEventListener('drop', (e) => {
            const file = e.dataTransfer.files[0];
            if (file) {
                fileInput.files = e.dataTransfer.files;
                showFile(file);
            }
        });

        removeFile.addEventListener('click', () => {
            fileInput.value = '';
            fileSelected.classList.remove('show');
            dropzone.style.display = 'block';
            submitBtn.disabled = true;
        });
    </script>

@endsection