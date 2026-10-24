@extends('layouts.app')

@section('title', 'Announcements')

@section('page-title', 'Announcements')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}">Dashboard</a>
    <span class="sep">/</span>
    <span>Announcements</span>
@endsection

@if ($canPost)
    @section('page-actions')
        <a href="{{ route('announcements.create') }}"
           style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;background:var(--pts-green);color:#fff;border-radius:8px;font-size:12.5px;font-weight:600;text-decoration:none;">
            <i class="fa-solid fa-plus" style="font-size:11px;"></i>
            Post announcement
        </a>
    @endsection
@endif

@section('styles')
    <style>
        /* ---------- Summary strip ---------- */
        .ann-summary {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 20px;
        }

        @media (max-width: 640px) {
            .ann-summary { grid-template-columns: 1fr; }
        }

        .ann-summary__card {
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 12px;
            padding: 14px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .ann-summary__icon {
            width: 38px;
            height: 38px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
        }

        .ann-summary__icon.total  { background: var(--pts-blue-light);   color: var(--pts-blue); }
        .ann-summary__icon.urgent { background: var(--pts-red-light);    color: var(--pts-red); }
        .ann-summary__icon.high   { background: var(--pts-amber-light);  color: var(--pts-amber); }

        .ann-summary__value {
            font-size: 20px;
            font-weight: 700;
            color: #111827;
            line-height: 1;
        }

        .ann-summary__label {
            font-size: 11.5px;
            color: var(--pts-grey);
            margin-top: 3px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 600;
        }

        /* ---------- Filter bar ---------- */
        .ann-filters {
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 12px;
            padding: 12px 14px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 16px;
            margin-bottom: 16px;
        }

        .ann-filters__group {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        .ann-filters__label {
            font-size: 11px;
            font-weight: 700;
            color: var(--pts-grey);
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-right: 4px;
        }

        .ann-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 11px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            color: #374151;
            background: var(--pts-bg);
            border: 1px solid transparent;
            text-decoration: none;
            transition: all 0.15s;
            cursor: pointer;
        }

        .ann-pill:hover {
            background: var(--pts-green-light);
            color: var(--pts-green-dark);
        }

        .ann-pill.active {
            background: var(--pts-green);
            color: #fff;
        }

        /* ---------- Announcement list ---------- */
        .ann-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .ann-card {
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 12px;
            padding: 18px 20px;
            display: flex;
            gap: 16px;
            text-decoration: none;
            color: inherit;
            transition: border-color 0.15s, box-shadow 0.15s;
            position: relative;
            overflow: hidden;
        }

        .ann-card:hover {
            border-color: var(--pts-green);
            box-shadow: 0 4px 14px rgba(122, 193, 66, 0.08);
        }

        .ann-card::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: var(--pts-blue);
        }

        .ann-card.priority-urgent::before { background: var(--pts-red); }
        .ann-card.priority-high::before   { background: var(--pts-amber); }
        .ann-card.priority-normal::before { background: var(--pts-blue); }
        .ann-card.priority-low::before    { background: var(--pts-grey); }

        .ann-card__icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            flex-shrink: 0;
            background: var(--pts-bg);
            color: var(--pts-grey);
        }

        .ann-card.priority-urgent .ann-card__icon { background: var(--pts-red-light);   color: var(--pts-red); }
        .ann-card.priority-high   .ann-card__icon { background: var(--pts-amber-light); color: var(--pts-amber); }
        .ann-card.priority-normal .ann-card__icon { background: var(--pts-blue-light);  color: var(--pts-blue); }

        .ann-card__body {
            flex: 1;
            min-width: 0;
        }

        .ann-card__head {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 6px;
        }

        .ann-card__title {
            font-size: 15px;
            font-weight: 700;
            color: #111827;
            line-height: 1.3;
        }

        .ann-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 10.5px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 12px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .ann-badge.priority-urgent { background: var(--pts-red-light);   color: var(--pts-red); }
        .ann-badge.priority-high   { background: var(--pts-amber-light); color: var(--pts-amber); }
        .ann-badge.priority-normal { background: var(--pts-blue-light);  color: var(--pts-blue); }
        .ann-badge.priority-low    { background: var(--pts-bg);          color: var(--pts-grey); }

        .ann-badge.audience {
            background: var(--pts-green-light);
            color: var(--pts-green-dark);
        }

        .ann-card__excerpt {
            font-size: 13px;
            color: #4B5563;
            line-height: 1.55;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin-bottom: 8px;
        }

        .ann-card__meta {
            display: flex;
            align-items: center;
            gap: 14px;
            font-size: 11.5px;
            color: var(--pts-grey);
            flex-wrap: wrap;
        }

        .ann-card__meta i {
            font-size: 11px;
            margin-right: 4px;
        }

        /* ---------- Empty state ---------- */
        .ann-empty {
            background: var(--pts-white);
            border: 1px dashed var(--pts-grey-light);
            border-radius: 12px;
            padding: 56px 24px;
            text-align: center;
        }

        .ann-empty__icon {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: var(--pts-green-light);
            color: var(--pts-green-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin: 0 auto 14px;
        }

        .ann-empty__title {
            font-size: 16px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 4px;
        }

        .ann-empty__text {
            font-size: 13px;
            color: var(--pts-grey);
            margin-bottom: 18px;
        }

        /* Pagination tweaks */
        .ann-list + nav,
        .ann-list ~ nav {
            margin-top: 20px;
        }
    </style>
@endsection

@section('content')

    {{-- Summary strip --}}
    <div class="ann-summary">
        <div class="ann-summary__card">
            <div class="ann-summary__icon total">
                <i class="fa-solid fa-bullhorn"></i>
            </div>
            <div>
                <div class="ann-summary__value">{{ $counts['total'] ?? 0 }}</div>
                <div class="ann-summary__label">Total active</div>
            </div>
        </div>
        <div class="ann-summary__card">
            <div class="ann-summary__icon urgent">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <div class="ann-summary__value">{{ $counts['urgent'] ?? 0 }}</div>
                <div class="ann-summary__label">Urgent</div>
            </div>
        </div>
        <div class="ann-summary__card">
            <div class="ann-summary__icon high">
                <i class="fa-solid fa-circle-exclamation"></i>
            </div>
            <div>
                <div class="ann-summary__value">{{ $counts['high'] ?? 0 }}</div>
                <div class="ann-summary__label">High priority</div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="ann-filters">
        <div class="ann-filters__group">
            <span class="ann-filters__label">Audience</span>
            @php
                $audienceOptions = [
                    'all' => 'All',
                    'staff' => 'Staff',
                    'teachers' => 'Teachers',
                    'students' => 'Students',
                    'guardians' => 'Guardians',
                    'class' => 'Class',
                ];
            @endphp
            @foreach ($audienceOptions as $value => $label)
                <a href="{{ route('announcements.index', array_filter(['audience' => $value, 'priority' => $selectedPriority !== 'all' ? $selectedPriority : null])) }}"
                   class="ann-pill {{ $selectedAudience === $value ? 'active' : '' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <div class="ann-filters__group">
            <span class="ann-filters__label">Priority</span>
            @php
                $priorityOptions = [
                    'all' => 'All',
                    'urgent' => 'Urgent',
                    'high' => 'High',
                    'normal' => 'Normal',
                    'low' => 'Low',
                ];
            @endphp
            @foreach ($priorityOptions as $value => $label)
                <a href="{{ route('announcements.index', array_filter(['audience' => $selectedAudience !== 'all' ? $selectedAudience : null, 'priority' => $value])) }}"
                   class="ann-pill {{ $selectedPriority === $value ? 'active' : '' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        @if ($selectedAudience !== 'all' || $selectedPriority !== 'all')
            <a href="{{ route('announcements.index') }}"
               class="ann-pill"
               style="margin-left:auto;background:transparent;color:var(--pts-grey);">
                <i class="fa-solid fa-xmark"></i> Clear filters
            </a>
        @endif
    </div>

    {{-- List --}}
    @if ($announcements->count() > 0)
        <div class="ann-list">
            @foreach ($announcements as $announcement)
                <a href="{{ route('announcements.show', $announcement->id) }}"
                   class="ann-card priority-{{ $announcement->priority }}">

                    <div class="ann-card__icon">
                        <i class="fa-solid {{ $announcement->priority === 'urgent' ? 'fa-triangle-exclamation' : 'fa-bullhorn' }}"></i>
                    </div>

                    <div class="ann-card__body">
                        <div class="ann-card__head">
                            <span class="ann-card__title">{{ $announcement->title }}</span>
                            <span class="ann-badge priority-{{ $announcement->priority }}">
                                {{ $announcement->priority_label }}
                            </span>
                            <span class="ann-badge audience">
                                {{ $announcement->audience_label }}
                            </span>
                        </div>

                        <div class="ann-card__excerpt">
                            {{ \Illuminate\Support\Str::limit(strip_tags($announcement->body), 180) }}
                        </div>

                        <div class="ann-card__meta">
                            <span>
                                <i class="fa-solid fa-user"></i>
                                {{ $announcement->author->name ?? 'Admin' }}
                            </span>
                            <span>
                                <i class="fa-regular fa-clock"></i>
                                {{ $announcement->publish_at ? $announcement->publish_at->diffForHumans() : $announcement->created_at->diffForHumans() }}
                            </span>
                            @if ($announcement->expires_at)
                                <span>
                                    <i class="fa-regular fa-calendar-xmark"></i>
                                    Expires {{ $announcement->expires_at->format('M j, Y') }}
                                </span>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div style="margin-top:20px;">
            {{ $announcements->links() }}
        </div>
    @else
        <div class="ann-empty">
            <div class="ann-empty__icon">
                <i class="fa-solid fa-bullhorn"></i>
            </div>
            <div class="ann-empty__title">No announcements yet</div>
            <div class="ann-empty__text">
                @if ($selectedAudience !== 'all' || $selectedPriority !== 'all')
                    No announcements match your filters.
                @elseif ($canPost)
                    Post the first announcement to keep your school informed.
                @else
                    You'll see announcements here when admins post them.
                @endif
            </div>
            @if ($canPost)
                <a href="{{ route('announcements.create') }}"
                   style="display:inline-flex;align-items:center;gap:6px;padding:9px 16px;background:var(--pts-green);color:#fff;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;">
                    <i class="fa-solid fa-plus" style="font-size:11px;"></i>
                    Post announcement
                </a>
            @endif
        </div>
    @endif

@endsection