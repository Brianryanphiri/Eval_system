@extends('settings.partials._layout')

@section('settings-content')

    <a href="{{ route('settings.overview') }}" class="settings-back">
        <i class="fa-solid fa-arrow-left"></i> Back to Settings
    </a>

    <div class="settings-panel__title">Assessment Policy</div>
    <div class="settings-panel__sub">
        Define the assessment types used across all subjects, how much each contributes to the final grade, and how finals are calculated.
    </div>

    @if ($errors->any())
        <div style="background:#FEF2F2; color:#991B1B; border:1px solid #FECACA;
                    padding:12px 16px; border-radius:9px; font-size:13px; margin-bottom:18px;">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- ============ ASSESSMENT TYPES ============ --}}
    <form method="POST" action="{{ route('settings.policy.update') }}" id="policyForm">
        @csrf

        <div style="margin-bottom: 26px;">
            <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em;
                        color: var(--pts-green-dark); margin-bottom: 12px; padding-bottom: 8px;
                        border-bottom: 1px solid var(--pts-grey-light);">
                Assessment Types &amp; Weights
            </div>

            <div style="border: 1px solid var(--pts-grey-light); border-radius: 12px; overflow: hidden; margin-bottom: 12px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="text-align:left; font-size:10.5px; text-transform:uppercase; letter-spacing:0.06em; color:var(--pts-grey); font-weight:700; padding:11px 14px; background:var(--pts-bg); border-bottom:1px solid var(--pts-grey-light); width:50px;">On</th>
                            <th style="text-align:left; font-size:10.5px; text-transform:uppercase; letter-spacing:0.06em; color:var(--pts-grey); font-weight:700; padding:11px 14px; background:var(--pts-bg); border-bottom:1px solid var(--pts-grey-light);">Name</th>
                            <th style="text-align:left; font-size:10.5px; text-transform:uppercase; letter-spacing:0.06em; color:var(--pts-grey); font-weight:700; padding:11px 14px; background:var(--pts-bg); border-bottom:1px solid var(--pts-grey-light);">Key</th>
                            <th style="text-align:center; font-size:10.5px; text-transform:uppercase; letter-spacing:0.06em; color:var(--pts-grey); font-weight:700; padding:11px 14px; background:var(--pts-bg); border-bottom:1px solid var(--pts-grey-light); width:130px;">Weight (%)</th>
                            <th style="text-align:center; font-size:10.5px; text-transform:uppercase; letter-spacing:0.06em; color:var(--pts-grey); font-weight:700; padding:11px 14px; background:var(--pts-bg); border-bottom:1px solid var(--pts-grey-light); width:90px;">In Use</th>
                            <th style="width:60px; background:var(--pts-bg); border-bottom:1px solid var(--pts-grey-light);"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($types as $i => $type)
                            <tr>
                                <td style="padding: 11px 14px; border-bottom: 1px solid var(--pts-grey-light);">
                                    <input type="hidden" name="types[{{ $i }}][id]" value="{{ $type['id'] }}">
                                    <input type="hidden" name="types[{{ $i }}][is_active]" value="0">
                                    <input type="checkbox" name="types[{{ $i }}][is_active]" value="1"
                                           @checked($type['is_active'])
                                           style="width:18px; height:18px; accent-color: var(--pts-green); cursor:pointer;">
                                </td>
                                <td style="padding: 11px 14px; border-bottom: 1px solid var(--pts-grey-light);">
                                    <input type="text" name="types[{{ $i }}][name]" value="{{ $type['name'] }}" required
                                           style="width:100%; padding:8px 10px; border:1.5px solid transparent;
                                                  border-radius:7px; font-size:13.5px; font-weight:600; font-family:inherit;
                                                  outline:none; background:transparent;"
                                           onfocus="this.style.borderColor='var(--pts-green)'; this.style.background='#fff';"
                                           onblur="this.style.borderColor='transparent'; this.style.background='transparent';">
                                </td>
                                <td style="padding: 11px 14px; border-bottom: 1px solid var(--pts-grey-light);">
                                    <input type="text" name="types[{{ $i }}][key]" value="{{ $type['key'] }}" required
                                           style="width:100%; padding:8px 10px; border:1.5px solid transparent;
                                                  border-radius:7px; font-size:12px; font-family:'Courier New', monospace;
                                                  color:var(--pts-grey); outline:none; background:transparent;"
                                           onfocus="this.style.borderColor='var(--pts-green)'; this.style.background='#fff';"
                                           onblur="this.style.borderColor='transparent'; this.style.background='transparent';">
                                </td>
                                <td style="padding: 11px 14px; border-bottom: 1px solid var(--pts-grey-light); text-align:center;">
                                    <input type="number" name="types[{{ $i }}][weight]" value="{{ $type['weight'] }}"
                                           min="0" max="100" step="0.01" required
                                           class="weight-input"
                                           style="width:90px; padding:8px 10px; border:1.5px solid var(--pts-grey-light);
                                                  border-radius:7px; font-size:13.5px; font-weight:700; text-align:center;
                                                  font-family:inherit; outline:none;">
                                </td>
                                <td style="padding: 11px 14px; border-bottom: 1px solid var(--pts-grey-light); text-align:center;">
                                    @if ($type['usage_count'] > 0)
                                        <span style="font-size:11px; font-weight:700; color:var(--pts-green-dark); background:var(--pts-green-light);
                                                     padding:3px 8px; border-radius:20px;">
                                            {{ $type['usage_count'] }}
                                        </span>
                                    @else
                                        <span style="font-size:11px; color:var(--pts-grey);">—</span>
                                    @endif
                                </td>
                                <td style="padding: 11px 8px; border-bottom: 1px solid var(--pts-grey-light); text-align:center;">
                                    @if ($type['usage_count'] === 0)
                                        <button type="button"
                                                onclick="if (confirm('Delete {{ $type['name'] }}?')) document.getElementById('delete-{{ $type['id'] }}').submit();"
                                                style="background:none; border:none; color:#DC2626; cursor:pointer; font-size:13px; padding:4px 8px;">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    @else
                                        <button type="button" disabled
                                                title="Cannot delete — used by {{ $type['usage_count'] }} assessment(s)"
                                                style="background:none; border:none; color:#D1D5DB; cursor:not-allowed; font-size:13px; padding:4px 8px;">
                                            <i class="fa-solid fa-lock"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Live total --}}
            <div id="weightTotal" style="display:flex; align-items:center; justify-content:space-between;
                                          padding: 12px 16px; border-radius: 10px; font-size: 13px;">
            </div>
        </div>

        {{-- ============ POLICY TOGGLES ============ --}}
        <div style="margin-bottom: 26px;">
            <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em;
                        color: var(--pts-green-dark); margin-bottom: 12px; padding-bottom: 8px;
                        border-bottom: 1px solid var(--pts-grey-light);">
                Calculation Rules
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 18px;">
                <div>
                    <label style="display:block; font-size:11.5px; font-weight:700; color:#374151; margin-bottom:5px;">
                        Rounding of final grades
                    </label>
                    <select name="policy[rounding]"
                            style="width:100%; padding:10px 12px; border:1.5px solid var(--pts-grey-light);
                                   border-radius:9px; font-size:13.5px; font-family:inherit; outline:none; background:#fff;">
                        <option value="nearest_int" @selected(($settings['policy.rounding'] ?? 'nearest_int') === 'nearest_int')>Nearest whole number (e.g. 74)</option>
                        <option value="one_decimal" @selected(($settings['policy.rounding'] ?? '') === 'one_decimal')>One decimal (e.g. 73.6)</option>
                        <option value="two_decimals" @selected(($settings['policy.rounding'] ?? '') === 'two_decimals')>Two decimals (e.g. 73.56)</option>
                    </select>
                </div>

                <div>
                    <label style="display:block; font-size:11.5px; font-weight:700; color:#374151; margin-bottom:5px;">
                        Maximum CA contribution (%)
                    </label>
                    <input type="number" name="policy[default_ca_cap]"
                           value="{{ $settings['policy.default_ca_cap'] ?? 40 }}"
                           min="0" max="100"
                           style="width:100%; padding:10px 12px; border:1.5px solid var(--pts-grey-light);
                                  border-radius:9px; font-size:13.5px; font-family:inherit; outline:none;">
                    <div style="font-size:11px; color:var(--pts-grey); margin-top:5px;">
                        The maximum percentage that continuous assessment can contribute.
                    </div>
                </div>
            </div>

            @php
                $toggles = [
                    'policy.auto_calculate_final' => [
                        'Auto-calculate final grades',
                        'Compute final grades automatically when all marks are entered.',
                    ],
                    'policy.allow_teacher_override' => [
                        'Allow teacher override of final grades',
                        'Teachers can manually adjust the auto-computed final grade.',
                    ],
                    'policy.require_weights_complete' => [
                        'Require weights to sum to 100%',
                        'Active types must total exactly 100% before assessments can be created.',
                    ],
                ];
            @endphp

            <div style="display: flex; flex-direction: column; gap: 4px;">
                @foreach ($toggles as $key => [$title, $desc])
                    @php $value = $settings[$key] ?? false; @endphp
                    <label style="display:flex; align-items:flex-start; gap:14px; padding: 12px 14px;
                                  border-radius: 10px; cursor: pointer;"
                           onmouseover="this.style.background='var(--pts-bg)'"
                           onmouseout="this.style.background='transparent'">
                        <input type="hidden" name="{{ $key }}" value="0">
                        <input type="checkbox" name="{{ $key }}" value="1" @checked($value)
                               style="margin-top: 3px; width: 18px; height: 18px;
                                      accent-color: var(--pts-green); cursor: pointer;">
                        <div style="flex: 1;">
                            <div style="font-size: 13.5px; font-weight: 700; color: #111827; margin-bottom: 3px;">
                                {{ $title }}
                            </div>
                            <div style="font-size: 12px; color: var(--pts-grey); line-height: 1.5;">
                                {{ $desc }}
                            </div>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>

        <div style="display:flex; justify-content: flex-end; gap: 10px; margin-top: 28px; padding-top: 20px;
                    border-top: 1px solid var(--pts-grey-light);">
            <a href="{{ route('settings.overview') }}"
               style="padding: 11px 20px; border: 1.5px solid var(--pts-grey-light); border-radius: 9px;
                      font-size: 13px; font-weight: 700; color: #374151; text-decoration: none; background: #fff;">
                Cancel
            </a>
            <button type="submit" id="saveBtn"
                    style="padding: 11px 24px; background: var(--pts-black); color: #fff; border: none;
                           border-radius: 9px; font-size: 13px; font-weight: 700; cursor: pointer;">
                <i class="fa-solid fa-check"></i> Save Policy
            </button>
        </div>
    </form>

    {{-- ============ ADD NEW TYPE ============ --}}
    <div style="margin-top: 22px; padding: 18px; background: var(--pts-bg); border-radius: 12px;">
        <div style="font-size: 12px; font-weight: 800; color: #111827; margin-bottom: 10px;">
            <i class="fa-solid fa-plus" style="color: var(--pts-green-dark);"></i> Add a new assessment type
        </div>

        <form method="POST" action="{{ route('settings.policy.types.store') }}" style="display:flex; gap:10px;">
            @csrf
            <input type="text" name="name" placeholder="e.g. Practical Project" required
                   style="flex:1; padding:10px 14px; border:1.5px solid var(--pts-grey-light);
                          border-radius:9px; font-size:13.5px; font-family:inherit; outline:none;">
            <button type="submit"
                    style="padding: 10px 20px; background: var(--pts-black); color: #fff; border: none;
                           border-radius: 9px; font-size: 13px; font-weight: 700; cursor: pointer; white-space: nowrap;">
                Add Type
            </button>
        </form>
        <div style="font-size:11px; color:var(--pts-grey); margin-top:8px;">
            The key is generated automatically. Set its weight in the table above and save.
        </div>
    </div>

    {{-- Hidden delete forms --}}
    @foreach ($types as $type)
        @if ($type['usage_count'] === 0)
            <form method="POST" action="{{ route('settings.policy.types.destroy', $type['id']) }}"
                  id="delete-{{ $type['id'] }}" style="display:none;">
                @csrf @method('DELETE')
            </form>
        @endif
    @endforeach

    <script>
        (function() {
            const inputs = document.querySelectorAll('.weight-input');
            const totalEl = document.getElementById('weightTotal');
            const saveBtn = document.getElementById('saveBtn');

            function updateTotal() {
                let total = 0;
                let activeCount = 0;
                document.querySelectorAll('tbody tr').forEach((row) => {
                    const checkbox = row.querySelector('input[type=checkbox]');
                    const weight = parseFloat(row.querySelector('.weight-input')?.value || 0);
                    if (checkbox && checkbox.checked) {
                        total += weight;
                        activeCount++;
                    }
                });

                total = Math.round(total * 100) / 100;

                if (Math.abs(total - 100) < 0.01) {
                    totalEl.style.background = 'var(--pts-green-light)';
                    totalEl.style.color = 'var(--pts-green-dark)';
                    totalEl.innerHTML = '<strong>Total active weight: ' + total + '%</strong><span><i class="fa-solid fa-circle-check"></i> Perfect, sums to 100%</span>';
                    saveBtn.disabled = false;
                    saveBtn.style.opacity = '1';
                    saveBtn.style.cursor = 'pointer';
                } else {
                    totalEl.style.background = '#FEF2F2';
                    totalEl.style.color = '#991B1B';
                    totalEl.innerHTML = '<strong>Total active weight: ' + total + '%</strong><span><i class="fa-solid fa-triangle-exclamation"></i> Must equal 100% before saving</span>';
                    saveBtn.disabled = true;
                    saveBtn.style.opacity = '0.5';
                    saveBtn.style.cursor = 'not-allowed';
                }
            }

            inputs.forEach(i => i.addEventListener('input', updateTotal));
            document.querySelectorAll('input[type=checkbox]').forEach(c => c.addEventListener('change', updateTotal));

            updateTotal();
        })();
    </script>

@endsection