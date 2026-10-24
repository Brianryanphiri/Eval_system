@extends('settings.partials._layout')

@section('settings-content')

    <a href="{{ route('settings.overview') }}" class="settings-back">
        <i class="fa-solid fa-arrow-left"></i> Back to Settings
    </a>

    <div class="settings-panel__title">Help &amp; Support</div>
    <div class="settings-panel__sub">
        Guides, answers to common questions, and how to reach us.
    </div>

    {{-- Quick links --}}
    <div style="margin-bottom: 28px;">
        <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em;
                        color: var(--pts-green-dark); margin-bottom: 12px; padding-bottom: 8px;
                        border-bottom: 1px solid var(--pts-grey-light);">
            Quick Links
        </div>

        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px;">
            <a href="{{ route('settings.feedback') }}" style="display: block; padding: 16px; border: 1px solid var(--pts-grey-light); border-radius: 11px;
                          text-decoration: none; color: inherit; transition: border-color 0.15s;"
                onmouseover="this.style.borderColor='var(--pts-green)'"
                onmouseout="this.style.borderColor='var(--pts-grey-light)'">
                <i class="fa-regular fa-comment"
                    style="font-size: 20px; color: var(--pts-green-dark); display: block; margin-bottom: 10px;"></i>
                <div style="font-size: 13.5px; font-weight: 700; color: #111827; margin-bottom: 3px;">Send Feedback</div>
                <div style="font-size: 12px; color: var(--pts-grey); line-height: 1.4;">
                    Report a bug or suggest a new feature.
                </div>
            </a>

            <a href="{{ route('settings.changelog') }}" style="display: block; padding: 16px; border: 1px solid var(--pts-grey-light); border-radius: 11px;
                          text-decoration: none; color: inherit; transition: border-color 0.15s;"
                onmouseover="this.style.borderColor='var(--pts-green)'"
                onmouseout="this.style.borderColor='var(--pts-grey-light)'">
                <i class="fa-solid fa-code-branch"
                    style="font-size: 20px; color: var(--pts-green-dark); display: block; margin-bottom: 10px;"></i>
                <div style="font-size: 13.5px; font-weight: 700; color: #111827; margin-bottom: 3px;">What's New</div>
                <div style="font-size: 12px; color: var(--pts-grey); line-height: 1.4;">
                    See recent updates and improvements.
                </div>
            </a>

            <a href="{{ route('settings.status') }}" style="display: block; padding: 16px; border: 1px solid var(--pts-grey-light); border-radius: 11px;
                          text-decoration: none; color: inherit; transition: border-color 0.15s;"
                onmouseover="this.style.borderColor='var(--pts-green)'"
                onmouseout="this.style.borderColor='var(--pts-grey-light)'">
                <i class="fa-solid fa-heart-pulse"
                    style="font-size: 20px; color: var(--pts-green-dark); display: block; margin-bottom: 10px;"></i>
                <div style="font-size: 13.5px; font-weight: 700; color: #111827; margin-bottom: 3px;">System Status</div>
                <div style="font-size: 12px; color: var(--pts-grey); line-height: 1.4;">
                    Check environment health and diagnostics.
                </div>
            </a>
        </div>
    </div>

    {{-- FAQ --}}
    <div style="margin-bottom: 28px;">
        <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em;
                        color: var(--pts-green-dark); margin-bottom: 12px; padding-bottom: 8px;
                        border-bottom: 1px solid var(--pts-grey-light);">
            Frequently Asked Questions
        </div>

        @php
            $faqs = [
                [
                    'How do I add students?',
                    'Go to Students in the sidebar and click "Add Student". You can also import a whole class using the CSV import feature.',
                ],
                [
                    'How do I create an assessment?',
                    'Open Examinations, click "New Assessment", fill in the details, then add questions. Once marks are entered you can lock it to prevent further edits.',
                ],
                [
                    'What if weights don\'t sum to 100%?',
                    'Go to Settings → Assessment Policy and adjust the weights per type. The system will block saving until the active types sum to exactly 100%.',
                ],
                [
                    'How do I generate report cards?',
                    'Open a student profile and click "Report Card" at the top. From there you can print, download as PDF, or email it directly to guardians.',
                ],
                [
                    'Can I switch between light and dark mode?',
                    'Yes — under Settings → Theme. Admins set the school default; users can toggle if the setting allows.',
                ],
                [
                    'How do interventions work?',
                    'Open a student profile and click "New intervention" in the three-dot menu. Interventions track a before and after score with a review date.',
                ],
            ];
        @endphp

        <div style="display: flex; flex-direction: column; gap: 10px;">
            @foreach ($faqs as $i => [$question, $answer])
                <details style="border: 1px solid var(--pts-grey-light); border-radius: 10px; padding: 0;
                                        background: #fff; overflow: hidden;">
                    <summary style="cursor: pointer; padding: 14px 18px; font-size: 13.5px; font-weight: 700;
                                            color: #111827; list-style: none; display: flex; align-items: center; gap: 10px;">
                        <i class="fa-solid fa-circle-question" style="color: var(--pts-green-dark); font-size: 14px;"></i>
                        <span style="flex: 1;">{{ $question }}</span>
                        <i class="fa-solid fa-chevron-down" style="color: var(--pts-grey); font-size: 11px;"></i>
                    </summary>
                    <div style="padding: 0 18px 16px 42px; font-size: 13px; color: #374151; line-height: 1.6;">
                        {{ $answer }}
                    </div>
                </details>
            @endforeach
        </div>
    </div>

    {{-- Contact --}}
    <div>
        <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em;
                        color: var(--pts-green-dark); margin-bottom: 12px; padding-bottom: 8px;
                        border-bottom: 1px solid var(--pts-grey-light);">
            Contact Support
        </div>

        <div style="padding: 18px; background: var(--pts-bg); border-radius: 12px;">
            <div style="display: flex; align-items: center; gap: 14px; margin-bottom: 12px;">
                <div style="width: 42px; height: 42px; border-radius: 10px; background: var(--pts-green-light);
                                color: var(--pts-green-dark); display: flex; align-items: center;
                                justify-content: center; flex-shrink: 0;">
                    <i class="fa-solid fa-headset"></i>
                </div>
                <div>
                    <div style="font-size: 14px; font-weight: 700; color: #111827;">Pro Tech Solutions Support</div>
                    <div style="font-size: 12.5px; color: var(--pts-grey);">Mon–Fri · 08:00–17:00 CAT</div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-top: 14px;">
                <div>
                    <div style="font-size: 10.5px; text-transform: uppercase; letter-spacing: 0.06em;
                                    color: var(--pts-grey); font-weight: 700; margin-bottom: 4px;">
                        Email
                    </div>
                    <a href="mailto:support@protech.mw"
                        style="font-size: 13px; color: var(--pts-green-dark); text-decoration: none; font-weight: 600;">
                        support@protech.mw
                    </a>
                </div>
                <div>
                    <div style="font-size: 10.5px; text-transform: uppercase; letter-spacing: 0.06em;
                                    color: var(--pts-grey); font-weight: 700; margin-bottom: 4px;">
                        Phone
                    </div>
                    <div style="font-size: 13px; color: #111827; font-weight: 600;">
                        +265 1 234 567
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection