@extends('layouts.app')

@section('title', 'Reports')
@section('page-title', 'Reports')

@section('content')

    <div class="panel"
        style="background:#fff;border:1px solid var(--pts-grey-light);border-radius:14px;padding:60px 24px;text-align:center;">
        <div
            style="width:64px;height:64px;border-radius:16px;background:var(--pts-green-light);color:var(--pts-green-dark);display:inline-flex;align-items:center;justify-content:center;font-size:26px;margin-bottom:18px;">
            <i class="fa-solid fa-file-lines"></i>
        </div>
        <h2 style="font-size:20px;font-weight:800;color:#111827;margin-bottom:8px;">Reports Hub</h2>
        <p style="font-size:14px;color:var(--pts-grey);max-width:520px;margin:0 auto 20px;line-height:1.6;">
            This is where you will browse report cards, generate class summaries, and print or email results.
            The feature is under construction and will be available soon.
        </p>
        <span
            style="display:inline-block;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:var(--pts-grey);padding:6px 14px;background:var(--pts-bg);border-radius:20px;">
            Coming Soon
        </span>
    </div>

@endsection