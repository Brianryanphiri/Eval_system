@extends('settings.partials._layout')

@section('settings-content')

    <div class="settings-panel__title">School Settings</div>
    <div class="settings-panel__sub">
        Configure how assessments, grading, staff, and academic periods work for your institution.
    </div>

    @if (!$school->setup_completed)
        <div class="settings-banner">
            <i class="fa-solid fa-circle-info"></i>
            <div class="settings-banner__text">
                Your school hasn't finished the setup wizard yet. Complete it to unlock all features.
                <a href="{{ route('setup.step1') }}" style="color:var(--pts-green-dark); font-weight:700;">
                    Finish setup
                </a>
            </div>
        </div>
    @endif

    {{-- ============ ACADEMIC & ASSESSMENT ============ --}}
    <div class="settings-section">
        <div class="settings-section__title">Academic &amp; Assessment</div>
        <div class="settings-grid">

            <a href="{{ route('settings.grading') }}" class="settings-card">
                <div class="settings-card__head">
                    <div class="settings-card__icon-wrap"><i class="fa-solid fa-graduation-cap"></i></div>
                    <div class="settings-card__title">Grading Scales</div>
                </div>
                <div class="settings-card__desc">Letter grades, percentage bands, achievement levels.</div>
                <div class="settings-card__footer">
                    <span class="settings-card__status {{ $scaleCount > 0 ? 'ok' : 'todo' }}">
                        {{ $scaleCount > 0 ? $scaleCount . ' bands configured' : 'Set up required' }}
                    </span>
                    <i class="fa-solid fa-arrow-right settings-card__arrow"></i>
                </div>
            </a>

            <a href="{{ route('settings.policy') }}" class="settings-card">
                <div class="settings-card__head">
                    <div class="settings-card__icon-wrap"><i class="fa-solid fa-clipboard-list"></i></div>
                    <div class="settings-card__title">Assessment Policy</div>
                </div>
                <div class="settings-card__desc">Types, weights, contribution rules.</div>
                <div class="settings-card__footer">
                    <span class="settings-card__status {{ $typeCount > 0 ? 'ok' : 'todo' }}">
                        {{ $typeCount > 0 ? $typeCount . ' types · weights set' : 'No types yet' }}
                    </span>
                    <i class="fa-solid fa-arrow-right settings-card__arrow"></i>
                </div>
            </a>

            <a href="{{ route('settings.weighting-templates') }}" class="settings-card">
                <div class="settings-card__head">
                    <div class="settings-card__icon-wrap"><i class="fa-solid fa-scale-balanced"></i></div>
                    <div class="settings-card__title">Weighting Templates</div>
                </div>
                <div class="settings-card__desc">Reusable CA/Exam weight presets.</div>
                <div class="settings-card__footer">
                    <span class="settings-card__status ok">Manage presets</span>
                    <i class="fa-solid fa-arrow-right settings-card__arrow"></i>
                </div>
            </a>

            <a href="{{ route('settings.assessment-types') }}" class="settings-card">
                <div class="settings-card__head">
                    <div class="settings-card__icon-wrap"><i class="fa-solid fa-list-check"></i></div>
                    <div class="settings-card__title">Assessment Types</div>
                </div>
                <div class="settings-card__desc">Quiz, mid-term, exam, project categories.</div>
                <div class="settings-card__footer">
                    <span class="settings-card__status ok">Manage types</span>
                    <i class="fa-solid fa-arrow-right settings-card__arrow"></i>
                </div>
            </a>

            <a href="{{ route('settings.comment-banks') }}" class="settings-card">
                <div class="settings-card__head">
                    <div class="settings-card__icon-wrap"><i class="fa-solid fa-comment-dots"></i></div>
                    <div class="settings-card__title">Comment Banks</div>
                </div>
                <div class="settings-card__desc">Pre-written teacher comments by band.</div>
                <div class="settings-card__footer">
                    <span class="settings-card__status ok">Manage comments</span>
                    <i class="fa-solid fa-arrow-right settings-card__arrow"></i>
                </div>
            </a>

            <a href="{{ route('settings.report-card-templates') }}" class="settings-card">
                <div class="settings-card__head">
                    <div class="settings-card__icon-wrap"><i class="fa-solid fa-file-lines"></i></div>
                    <div class="settings-card__title">Report Card Templates</div>
                </div>
                <div class="settings-card__desc">Classic, modern, or custom layouts.</div>
                <div class="settings-card__footer">
                    <span class="settings-card__status ok">Choose layout</span>
                    <i class="fa-solid fa-arrow-right settings-card__arrow"></i>
                </div>
            </a>

            <a href="{{ route('settings.report-builder') }}" class="settings-card">
                <div class="settings-card__head">
                    <div class="settings-card__icon-wrap"><i class="fa-solid fa-chart-line"></i></div>
                    <div class="settings-card__title">Report Builder</div>
                </div>
                <div class="settings-card__desc">Drag-and-drop custom reports.</div>
                <div class="settings-card__footer">
                    <span class="settings-card__status ok">Build reports</span>
                    <i class="fa-solid fa-arrow-right settings-card__arrow"></i>
                </div>
            </a>

            <a href="{{ route('settings.scheduled-reports') }}" class="settings-card">
                <div class="settings-card__head">
                    <div class="settings-card__icon-wrap"><i class="fa-regular fa-clock"></i></div>
                    <div class="settings-card__title">Scheduled Reports</div>
                </div>
                <div class="settings-card__desc">Auto-email summaries on a schedule.</div>
                <div class="settings-card__footer">
                    <span class="settings-card__status ok">Manage schedules</span>
                    <i class="fa-solid fa-arrow-right settings-card__arrow"></i>
                </div>
            </a>

            <a href="{{ route('settings.calendar') }}" class="settings-card">
                <div class="settings-card__head">
                    <div class="settings-card__icon-wrap"><i class="fa-regular fa-calendar"></i></div>
                    <div class="settings-card__title">Academic Calendar</div>
                </div>
                <div class="settings-card__desc">Years, terms, current-period flags.</div>
                <div class="settings-card__footer">
                    <span class="settings-card__status {{ $yearCount > 0 ? 'ok' : 'todo' }}">
                        {{ $yearCount > 0 ? $yearCount . ' years · terms set' : 'Add academic year' }}
                    </span>
                    <i class="fa-solid fa-arrow-right settings-card__arrow"></i>
                </div>
            </a>

            <a href="{{ route('settings.timetable-periods') }}" class="settings-card">
                <div class="settings-card__head">
                    <div class="settings-card__icon-wrap"><i class="fa-solid fa-clock-rotate-left"></i></div>
                    <div class="settings-card__title">Timetable Periods</div>
                </div>
                <div class="settings-card__desc">Define the daily bell schedule.</div>
                <div class="settings-card__footer">
                    <span class="settings-card__status ok">Manage periods</span>
                    <i class="fa-solid fa-arrow-right settings-card__arrow"></i>
                </div>
            </a>

            <a href="{{ route('settings.year-rollover') }}" class="settings-card">
                <div class="settings-card__head">
                    <div class="settings-card__icon-wrap"><i class="fa-solid fa-arrow-rotate-right"></i></div>
                    <div class="settings-card__title">Year Rollover</div>
                </div>
                <div class="settings-card__desc">Copy classes and subjects to a new year.</div>
                <div class="settings-card__footer">
                    <span class="settings-card__status ok">Run rollover</span>
                    <i class="fa-solid fa-arrow-right settings-card__arrow"></i>
                </div>
            </a>

            <a href="{{ route('settings.term-rollover') }}" class="settings-card">
                <div class="settings-card__head">
                    <div class="settings-card__icon-wrap"><i class="fa-solid fa-forward"></i></div>
                    <div class="settings-card__title">Term Rollover</div>
                </div>
                <div class="settings-card__desc">Close one term, open the next.</div>
                <div class="settings-card__footer">
                    <span class="settings-card__status ok">Advance term</span>
                    <i class="fa-solid fa-arrow-right settings-card__arrow"></i>
                </div>
            </a>

            <a href="{{ route('settings.retention') }}" class="settings-card">
                <div class="settings-card__head">
                    <div class="settings-card__icon-wrap"><i class="fa-solid fa-box-archive"></i></div>
                    <div class="settings-card__title">Data Retention</div>
                </div>
                <div class="settings-card__desc">How long to keep old results.</div>
                <div class="settings-card__footer">
                    <span class="settings-card__status ok">Set retention</span>
                    <i class="fa-solid fa-arrow-right settings-card__arrow"></i>
                </div>
            </a>

            <a href="{{ route('settings.peer-comparison') }}" class="settings-card">
                <div class="settings-card__head">
                    <div class="settings-card__icon-wrap"><i class="fa-solid fa-users-viewfinder"></i></div>
                    <div class="settings-card__title">Peer Comparison</div>
                </div>
                <div class="settings-card__desc">What students see about their rank.</div>
                <div class="settings-card__footer">
                    <span class="settings-card__status ok">Control visibility</span>
                    <i class="fa-solid fa-arrow-right settings-card__arrow"></i>
                </div>
            </a>

            <a href="{{ route('settings.custom-reports') }}" class="settings-card">
                <div class="settings-card__head">
                    <div class="settings-card__icon-wrap"><i class="fa-solid fa-sliders"></i></div>
                    <div class="settings-card__title">Custom Reports</div>
                </div>
                <div class="settings-card__desc">Saved report definitions.</div>
                <div class="settings-card__footer">
                    <span class="settings-card__status ok">View saved</span>
                    <i class="fa-solid fa-arrow-right settings-card__arrow"></i>
                </div>
            </a>

        </div>
    </div>

    {{-- ============ PEOPLE & ROLES ============ --}}
    <div class="settings-section">
        <div class="settings-section__title">People &amp; Roles</div>
        <div class="settings-grid">

            <a href="{{ route('settings.profile') }}" class="settings-card">
                <div class="settings-card__head">
                    <div class="settings-card__icon-wrap"><i class="fa-solid fa-school"></i></div>
                    <div class="settings-card__title">Institution Profile</div>
                </div>
                <div class="settings-card__desc">Name, logo, address, contact.</div>
                <div class="settings-card__footer">
                    <span class="settings-card__status {{ $school->name && $school->logo_path ? 'ok' : 'warn' }}">
                        {{ $school->name && $school->logo_path ? 'Fully configured' : 'Needs logo' }}
                    </span>
                    <i class="fa-solid fa-arrow-right settings-card__arrow"></i>
                </div>
            </a>

            <a href="{{ route('staff.index') }}" class="settings-card">
                <div class="settings-card__head">
                    <div class="settings-card__icon-wrap"><i class="fa-solid fa-user-tie"></i></div>
                    <div class="settings-card__title">Staff Management</div>
                </div>
                <div class="settings-card__desc">Teachers, admins, and roles.</div>
                <div class="settings-card__footer">
                    <span class="settings-card__status ok">Manage staff</span>
                    <i class="fa-solid fa-arrow-right settings-card__arrow"></i>
                </div>
            </a>

            <a href="{{ route('settings.staff-matrix') }}" class="settings-card">
                <div class="settings-card__head">
                    <div class="settings-card__icon-wrap"><i class="fa-solid fa-table-cells"></i></div>
                    <div class="settings-card__title">Staff Roles Matrix</div>
                </div>
                <div class="settings-card__desc">Assign staff to classes and subjects.</div>
                <div class="settings-card__footer">
                    <span class="settings-card__status ok">Assign roles</span>
                    <i class="fa-solid fa-arrow-right settings-card__arrow"></i>
                </div>
            </a>

            <a href="{{ route('settings.roles') }}" class="settings-card">
                <div class="settings-card__head">
                    <div class="settings-card__icon-wrap"><i class="fa-solid fa-shield-halved"></i></div>
                    <div class="settings-card__title">Roles &amp; Permissions</div>
                </div>
                <div class="settings-card__desc">Fine-grained access control.</div>
                <div class="settings-card__footer">
                    <span class="settings-card__status ok">{{ $permissionCount }} permissions</span>
                    <i class="fa-solid fa-arrow-right settings-card__arrow"></i>
                </div>
            </a>

            <a href="{{ route('settings.custom-fields') }}" class="settings-card">
                <div class="settings-card__head">
                    <div class="settings-card__icon-wrap"><i class="fa-solid fa-table-columns"></i></div>
                    <div class="settings-card__title">Custom Fields</div>
                </div>
                <div class="settings-card__desc">Extra columns for students or staff.</div>
                <div class="settings-card__footer">
                    <span class="settings-card__status ok">Add fields</span>
                    <i class="fa-solid fa-arrow-right settings-card__arrow"></i>
                </div>
            </a>

            <a href="{{ route('settings.student-portal') }}" class="settings-card">
                <div class="settings-card__head">
                    <div class="settings-card__icon-wrap"><i class="fa-solid fa-user-graduate"></i></div>
                    <div class="settings-card__title">Student Portal</div>
                </div>
                <div class="settings-card__desc">What students can see and do.</div>
                <div class="settings-card__footer">
                    <span class="settings-card__status ok">Control access</span>
                    <i class="fa-solid fa-arrow-right settings-card__arrow"></i>
                </div>
            </a>

            <a href="{{ route('settings.guardian-portal') }}" class="settings-card">
                <div class="settings-card__head">
                    <div class="settings-card__icon-wrap"><i class="fa-solid fa-user-shield"></i></div>
                    <div class="settings-card__title">Guardian Portal</div>
                </div>
                <div class="settings-card__desc">Parent visibility and permissions.</div>
                <div class="settings-card__footer">
                    <span class="settings-card__status ok">Control access</span>
                    <i class="fa-solid fa-arrow-right settings-card__arrow"></i>
                </div>
            </a>

            <a href="{{ route('settings.campuses') }}" class="settings-card">
                <div class="settings-card__head">
                    <div class="settings-card__icon-wrap"><i class="fa-solid fa-building-columns"></i></div>
                    <div class="settings-card__title">Multi-Campus</div>
                </div>
                <div class="settings-card__desc">Sub-schools under one admin.</div>
                <div class="settings-card__footer">
                    <span class="settings-card__status ok">Manage branches</span>
                    <i class="fa-solid fa-arrow-right settings-card__arrow"></i>
                </div>
            </a>

            <a href="{{ route('settings.alumni') }}" class="settings-card">
                <div class="settings-card__head">
                    <div class="settings-card__icon-wrap"><i class="fa-solid fa-cake-candles"></i></div>
                    <div class="settings-card__title">Alumni Tracking</div>
                </div>
                <div class="settings-card__desc">Graduated students and their records.</div>
                <div class="settings-card__footer">
                    <span class="settings-card__status ok">View archive</span>
                    <i class="fa-solid fa-arrow-right settings-card__arrow"></i>
                </div>
            </a>

        </div>
    </div>

    {{-- ============ COMMUNICATION ============ --}}
    <div class="settings-section">
        <div class="settings-section__title">Communication</div>
        <div class="settings-grid">

            <a href="{{ route('settings.notifications') }}" class="settings-card">
                <div class="settings-card__head">
                    <div class="settings-card__icon-wrap"><i class="fa-regular fa-bell"></i></div>
                    <div class="settings-card__title">Notifications</div>
                </div>
                <div class="settings-card__desc">Which emails and alerts go out.</div>
                <div class="settings-card__footer">
                    <span class="settings-card__status ok">Manage alerts</span>
                    <i class="fa-solid fa-arrow-right settings-card__arrow"></i>
                </div>
            </a>

            <a href="{{ route('settings.email-templates') }}" class="settings-card">
                <div class="settings-card__head">
                    <div class="settings-card__icon-wrap"><i class="fa-regular fa-envelope-open"></i></div>
                    <div class="settings-card__title">Email Templates</div>
                </div>
                <div class="settings-card__desc">Edit automated email wording.</div>
                <div class="settings-card__footer">
                    <span class="settings-card__status ok">Edit templates</span>
                    <i class="fa-solid fa-arrow-right settings-card__arrow"></i>
                </div>
            </a>

            <a href="{{ route('settings.meetings') }}" class="settings-card">
                <div class="settings-card__head">
                    <div class="settings-card__icon-wrap"><i class="fa-regular fa-calendar-check"></i></div>
                    <div class="settings-card__title">Meeting Scheduler</div>
                </div>
                <div class="settings-card__desc">Parent-teacher appointment slots.</div>
                <div class="settings-card__footer">
                    <span class="settings-card__status ok">Open scheduler</span>
                    <i class="fa-solid fa-arrow-right settings-card__arrow"></i>
                </div>
            </a>

            <a href="{{ route('settings.feedback') }}" class="settings-card">
                <div class="settings-card__head">
                    <div class="settings-card__icon-wrap"><i class="fa-regular fa-comment"></i></div>
                    <div class="settings-card__title">Feedback Inbox</div>
                </div>
                <div class="settings-card__desc">Bug reports and feature requests.</div>
                <div class="settings-card__footer">
                    <span class="settings-card__status ok">View inbox</span>
                    <i class="fa-solid fa-arrow-right settings-card__arrow"></i>
                </div>
            </a>

            <a href="{{ route('settings.help') }}" class="settings-card">
                <div class="settings-card__head">
                    <div class="settings-card__icon-wrap"><i class="fa-regular fa-circle-question"></i></div>
                    <div class="settings-card__title">Help &amp; Support</div>
                </div>
                <div class="settings-card__desc">FAQ, tutorials, contact support.</div>
                <div class="settings-card__footer">
                    <span class="settings-card__status ok">Get help</span>
                    <i class="fa-solid fa-arrow-right settings-card__arrow"></i>
                </div>
            </a>

            <a href="{{ route('settings.changelog') }}" class="settings-card">
                <div class="settings-card__head">
                    <div class="settings-card__icon-wrap"><i class="fa-solid fa-code-branch"></i></div>
                    <div class="settings-card__title">Changelog</div>
                </div>
                <div class="settings-card__desc">What's new in each release.</div>
                <div class="settings-card__footer">
                    <span class="settings-card__status ok">View updates</span>
                    <i class="fa-solid fa-arrow-right settings-card__arrow"></i>
                </div>
            </a>

        </div>
    </div>

    {{-- ============ SECURITY & SYSTEM ============ --}}
    <div class="settings-section">
        <div class="settings-section__title">Security &amp; System</div>
        <div class="settings-grid">

            <a href="{{ route('settings.security') }}" class="settings-card">
                <div class="settings-card__head">
                    <div class="settings-card__icon-wrap"><i class="fa-solid fa-shield"></i></div>
                    <div class="settings-card__title">Security</div>
                </div>
                <div class="settings-card__desc">Overall security posture and defaults.</div>
                <div class="settings-card__footer">
                    <span class="settings-card__status ok">Review settings</span>
                    <i class="fa-solid fa-arrow-right settings-card__arrow"></i>
                </div>
            </a>

            <a href="{{ route('settings.password-policy') }}" class="settings-card">
                <div class="settings-card__head">
                    <div class="settings-card__icon-wrap"><i class="fa-solid fa-key"></i></div>
                    <div class="settings-card__title">Password Policy</div>
                </div>
                <div class="settings-card__desc">Length, complexity, expiry rules.</div>
                <div class="settings-card__footer">
                    <span class="settings-card__status ok">Set rules</span>
                    <i class="fa-solid fa-arrow-right settings-card__arrow"></i>
                </div>
            </a>

            <a href="{{ route('settings.two-factor') }}" class="settings-card">
                <div class="settings-card__head">
                    <div class="settings-card__icon-wrap"><i class="fa-solid fa-mobile-screen"></i></div>
                    <div class="settings-card__title">Two-Factor Auth</div>
                </div>
                <div class="settings-card__desc">Require 2FA for admin accounts.</div>
                <div class="settings-card__footer">
                    <span class="settings-card__status ok">Enforce 2FA</span>
                    <i class="fa-solid fa-arrow-right settings-card__arrow"></i>
                </div>
            </a>

            <a href="{{ route('settings.sessions') }}" class="settings-card">
                <div class="settings-card__head">
                    <div class="settings-card__icon-wrap"><i class="fa-regular fa-hourglass-half"></i></div>
                    <div class="settings-card__title">Session Management</div>
                </div>
                <div class="settings-card__desc">Timeout and active sessions.</div>
                <div class="settings-card__footer">
                    <span class="settings-card__status ok">Manage sessions</span>
                    <i class="fa-solid fa-arrow-right settings-card__arrow"></i>
                </div>
            </a>

            <a href="{{ route('settings.api-keys') }}" class="settings-card">
                <div class="settings-card__head">
                    <div class="settings-card__icon-wrap"><i class="fa-solid fa-code"></i></div>
                    <div class="settings-card__title">API Keys</div>
                </div>
                <div class="settings-card__desc">Tokens for external integrations.</div>
                <div class="settings-card__footer">
                    <span class="settings-card__status ok">Manage keys</span>
                    <i class="fa-solid fa-arrow-right settings-card__arrow"></i>
                </div>
            </a>

            <a href="{{ route('settings.audit') }}" class="settings-card">
                <div class="settings-card__head">
                    <div class="settings-card__icon-wrap"><i class="fa-solid fa-list-ul"></i></div>
                    <div class="settings-card__title">Audit Log</div>
                </div>
                <div class="settings-card__desc">Every change and login, timestamped.</div>
                <div class="settings-card__footer">
                    <span class="settings-card__status ok">View log</span>
                    <i class="fa-solid fa-arrow-right settings-card__arrow"></i>
                </div>
            </a>

            <a href="{{ route('settings.status') }}" class="settings-card">
                <div class="settings-card__head">
                    <div class="settings-card__icon-wrap"><i class="fa-solid fa-heart-pulse"></i></div>
                    <div class="settings-card__title">System Status</div>
                </div>
                <div class="settings-card__desc">Queue, mail, cron health.</div>
                <div class="settings-card__footer">
                    <span class="settings-card__status ok">Check health</span>
                    <i class="fa-solid fa-arrow-right settings-card__arrow"></i>
                </div>
            </a>

            <a href="{{ route('settings.flags') }}" class="settings-card">
                <div class="settings-card__head">
                    <div class="settings-card__icon-wrap"><i class="fa-solid fa-toggle-on"></i></div>
                    <div class="settings-card__title">Feature Flags</div>
                </div>
                <div class="settings-card__desc">Turn modules on or off per school.</div>
                <div class="settings-card__footer">
                    <span class="settings-card__status ok">Toggle modules</span>
                    <i class="fa-solid fa-arrow-right settings-card__arrow"></i>
                </div>
            </a>

        </div>
    </div>

    {{-- ============ BRANDING & APPEARANCE ============ --}}
    <div class="settings-section">
        <div class="settings-section__title">Branding &amp; Appearance</div>
        <div class="settings-grid">

            <a href="{{ route('settings.branding') }}" class="settings-card">
                <div class="settings-card__head">
                    <div class="settings-card__icon-wrap"><i class="fa-solid fa-palette"></i></div>
                    <div class="settings-card__title">Theme &amp; Branding</div>
                </div>
                <div class="settings-card__desc">Overall look and feel.</div>
                <div class="settings-card__footer">
                    <span class="settings-card__status ok">Customize theme</span>
                    <i class="fa-solid fa-arrow-right settings-card__arrow"></i>
                </div>
            </a>

            <a href="{{ route('settings.accent-color') }}" class="settings-card">
                <div class="settings-card__head">
                    <div class="settings-card__icon-wrap"><i class="fa-solid fa-droplet"></i></div>
                    <div class="settings-card__title">Accent Color</div>
                </div>
                <div class="settings-card__desc">Pick your school's accent color.</div>
                <div class="settings-card__footer">
                    <span class="settings-card__status ok">Pick color</span>
                    <i class="fa-solid fa-arrow-right settings-card__arrow"></i>
                </div>
            </a>

            <a href="{{ route('settings.dark-mode') }}" class="settings-card">
                <div class="settings-card__head">
                    <div class="settings-card__icon-wrap"><i class="fa-solid fa-moon"></i></div>
                    <div class="settings-card__title">Light / Dark Mode</div>
                </div>
                <div class="settings-card__desc">Default theme for new users.</div>
                <div class="settings-card__footer">
                    <span class="settings-card__status ok">Set default</span>
                    <i class="fa-solid fa-arrow-right settings-card__arrow"></i>
                </div>
            </a>

            <a href="{{ route('settings.login-background') }}" class="settings-card">
                <div class="settings-card__head">
                    <div class="settings-card__icon-wrap"><i class="fa-solid fa-image"></i></div>
                    <div class="settings-card__title">Login Background</div>
                </div>
                <div class="settings-card__desc">Image shown on the login page.</div>
                <div class="settings-card__footer">
                    <span class="settings-card__status ok">Upload image</span>
                    <i class="fa-solid fa-arrow-right settings-card__arrow"></i>
                </div>
            </a>

            <a href="{{ route('settings.favicon') }}" class="settings-card">
                <div class="settings-card__head">
                    <div class="settings-card__icon-wrap"><i class="fa-regular fa-star"></i></div>
                    <div class="settings-card__title">Favicon</div>
                </div>
                <div class="settings-card__desc">Browser tab icon.</div>
                <div class="settings-card__footer">
                    <span class="settings-card__status ok">Upload favicon</span>
                    <i class="fa-solid fa-arrow-right settings-card__arrow"></i>
                </div>
            </a>

            <a href="{{ route('settings.logo') }}" class="settings-card">
                <div class="settings-card__head">
                    <div class="settings-card__icon-wrap"><i class="fa-solid fa-image-portrait"></i></div>
                    <div class="settings-card__title">Custom Logo</div>
                </div>
                <div class="settings-card__desc">School logo for reports and the app.</div>
                <div class="settings-card__footer">
                    <span class="settings-card__status ok">Upload logo</span>
                    <i class="fa-solid fa-arrow-right settings-card__arrow"></i>
                </div>
            </a>

            <a href="{{ route('settings.signatures') }}" class="settings-card">
                <div class="settings-card__head">
                    <div class="settings-card__icon-wrap"><i class="fa-solid fa-signature"></i></div>
                    <div class="settings-card__title">Digital Signatures</div>
                </div>
                <div class="settings-card__desc">Auto-stamp report cards with signatures.</div>
                <div class="settings-card__footer">
                    <span class="settings-card__status ok">Manage signatures</span>
                    <i class="fa-solid fa-arrow-right settings-card__arrow"></i>
                </div>
            </a>

            <a href="{{ route('settings.localization') }}" class="settings-card">
                <div class="settings-card__head">
                    <div class="settings-card__icon-wrap"><i class="fa-solid fa-globe"></i></div>
                    <div class="settings-card__title">Localization</div>
                </div>
                <div class="settings-card__desc">Language, dates, timezone, currency.</div>
                <div class="settings-card__footer">
                    <span class="settings-card__status ok">Set locale</span>
                    <i class="fa-solid fa-arrow-right settings-card__arrow"></i>
                </div>
            </a>

        </div>
    </div>

@endsection