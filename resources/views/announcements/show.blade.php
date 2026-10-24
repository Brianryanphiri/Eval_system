@extends('layouts.app')

@section('title', $announcement->title)

@section('page-title', 'Announcement')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}">Dashboard</a>
    <span class="sep">/</span>
    <a href="{{ route('announcements.index') }}">Announcements</a>
    <span class="sep">/</span>
    <span>{{ \Illuminate\Support\Str::limit($announcement->title, 40) }}</span>
@endsection

@section('page-actions')
    <a href="{{ route('announcements.index') }}"
        style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;background:var(--pts-bg);color:#111827;border-radius:8px;font-size:12.5px;font-weight:600;text-decoration:none;">
        <i class="fa-solid fa-arrow-left" style="font-size:11px;"></i>
        All announcements
    </a>
@endsection

@section('styles')
    <style>
        .ann-show {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 320px;
            gap: 20px;
            align-items: start;
        }

        @media (max-width: 1024px) {
            .ann-show {
                grid-template-columns: 1fr;
            }

            .ann-show__aside {
                position: static !important;
            }
        }

        /* ---------- Main card ---------- */
        .ann-show__main {
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 12px;
            overflow: hidden;
            position: relative;
        }

        .ann-show__main::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 5px;
            background: var(--pts-blue);
        }

        .ann-show__main.priority-urgent::before {
            background: var(--pts-red);
        }

        .ann-show__main.priority-high::before {
            background: var(--pts-amber);
        }

        .ann-show__main.priority-normal::before {
            background: var(--pts-blue);
        }

        .ann-show__main.priority-low::before {
            background: var(--pts-grey);
        }

        .ann-show__head {
            padding: 24px 28px 20px 32px;
            border-bottom: 1px solid var(--pts-grey-light);
        }

        .ann-show__badges {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 12px;
        }

        .ann-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 10.5px;
            font-weight: 700;
            padding: 3px 9px;
            border-radius: 12px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .ann-badge.priority-urgent {
            background: var(--pts-red-light);
            color: var(--pts-red);
        }

        .ann-badge.priority-high {
            background: var(--pts-amber-light);
            color: var(--pts-amber);
        }

        .ann-badge.priority-normal {
            background: var(--pts-blue-light);
            color: var(--pts-blue);
        }

        .ann-badge.priority-low {
            background: var(--pts-bg);
            color: var(--pts-grey);
        }

        .ann-badge.audience {
            background: var(--pts-green-light);
            color: var(--pts-green-dark);
        }

        .ann-badge.expired {
            background: var(--pts-bg);
            color: var(--pts-grey);
        }

        .ann-show__title {
            font-size: 22px;
            font-weight: 800;
            color: #111827;
            line-height: 1.3;
            margin-bottom: 10px;
            word-wrap: break-word;
        }

        .ann-show__meta-row {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 16px;
            font-size: 12.5px;
            color: var(--pts-grey);
        }

        .ann-show__meta-row span {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .ann-show__meta-row i {
            font-size: 12px;
        }

        .ann-show__author-avatar {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: var(--pts-green);
            color: var(--pts-black);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: 700;
        }

        /* ---------- Body ---------- */
        .ann-show__body {
            padding: 24px 28px 28px 32px;
            font-size: 14.5px;
            line-height: 1.7;
            color: #1F2937;
            white-space: pre-wrap;
            word-wrap: break-word;
            min-height: 120px;
        }

        .ann-show__body a {
            color: var(--pts-green-dark);
        }

        /* ---------- Aside ---------- */
        .ann-show__aside {
            position: sticky;
            top: 92px;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .ann-side-card {
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 12px;
            overflow: hidden;
        }

        .ann-side-card__head {
            padding: 11px 16px;
            background: var(--pts-bg);
            border-bottom: 1px solid var(--pts-grey-light);
            font-size: 11px;
            font-weight: 700;
            color: var(--pts-grey);
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .ann-side-card__body {
            padding: 14px 16px;
        }

        .ann-detail {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid var(--pts-grey-light);
            font-size: 12.5px;
        }

        .ann-detail:last-child {
            border-bottom: none;
        }

        .ann-detail__label {
            color: var(--pts-grey);
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .ann-detail__label i {
            font-size: 11px;
            width: 14px;
            text-align: center;
        }

        .ann-detail__value {
            color: #111827;
            font-weight: 600;
            text-align: right;
            max-width: 60%;
            word-wrap: break-word;
        }

        /* ---------- Actions ---------- */
        .ann-actions {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .ann-action-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 12.5px;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid transparent;
            font-family: inherit;
            text-decoration: none;
            transition: all 0.15s;
            width: 100%;
        }

        .ann-action-btn.danger {
            background: var(--pts-red-light);
            color: var(--pts-red);
        }

        .ann-action-btn.danger:hover {
            background: var(--pts-red);
            color: #fff;
        }

        .ann-action-btn.ghost {
            background: var(--pts-bg);
            color: #111827;
        }

        .ann-action-btn.ghost:hover {
            background: var(--pts-grey-light);
        }

        .ann-action-btn i {
            font-size: 12px;
        }
    </style>
@endsection

@section('content')

    @php
        $isExpired = $announcement->expires_at && $announcement->expires_at->isPast();
    @endphp

    <div class="ann-show">

        {{-- ---------- Main ---------- --}}
        <article class="ann-show__main priority-{{ $announcement->priority }}">

            <header class="ann-show__head">
                <div class="ann-show__badges">
                    <span class="ann-badge priority-{{ $announcement->priority }}">
                        <i class="fa-solid {{ $announcement->priority === 'urgent' ? 'fa-triangle-exclamation' : 'fa-circle' }}"
                            style="font-size:8px;"></i>
                        {{ $announcement->priority_label }}
                    </span>
                    <span class="ann-badge audience">
                        <i class="fa-solid fa-bullseye" style="font-size:9px;"></i>
                        {{ $announcement->audience_label }}
                    </span>
                    @if ($isExpired)
                        <span class="ann-badge expired">
                            <i class="fa-solid fa-clock-rotate-left" style="font-size:9px;"></i>
                            Expired
                        </span>
                    @endif
                </div>

                <h1 class="ann-show__title">{{ $announcement->title }}</h1>

                <div class="ann-show__meta-row">
                    <span>
                        <span class="ann-show__author-avatar">
                            {{ strtoupper(substr($announcement->author->name ?? 'A', 0, 1)) }}
                        </span>
                        {{ $announcement->author->name ?? 'Admin' }}
                    </span>
                    <span>
                        <i class="fa-regular fa-clock"></i>
                        {{ $announcement->publish_at ? $announcement->publish_at->format('M j, Y · g:i A') : $announcement->created_at->format('M j, Y · g:i A') }}
                    </span>
                    <span>
                        <i class="fa-regular fa-calendar"></i>
                        {{ $announcement->publish_at ? $announcement->publish_at->diffForHumans() : $announcement->created_at->diffForHumans() }}
                    </span>
                </div>
            </header>

            <div class="ann-show__body">{{ $announcement->body }}</div>

        </article>

        {{-- ---------- Aside ---------- --}}
        <aside class="ann-show__aside">

            {{-- Details --}}
            <div class="ann-side-card">
                <div class="ann-side-card__head">Details</div>
                <div class="ann-side-card__body">

                    <div class="ann-detail">
                        <span class="ann-detail__label">
                            <i class="fa-solid fa-user"></i> Author
                        </span>
                        <span class="ann-detail__value">
                            {{ $announcement->author->name ?? 'Admin' }}
                        </span>
                    </div>

                    <div class="ann-detail">
                        <span class="ann-detail__label">
                            <i class="fa-solid fa-bullseye"></i> Audience
                        </span>
                        <span class="ann-detail__value">
                            {{ $announcement->audience_label }}
                        </span>
                    </div>

                    <div class="ann-detail">
                        <span class="ann-detail__label">
                            <i class="fa-solid fa-flag"></i> Priority
                        </span>
                        <span class="ann-detail__value">
                            {{ $announcement->priority_label }}
                        </span>
                    </div>

                    <div class="ann-detail">
                        <span class="ann-detail__label">
                            <i class="fa-regular fa-calendar-check"></i> Published
                        </span>
                        <span class="ann-detail__value">
                            {{ $announcement->publish_at ? $announcement->publish_at->format('M j, Y') : $announcement->created_at->format('M j, Y') }}
                        </span>
                    </div>

                    @if ($announcement->expires_at)
                        <div class="ann-detail">
                            <span class="ann-detail__label">
                                <i class="fa-regular fa-calendar-xmark"></i> Expires
                            </span>
                            <span class="ann-detail__value">
                                {{ $announcement->expires_at->format('M j, Y') }}
                            </span>
                        </div>
                    @endif

                    <div class="ann-detail">
                        <span class="ann-detail__label">
                            <i class="fa-solid fa-circle-dot"></i> Status
                        </span>
                        <span class="ann-detail__value"
                            style="color:{{ $isExpired ? 'var(--pts-grey)' : 'var(--pts-green-dark)' }};">
                            {{ $isExpired ? 'Expired' : 'Active' }}
                        </span>
                    </div>

                </div>
            </div>

            {{-- Admin actions --}}
            @if ($canPost)
                <div class="ann-side-card">
                    <div class="ann-side-card__head">Admin actions</div>
                    <div class="ann-side-card__body">
                        <div class="ann-actions">
                            <a href="{{ route('announcements.create') }}" class="ann-action-btn ghost">
                                <i class="fa-solid fa-plus"></i>
                                New announcement
                            </a>

                            <form method="POST" action="{{ route('announcements.destroy', $announcement->id) }}"
                                data-confirm="Delete this announcement permanently? This cannot be undone.">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="ann-action-btn danger">
                                    <i class="fa-solid fa-trash"></i>
                                    Delete announcement
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Quick link back --}}
            <a href="{{ route('announcements.index') }}" class="ann-action-btn ghost" style="text-decoration:none;">
                <i class="fa-solid fa-list"></i>
                View all announcements
            </a>

        </aside>

    </div>

@endsection