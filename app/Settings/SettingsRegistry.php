<?php

namespace App\Settings;

class SettingsRegistry
{
    public static function groups(): array
    {
        return [
            'notifications' => [
                'label' => 'Notifications',
                'description' => 'Choose which automated emails and alerts are sent.',
                'fields' => [
                    'notify.report_card_sent' => ['type' => 'bool', 'default' => true, 'label' => 'Report card sent', 'description' => 'Send an email to a guardian when a report card is generated.'],
                    'notify.timetable_sent' => ['type' => 'bool', 'default' => false, 'label' => 'Timetable sent', 'description' => 'Send the class timetable to guardians when published.'],
                    'notify.guardian_update' => ['type' => 'bool', 'default' => true, 'label' => 'Guardian progress updates', 'description' => 'Send a summary email when a teacher posts a progress update.'],
                    'notify.absence_alert' => ['type' => 'bool', 'default' => false, 'label' => 'Absence alerts', 'description' => 'Notify guardians the same day their child is marked absent.'],
                    'notify.intervention_reminder' => ['type' => 'bool', 'default' => true, 'label' => 'Intervention review reminders', 'description' => 'Remind guardians when an intervention review date is approaching.'],
                    'notify.assessment_locked' => ['type' => 'bool', 'default' => true, 'label' => 'Assessment locked', 'description' => 'Notify the assessment creator when marking is locked.'],
                    'notify.marking_reminder' => ['type' => 'bool', 'default' => false, 'label' => 'Marking reminders', 'description' => 'Remind teachers to mark assessments that are past due.'],
                    'notify.weekly_summary' => ['type' => 'bool', 'default' => true, 'label' => 'Weekly summary', 'description' => 'Send admins a weekly digest of school performance.'],
                    'notify.at_risk_digest' => ['type' => 'bool', 'default' => true, 'label' => 'At-risk student digest', 'description' => 'Send a daily list of students flagged as at-risk.'],
                    'notify.new_staff_added' => ['type' => 'bool', 'default' => true, 'label' => 'New staff added', 'description' => 'Notify admins when a new staff member is created.'],
                    'notify.password_changed' => ['type' => 'bool', 'default' => true, 'label' => 'Password changed', 'description' => 'Send a confirmation when a user changes their password.'],
                    'notify.login_from_new_device' => ['type' => 'bool', 'default' => false, 'label' => 'Login from new device', 'description' => 'Alert users when their account signs in from an unrecognized device.'],
                ],
            ],
            'student_portal' => [
                'label' => 'Student Portal',
                'description' => 'Control what students can see and do on their portal.',
                'fields' => [
                    'portal.students_enabled' => ['type' => 'bool', 'default' => true, 'label' => 'Enable student portal', 'description' => 'Allow students to sign in and view their information.'],
                    'portal.show_grades' => ['type' => 'bool', 'default' => true, 'label' => 'Show grades', 'description' => 'Students can see their marks and grades.'],
                    'portal.show_rank' => ['type' => 'bool', 'default' => false, 'label' => 'Show class rank', 'description' => 'Students can see their position in class.'],
                    'portal.show_attendance' => ['type' => 'bool', 'default' => true, 'label' => 'Show attendance', 'description' => 'Students can see their attendance record.'],
                    'portal.show_interventions' => ['type' => 'bool', 'default' => false, 'label' => 'Show interventions', 'description' => 'Students can see interventions targeted at them.'],
                    'portal.show_report_cards' => ['type' => 'bool', 'default' => true, 'label' => 'Show report cards', 'description' => 'Students can download their report cards.'],
                    'portal.allow_self_profile_edit' => ['type' => 'bool', 'default' => false, 'label' => 'Allow self profile edit', 'description' => 'Students can edit their own contact details.'],
                    'portal.show_upcoming_assessments' => ['type' => 'bool', 'default' => true, 'label' => 'Show upcoming assessments', 'description' => 'Students can see assessments that are scheduled.'],
                ],
            ],
            'guardian_portal' => [
                'label' => 'Guardian Portal',
                'description' => 'Control what parents and guardians can see and do.',
                'fields' => [
                    'guardian.enabled' => ['type' => 'bool', 'default' => true, 'label' => 'Enable guardian portal', 'description' => 'Allow guardians to sign in and view their children.'],
                    'guardian.show_grades' => ['type' => 'bool', 'default' => true, 'label' => 'Show grades', 'description' => 'Guardians can see their child marks and grades.'],
                    'guardian.show_attendance' => ['type' => 'bool', 'default' => true, 'label' => 'Show attendance', 'description' => 'Guardians can see their child attendance record.'],
                    'guardian.show_interventions' => ['type' => 'bool', 'default' => true, 'label' => 'Show interventions', 'description' => 'Guardians can see interventions targeted at their child.'],
                    'guardian.show_report_cards' => ['type' => 'bool', 'default' => true, 'label' => 'Show report cards', 'description' => 'Guardians can download their child report cards.'],
                    'guardian.can_message_teachers' => ['type' => 'bool', 'default' => false, 'label' => 'Allow messaging teachers', 'description' => 'Guardians can send messages to teachers through the portal.'],
                    'guardian.can_request_meetings' => ['type' => 'bool', 'default' => true, 'label' => 'Allow meeting requests', 'description' => 'Guardians can request parent-teacher meetings.'],
                    'guardian.multiple_children' => ['type' => 'bool', 'default' => true, 'label' => 'Multiple children per login', 'description' => 'Guardians with several children see them all under one account.'],
                ],
            ],
            'security' => [
                'label' => 'Security',
                'description' => 'Login rules and account protection.',
                'fields' => [
                    'security.force_https' => ['type' => 'bool', 'default' => false, 'label' => 'Force HTTPS', 'description' => 'Redirect every request to HTTPS.'],
                    'security.alert_new_device' => ['type' => 'bool', 'default' => true, 'label' => 'Alert on new device', 'description' => 'Email users when their account signs in from a new device.'],
                    'security.lockout_after_attempts' => ['type' => 'bool', 'default' => true, 'label' => 'Lockout after failed attempts', 'description' => 'Temporarily block a login after too many failed attempts.'],
                    'security.lockout_attempts' => ['type' => 'int', 'default' => 5, 'label' => 'Attempts before lockout', 'description' => 'Number of failed logins before a temporary lock.'],
                    'security.lockout_minutes' => ['type' => 'int', 'default' => 15, 'label' => 'Lockout duration (minutes)', 'description' => 'How long the lock lasts.'],
                    'security.password_reset_expiry' => ['type' => 'int', 'default' => 60, 'label' => 'Password reset link expiry', 'description' => 'Minutes before a reset link expires.'],
                    'security.admin_ip_whitelist' => ['type' => 'bool', 'default' => false, 'label' => 'Admin IP whitelist', 'description' => 'Restrict admin access to whitelisted IP addresses.'],
                    'security.require_email_verification' => ['type' => 'bool', 'default' => false, 'label' => 'Require email verification', 'description' => 'Users must verify their email before signing in.'],
                ],
            ],
            'password' => [
                'label' => 'Password Policy',
                'description' => 'Rules applied when users set or change passwords.',
                'fields' => [
                    'password.min_length' => ['type' => 'int', 'default' => 8, 'label' => 'Minimum length', 'description' => 'Shortest allowed password.'],
                    'password.require_uppercase' => ['type' => 'bool', 'default' => true, 'label' => 'Require uppercase', 'description' => 'At least one uppercase letter.'],
                    'password.require_lowercase' => ['type' => 'bool', 'default' => true, 'label' => 'Require lowercase', 'description' => 'At least one lowercase letter.'],
                    'password.require_number' => ['type' => 'bool', 'default' => true, 'label' => 'Require number', 'description' => 'At least one number.'],
                    'password.require_symbol' => ['type' => 'bool', 'default' => false, 'label' => 'Require symbol', 'description' => 'At least one symbol.'],
                    'password.expiry_days' => ['type' => 'int', 'default' => 0, 'label' => 'Password expiry (days)', 'description' => 'Days before a password must be changed. 0 disables expiry.'],
                    'password.prevent_reuse_count' => ['type' => 'int', 'default' => 3, 'label' => 'Prevent reuse count', 'description' => 'How many past passwords cannot be reused.'],
                ],
            ],
            'two_factor' => [
                'label' => 'Two-Factor Authentication',
                'description' => 'Additional verification step at login.',
                'fields' => [
                    '2fa.available' => ['type' => 'bool', 'default' => true, 'label' => 'Enable 2FA', 'description' => 'Allow users to enable two-factor authentication.'],
                    '2fa.required_admins' => ['type' => 'bool', 'default' => false, 'label' => 'Require for admins', 'description' => 'Force 2FA on all admin accounts.'],
                    '2fa.required_teachers' => ['type' => 'bool', 'default' => false, 'label' => 'Require for teachers', 'description' => 'Force 2FA on all teacher accounts.'],
                    '2fa.required_all' => ['type' => 'bool', 'default' => false, 'label' => 'Require for all users', 'description' => 'Force 2FA on every user.'],
                    '2fa.method_email' => ['type' => 'bool', 'default' => true, 'label' => 'Allow email codes', 'description' => 'Users can receive a code by email.'],
                    '2fa.method_totp' => ['type' => 'bool', 'default' => false, 'label' => 'Allow authenticator apps', 'description' => 'Users can use Google Authenticator or similar.'],
                    '2fa.remember_days' => ['type' => 'int', 'default' => 30, 'label' => 'Remember device (days)', 'description' => 'How long a trusted device stays trusted.'],
                ],
            ],
            'sessions' => [
                'label' => 'Session Management',
                'description' => 'How long users stay signed in.',
                'fields' => [
                    'session.lifetime_minutes' => ['type' => 'int', 'default' => 120, 'label' => 'Session lifetime (minutes)', 'description' => 'Minutes of inactivity before sign out.'],
                    'session.force_logout_on_password' => ['type' => 'bool', 'default' => true, 'label' => 'Log out on password change', 'description' => 'Sign out every device when the password changes.'],
                    'session.single_device_only' => ['type' => 'bool', 'default' => false, 'label' => 'Single device only', 'description' => 'Only one active device per user.'],
                    'session.remember_me_days' => ['type' => 'int', 'default' => 30, 'label' => 'Remember me duration (days)', 'description' => 'Days a remembered session stays valid.'],
                    'session.log_out_on_browser_close' => ['type' => 'bool', 'default' => false, 'label' => 'Log out on browser close', 'description' => 'End the session when the browser closes.'],
                ],
            ],
            'policy' => [
                'label' => 'Assessment Policy',
                'description' => 'How final marks are calculated.',
                'fields' => [
                    'policy.rounding' => ['type' => 'string', 'default' => 'nearest_int', 'label' => 'Rounding method', 'description' => 'How final percentages are rounded.'],
                    'policy.auto_calculate_final' => ['type' => 'bool', 'default' => true, 'label' => 'Auto-calculate final marks', 'description' => 'Compute final marks automatically.'],
                    'policy.allow_teacher_override' => ['type' => 'bool', 'default' => false, 'label' => 'Allow teacher override', 'description' => 'Teachers can override calculated final marks.'],
                    'policy.require_weights_complete' => ['type' => 'bool', 'default' => true, 'label' => 'Require weights to sum to 100', 'description' => 'Save is blocked if weights do not sum.'],
                    'policy.default_ca_cap' => ['type' => 'int', 'default' => 40, 'label' => 'Default CA cap (%)', 'description' => 'Upper limit on continuous assessment contribution.'],
                ],
            ],
            'peer' => [
                'label' => 'Peer Comparison',
                'description' => 'What students and guardians see about peers.',
                'fields' => [
                    'peer.show_rank' => ['type' => 'bool', 'default' => false, 'label' => 'Show rank', 'description' => 'Display the student class position.'],
                    'peer.show_percentile' => ['type' => 'bool', 'default' => true, 'label' => 'Show percentile', 'description' => 'Display the student percentile.'],
                    'peer.show_class_average' => ['type' => 'bool', 'default' => true, 'label' => 'Show class average', 'description' => 'Display the class average.'],
                    'peer.show_subject_average' => ['type' => 'bool', 'default' => true, 'label' => 'Show subject average', 'description' => 'Display the subject average.'],
                    'peer.show_highest_lowest' => ['type' => 'bool', 'default' => false, 'label' => 'Show highest and lowest', 'description' => 'Display the highest and lowest mark in class.'],
                    'peer.hide_names' => ['type' => 'bool', 'default' => true, 'label' => 'Hide other student names', 'description' => 'Never reveal other students names.'],
                    'peer.min_class_size' => ['type' => 'int', 'default' => 5, 'label' => 'Minimum class size', 'description' => 'Do not show comparisons for classes below this size.'],
                ],
            ],
            'retention' => [
                'label' => 'Data Retention',
                'description' => 'How long data is kept before archiving or deleting.',
                'fields' => [
                    'retention.assessment_results_years' => ['type' => 'int', 'default' => 7, 'label' => 'Assessment results (years)', 'description' => 'Years to keep assessment results.'],
                    'retention.student_marks_years' => ['type' => 'int', 'default' => 7, 'label' => 'Student marks (years)', 'description' => 'Years to keep student marks.'],
                    'retention.audit_log_months' => ['type' => 'int', 'default' => 24, 'label' => 'Audit log (months)', 'description' => 'Months to keep audit log entries.'],
                    'retention.inactive_students_years' => ['type' => 'int', 'default' => 10, 'label' => 'Inactive students (years)', 'description' => 'Years before inactive student records archive.'],
                    'retention.auto_archive_graduates' => ['type' => 'bool', 'default' => true, 'label' => 'Auto-archive graduates', 'description' => 'Archive graduated students automatically.'],
                    'retention.keep_report_cards_years' => ['type' => 'int', 'default' => 99, 'label' => 'Report cards (years)', 'description' => 'Years to keep report cards.'],
                ],
            ],
            'features' => [
                'label' => 'Feature Flags',
                'description' => 'Turn whole modules on or off for this school.',
                'fields' => [
                    'features.interventions' => ['type' => 'bool', 'default' => true, 'label' => 'Interventions', 'description' => 'Enable the interventions module.'],
                    'features.analytics' => ['type' => 'bool', 'default' => true, 'label' => 'Analytics', 'description' => 'Enable question and cohort analytics.'],
                    'features.report_cards' => ['type' => 'bool', 'default' => true, 'label' => 'Report cards', 'description' => 'Enable report cards.'],
                    'features.guardian_portal' => ['type' => 'bool', 'default' => true, 'label' => 'Guardian portal', 'description' => 'Enable the guardian portal.'],
                    'features.student_portal' => ['type' => 'bool', 'default' => true, 'label' => 'Student portal', 'description' => 'Enable the student portal.'],
                    'features.public_portal' => ['type' => 'bool', 'default' => false, 'label' => 'Public portal', 'description' => 'Allow public read-only access to school information.'],
                    'features.sms_notifications' => ['type' => 'bool', 'default' => false, 'label' => 'SMS notifications', 'description' => 'Enable SMS notifications.'],
                    'features.bulk_import' => ['type' => 'bool', 'default' => true, 'label' => 'Bulk import', 'description' => 'Enable CSV bulk import for students and staff.'],
                    'features.api_access' => ['type' => 'bool', 'default' => false, 'label' => 'API access', 'description' => 'Enable API tokens and external integrations.'],
                ],
            ],
            'branding' => [
                'label' => 'Branding & Appearance',
                'description' => 'Theme, login page, and default colours.',
                'fields' => [
                    'branding.default_theme' => ['type' => 'string', 'default' => 'light', 'label' => 'Default theme', 'description' => 'Light, dark, or system.'],
                    'branding.allow_user_switch' => ['type' => 'bool', 'default' => true, 'label' => 'Allow user theme switch', 'description' => 'Users can override the default theme.'],
                    'branding.dark_accent_auto' => ['type' => 'bool', 'default' => true, 'label' => 'Auto dark accent', 'description' => 'Adjust the accent colour for dark mode.'],
                    'branding.login_bg_overlay' => ['type' => 'int', 'default' => 55, 'label' => 'Login background overlay', 'description' => 'Darkening percentage of the login background.'],
                    'branding.login_bg_blur' => ['type' => 'int', 'default' => 0, 'label' => 'Login background blur', 'description' => 'Blur amount for the login background.'],
                ],
            ],
            'locale' => [
                'label' => 'Localization',
                'description' => 'Language, date, time, and number formats.',
                'fields' => [
                    'locale.language' => ['type' => 'string', 'default' => 'en', 'label' => 'Language', 'description' => 'Default interface language.'],
                    'locale.date_format' => ['type' => 'string', 'default' => 'd M Y', 'label' => 'Date format', 'description' => 'How dates are displayed.'],
                    'locale.time_format' => ['type' => 'string', 'default' => 'H:i', 'label' => 'Time format', 'description' => 'How times are displayed.'],
                    'locale.week_start' => ['type' => 'string', 'default' => 'monday', 'label' => 'Week starts on', 'description' => 'First day of the week.'],
                    'locale.number_format' => ['type' => 'string', 'default' => '1,234.56', 'label' => 'Number format', 'description' => 'How numbers are grouped and separated.'],
                ],
            ],
        ];
    }

    public static function fields(): array
    {
        $fields = [];
        foreach (static::groups() as $group) {
            foreach ($group['fields'] as $key => $definition) {
                $fields[$key] = $definition;
            }
        }
        return $fields;
    }

    public static function keys(): array
    {
        return array_keys(static::fields());
    }

    public static function defaults(): array
    {
        $defaults = [];
        foreach (static::fields() as $key => $definition) {
            $defaults[$key] = $definition['default'];
        }
        return $defaults;
    }

    public static function group(string $groupKey): ?array
    {
        return static::groups()[$groupKey] ?? null;
    }
}