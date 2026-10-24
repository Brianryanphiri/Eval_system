@extends('layouts.app')

@section('title', 'Timetable')
@section('page-title', 'Timetable')

@section('styles')
    <style>
        .tt-sub {
            font-size: 13.5px;
            color: var(--pts-grey);
            margin-bottom: 20px;
        }

        .tt-quick {
            margin-bottom: 20px;
        }

        .tt-quick .class-card {
            border-color: var(--pts-green);
            background: var(--pts-green-light);
        }

        .tt-quick .class-card__arrow {
            color: var(--pts-green-dark);
        }

        .class-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 14px;
        }

        .class-card {
            background: #fff;
            border: 1px solid var(--pts-grey-light);
            border-radius: 12px;
            padding: 18px;
            text-decoration: none;
            color: inherit;
            display: block;
            transition: border-color 0.15s, transform 0.15s;
        }

        .class-card:hover {
            border-color: var(--pts-green);
            transform: translateY(-1px);
        }

        .class-card__name {
            font-size: 15px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 4px;
        }

        .class-card__meta {
            font-size: 12px;
            color: var(--pts-grey);
        }

        .class-card__arrow {
            margin-top: 14px;
            color: var(--pts-green-dark);
            font-size: 12px;
            font-weight: 700;
        }

        .empty-note {
            padding: 40px 20px;
            text-align: center;
            color: var(--pts-grey);
            font-size: 13.5px;
            background: #fff;
            border: 1px solid var(--pts-grey-light);
            border-radius: 12px;
        }

        .tt-section-title {
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--pts-grey);
            margin-bottom: 12px;
        }
    </style>
@endsection

@section('content')

    @php
        $user = auth()->user();
        $isTeacher = $user->role === 'teacher';
        $isAdminTier = in_array($user->role, ['super_admin', 'school_admin', 'academic_director', 'head_of_department']);
    @endphp

    <div class="tt-sub">View or edit weekly timetables.</div>

    @if ($isTeacher)
        <div class="tt-quick">
            <a href="{{ route('timetable.teacher') }}" class="class-card">
                <div class="class-card__name">
                    <i class="fa-solid fa-calendar-days" style="color: var(--pts-green-dark); margin-right: 6px;"></i>
                    My week
                </div>
                <div class="class-card__meta">
                    View your own timetable across all classes.
                </div>
                <div class="class-card__arrow">
                    Open my week <i class="fa-solid fa-arrow-right"></i>
                </div>
            </a>
        </div>
    @endif

    @if ($isAdminTier)
        <div class="tt-quick">
            <a href="{{ route('timetable.teacher') }}" class="class-card">
                <div class="class-card__name">
                    <i class="fa-solid fa-user-clock" style="color: var(--pts-green-dark); margin-right: 6px;"></i>
                    Teacher timetable
                </div>
                <div class="class-card__meta">
                    View any teacher's week across all classes.
                </div>
                <div class="class-card__arrow">
                    Open teacher view <i class="fa-solid fa-arrow-right"></i>
                </div>
            </a>
        </div>
    @endif

    <div class="tt-section-title">Classes</div>

    @if ($classes->isEmpty())
        <div class="empty-note">
            No classes assigned to you yet. Contact your school admin.
        </div>
    @else
        <div class="class-grid">
            @foreach ($classes as $class)
                <a href="{{ route('timetable.show', $class->id) }}" class="class-card">
                    <div class="class-card__name">{{ $class->name }}</div>
                    <div class="class-card__meta">
                        {{ $class->grade_level ?? 'Class' }}
                    </div>
                    <div class="class-card__arrow">
                        Open timetable <i class="fa-solid fa-arrow-right"></i>
                    </div>
                </a>
            @endforeach
        </div>
    @endif

@endsection