@extends('settings.partials._layout')

@section('settings-content')

    <a href="{{ route('settings.overview') }}" class="settings-back">
        <i class="fa-solid fa-arrow-left"></i> Back to Settings
    </a>

    <div class="settings-panel__title">Feedback Inbox</div>
    <div class="settings-panel__sub">
        Report a bug, request a feature, or ask a question. Your feedback goes directly to the Pro Tech team.
    </div>

    @if ($errors->any())
        <div style="background:#FEF2F2; color:#991B1B; border:1px solid #FECACA;
                            padding:12px 16px; border-radius:9px; font-size:13px; margin-bottom:18px;">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- Submit form --}}
    <div style="padding: 20px; background: var(--pts-bg); border-radius: 12px; margin-bottom: 28px;">
        <div style="font-size: 13px; font-weight: 800; color: #111827; margin-bottom: 14px;">
            <i class="fa-solid fa-paper-plane" style="color: var(--pts-green-dark);"></i> Send feedback
        </div>

        <form method="POST" action="{{ route('settings.feedback.submit') }}">
            @csrf

            <div style="margin-bottom: 14px;">
                <label style="display:block; font-size:11.5px; font-weight:700; color:#374151; margin-bottom:6px;">
                    Type
                </label>
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px;">
                    @foreach (['bug' => 'Bug report', 'feature' => 'Feature request', 'question' => 'Question', 'other' => 'Other'] as $value => $label)
                        <label style="display:flex; align-items:center; gap:8px; padding: 9px 12px; background:#fff;
                                              border: 1.5px solid var(--pts-grey-light); border-radius: 9px; cursor: pointer;
                                              font-size: 12.5px; font-weight: 600; color: #374151;">
                            <input type="radio" name="type" value="{{ $value }}" @checked($value === 'bug')
                                style="accent-color: var(--pts-green);">
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div style="margin-bottom: 14px;">
                <label style="display:block; font-size:11.5px; font-weight:700; color:#374151; margin-bottom:6px;">
                    Subject
                </label>
                <input type="text" name="subject" value="{{ old('subject') }}" required maxlength="150"
                    placeholder="Short summary of your feedback"
                    style="width:100%; padding:10px 14px; border:1.5px solid var(--pts-grey-light);
                                  border-radius:9px; font-size:13.5px; font-family:inherit; outline:none; background:#fff;">
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display:block; font-size:11.5px; font-weight:700; color:#374151; margin-bottom:6px;">
                    Details
                </label>
                <textarea name="message" rows="5" required minlength="10" maxlength="2000"
                    placeholder="What happened? What were you trying to do? The more detail the better." style="width:100%; padding:10px 14px; border:1.5px solid var(--pts-grey-light);
                                     border-radius:9px; font-size:13.5px; font-family:inherit; outline:none;
                                     resize:vertical; background:#fff;">{{ old('message') }}</textarea>
            </div>

            <button type="submit" style="padding: 11px 24px; background: var(--pts-black); color: #fff; border: none;
                               border-radius: 9px; font-size: 13px; font-weight: 700; cursor: pointer;">
                <i class="fa-solid fa-paper-plane"></i> Submit Feedback
            </button>
        </form>
    </div>

    {{-- Submitted entries --}}
    <div>
        <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em;
                        color: var(--pts-green-dark); margin-bottom: 12px; padding-bottom: 8px;
                        border-bottom: 1px solid var(--pts-grey-light);">
            Recent Submissions
        </div>

        @if (empty($submitted))
            <div style="padding: 30px; text-align: center; background: var(--pts-bg); border-radius: 12px;">
                <i class="fa-regular fa-comment-dots" style="font-size: 28px; color: var(--pts-grey-light);
                                                                     display: block; margin-bottom: 10px;"></i>
                <div style="font-size: 13px; color: var(--pts-grey);">
                    No feedback submitted yet. Your first entry will appear here.
                </div>
            </div>
        @else
            <div style="display: flex; flex-direction: column; gap: 10px;">
                @foreach ($submitted as $entry)
                    @php
                        $typeColors = [
                            'bug' => ['bg' => '#FEE2E2', 'fg' => '#991B1B'],
                            'feature' => ['bg' => '#DBEAFE', 'fg' => '#1E40AF'],
                            'question' => ['bg' => '#FEF9C3', 'fg' => '#854D0E'],
                            'other' => ['bg' => '#F3F4F6', 'fg' => '#374151'],
                        ];
                        $tc = $typeColors[$entry['type']] ?? $typeColors['other'];
                    @endphp
                    <div
                        style="border: 1px solid var(--pts-grey-light); border-radius: 11px; padding: 14px 18px; background: #fff;">
                        <div
                            style="display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; margin-bottom: 8px;">
                            <div style="flex: 1;">
                                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                                    <span style="font-size: 9.5px; font-weight: 800; padding: 3px 8px; border-radius: 4px;
                                                             background: {{ $tc['bg'] }}; color: {{ $tc['fg'] }};
                                                             letter-spacing: 0.06em; text-transform: uppercase;">
                                        {{ $entry['type'] }}
                                    </span>
                                    <span style="font-size: 13.5px; font-weight: 700; color: #111827;">
                                        {{ $entry['subject'] }}
                                    </span>
                                </div>
                                <div style="font-size: 11.5px; color: var(--pts-grey);">
                                    {{ $entry['user'] }} · {{ \Carbon\Carbon::parse($entry['created_at'])->diffForHumans() }}
                                </div>
                            </div>
                            <form method="POST" action="{{ route('settings.feedback.destroy') }}"
                                onsubmit="return confirm('Remove this entry?');" style="flex-shrink: 0;">
                                @csrf @method('DELETE')
                                <input type="hidden" name="id" value="{{ $entry['id'] }}">
                                <button type="submit" style="background: none; border: none; color: #DC2626; cursor: pointer;
                                                           font-size: 12px; padding: 4px 8px;" title="Remove">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </form>
                        </div>
                        <div style="font-size: 13px; color: #374151; line-height: 1.6; padding-top: 8px;
                                                border-top: 1px solid var(--pts-grey-light); white-space: pre-wrap;">
                            {{ $entry['message'] }}</div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

@endsection