@extends('settings.partials._layout')

@section('settings-content')

    <a href="{{ route('settings.overview') }}" class="settings-back">
        <i class="fa-solid fa-arrow-left"></i> Back to Settings
    </a>

    <div class="settings-panel__title">Notifications</div>
    <div class="settings-panel__sub">
        Choose which automated emails and alerts are sent to your staff and guardians.
    </div>

    @if ($errors->any())
        <div style="background:#FEF2F2; color:#991B1B; border:1px solid #FECACA;
                            padding:12px 16px; border-radius:9px; font-size:13px; margin-bottom:18px;">
            {{ $errors->first() }}
        </div>
    @endif

    <x-settings.form
        :action="route('settings.notifications.update')"
        :cancel="route('settings.overview')"
        submit-label="Save Preferences"
    >
        @foreach (['Guardian & Parent Emails' => [
            'notify.report_card_sent' => ['Report card sent', 'Send an email to a guardian when a report card is generated for their child.'],
            'notify.timetable_sent' => ['Timetable sent', 'Send a class timetable to guardians when it is published.'],
            'notify.guardian_update' => ['Guardian progress updates', 'Send a summary email when a teacher posts a progress update.'],
            'notify.absence_alert' => ['Absence alerts', 'Notify guardians the same day their child is marked absent.'],
            'notify.intervention_reminder' => ['Intervention review reminders', 'Remind guardians when an intervention review date is approaching.'],
        ], 'Staff Emails' => [
            'notify.assessment_locked' => ['Assessment locked', 'Notify the assessment creator when marking is locked.'],
            'notify.marking_reminder' => ['Marking reminders', 'Remind teachers to mark assessments that are past due.'],
            'notify.weekly_summary' => ['Weekly summary', 'Send admins a weekly digest of school performance and alerts.'],
            'notify.at_risk_digest' => ['At-risk student digest', 'Send a daily list of students flagged as at-risk to admins.'],
        ], 'System Emails' => [
            'notify.new_staff_added' => ['New staff added', 'Notify admins when a new staff member is created.'],
            'notify.password_changed' => ['Password changed', 'Send a confirmation email when a user changes their password.'],
            'notify.login_from_new_device' => ['Login from new device', 'Alert users when their account is signed in from an unrecognized device.'],
        ]] as $groupLabel => $toggles)
            <x-settings.section :title="$groupLabel">
                @foreach ($toggles as $key => [$title, $desc])
                    <x-settings.toggle
                        :name="$key"
                        :title="$title"
                        :description="$desc"
                        :value="old($key, $settings[$key] ?? false)"
                    />
                @endforeach
            </x-settings.section>
        @endforeach
    </x-settings.form>

@endsection