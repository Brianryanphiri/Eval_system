@props([
    'title',
    'description' => null,
])

<div {{ $attributes->merge(['class' => 'settings-section']) }} style="margin-bottom:26px;">
    <div style="font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;
                color:var(--pts-green-dark); margin-bottom:12px; padding-bottom:8px;
                border-bottom:1px solid var(--pts-grey-light);">
        {{ $title }}
    </div>
    @if ($description)
        <div style="font-size:12px; color:var(--pts-grey); line-height:1.5; margin-bottom:12px;">
            {{ $description }}
        </div>
    @endif
    <div style="display:flex; flex-direction:column; gap:4px;">
        {{ $slot }}
    </div>
</div>