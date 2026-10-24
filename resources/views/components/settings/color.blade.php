@props([
    'name',
    'title' => null,
    'description' => null,
    'value' => '#000000',
    'required' => false,
    'disabled' => false,
])

<div {{ $attributes->merge(['class' => 'settings-row']) }} style="padding:12px 14px; border-radius:10px;">
    @if ($title)
        <label for="field-{{ $name }}"
            style="display:block; font-size:13.5px; font-weight:700; color:#111827; margin-bottom:3px;">
            {{ $title }}
            @if ($required)
                <span style="color:var(--pts-red);">*</span>
            @endif
        </label>
    @endif
    @if ($description)
        <div style="font-size:12px; color:var(--pts-grey); line-height:1.5; margin-bottom:8px;">
            {{ $description }}
        </div>
    @endif
    @php $current = old($name, $value); @endphp
    <div style="display:flex; align-items:center; gap:12px;">
        <input type="color" id="field-{{ $name }}" name="{{ $name }}" value="{{ $current }}" @required($required)
            @disabled($disabled)
            style="width:44px; height:44px; border:1.5px solid var(--pts-grey-light); border-radius:8px; background:#fff; cursor:pointer; padding:2px;">
        <span id="field-{{ $name }}-value" style="font-size:13px; color:var(--pts-grey); font-family:monospace;">
            {{ $current }}
        </span>
    </div>
    @error($name)
        <div style="font-size:12px; color:var(--pts-red); margin-top:6px;">{{ $message }}</div>
    @enderror
</div>

@once
    <script>
        document.addEventListener('input', function (e) {
            if (e.target.type === 'color' && e.target.id) {
                var target = document.getElementById(e.target.id + '-value');
                if (target) target.textContent = e.target.value.toUpperCase();
            }
        });
    </script>
@endonce