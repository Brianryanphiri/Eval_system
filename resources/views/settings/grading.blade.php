@extends('settings.partials._layout')

@section('settings-content')

    <a href="{{ route('settings.overview') }}" class="settings-back">
        <i class="fa-solid fa-arrow-left"></i> Back to Settings
    </a>

    <div class="settings-panel__title">Grading Scales</div>
    <div class="settings-panel__sub">
        Define the letter grades and percentage bands used across all subjects.
    </div>

    @if ($errors->any())
        <div style="background:#FEF2F2; color:#991B1B; border:1px solid #FECACA;
                    padding:12px 16px; border-radius:9px; font-size:13px; margin-bottom:18px;">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- Add band form --}}
    <div style="background: var(--pts-bg); border-radius: 12px; padding: 18px 20px; margin-bottom: 22px;">
        <div style="font-size: 13px; font-weight: 800; color: #111827; margin-bottom: 12px;">
            <i class="fa-solid fa-plus" style="color: var(--pts-green-dark);"></i> Add a grade band
        </div>

        <form method="POST" action="{{ route('settings.grading.store') }}">
            @csrf

            <div style="display: grid; grid-template-columns: 90px 110px 110px 1fr auto; gap: 10px; align-items: end;">
                <div>
                    <label style="display:block; font-size:10.5px; font-weight:700; color:#6B7280; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:4px;">
                        Letter
                    </label>
                    <input type="text" name="letter" value="{{ old('letter') }}" required maxlength="5"
                        placeholder="A"
                        style="width:100%; padding:9px 11px; border:1.5px solid var(--pts-grey-light);
                               border-radius:8px; font-size:13px; font-family:inherit; outline:none; text-transform:uppercase;">
                </div>

                <div>
                    <label style="display:block; font-size:10.5px; font-weight:700; color:#6B7280; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:4px;">
                        Min %
                    </label>
                    <input type="number" name="min_score" value="{{ old('min_score') }}" required min="0" max="100"
                        style="width:100%; padding:9px 11px; border:1.5px solid var(--pts-grey-light);
                               border-radius:8px; font-size:13px; font-family:inherit; outline:none;">
                </div>

                <div>
                    <label style="display:block; font-size:10.5px; font-weight:700; color:#6B7280; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:4px;">
                        Max %
                    </label>
                    <input type="number" name="max_score" value="{{ old('max_score') }}" required min="0" max="100"
                        style="width:100%; padding:9px 11px; border:1.5px solid var(--pts-grey-light);
                               border-radius:8px; font-size:13px; font-family:inherit; outline:none;">
                </div>

                <div>
                    <label style="display:block; font-size:10.5px; font-weight:700; color:#6B7280; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:4px;">
                        Interpretation
                    </label>
                    <input type="text" name="interpretation" value="{{ old('interpretation') }}" required maxlength="50"
                        placeholder="Excellent"
                        style="width:100%; padding:9px 11px; border:1.5px solid var(--pts-grey-light);
                               border-radius:8px; font-size:13px; font-family:inherit; outline:none;">
                </div>

                <button type="submit"
                    style="padding: 9px 18px; background: var(--pts-black); color: #fff; border: none;
                           border-radius: 8px; font-size: 12.5px; font-weight: 700; cursor: pointer; white-space: nowrap;">
                    Add Band
                </button>
            </div>
        </form>
    </div>

    {{-- Existing bands --}}
    @if ($bands->isEmpty())
        <div class="settings-placeholder">
            <i class="fa-solid fa-graduation-cap"></i>
            <div style="font-size:15px; font-weight:700; color:#111827; margin-bottom:6px;">
                No grade bands configured
            </div>
            <div>
                Add your first band above. Common ranges start at A (80–100) down to F (0–39).
            </div>
        </div>
    @else
        <div style="border: 1px solid var(--pts-grey-light); border-radius: 12px; overflow: hidden;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="text-align:left; font-size:10.5px; text-transform:uppercase; letter-spacing:0.06em; color:var(--pts-grey); font-weight:700; padding:12px 16px; background:var(--pts-bg); border-bottom:1px solid var(--pts-grey-light); width:70px;">Letter</th>
                        <th style="text-align:left; font-size:10.5px; text-transform:uppercase; letter-spacing:0.06em; color:var(--pts-grey); font-weight:700; padding:12px 16px; background:var(--pts-bg); border-bottom:1px solid var(--pts-grey-light); width:120px;">Range</th>
                        <th style="text-align:left; font-size:10.5px; text-transform:uppercase; letter-spacing:0.06em; color:var(--pts-grey); font-weight:700; padding:12px 16px; background:var(--pts-bg); border-bottom:1px solid var(--pts-grey-light);">Interpretation</th>
                        <th style="text-align:right; font-size:10.5px; text-transform:uppercase; letter-spacing:0.06em; color:var(--pts-grey); font-weight:700; padding:12px 16px; background:var(--pts-bg); border-bottom:1px solid var(--pts-grey-light); width:100px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($bands as $band)
                        <tr>
                            <td style="padding: 12px 16px; border-bottom: 1px solid var(--pts-grey-light);">
                                <span style="display:inline-flex; align-items:center; justify-content:center;
                                             min-width: 34px; height: 34px; padding: 0 10px; border-radius: 8px;
                                             background: var(--pts-green-light); color: var(--pts-green-dark);
                                             font-weight: 800; font-size: 14px;">
                                    {{ $band->letter }}
                                </span>
                            </td>
                            <td style="padding: 12px 16px; border-bottom: 1px solid var(--pts-grey-light);
                                       font-size: 13px; color: #374151; font-weight: 600;">
                                {{ $band->min_score }} – {{ $band->max_score }}%
                            </td>
                            <td style="padding: 12px 16px; border-bottom: 1px solid var(--pts-grey-light);
                                       font-size: 13px; color: #374151;">
                                {{ $band->interpretation }}
                            </td>
                            <td style="padding: 12px 16px; border-bottom: 1px solid var(--pts-grey-light); text-align: right;">
                                <div style="display: inline-flex; gap: 6px;">
                                    <button type="button"
                                        onclick="openEditModal({{ $band->id }}, '{{ $band->letter }}', {{ $band->min_score }}, {{ $band->max_score }}, @js($band->interpretation))"
                                        style="width: 30px; height: 30px; border-radius: 7px; border: 1px solid var(--pts-grey-light);
                                               background: #fff; color: var(--pts-grey); cursor: pointer; font-size: 12px;">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>

                                    <form method="POST" action="{{ route('settings.grading.destroy', $band->id) }}"
                                          onsubmit="return confirm('Delete the {{ $band->letter }} band?');"
                                          style="display: inline;">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            style="width: 30px; height: 30px; border-radius: 7px; border: 1px solid #FECACA;
                                                   background: #fff; color: #DC2626; cursor: pointer; font-size: 12px;">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top: 18px; padding: 12px 16px; background: var(--pts-green-light);
                    border-left: 3px solid var(--pts-green); border-radius: 8px;
                    font-size: 12.5px; color: #1F2937; line-height: 1.5;">
            <strong style="color: var(--pts-green-dark);">{{ $bands->count() }} band{{ $bands->count() === 1 ? '' : 's' }} configured.</strong>
            These ranges are used across all subjects to convert percentages into letter grades on report cards.
        </div>
    @endif

    {{-- Edit modal --}}
    <div id="editBandModal" style="display:none; position:fixed; inset:0; background:rgba(15,15,15,0.55);
                z-index:100; align-items:center; justify-content:center; padding:20px;">
        <div style="background:#fff; border-radius:14px; padding:26px; max-width:520px; width:100%;
                    box-shadow:0 20px 60px rgba(0,0,0,0.25);">
            <div style="font-size:16px; font-weight:800; color:#111827; margin-bottom:6px;">
                Edit grade band
            </div>
            <div style="font-size:12.5px; color:var(--pts-grey); margin-bottom:18px;">
                Adjust the range, letter, or interpretation.
            </div>

            <form method="POST" id="editBandForm">
                @csrf @method('PUT')

                <div style="display:grid; grid-template-columns: 100px 1fr; gap: 14px; margin-bottom: 14px;">
                    <div>
                        <label style="display:block; font-size:11px; font-weight:700; color:#374151; margin-bottom:5px; text-transform:uppercase; letter-spacing:0.04em;">
                            Letter
                        </label>
                        <input type="text" name="letter" id="edit_letter" required maxlength="5"
                            style="width:100%; padding:10px 12px; border:1.5px solid var(--pts-grey-light);
                                   border-radius:8px; font-size:13px; font-family:inherit; outline:none; text-transform:uppercase;">
                    </div>
                    <div>
                        <label style="display:block; font-size:11px; font-weight:700; color:#374151; margin-bottom:5px; text-transform:uppercase; letter-spacing:0.04em;">
                            Interpretation
                        </label>
                        <input type="text" name="interpretation" id="edit_interpretation" required maxlength="50"
                            style="width:100%; padding:10px 12px; border:1.5px solid var(--pts-grey-light);
                                   border-radius:8px; font-size:13px; font-family:inherit; outline:none;">
                    </div>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px;">
                    <div>
                        <label style="display:block; font-size:11px; font-weight:700; color:#374151; margin-bottom:5px; text-transform:uppercase; letter-spacing:0.04em;">
                            Min %
                        </label>
                        <input type="number" name="min_score" id="edit_min_score" required min="0" max="100"
                            style="width:100%; padding:10px 12px; border:1.5px solid var(--pts-grey-light);
                                   border-radius:8px; font-size:13px; font-family:inherit; outline:none;">
                    </div>
                    <div>
                        <label style="display:block; font-size:11px; font-weight:700; color:#374151; margin-bottom:5px; text-transform:uppercase; letter-spacing:0.04em;">
                            Max %
                        </label>
                        <input type="number" name="max_score" id="edit_max_score" required min="0" max="100"
                            style="width:100%; padding:10px 12px; border:1.5px solid var(--pts-grey-light);
                                   border-radius:8px; font-size:13px; font-family:inherit; outline:none;">
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                    <button type="button" onclick="closeEditModal()"
                        style="padding: 10px 18px; border: 1.5px solid var(--pts-grey-light); background: #fff;
                               border-radius: 9px; font-size: 13px; font-weight: 700; color: #374151; cursor: pointer;">
                        Cancel
                    </button>
                    <button type="submit"
                        style="padding: 10px 20px; background: var(--pts-black); color: #fff; border: none;
                               border-radius: 9px; font-size: 13px; font-weight: 700; cursor: pointer;">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(id, letter, min, max, interpretation) {
            document.getElementById('editBandForm').action = '/settings/grading/' + id;
            document.getElementById('edit_letter').value = letter;
            document.getElementById('edit_min_score').value = min;
            document.getElementById('edit_max_score').value = max;
            document.getElementById('edit_interpretation').value = interpretation;
            document.getElementById('editBandModal').style.display = 'flex';
        }

        function closeEditModal() {
            document.getElementById('editBandModal').style.display = 'none';
        }

        document.getElementById('editBandModal')?.addEventListener('click', function(e) {
            if (e.target === this) closeEditModal();
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeEditModal();
        });
    </script>

@endsection