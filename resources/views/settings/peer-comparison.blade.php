@extends('settings.partials._layout')

@section('settings-content')

    <a href="{{ route('settings.overview') }}" class="settings-back">
        <i class="fa-solid fa-arrow-left"></i> Back to Settings
    </a>

    <div class="settings-panel__title">Peer Comparison</div>
    <div class="settings-panel__sub">
        Decide what students see about their performance relative to their classmates. Reducing comparison pressure often
        improves engagement.
    </div>

    <form method="POST" action="{{ route('settings.peer-comparison.update') }}">
        @csrf

        @php
            $groups = [
                'What Students See' => [
                    'peer.show_rank' => [
                        'Exact class rank',
                        'Show "you are 14th out of 86". Most impactful, most pressure.',
                    ],
                    'peer.show_percentile' => [
                        'Percentile band',
                        'Show "you are in the top 25%". Less precise, less pressure.',
                    ],
                    'peer.show_class_average' => [
                        'Class average',
                        'Show the class average for each subject, so students can see where they stand.',
                    ],
                    'peer.show_subject_average' => [
                        'Subject-specific average',
                        'Show the average per subject rather than one overall number.',
                    ],
                    'peer.show_highest_lowest' => [
                        'Highest and lowest scores',
                        'Show the highest and lowest marks in the class for context.',
                    ],
                ],
                'Privacy' => [
                    'peer.hide_names' => [
                        'Hide other students\' names',
                        'Never show which classmate scored what. Only show anonymous statistics.',
                    ],
                ],
            ];
        @endphp

        @foreach ($groups as $groupLabel => $toggles)
            <div style="margin-bottom: 26px;">
                <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em;
                                    color: var(--pts-green-dark); margin-bottom: 12px; padding-bottom: 8px;
                                    border-bottom: 1px solid var(--pts-grey-light);">
                    {{ $groupLabel }}
                </div>

                <div style="display: flex; flex-direction: column; gap: 4px;">
                    @foreach ($toggles as $key => [$title, $desc])
                        @php $value = $settings[$key] ?? false; @endphp
                        <label style="display:flex; align-items:flex-start; gap:14px; padding: 12px 14px;
                                                  border-radius: 10px; cursor: pointer;"
                            onmouseover="this.style.background='var(--pts-bg)'" onmouseout="this.style.background='transparent'">
                            <input type="hidden" name="{{ $key }}" value="0">
                            <input type="checkbox" name="{{ $key }}" value="1" @checked($value) style="margin-top: 3px; width: 18px; height: 18px;
                                                      accent-color: var(--pts-green); cursor: pointer;">
                            <div style="flex: 1;">
                                <div style="font-size: 13.5px; font-weight: 700; color: #111827; margin-bottom: 3px;">
                                    {{ $title }}
                                </div>
                                <div style="font-size: 12px; color: var(--pts-grey); line-height: 1.5;">
                                    {{ $desc }}
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>
        @endforeach

        <div style="margin-bottom: 26px; padding: 16px; background: var(--pts-bg); border-radius: 10px;">
            <label style="display:block; font-size:11.5px; font-weight:700; color:#374151; margin-bottom:5px;">
                Minimum class size for showing statistics
            </label>
            <div style="display:flex; align-items:center; gap:10px;">
                <input type="number" name="peer.min_class_size" value="{{ $settings['peer.min_class_size'] ?? 5 }}" min="1"
                    max="50" style="width:100px; padding:10px 12px; border:1.5px solid var(--pts-grey-light);
                                  border-radius:9px; font-size:13.5px; font-family:inherit; outline:none;">
                <span style="font-size:13px; color:var(--pts-grey);">students</span>
            </div>
            <div style="font-size:11px; color:var(--pts-grey); margin-top:6px;">
                In classes smaller than this, no comparison statistics are shown — protects small groups.
            </div>
        </div>

        <div style="display:flex; justify-content: flex-end; gap: 10px; margin-top: 28px; padding-top: 20px;
                        border-top: 1px solid var(--pts-grey-light);">
            <a href="{{ route('settings.overview') }}" style="padding: 11px 20px; border: 1.5px solid var(--pts-grey-light); border-radius: 9px;
                          font-size: 13px; font-weight: 700; color: #374151; text-decoration: none; background: #fff;">
                Cancel
            </a>
            <button type="submit" style="padding: 11px 24px; background: var(--pts-black); color: #fff; border: none;
                               border-radius: 9px; font-size: 13px; font-weight: 700; cursor: pointer;">
                <i class="fa-solid fa-check"></i> Save Settings
            </button>
        </div>
    </form>

@endsection