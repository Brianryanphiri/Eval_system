@extends('settings.partials._layout')

@section('settings-content')

    <a href="{{ route('settings.overview') }}" class="settings-back">
        <i class="fa-solid fa-arrow-left"></i> Back to Settings
    </a>

    <div class="settings-panel__title">Weighting Templates</div>
    <div class="settings-panel__sub">
        Save reusable weight presets and apply them to the school's live assessment weights in one click.
    </div>

    @if ($errors->any())
        <div style="background:#FEF2F2; color:#991B1B; border:1px solid #FECACA;
                            padding:12px 16px; border-radius:9px; font-size:13px; margin-bottom:18px;">
            {{ $errors->first() }}
        </div>
    @endif

    @if ($types->isEmpty())
        <div style="padding: 30px 20px; text-align:center; background: var(--pts-bg); border-radius: 12px;">
            <div style="font-size: 15px; font-weight:700; color:#111827; margin-bottom:6px;">
                No assessment types yet
            </div>
            <div style="font-size: 13px; color: var(--pts-grey); margin-bottom: 14px;">
                Templates are built from your assessment types. Create at least one first.
            </div>
            <a href="{{ route('settings.policy') }}" style="display:inline-block; padding: 10px 20px; background: var(--pts-black); color: #fff;
                              border-radius: 9px; font-size: 13px; font-weight: 700; text-decoration: none;">
                Go to Assessment Policy
            </a>
        </div>
    @else

        {{-- Existing templates --}}
        @if ($templates->isEmpty())
            <div
                style="padding: 40px 20px; text-align:center; background: var(--pts-bg); border-radius: 12px; margin-bottom: 22px;">
                <i class="fa-solid fa-scale-balanced"
                    style="font-size: 30px; color: var(--pts-grey-light); display:block; margin-bottom: 12px;"></i>
                <div style="font-size: 15px; font-weight:700; color:#111827; margin-bottom:6px;">
                    No templates yet
                </div>
                <div style="font-size: 13px; color: var(--pts-grey);">
                    Create your first preset below, for example "Standard Secondary: CA 30 / Mid-Term 20 / End-Term 50".
                </div>
            </div>
        @else
            <div style="display:flex; flex-direction:column; gap: 14px; margin-bottom: 22px;">
                @foreach ($templates as $tpl)
                    @php
                        $borderColor = $tpl['is_default'] ? 'var(--pts-green)' : 'var(--pts-grey-light)';
                    @endphp
                    <div style="border: 1.5px solid {{ $borderColor }}; border-radius: 12px; padding: 18px 20px;
                                                background: {{ $tpl['is_default'] ? 'var(--pts-green-light)' : '#fff' }};">

                        {{-- Header --}}
                        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom: 12px;">
                            <div style="flex: 1;">
                                <div style="display:flex; align-items:center; gap:10px; margin-bottom:4px;">
                                    <span style="font-size: 15px; font-weight: 800; color: #111827;">{{ $tpl['name'] }}</span>
                                    @if ($tpl['is_default'])
                                        <span
                                            style="font-size: 10.5px; font-weight: 700; padding: 2px 9px; border-radius: 20px;
                                                                         background: var(--pts-green); color: #fff; text-transform: uppercase; letter-spacing: 0.05em;">
                                            Default
                                        </span>
                                    @endif
                                    @if (!$tpl['is_complete'])
                                        <span style="font-size: 10.5px; font-weight: 700; padding: 2px 9px; border-radius: 20px;
                                                                         background: #FEF2F2; color: #991B1B;">
                                            {{ $tpl['total'] }}% — incomplete
                                        </span>
                                    @endif
                                </div>
                                @if ($tpl['description'])
                                    <div style="font-size: 12.5px; color: var(--pts-grey);">{{ $tpl['description'] }}</div>
                                @endif
                            </div>
                        </div>

                        {{-- Items --}}
                        <div style="display:flex; flex-wrap:wrap; gap: 8px; margin-bottom: 14px;">
                            @foreach ($tpl['items'] as $item)
                                <div style="display:flex; align-items:center; gap: 8px; padding: 6px 12px;
                                                                border-radius: 8px; background: rgba(15,15,15,0.04); font-size: 12.5px;">
                                    <span style="font-weight: 600; color: #111827;">{{ $item['type'] }}</span>
                                    <span style="font-weight: 800; color: var(--pts-green-dark);">{{ $item['weight'] }}%</span>
                                </div>
                            @endforeach
                        </div>

                        {{-- Actions --}}
                        <div
                            style="display:flex; flex-wrap: wrap; gap: 8px; padding-top: 12px; border-top: 1px solid var(--pts-grey-light);">
                            @if (!$tpl['is_default'] && $tpl['is_complete'])
                                <form method="POST" action="{{ route('settings.weighting-templates.setDefault', $tpl['id']) }}"
                                    style="display:inline;">
                                    @csrf
                                    <button type="submit"
                                        style="padding: 8px 14px; background: #fff; border: 1.5px solid var(--pts-grey-light);
                                                                       border-radius: 8px; font-size: 12px; font-weight: 700; color: #374151; cursor: pointer;">
                                        <i class="fa-solid fa-star"></i> Set as Default
                                    </button>
                                </form>
                            @endif

                            @if ($tpl['is_complete'])
                                <form method="POST" action="{{ route('settings.weighting-templates.apply', $tpl['id']) }}"
                                    style="display:inline;"
                                    onsubmit="return confirm('Apply this template to the school\'s live assessment weights?');">
                                    @csrf
                                    <button type="submit"
                                        style="padding: 8px 14px; background: var(--pts-black); color: #fff; border: none;
                                                                       border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer;">
                                        <i class="fa-solid fa-check"></i> Apply Now
                                    </button>
                                </form>
                            @endif

                            <form method="POST" action="{{ route('settings.weighting-templates.destroy', $tpl['id']) }}"
                                style="display:inline; margin-left:auto;" onsubmit="return confirm('Delete \" {{ $tpl['name'] }}\"?');">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    style="padding: 8px 14px; background: #fff; border: 1.5px solid #FECACA;
                                                               border-radius: 8px; font-size: 12px; font-weight: 700; color: #DC2626; cursor: pointer;">
                                    <i class="fa-solid fa-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Create new template --}}
        <div style="padding: 22px; background: var(--pts-bg); border-radius: 12px;">
            <div style="font-size: 13px; font-weight: 800; color: #111827; margin-bottom: 14px;">
                <i class="fa-solid fa-plus" style="color: var(--pts-green-dark);"></i> Create a new template
            </div>

            <form method="POST" action="{{ route('settings.weighting-templates.store') }}">
                @csrf

                <div style="display:grid; grid-template-columns: 1fr 2fr; gap: 14px; margin-bottom: 16px;">
                    <div>
                        <label
                            style="display:block; font-size:11px; font-weight:700; color:#374151; margin-bottom:5px; text-transform:uppercase; letter-spacing:0.04em;">
                            Template Name
                        </label>
                        <input type="text" name="name" required placeholder="e.g. Standard Secondary" style="width:100%; padding:10px 14px; border:1.5px solid var(--pts-grey-light);
                                              border-radius:9px; font-size:13.5px; font-family:inherit; outline:none;">
                    </div>
                    <div>
                        <label
                            style="display:block; font-size:11px; font-weight:700; color:#374151; margin-bottom:5px; text-transform:uppercase; letter-spacing:0.04em;">
                            Description (optional)
                        </label>
                        <input type="text" name="description" placeholder="e.g. Used for Form 1–4" style="width:100%; padding:10px 14px; border:1.5px solid var(--pts-grey-light);
                                              border-radius:9px; font-size:13.5px; font-family:inherit; outline:none;">
                    </div>
                </div>

                <div style="margin-bottom: 16px;">
                    <label
                        style="display:block; font-size:11px; font-weight:700; color:#374151; margin-bottom:8px; text-transform:uppercase; letter-spacing:0.04em;">
                        Weights per assessment type
                    </label>

                    <div style="display:flex; flex-direction:column; gap:8px;">
                        @foreach ($types as $i => $type)
                            <div style="display:flex; align-items:center; gap:12px; padding: 10px 14px;
                                                    background: #fff; border-radius: 9px;">
                                <input type="hidden" name="weights[{{ $i }}][type_id]" value="{{ $type['id'] }}">
                                <span style="flex:1; font-size: 13.5px; font-weight: 600; color: #111827;">
                                    {{ $type['name'] }}
                                </span>
                                <input type="number" name="weights[{{ $i }}][weight]" value="0" min="0" max="100" step="0.01"
                                    required class="new-weight-input" style="width: 110px; padding: 8px 12px; border: 1.5px solid var(--pts-grey-light);
                                                          border-radius: 8px; font-size: 13.5px; font-weight: 700; text-align: center;
                                                          font-family: inherit; outline: none;">
                                <span style="font-size: 13px; color: var(--pts-grey); width: 20px;">%</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div style="display:flex; justify-content:space-between; align-items:center;
                                    padding: 12px 14px; background: #fff; border-radius: 9px; margin-bottom: 14px;">
                    <div style="font-size: 13px; color: var(--pts-grey);">
                        Total weight:
                        <strong id="new-total" style="color: #111827; font-size: 14px;">0%</strong>
                    </div>
                    <label style="display:flex; align-items:center; gap:8px; font-size: 13px; cursor: pointer;">
                        <input type="hidden" name="is_default" value="0">
                        <input type="checkbox" name="is_default" value="1"
                            style="width:16px; height:16px; accent-color: var(--pts-green);">
                        Set as default
                    </label>
                </div>

                <button type="submit" id="create-btn" style="padding: 11px 24px; background: var(--pts-black); color: #fff; border: none;
                                       border-radius: 9px; font-size: 13px; font-weight: 700; cursor: pointer;">
                    <i class="fa-solid fa-check"></i> Create Template
                </button>
            </form>
        </div>

        <script>
            (function () {
                const inputs = document.querySelectorAll('.new-weight-input');
                const totalEl = document.getElementById('new-total');
                const createBtn = document.getElementById('create-btn');

                function updateTotal() {
                    let total = 0;
                    inputs.forEach(i => total += parseFloat(i.value || 0));
                    total = Math.round(total * 100) / 100;

                    totalEl.textContent = total + '%';

                    if (Math.abs(total - 100) < 0.01) {
                        totalEl.style.color = 'var(--pts-green-dark)';
                        createBtn.disabled = false;
                        createBtn.style.opacity = '1';
                        createBtn.style.cursor = 'pointer';
                    } else {
                        totalEl.style.color = '#DC2626';
                        createBtn.disabled = true;
                        createBtn.style.opacity = '0.5';
                        createBtn.style.cursor = 'not-allowed';
                    }
                }

                inputs.forEach(i => i.addEventListener('input', updateTotal));
                updateTotal();
            })();
        </script>

    @endif

@endsection