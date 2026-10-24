@props([
    'name',
    'title' => null,
    'description' => null,
    'value' => '',
    'placeholder' => '',
    'min' => null,
    'max' => null,
    'step' => '1',
    'required' => false,
    'disabled' => false,
    'suffix' => null,
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
    <div style="display:flex; align-items:center; gap:8px;">
        <input type="number" id="field-{{ $name }}" name="{{ $name }}" value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}" @if (!is_null($min)) min="{{ $min }}" @endif @if (!is_null($max))
            max="{{ $max }}" @endif step="{{ $step }}" @required($required) @disabled($disabled)
            style="width:140px; padding:10px 12px; border:1.5px solid var(--pts-grey-light); border-radius:8px; font-size:13px; font-family:inherit; background:#fff; color:#111827; outline:none;"
            onfocus="this.style.borderColor='var(--pts-green)'" onblur="this.style.borderColor='var(--pts-grey-light)'">
        @if ($suffix)
            <span style="font-size:13px; color:var(--pts-grey);">{{ $suffix }}</span>
        @endif
    </div>
    @error($name)
        <div style="font-size:12px; color:var(--pts-red); margin-top:6px;">{{ $message }}</div>
    @enderror
</div>