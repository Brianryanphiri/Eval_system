@extends('settings.partials._layout')

@section('settings-content')

    <a href="{{ route('settings.overview') }}" class="settings-back">
        <i class="fa-solid fa-arrow-left"></i> Back to Settings
    </a>

    <div class="settings-panel__title">Email Templates</div>
    <div class="settings-panel__sub">
        Customise the wording of automated emails. Use <code
            style="background: var(--pts-bg); padding: 2px 6px; border-radius: 4px; font-size: 12px;">@{{variable}}</code>
        placeholders — they're replaced with real data when the email is sent.
    </div>

    @if ($errors->any())
        <div style="background:#FEF2F2; color:#991B1B; border:1px solid #FECACA;
                            padding:12px 16px; border-radius:9px; font-size:13px; margin-bottom:18px;">
            {{ $errors->first() }}
        </div>
    @endif

    @php
        $availableVars = [
            'report_card' => ['student_name', 'student_number', 'school_name', 'term', 'academic_year'],
            'guardian_update' => ['student_name', 'custom_message', 'school_name'],
            'password_reset' => ['reset_link', 'email', 'school_name'],
            'absence_alert' => ['student_name', 'date', 'school_name'],
            'intervention_reminder' => ['student_name', 'review_date', 'school_name'],
        ];
    @endphp

    <div style="display: flex; flex-direction: column; gap: 14px;">
        @foreach ($templates as $tpl)
            @php $vars = $availableVars[$tpl['key']] ?? []; @endphp
            <details style="border: 1px solid var(--pts-grey-light); border-radius: 12px; overflow: hidden; background: #fff;">
                <summary style="padding: 16px 20px; cursor: pointer; display: flex; align-items: center;
                                        gap: 12px; list-style: none;">
                    <i class="fa-solid fa-envelope-open" style="color: var(--pts-green-dark);"></i>
                    <div style="flex: 1;">
                        <div style="font-size: 13.5px; font-weight: 700; color: #111827;">
                            {{ $tpl['name'] }}
                        </div>
                        <div style="font-size: 11.5px; color: var(--pts-grey); margin-top: 2px;">
                            {{ $tpl['subject'] }}
                        </div>
                    </div>
                    @if ($tpl['is_custom'])
                        <span style="font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 4px;
                                                 background: var(--pts-green-light); color: var(--pts-green-dark);">
                            CUSTOMISED
                        </span>
                    @endif
                    @if (!$tpl['is_active'])
                        <span style="font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 4px;
                                                 background: #FEF2F2; color: #991B1B;">
                            DISABLED
                        </span>
                    @endif
                    <i class="fa-solid fa-chevron-down" style="color: var(--pts-grey); font-size: 11px;"></i>
                </summary>

                <div style="padding: 0 20px 20px; border-top: 1px solid var(--pts-grey-light);">
                    <form method="POST" action="{{ route('settings.email-templates.update') }}" style="margin-top: 16px;">
                        @csrf
                        <input type="hidden" name="key" value="{{ $tpl['key'] }}">

                        <div style="margin-bottom: 12px;">
                            <label
                                style="display:block; font-size:11px; font-weight:700; color:#374151; margin-bottom:5px; text-transform:uppercase; letter-spacing:0.04em;">
                                Subject line
                            </label>
                            <input type="text" name="subject" value="{{ $tpl['subject'] }}" required maxlength="255" style="width:100%; padding:10px 12px; border:1.5px solid var(--pts-grey-light);
                                                  border-radius:9px; font-size:13px; font-family:inherit; outline:none;">
                        </div>

                        <div style="margin-bottom: 12px;">
                            <label
                                style="display:block; font-size:11px; font-weight:700; color:#374151; margin-bottom:5px; text-transform:uppercase; letter-spacing:0.04em;">
                                Body
                            </label>
                            <textarea name="body" rows="8" required maxlength="5000"
                                style="width:100%; padding:10px 12px; border:1.5px solid var(--pts-grey-light);
                                                     border-radius:9px; font-size:13px; font-family:'Courier New', monospace;
                                                     outline:none; resize:vertical; line-height: 1.6;">{{ $tpl['body'] }}</textarea>
                        </div>

                        @if (!empty($vars))
                            <div style="padding: 10px 14px; background: var(--pts-bg); border-radius: 8px;
                                                    font-size: 11.5px; color: var(--pts-grey); margin-bottom: 14px;">
                                <strong style="color: #111827;">Available variables:</strong>
                                @foreach ($vars as $v)
                                    <code
                                        style="background: #fff; padding: 2px 6px; border-radius: 4px;
                                                                 margin-left: 4px; font-size: 11px;">{{ '{' . '{' . $v . '}' . '}' }}</code>
                                @endforeach
                            </div>
                        @endif

                        <div style="display: flex; align-items: center; justify-content: space-between;
                                            padding-top: 14px; border-top: 1px solid var(--pts-grey-light);">
                            <label style="display:flex; align-items:center; gap:8px; font-size: 12.5px; cursor: pointer;">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1" @checked($tpl['is_active'])
                                    style="width: 16px; height: 16px; accent-color: var(--pts-green);">
                                Send this email
                            </label>

                            <div style="display: flex; gap: 8px;">
                                @if ($tpl['is_custom'])
                                    <button type="button"
                                        onclick="if (confirm('Reset to default wording?')) document.getElementById('reset-{{ $tpl['key'] }}').submit();"
                                        style="padding: 9px 16px; background: #fff; border: 1.5px solid var(--pts-grey-light);
                                                               border-radius: 9px; font-size: 12.5px; font-weight: 700;
                                                               color: #374151; cursor: pointer;">
                                        <i class="fa-solid fa-rotate-left"></i> Reset
                                    </button>
                                @endif
                                <button type="submit" style="padding: 9px 20px; background: var(--pts-black); color: #fff;
                                                       border: none; border-radius: 9px; font-size: 12.5px;
                                                       font-weight: 700; cursor: pointer;">
                                    <i class="fa-solid fa-check"></i> Save
                                </button>
                            </div>
                        </div>
                    </form>

                    @if ($tpl['is_custom'])
                        <form method="POST" action="{{ route('settings.email-templates.reset') }}" id="reset-{{ $tpl['key'] }}"
                            style="display: none;">
                            @csrf @method('DELETE')
                            <input type="hidden" name="key" value="{{ $tpl['key'] }}">
                        </form>
                    @endif
                </div>
            </details>
        @endforeach
    </div>

@endsection