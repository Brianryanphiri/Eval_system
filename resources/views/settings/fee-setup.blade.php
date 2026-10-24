@extends('layouts.app')

@section('title', 'Fee Setup')
@section('page-title', 'Fee Setup')

@section('styles')
    <style>
        .page-header__title {
            font-size: 20px;
            font-weight: 800;
            color: #111827;
            margin-bottom: 4px;
        }

        .page-sub {
            font-size: 13.5px;
            color: var(--pts-grey);
            margin-bottom: 20px;
        }

        .page-tabs {
            display: flex;
            gap: 4px;
            margin-bottom: 20px;
        }

        .page-tab {
            padding: 10px 16px;
            font-size: 13px;
            font-weight: 600;
            color: var(--pts-grey);
            cursor: pointer;
            border-radius: 9px;
            background: var(--pts-bg);
        }

        .page-tab.active {
            background: var(--pts-black);
            color: #fff;
        }

        .layout-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
        }

        .panel {
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 14px;
            padding: 24px;
            margin-bottom: 18px;
        }

        .panel__title {
            font-size: 15px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 16px;
        }

        .cat-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            border: 1.5px solid var(--pts-grey-light);
            border-radius: 10px;
            margin-bottom: 10px;
        }

        .cat-row:last-child {
            margin-bottom: 0;
        }

        .cat-row__name {
            font-size: 13.5px;
            font-weight: 700;
            color: #111827;
            flex: 1;
        }

        .cat-pill {
            font-size: 10.5px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 20px;
        }

        .cat-pill.compulsory {
            background: var(--pts-green-light);
            color: var(--pts-green-dark);
        }

        .cat-pill.optional {
            background: var(--pts-bg);
            color: var(--pts-grey);
        }

        .row-remove {
            background: none;
            border: none;
            color: #DC2626;
            cursor: pointer;
            font-size: 13px;
        }

        .struct-table {
            width: 100%;
            border-collapse: collapse;
        }

        .struct-table th {
            text-align: left;
            font-size: 10.5px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--pts-grey);
            padding: 10px 8px;
            border-bottom: 1.5px solid var(--pts-grey-light);
        }

        .struct-table td {
            padding: 10px 8px;
            font-size: 13px;
            color: #374151;
            border-bottom: 1px solid var(--pts-grey-light);
        }

        .struct-table tr:last-child td {
            border-bottom: none;
        }

        .amount-cell {
            font-weight: 700;
            color: #111827;
        }

        .sidebar-form .form-group {
            margin-bottom: 14px;
        }

        .form-label {
            display: block;
            font-size: 12.5px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }

        .form-input,
        .form-select {
            width: 100%;
            padding: 10px 12px;
            border: 1.5px solid var(--pts-grey-light);
            border-radius: 9px;
            font-size: 13px;
            color: #111827;
            outline: none;
            background: #fff;
        }

        .form-input:focus,
        .form-select:focus {
            border-color: var(--pts-green);
        }

        .toggle-row {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            background: var(--pts-bg);
            border-radius: 9px;
            margin-bottom: 14px;
        }

        .toggle-row input {
            width: 16px;
            height: 16px;
            accent-color: var(--pts-green);
        }

        .toggle-row label {
            font-size: 12.5px;
            font-weight: 600;
            color: #374151;
        }

        .btn-primary-full {
            width: 100%;
            padding: 11px;
            background: var(--pts-black);
            color: #fff;
            border: none;
            border-radius: 9px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
        }

        .btn-primary-full:hover {
            background: var(--pts-green-dark);
        }

        .empty-note {
            text-align: center;
            padding: 30px 20px;
            color: var(--pts-grey);
            font-size: 13.5px;
        }

        @media (max-width: 1000px) {
            .layout-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')

    <div class="page-header__title">Fee Setup</div>
    <div class="page-sub">Define what this school charges, and how much, per class and term.</div>

    @if (session('success'))
        <div
            style="background: var(--pts-green-light); color: var(--pts-green-dark); border: 1px solid var(--pts-green); padding: 12px 16px; border-radius: 9px; font-size: 13px; margin-bottom: 18px;">
            {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div
            style="background:#FEF2F2; color:#B91C1C; border:1px solid #FECACA; padding:10px 14px; border-radius:8px; font-size:13px; margin-bottom:18px;">
            {{ $errors->first() }}</div>
    @endif

    <div class="page-tabs">
        <div class="page-tab active" onclick="switchTab('categories', this)">Fee Categories</div>
        <div class="page-tab" onclick="switchTab('structures', this)">Amounts by Class &amp; Term</div>
    </div>

    {{-- CATEGORIES TAB --}}
    <div id="categoriesTab">
        <div class="layout-grid">
            <div>
                <div class="panel">
                    <div class="panel__title">Fee Categories</div>
                    @forelse ($categories as $cat)
                        <div class="cat-row">
                            <div class="cat-row__name">{{ $cat->name }}</div>
                            <span class="cat-pill {{ $cat->is_compulsory ? 'compulsory' : 'optional' }}">
                                {{ $cat->is_compulsory ? 'Compulsory' : 'Optional, declared per term' }}
                            </span>
                            <form method="POST" action="{{ route('settings.fees.categories.destroy', $cat) }}"
                                onsubmit="return confirm('Remove this fee category? Existing invoices are not affected.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="row-remove"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </div>
                    @empty
                        <div class="empty-note">No fee categories yet. Add one using the form on the right.</div>
                    @endforelse
                </div>
            </div>

            <div>
                <div class="panel">
                    <div class="panel__title">Add Fee Category</div>
                    <form method="POST" action="{{ route('settings.fees.categories.store') }}" class="sidebar-form">
                        @csrf
                        <div class="form-group">
                            <label class="form-label">Category Name</label>
                            <input type="text" name="name" class="form-input" placeholder="Tuition, Boarding, Transport"
                                required>
                        </div>
                        <div class="toggle-row">
                            <input type="checkbox" name="is_compulsory" id="is_compulsory" value="1" checked>
                            <label for="is_compulsory">Compulsory, applies to every student automatically</label>
                        </div>
                        <div style="font-size:11.5px; color:var(--pts-grey); margin-top:-8px; margin-bottom:14px;">Uncheck
                            this for fees a student must specifically declare, like Boarding or Transport.</div>
                        <button type="submit" class="btn-primary-full">Add Category</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- STRUCTURES TAB --}}
    <div id="structuresTab" style="display:none;">
        <div class="layout-grid">
            <div>
                <div class="panel">
                    <div class="panel__title">Configured Amounts</div>
                    @if ($structures->isEmpty())
                        <div class="empty-note">No amounts set yet. Add one using the form on the right.</div>
                    @else
                        <table class="struct-table">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Class</th>
                                    <th>Term</th>
                                    <th>Amount</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($structures as $s)
                                    <tr>
                                        <td>{{ $s->feeCategory->name }}</td>
                                        <td>{{ $s->classroom?->name ?? 'All Classes' }}</td>
                                        <td>{{ $s->term->name }} ({{ $s->term->academicYear->label }})</td>
                                        <td class="amount-cell">{{ number_format($s->amount, 2) }}</td>
                                        <td>
                                            <form method="POST" action="{{ route('settings.fees.structures.destroy', $s) }}"
                                                onsubmit="return confirm('Remove this fee amount?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="row-remove"><i class="fa-solid fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

            <div>
                <div class="panel">
                    <div class="panel__title">Set an Amount</div>
                    <form method="POST" action="{{ route('settings.fees.structures.store') }}" class="sidebar-form">
                        @csrf
                        <div class="form-group">
                            <label class="form-label">Fee Category</label>
                            <select name="fee_category_id" class="form-select" required>
                                <option value="" disabled selected>Select category</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Class</label>
                            <select name="class_id" class="form-select">
                                <option value="">All Classes</option>
                                @foreach ($classrooms as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Term</label>
                            <select name="term_id" class="form-select" required>
                                <option value="" disabled selected>Select term</option>
                                @foreach ($terms as $t)
                                    <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->academicYear->label }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Amount</label>
                            <input type="number" name="amount" class="form-input" step="0.01" min="0" placeholder="150000"
                                required>
                        </div>
                        <button type="submit" class="btn-primary-full">Save Amount</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function switchTab(tab, el) {
            document.querySelectorAll('.page-tab').forEach(t => t.classList.remove('active'));
            el.classList.add('active');
            document.getElementById('categoriesTab').style.display = tab === 'categories' ? 'block' : 'none';
            document.getElementById('structuresTab').style.display = tab === 'structures' ? 'block' : 'none';
        }
    </script>

@endsection