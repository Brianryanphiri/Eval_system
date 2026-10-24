@props([
    'name',
    'title' => null,
    'description' => null,
    'value' => false,
    'disabled' => false,
])

<div {{ $attributes->merge(['class' => 'settings-row']) }}
    style="display:flex; align-items:flex-start; gap:14px; padding:12px 14px; border-radius:10px; transition:background 0.12s;"
    onmouseover="this.style.background='var(--pts-bg)'" onmouseout="this.style.background='transparent'">
    <input type="hidden" name="{{ $name }}" value="0">
    <label
        style="display:flex; align-items:flex-start; gap:14px; cursor:{{ $disabled ? 'not-allowed' : 'pointer' }}; width:100%;">
        <input type="checkbox" name="{{ $name }}" value="1" @checked((bool) $value) @disabled($disabled)
            style="margin-top:3px; width:18px; height:18px; accent-color:var(--pts-green); cursor:{{ $disabled ? 'not-allowed' : 'pointer' }};">
        <div style="flex:1;">
            @if ($title)
                <div style="font-size:13.5px; font-weight:700; color:#111827; margin-bottom:3px;">
                    {{ $title }}
                </div>
            @endif
            @if ($description)
                <div style="font-size:12px; color:var(--pts-grey); line-height:1.5;">
                    {{ $description }}
                </div>
            @endif
            {{ $slot }}
        </div>
    </label>
</div>