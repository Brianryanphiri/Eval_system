@extends('settings.partials._layout')

@section('settings-content')

    <a href="{{ route('settings.overview') }}" class="settings-back">
        <i class="fa-solid fa-arrow-left"></i> Back to Settings
    </a>

    <div class="settings-panel__title">Changelog</div>
    <div class="settings-panel__sub">
        Recent updates and improvements to the Pro Tech Solutions Evaluation System.
    </div>

    @php
        $releases = [
            [
                'version' => '2.4',
                'date' => '14 September 2026',
                'status' => 'latest',
                'changes' => [
                    ['added', 'Full settings hub with 45+ configuration pages'],
                    ['added', 'Weighting Templates — save and apply weight presets in one click'],
                    ['added', 'Dark Mode & Login Background customization'],
                    ['added', 'Localization settings — language, date, time, number formats'],
                    ['added', 'Peer Comparison visibility controls'],
                    ['added', 'Data Retention policies'],
                    ['added', 'Feature Flags to enable/disable modules per school'],
                    ['improved', 'Institution Profile now handles logo, favicon, and signature uploads'],
                    ['improved', 'School brand colors now apply across the entire app'],
                    ['fixed', 'Grade band overlap validation'],
                ],
            ],
            [
                'version' => '2.3',
                'date' => '12 September 2026',
                'status' => 'stable',
                'changes' => [
                    ['added', 'Assessment Policy with weighted contribution rules'],
                    ['added', 'Grading Scales editor with add/edit/delete'],
                    ['added', 'Notification preferences for staff and guardians'],
                    ['added', 'Student Portal visibility toggles'],
                    ['added', 'Guardian Portal configuration'],
                    ['improved', 'Assessment creation now respects school weighting policy'],
                ],
            ],
            [
                'version' => '2.2',
                'date' => '28 August 2026',
                'status' => 'stable',
                'changes' => [
                    ['added', 'Report card generation with PDF download and email'],
                    ['added', 'Interventions module with before/after scoring'],
                    ['added', 'Question analytics with topic mastery'],
                    ['improved', 'Marking interface now handles partial submissions'],
                ],
            ],
            [
                'version' => '2.1',
                'date' => '10 August 2026',
                'status' => 'stable',
                'changes' => [
                    ['added', 'Bulk student CSV import'],
                    ['added', 'Student photos and guardian linking'],
                    ['added', 'Academic calendar with years and terms'],
                    ['added', 'Role-based permissions'],
                ],
            ],
            [
                'version' => '2.0',
                'date' => '22 July 2026',
                'status' => 'legacy',
                'changes' => [
                    ['added', 'Initial multi-school release'],
                    ['added', 'Setup wizard for new schools'],
                    ['added', 'Students, classes, subjects, assessments core'],
                    ['added', 'Marking, results, and reporting'],
                ],
            ],
        ];

        $colors = [
            'added' => ['bg' => '#DCFCE7', 'fg' => '#166534', 'label' => 'NEW'],
            'improved' => ['bg' => '#DBEAFE', 'fg' => '#1E40AF', 'label' => 'IMPROVED'],
            'fixed' => ['bg' => '#FEF9C3', 'fg' => '#854D0E', 'label' => 'FIXED'],
            'removed' => ['bg' => '#FEE2E2', 'fg' => '#991B1B', 'label' => 'REMOVED'],
        ];

        $statusBadges = [
            'latest' => ['bg' => 'var(--pts-green)', 'fg' => '#fff', 'label' => 'LATEST'],
            'stable' => ['bg' => 'var(--pts-grey-light)', 'fg' => '#374151', 'label' => 'STABLE'],
            'legacy' => ['bg' => '#F3F4F6', 'fg' => '#9CA3AF', 'label' => 'LEGACY'],
        ];
    @endphp

    <div style="position: relative;">
        {{-- Vertical timeline line --}}
        <div style="position: absolute; left: 22px; top: 12px; bottom: 0; width: 2px;
                        background: var(--pts-grey-light);"></div>

        @foreach ($releases as $release)
            @php $badge = $statusBadges[$release['status']]; @endphp
            <div style="position: relative; padding-left: 58px; margin-bottom: 32px;">
                {{-- Timeline dot --}}
                <div style="position: absolute; left: 12px; top: 4px; width: 22px; height: 22px;
                                    border-radius: 50%; background: {{ $release['status'] === 'latest' ? 'var(--pts-green)' : '#fff' }};
                                    border: 3px solid {{ $release['status'] === 'latest' ? 'var(--pts-green)' : 'var(--pts-grey-light)' }};
                                    box-shadow: 0 0 0 4px #fff;">
                </div>

                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 4px;">
                    <span style="font-size: 18px; font-weight: 800; color: #111827;">
                        Version {{ $release['version'] }}
                    </span>
                    <span style="font-size: 9.5px; font-weight: 800; padding: 3px 9px; border-radius: 20px;
                                         background: {{ $badge['bg'] }}; color: {{ $badge['fg'] }};
                                         letter-spacing: 0.08em;">
                        {{ $badge['label'] }}
                    </span>
                </div>

                <div style="font-size: 12.5px; color: var(--pts-grey); margin-bottom: 14px;">
                    {{ $release['date'] }}
                </div>

                <div style="border: 1px solid var(--pts-grey-light); border-radius: 11px; padding: 14px 18px;
                                    background: #fff;">
                    <ul style="list-style: none; display: flex; flex-direction: column; gap: 8px;">
                        @foreach ($release['changes'] as [$type, $text])
                            @php $c = $colors[$type]; @endphp
                            <li style="display: flex; align-items: flex-start; gap: 10px; font-size: 13px; color: #374151;">
                                <span style="font-size: 9px; font-weight: 800; padding: 3px 7px; border-radius: 4px;
                                                         background: {{ $c['bg'] }}; color: {{ $c['fg'] }};
                                                         letter-spacing: 0.05em; flex-shrink: 0; margin-top: 2px;">
                                    {{ $c['label'] }}
                                </span>
                                <span style="flex: 1; line-height: 1.55;">{{ $text }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endforeach
    </div>

@endsection