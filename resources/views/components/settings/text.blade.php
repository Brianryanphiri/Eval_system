@props([
    'name',
    'title' => null,
    'description' => null,
    'value' => '',
    'placeholder' => '',
    'type' => 'text',
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
    <input type="{{ $type }}" id="field-{{ $name }}" name="{{ $name }}" value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}" @required($required) @disabled($disabled)
        style="width:100%; padding:10px 12px; border:1.5px solid var(--pts-grey-light); border-radius:8px; font-size:13px; font-family:inherit; background:#fff; color:#111827; outline:none;"
        onfocus="this.style.borderColor='var(--pts-green)'" onblur="this.style.borderColor='var(--pts-grey-light)'">
    @error($name)
        <div style="font-size:12px; color:var(--pts-red); margin-top:6px;">{{ $message }}</div>
    @enderror
</div>