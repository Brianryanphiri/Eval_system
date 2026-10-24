@props([
    'action',
    'method' => 'POST',
    'cancel' => null,
    'submitLabel' => 'Save Changes',
])

<form method="POST" action="{{ $action }}" {{ $attributes }}>
    @csrf
    @if (strtoupper($method) !== 'POST')
        @method($method)
    @endif

    {{ $slot }}

    <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:28px; padding-top:20px;
                border-top:1px solid var(--pts-grey-light);">
        @if ($cancel)
            <a href="{{ $cancel }}" style="padding:11px 20px; border:1.5px solid var(--pts-grey-light); border-radius:9px;
                           font-size:13px; font-weight:700; color:#374151; text-decoration:none; background:#fff;">
                Cancel
            </a>
        @endif
        <button type="submit" style="padding:11px 24px; background:var(--pts-black); color:#fff; border:none;
                   border-radius:9px; font-size:13px; font-weight:700; cursor:pointer;">
            {{ $submitLabel }}
        </button>
    </div>
</form>