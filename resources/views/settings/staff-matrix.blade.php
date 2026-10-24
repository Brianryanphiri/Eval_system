@extends('settings.partials._layout')

@section('settings-content')

    <a href="{{ route('settings.overview') }}" class="settings-back">
        <i class="fa-solid fa-arrow-left"></i> Back to Settings
    </a>

    <div class="settings-panel__title">Staff Roles Matrix</div>
    <div class="settings-panel__sub">
        A read-only overview of who teaches what. Manage teacher assignments from the
        <a href="{{ route('academic.index') }}" style="color: var(--pts-green-dark); font-weight: 600;">Classes &amp;
            Subjects</a> page.
    </div>

    @if ($classrooms->isEmpty() || $subjects->isEmpty())
        <div style="padding: 50px 20px; text-align: center; background: var(--pts-bg); border-radius: 12px;">
            <i class="fa-solid fa-table-cells" style="font-size: 28px; color: var(--pts-grey-light);
                                                            display: block; margin-bottom: 12px;"></i>
            <div style="font-size: 14px; font-weight: 700; color: #111827; margin-bottom: 4px;">
                Nothing to display yet
            </div>
            <div style="font-size: 12.5px; color: var(--pts-grey); margin-bottom: 16px;">
                @if ($classrooms->isEmpty() && $subjects->isEmpty())
                    Create at least one class and one subject to see the matrix.
                @elseif ($classrooms->isEmpty())
                    Create at least one class to see the matrix.
                @else
                    Create at least one subject to see the matrix.
                @endif
            </div>
            <a href="{{ route('academic.index') }}" style="display: inline-block; padding: 10px 20px; background: var(--pts-black); color: #fff;
                              border-radius: 9px; font-size: 13px; font-weight: 700; text-decoration: none;">
                Go to Classes &amp; Subjects
            </a>
        </div>
    @else
        {{-- Summary stats --}}
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 22px;">
            <div style="padding: 14px 16px; background: var(--pts-bg); border-radius: 11px;">
                <div style="font-size: 22px; font-weight: 800; color: #111827; line-height: 1;">
                    {{ $staff->count() }}
                </div>
                <div style="font-size: 11.5px; color: var(--pts-grey); margin-top: 4px; text-transform: uppercase;
                                    letter-spacing: 0.06em; font-weight: 700;">
                    Staff
                </div>
            </div>
            <div style="padding: 14px 16px; background: var(--pts-bg); border-radius: 11px;">
                <div style="font-size: 22px; font-weight: 800; color: #111827; line-height: 1;">
                    {{ $classrooms->count() }}
                </div>
                <div style="font-size: 11.5px; color: var(--pts-grey); margin-top: 4px; text-transform: uppercase;
                                    letter-spacing: 0.06em; font-weight: 700;">
                    Classes
                </div>
            </div>
            <div style="padding: 14px 16px; background: var(--pts-bg); border-radius: 11px;">
                <div style="font-size: 22px; font-weight: 800; color: #111827; line-height: 1;">
                    {{ $subjects->count() }}
                </div>
                <div style="font-size: 11.5px; color: var(--pts-grey); margin-top: 4px; text-transform: uppercase;
                                    letter-spacing: 0.06em; font-weight: 700;">
                    Subjects
                </div>
            </div>
            @php
                $totalAssignments = collect($assignments)->sum(fn($a) => count($a['subjects']));
                $unassigned = collect($assignments)->sum(fn($a) => collect($a['subjects'])->whereNull('teacher')->count());
            @endphp
            <div style="padding: 14px 16px; background: {{ $unassigned > 0 ? '#FEF2F2' : 'var(--pts-green-light)' }};
                                border-radius: 11px;">
                <div style="font-size: 22px; font-weight: 800; line-height: 1;
                                    color: {{ $unassigned > 0 ? '#DC2626' : 'var(--pts-green-dark)' }};">
                    {{ $unassigned }}
                </div>
                <div style="font-size: 11.5px; margin-top: 4px; text-transform: uppercase;
                                    letter-spacing: 0.06em; font-weight: 700;
                                    color: {{ $unassigned > 0 ? '#991B1B' : 'var(--pts-green-dark)' }};">
                    Unassigned
                </div>
            </div>
        </div>

        {{-- Matrix --}}
        @foreach ($assignments as $data)
            @if (empty($data['subjects']))
                @continue
            @endif

            <div style="border: 1px solid var(--pts-grey-light); border-radius: 12px; overflow: hidden; margin-bottom: 14px;">
                {{-- Class header --}}
                <div style="padding: 13px 18px; background: var(--pts-black); color: #fff;
                                        display: flex; align-items: center; justify-content: space-between;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <i class="fa-solid fa-chalkboard" style="color: var(--pts-green);"></i>
                        <span style="font-size: 14px; font-weight: 700;">{{ $data['classroom']->name }}</span>
                        @if ($data['classroom']->grade_level)
                            <span
                                style="font-size: 11px; color: #9CA3AF; padding: 2px 8px; background: rgba(255,255,255,0.08); border-radius: 12px;">
                                {{ $data['classroom']->grade_level }}
                            </span>
                        @endif
                    </div>
                    <span style="font-size: 11.5px; color: #9CA3AF;">
                        {{ count($data['subjects']) }} subject{{ count($data['subjects']) === 1 ? '' : 's' }}
                    </span>
                </div>

                {{-- Subject rows --}}
                <table style="width: 100%; border-collapse: collapse;">
                    @foreach ($data['subjects'] as $assignment)
                        <tr>
                            <td style="padding: 11px 18px; border-bottom: 1px solid var(--pts-grey-light);
                                                       font-size: 13px; font-weight: 600; color: #111827; width: 40%;">
                                {{ $assignment['subject']->name }}
                                @if ($assignment['subject']->code)
                                    <span style="font-size: 11px; color: var(--pts-grey); font-weight: 500; margin-left: 6px;">
                                        {{ $assignment['subject']->code }}
                                    </span>
                                @endif
                            </td>
                            <td style="padding: 11px 18px; border-bottom: 1px solid var(--pts-grey-light);
                                                       font-size: 13px; color: #374151;">
                                @if ($assignment['teacher'])
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <div style="width: 28px; height: 28px; border-radius: 50%;
                                                                        background: var(--pts-black); color: #fff;
                                                                        display: flex; align-items: center; justify-content: center;
                                                                        font-size: 11px; font-weight: 700; flex-shrink: 0;">
                                            {{ strtoupper(substr($assignment['teacher']->name, 0, 1)) }}
                                        </div>
                                        <span style="font-weight: 600; color: #111827;">{{ $assignment['teacher']->name }}</span>
                                    </div>
                                @else
                                    <span style="font-size: 12px; font-weight: 700; padding: 3px 10px; border-radius: 20px;
                                                                     background: #FEF2F2; color: #991B1B;">
                                        <i class="fa-solid fa-circle-exclamation"></i> Not assigned
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        @endforeach
    @endif

@endsection