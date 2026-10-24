<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Assessment;
use App\Models\AssessmentType;
use App\Models\AssessmentWeight;
use App\Models\AuditLog;
use App\Models\CommentBank;
use App\Models\EmailTemplate;
use App\Models\GradingScale;
use App\Models\Permission;
use App\Models\WeightingTemplate;
use App\Models\WeightingTemplateItem;
use App\Services\Settings;
use App\Settings\SettingsRegistry;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SettingsController extends Controller
{
    public function overview()
    {
        $school = auth()->user()->school;

        return view('settings.overview', [
            'school' => $school,
            'typeCount' => AssessmentType::where('school_id', $school->id)->count(),
            'scaleCount' => GradingScale::where('school_id', $school->id)->count(),
            'permissionCount' => Permission::count(),
            'yearCount' => AcademicYear::where('school_id', $school->id)->count(),
        ]);
    }

    public function profile()
    {
        $currencies = [
            'MWK' => 'Malawian Kwacha',
            'USD' => 'US Dollar',
            'GBP' => 'British Pound',
            'EUR' => 'Euro',
            'ZAR' => 'South African Rand',
            'KES' => 'Kenyan Shilling',
            'TZS' => 'Tanzanian Shilling',
            'UGX' => 'Ugandan Shilling',
            'ZMW' => 'Zambian Kwacha',
            'BWP' => 'Botswana Pula',
            'NGN' => 'Nigerian Naira',
            'GHS' => 'Ghanaian Cedi',
        ];

        return view('settings.profile', [
            'school' => auth()->user()->school,
            'timezones' => \DateTimeZone::listIdentifiers(),
            'currencies' => $currencies,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $school = auth()->user()->school;

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'motto' => 'nullable|string|max:150',
            'principal_name' => 'nullable|string|max:150',
            'institution_type' => 'nullable|string|in:primary,secondary,international,college',
            'academic_system' => 'nullable|string|in:primary,secondary,igcse,ib,alevel',
            'country' => 'nullable|string|max:100',
            'timezone' => 'nullable|string|max:50',
            'registration_number' => 'nullable|string|max:100',
            'founded_year' => 'nullable|integer|min:1800|max:' . (date('Y') + 1),
            'address_street' => 'nullable|string|max:200',
            'address_city' => 'nullable|string|max:100',
            'address_province' => 'nullable|string|max:100',
            'address_postal_code' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:50',
            'phone_secondary' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'email_signature' => 'nullable|string|max:1000',
            'primary_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'accent_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'public_portal_enabled' => 'nullable|boolean',
        ]);

        $data['public_portal_enabled'] = $request->boolean('public_portal_enabled');

        if ($request->hasFile('logo')) {
            $request->validate(['logo' => 'image|max:2048']);
            if ($school->logo_path) {
                \Storage::disk('public')->delete($school->logo_path);
            }
            $data['logo_path'] = $request->file('logo')->store('school-logos', 'public');
        }

        if ($request->hasFile('favicon')) {
            $request->validate(['favicon' => 'file|max:512|mimes:png,ico,jpg,jpeg,svg']);
            if ($school->favicon_path) {
                \Storage::disk('public')->delete($school->favicon_path);
            }
            $data['favicon_path'] = $request->file('favicon')->store('school-favicons', 'public');
        }

        if ($request->hasFile('principal_signature')) {
            $request->validate(['principal_signature' => 'image|max:1024']);
            if ($school->principal_signature_path) {
                \Storage::disk('public')->delete($school->principal_signature_path);
            }
            $data['principal_signature_path'] = $request->file('principal_signature')->store('school-signatures', 'public');
        }

        $school->update($data);

        return redirect()
            ->route('settings.profile')
            ->with('success', 'Institution profile updated.');
    }

    public function grading()
    {
        $school = auth()->user()->school;
        $bands = GradingScale::forSchool($school->id)->get();

        return view('settings.grading', ['bands' => $bands]);
    }

    public function storeGradeBand(Request $request)
    {
        $school = auth()->user()->school;

        $data = $request->validate([
            'letter' => 'required|string|max:5',
            'min_score' => 'required|integer|min:0|max:100',
            'max_score' => 'required|integer|min:0|max:100|gte:min_score',
            'interpretation' => 'required|string|max:50',
        ]);

        $overlap = GradingScale::where('school_id', $school->id)
            ->where(function ($q) use ($data) {
                $q->whereBetween('min_score', [$data['min_score'], $data['max_score']])
                    ->orWhereBetween('max_score', [$data['min_score'], $data['max_score']])
                    ->orWhere(function ($q2) use ($data) {
                        $q2->where('min_score', '<=', $data['min_score'])
                            ->where('max_score', '>=', $data['max_score']);
                    });
            })
            ->exists();

        if ($overlap) {
            return back()
                ->withErrors(['min_score' => "The range {$data['min_score']}-{$data['max_score']} overlaps an existing band."])
                ->withInput();
        }

        GradingScale::create([
            'school_id' => $school->id,
            'letter' => strtoupper($data['letter']),
            'min_score' => $data['min_score'],
            'max_score' => $data['max_score'],
            'interpretation' => $data['interpretation'],
        ]);

        return back()->with('success', "Grade band {$data['letter']} added.");
    }

    public function updateGradeBand(Request $request, GradingScale $band)
    {
        abort_unless($band->school_id === auth()->user()->school_id, 403);

        $data = $request->validate([
            'letter' => 'required|string|max:5',
            'min_score' => 'required|integer|min:0|max:100',
            'max_score' => 'required|integer|min:0|max:100|gte:min_score',
            'interpretation' => 'required|string|max:50',
        ]);

        $overlap = GradingScale::where('school_id', $band->school_id)
            ->where('id', '!=', $band->id)
            ->where(function ($q) use ($data) {
                $q->whereBetween('min_score', [$data['min_score'], $data['max_score']])
                    ->orWhereBetween('max_score', [$data['min_score'], $data['max_score']])
                    ->orWhere(function ($q2) use ($data) {
                        $q2->where('min_score', '<=', $data['min_score'])
                            ->where('max_score', '>=', $data['max_score']);
                    });
            })
            ->exists();

        if ($overlap) {
            return back()->withErrors([
                'min_score' => "The range {$data['min_score']}-{$data['max_score']} overlaps another band.",
            ]);
        }

        $band->update([
            'letter' => strtoupper($data['letter']),
            'min_score' => $data['min_score'],
            'max_score' => $data['max_score'],
            'interpretation' => $data['interpretation'],
        ]);

        return back()->with('success', 'Grade band updated.');
    }

    public function destroyGradeBand(GradingScale $band)
    {
        abort_unless($band->school_id === auth()->user()->school_id, 403);

        $band->delete();

        return back()->with('success', 'Grade band removed.');
    }

    public function notifications()
    {
        $group = SettingsRegistry::group('notifications');

        $defaults = [];
        foreach ($group['fields'] as $key => $definition) {
            $defaults[$key] = $definition['default'];
        }

        $settings = Settings::all($defaults);

        return view('settings.notifications', compact('settings'));
    }

    public function updateNotifications(Request $request)
    {
        $group = SettingsRegistry::group('notifications');

        $values = [];
        foreach (array_keys($group['fields']) as $key) {
            $values[$key] = $request->boolean($key);
        }

        Settings::setMany($values);

        return redirect()
            ->route('settings.notifications')
            ->with('success', 'Notification preferences saved.');
    }

    public function studentPortal()
    {
        $settings = Settings::all([
            'portal.students_enabled' => true,
            'portal.show_grades' => true,
            'portal.show_rank' => false,
            'portal.show_attendance' => true,
            'portal.show_interventions' => false,
            'portal.show_report_cards' => true,
            'portal.allow_self_profile_edit' => false,
            'portal.show_upcoming_assessments' => true,
        ]);

        return view('settings.student-portal', compact('settings'));
    }

    public function updateStudentPortal(Request $request)
    {
        $keys = [
            'portal.students_enabled',
            'portal.show_grades',
            'portal.show_rank',
            'portal.show_attendance',
            'portal.show_interventions',
            'portal.show_report_cards',
            'portal.allow_self_profile_edit',
            'portal.show_upcoming_assessments',
        ];

        foreach ($keys as $key) {
            Settings::set($key, $request->boolean($key));
        }

        return redirect()
            ->route('settings.student-portal')
            ->with('success', 'Student portal settings saved.');
    }

    public function guardianPortal()
    {
        $settings = Settings::all([
            'guardian.enabled' => true,
            'guardian.show_grades' => true,
            'guardian.show_attendance' => true,
            'guardian.show_interventions' => true,
            'guardian.show_report_cards' => true,
            'guardian.can_message_teachers' => false,
            'guardian.can_request_meetings' => true,
            'guardian.multiple_children' => true,
        ]);

        return view('settings.guardian-portal', compact('settings'));
    }

    public function updateGuardianPortal(Request $request)
    {
        $keys = [
            'guardian.enabled',
            'guardian.show_grades',
            'guardian.show_attendance',
            'guardian.show_interventions',
            'guardian.show_report_cards',
            'guardian.can_message_teachers',
            'guardian.can_request_meetings',
            'guardian.multiple_children',
        ];

        foreach ($keys as $key) {
            Settings::set($key, $request->boolean($key));
        }

        return redirect()
            ->route('settings.guardian-portal')
            ->with('success', 'Guardian portal settings saved.');
    }

    public function security()
    {
        $settings = Settings::all([
            'security.force_https' => false,
            'security.alert_new_device' => true,
            'security.lockout_after_attempts' => true,
            'security.lockout_attempts' => 5,
            'security.lockout_minutes' => 15,
            'security.password_reset_expiry' => 60,
            'security.admin_ip_whitelist' => false,
            'security.require_email_verification' => false,
        ]);

        return view('settings.security', compact('settings'));
    }

    public function updateSecurity(Request $request)
    {
        $boolKeys = [
            'security.force_https',
            'security.alert_new_device',
            'security.lockout_after_attempts',
            'security.admin_ip_whitelist',
            'security.require_email_verification',
        ];

        foreach ($boolKeys as $key) {
            Settings::set($key, $request->boolean($key));
        }

        $intKeys = [
            'security.lockout_attempts',
            'security.lockout_minutes',
            'security.password_reset_expiry',
        ];

        foreach ($intKeys as $key) {
            Settings::set($key, (int) $request->input($key, 0));
        }

        return redirect()
            ->route('settings.security')
            ->with('success', 'Security settings saved.');
    }

    public function passwordPolicy()
    {
        $settings = Settings::all([
            'password.min_length' => 8,
            'password.require_uppercase' => true,
            'password.require_lowercase' => true,
            'password.require_number' => true,
            'password.require_symbol' => false,
            'password.expiry_days' => 0,
            'password.prevent_reuse_count' => 3,
        ]);

        return view('settings.password-policy', compact('settings'));
    }

    public function updatePasswordPolicy(Request $request)
    {
        $boolKeys = [
            'password.require_uppercase',
            'password.require_lowercase',
            'password.require_number',
            'password.require_symbol',
        ];

        foreach ($boolKeys as $key) {
            Settings::set($key, $request->boolean($key));
        }

        $intKeys = [
            'password.min_length',
            'password.expiry_days',
            'password.prevent_reuse_count',
        ];

        foreach ($intKeys as $key) {
            Settings::set($key, (int) $request->input($key, 0));
        }

        return redirect()
            ->route('settings.password-policy')
            ->with('success', 'Password policy saved.');
    }

    public function twoFactor()
    {
        $settings = Settings::all([
            '2fa.available' => true,
            '2fa.required_admins' => false,
            '2fa.required_teachers' => false,
            '2fa.required_all' => false,
            '2fa.method_email' => true,
            '2fa.method_totp' => false,
            '2fa.remember_days' => 30,
        ]);

        return view('settings.two-factor', compact('settings'));
    }

    public function updateTwoFactor(Request $request)
    {
        $boolKeys = [
            '2fa.available',
            '2fa.required_admins',
            '2fa.required_teachers',
            '2fa.required_all',
            '2fa.method_email',
            '2fa.method_totp',
        ];

        foreach ($boolKeys as $key) {
            Settings::set($key, $request->boolean($key));
        }

        Settings::set('2fa.remember_days', (int) $request->input('2fa.remember_days', 30));

        return redirect()
            ->route('settings.two-factor')
            ->with('success', 'Two-factor authentication settings saved.');
    }

    public function sessions()
    {
        $settings = Settings::all([
            'session.lifetime_minutes' => 120,
            'session.force_logout_on_password' => true,
            'session.single_device_only' => false,
            'session.remember_me_days' => 30,
            'session.log_out_on_browser_close' => false,
        ]);

        return view('settings.sessions', compact('settings'));
    }

    public function updateSessions(Request $request)
    {
        $boolKeys = [
            'session.force_logout_on_password',
            'session.single_device_only',
            'session.log_out_on_browser_close',
        ];

        foreach ($boolKeys as $key) {
            Settings::set($key, $request->boolean($key));
        }

        $intKeys = [
            'session.lifetime_minutes',
            'session.remember_me_days',
        ];

        foreach ($intKeys as $key) {
            Settings::set($key, (int) $request->input($key, 0));
        }

        return redirect()
            ->route('settings.sessions')
            ->with('success', 'Session settings saved.');
    }

    public function policy()
    {
        $school = auth()->user()->school;

        $types = AssessmentType::where('school_id', $school->id)
            ->with(['weights' => fn($q) => $q->whereNull('subject_id')])
            ->orderBy('id')
            ->get()
            ->map(fn($type) => [
                'id' => $type->id,
                'key' => $type->key,
                'name' => $type->name,
                'is_active' => $type->is_active,
                'weight' => (float) ($type->weights->first()->weight_percentage ?? 0),
                'usage_count' => Assessment::where('assessment_type_id', $type->id)->count(),
            ]);

        $totalWeight = $types->sum('weight');

        $settings = Settings::all([
            'policy.rounding' => 'nearest_int',
            'policy.auto_calculate_final' => true,
            'policy.allow_teacher_override' => false,
            'policy.require_weights_complete' => true,
            'policy.default_ca_cap' => 40,
        ]);

        return view('settings.policy', compact('types', 'totalWeight', 'settings'));
    }

    public function updatePolicy(Request $request)
    {
        $school = auth()->user()->school;

        $data = $request->validate([
            'types' => 'required|array|min:1',
            'types.*.id' => 'required|integer|exists:assessment_types,id',
            'types.*.name' => 'required|string|max:100',
            'types.*.key' => 'required|string|max:100',
            'types.*.weight' => 'required|numeric|min:0|max:100',
            'types.*.is_active' => 'nullable|boolean',
            'policy.rounding' => 'required|in:nearest_int,one_decimal,two_decimals',
            'policy.auto_calculate_final' => 'nullable|boolean',
            'policy.allow_teacher_override' => 'nullable|boolean',
            'policy.require_weights_complete' => 'nullable|boolean',
            'policy.default_ca_cap' => 'required|integer|min:0|max:100',
        ]);

        $activeTotal = 0;
        foreach ($data['types'] as $t) {
            if (!empty($t['is_active'])) {
                $activeTotal += (float) $t['weight'];
            }
        }

        if (abs($activeTotal - 100) > 0.01 && $request->boolean('policy.require_weights_complete', true)) {
            return back()
                ->withErrors(['types' => "Active assessment types must sum to exactly 100%. Currently: {$activeTotal}%."])
                ->withInput();
        }

        $keys = array_column($data['types'], 'key');
        if (count($keys) !== count(array_unique($keys))) {
            return back()->withErrors(['types' => 'Assessment type keys must be unique.'])->withInput();
        }

        foreach ($data['types'] as $t) {
            $type = AssessmentType::where('id', $t['id'])
                ->where('school_id', $school->id)
                ->firstOrFail();

            $type->update([
                'name' => $t['name'],
                'key' => Str::slug($t['key'], '_'),
                'is_active' => !empty($t['is_active']),
            ]);

            AssessmentWeight::updateOrCreate(
                [
                    'school_id' => $school->id,
                    'assessment_type_id' => $type->id,
                    'subject_id' => null,
                ],
                [
                    'weight_percentage' => (float) $t['weight'],
                ]
            );
        }

        Settings::set('policy.rounding', $data['policy']['rounding']);
        Settings::set('policy.auto_calculate_final', $request->boolean('policy.auto_calculate_final'));
        Settings::set('policy.allow_teacher_override', $request->boolean('policy.allow_teacher_override'));
        Settings::set('policy.require_weights_complete', $request->boolean('policy.require_weights_complete'));
        Settings::set('policy.default_ca_cap', (int) $data['policy']['default_ca_cap']);

        return redirect()
            ->route('settings.policy')
            ->with('success', 'Assessment policy saved.');
    }

    public function storeAssessmentType(Request $request)
    {
        $school = auth()->user()->school;

        $data = $request->validate([
            'name' => 'required|string|max:100',
        ]);

        $key = Str::slug($data['name'], '_');
        $base = $key;
        $i = 1;
        while (AssessmentType::where('school_id', $school->id)->where('key', $key)->exists()) {
            $key = $base . '_' . $i++;
        }

        AssessmentType::create([
            'school_id' => $school->id,
            'key' => $key,
            'name' => $data['name'],
            'is_active' => true,
        ]);

        return back()->with('success', "Assessment type \"{$data['name']}\" added. Set its weight below.");
    }

    public function destroyAssessmentType(AssessmentType $type)
    {
        abort_unless($type->school_id === auth()->user()->school_id, 403);

        $inUse = Assessment::where('assessment_type_id', $type->id)->exists();
        if ($inUse) {
            return back()->withErrors([
                'types' => "Cannot delete \"{$type->name}\" - it is used by one or more assessments.",
            ]);
        }

        $type->weights()->delete();
        $type->delete();

        return back()->with('success', "Assessment type \"{$type->name}\" removed.");
    }

    public function weightingTemplates()
    {
        $school = auth()->user()->school;

        $templates = WeightingTemplate::where('school_id', $school->id)
            ->with(['items.assessmentType'])
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get()
            ->map(fn($t) => [
                'id' => $t->id,
                'name' => $t->name,
                'description' => $t->description,
                'is_default' => $t->is_default,
                'is_active' => $t->is_active,
                'total' => $t->totalWeight(),
                'is_complete' => $t->isComplete(),
                'items' => $t->items->map(fn($i) => [
                    'type_id' => $i->assessment_type_id,
                    'type' => $i->assessmentType?->name ?? '-',
                    'weight' => (float) $i->weight_percentage,
                ])->values()->all(),
            ]);

        $types = AssessmentType::where('school_id', $school->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(fn($t) => ['id' => $t->id, 'name' => $t->name]);

        return view('settings.weighting-templates', compact('templates', 'types'));
    }

    public function storeWeightingTemplate(Request $request)
    {
        $school = auth()->user()->school;

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'weights' => 'required|array|min:1',
            'weights.*.type_id' => 'required|integer|exists:assessment_types,id',
            'weights.*.weight' => 'required|numeric|min:0|max:100',
            'is_default' => 'nullable|boolean',
        ]);

        $total = collect($data['weights'])->sum('weight');
        if (abs($total - 100) > 0.01) {
            return back()
                ->withErrors(['weights' => "Template weights must sum to 100%. Currently: {$total}%."])
                ->withInput();
        }

        if ($request->boolean('is_default')) {
            WeightingTemplate::where('school_id', $school->id)
                ->update(['is_default' => false]);
        }

        $template = WeightingTemplate::create([
            'school_id' => $school->id,
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'is_default' => $request->boolean('is_default'),
            'is_active' => true,
        ]);

        foreach ($data['weights'] as $w) {
            WeightingTemplateItem::create([
                'weighting_template_id' => $template->id,
                'assessment_type_id' => $w['type_id'],
                'weight_percentage' => (float) $w['weight'],
            ]);
        }

        return back()->with('success', "Template \"{$data['name']}\" created.");
    }

    public function updateWeightingTemplate(Request $request, WeightingTemplate $template)
    {
        abort_unless($template->school_id === auth()->user()->school_id, 403);

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'weights' => 'required|array|min:1',
            'weights.*.type_id' => 'required|integer|exists:assessment_types,id',
            'weights.*.weight' => 'required|numeric|min:0|max:100',
            'is_default' => 'nullable|boolean',
        ]);

        $total = collect($data['weights'])->sum('weight');
        if (abs($total - 100) > 0.01) {
            return back()
                ->withErrors(['weights' => "Template weights must sum to 100%. Currently: {$total}%."])
                ->withInput();
        }

        if ($request->boolean('is_default')) {
            WeightingTemplate::where('school_id', $template->school_id)
                ->where('id', '!=', $template->id)
                ->update(['is_default' => false]);
        }

        $template->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'is_default' => $request->boolean('is_default'),
        ]);

        $template->items()->delete();
        foreach ($data['weights'] as $w) {
            WeightingTemplateItem::create([
                'weighting_template_id' => $template->id,
                'assessment_type_id' => $w['type_id'],
                'weight_percentage' => (float) $w['weight'],
            ]);
        }

        return back()->with('success', "Template \"{$data['name']}\" updated.");
    }

    public function destroyWeightingTemplate(WeightingTemplate $template)
    {
        abort_unless($template->school_id === auth()->user()->school_id, 403);

        $name = $template->name;
        $template->items()->delete();
        $template->delete();

        return back()->with('success', "Template \"{$name}\" removed.");
    }

    public function setDefaultWeightingTemplate(WeightingTemplate $template)
    {
        abort_unless($template->school_id === auth()->user()->school_id, 403);

        if (!$template->isComplete()) {
            return back()->withErrors([
                'templates' => "Cannot set as default - weights don't sum to 100%.",
            ]);
        }

        WeightingTemplate::where('school_id', $template->school_id)
            ->update(['is_default' => false]);

        $template->update(['is_default' => true]);

        return back()->with('success', "\"{$template->name}\" is now the default template.");
    }

    public function applyWeightingTemplate(WeightingTemplate $template)
    {
        abort_unless($template->school_id === auth()->user()->school_id, 403);

        if (!$template->isComplete()) {
            return back()->withErrors([
                'templates' => "Cannot apply - weights don't sum to 100%.",
            ]);
        }

        $school = auth()->user()->school;

        foreach ($template->items as $item) {
            AssessmentWeight::updateOrCreate(
                [
                    'school_id' => $school->id,
                    'assessment_type_id' => $item->assessment_type_id,
                    'subject_id' => null,
                ],
                [
                    'weight_percentage' => $item->weight_percentage,
                ]
            );
        }

        return back()->with('success', "Applied \"{$template->name}\" to the school's assessment weights.");
    }

    public function peerComparison()
    {
        $settings = Settings::all([
            'peer.show_rank' => false,
            'peer.show_percentile' => true,
            'peer.show_class_average' => true,
            'peer.show_subject_average' => true,
            'peer.show_highest_lowest' => false,
            'peer.hide_names' => true,
            'peer.min_class_size' => 5,
        ]);

        return view('settings.peer-comparison', compact('settings'));
    }

    public function updatePeerComparison(Request $request)
    {
        $boolKeys = [
            'peer.show_rank',
            'peer.show_percentile',
            'peer.show_class_average',
            'peer.show_subject_average',
            'peer.show_highest_lowest',
            'peer.hide_names',
        ];

        foreach ($boolKeys as $key) {
            Settings::set($key, $request->boolean($key));
        }

        Settings::set('peer.min_class_size', (int) $request->input('peer.min_class_size', 5));

        return redirect()
            ->route('settings.peer-comparison')
            ->with('success', 'Peer comparison settings saved.');
    }

    public function retention()
    {
        $settings = Settings::all([
            'retention.assessment_results_years' => 7,
            'retention.student_marks_years' => 7,
            'retention.audit_log_months' => 24,
            'retention.inactive_students_years' => 10,
            'retention.auto_archive_graduates' => true,
            'retention.keep_report_cards_years' => 99,
        ]);

        return view('settings.retention', compact('settings'));
    }

    public function updateRetention(Request $request)
    {
        Settings::set('retention.auto_archive_graduates', $request->boolean('retention.auto_archive_graduates'));

        $intKeys = [
            'retention.assessment_results_years',
            'retention.student_marks_years',
            'retention.audit_log_months',
            'retention.inactive_students_years',
            'retention.keep_report_cards_years',
        ];

        foreach ($intKeys as $key) {
            Settings::set($key, (int) $request->input($key, 0));
        }

        return redirect()
            ->route('settings.retention')
            ->with('success', 'Data retention settings saved.');
    }

    public function flags()
    {
        $settings = Settings::all([
            'features.interventions' => true,
            'features.analytics' => true,
            'features.report_cards' => true,
            'features.guardian_portal' => true,
            'features.student_portal' => true,
            'features.public_portal' => false,
            'features.sms_notifications' => false,
            'features.ai_insights' => false,
            'features.bulk_import' => true,
            'features.api_access' => false,
        ]);

        return view('settings.flags', compact('settings'));
    }

    public function updateFlags(Request $request)
    {
        $keys = [
            'features.interventions',
            'features.analytics',
            'features.report_cards',
            'features.guardian_portal',
            'features.student_portal',
            'features.public_portal',
            'features.sms_notifications',
            'features.ai_insights',
            'features.bulk_import',
            'features.api_access',
        ];

        foreach ($keys as $key) {
            Settings::set($key, $request->boolean($key));
        }

        return redirect()
            ->route('settings.flags')
            ->with('success', 'Feature flags saved.');
    }

    public function branding()
    {
        return redirect()->route('settings.profile');
    }
    public function accentColor()
    {
        return redirect()->route('settings.profile');
    }
    public function favicon()
    {
        return redirect()->route('settings.profile');
    }
    public function logo()
    {
        return redirect()->route('settings.profile');
    }
    public function signatures()
    {
        return redirect()->route('settings.profile');
    }

    public function darkMode()
    {
        $settings = Settings::all([
            'branding.default_theme' => 'light',
            'branding.allow_user_switch' => true,
            'branding.dark_accent_auto' => true,
        ]);

        return view('settings.dark-mode', compact('settings'));
    }

    public function updateDarkMode(Request $request)
    {
        $data = $request->validate([
            'branding.default_theme' => 'required|in:light,dark,system',
        ]);

        Settings::set('branding.default_theme', $data['branding.default_theme']);
        Settings::set('branding.allow_user_switch', $request->boolean('branding.allow_user_switch'));
        Settings::set('branding.dark_accent_auto', $request->boolean('branding.dark_accent_auto'));

        return redirect()
            ->route('settings.dark-mode')
            ->with('success', 'Theme settings saved.');
    }

    public function loginBackground()
    {
        $settings = Settings::all([
            'branding.login_bg_path' => null,
            'branding.login_bg_overlay' => 55,
            'branding.login_bg_blur' => 0,
        ]);

        return view('settings.login-background', compact('settings'));
    }

    public function updateLoginBackground(Request $request)
    {
        if ($request->hasFile('background')) {
            $request->validate([
                'background' => 'image|max:4096|mimes:jpg,jpeg,png,webp',
            ]);

            $oldPath = Settings::get('branding.login_bg_path');
            if ($oldPath) {
                \Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('background')->store('login-backgrounds', 'public');
            Settings::set('branding.login_bg_path', $path);
        }

        if ($request->boolean('remove_background')) {
            $oldPath = Settings::get('branding.login_bg_path');
            if ($oldPath) {
                \Storage::disk('public')->delete($oldPath);
            }
            Settings::forget('branding.login_bg_path');
        }

        Settings::set('branding.login_bg_overlay', (int) $request->input('branding.login_bg_overlay', 55));
        Settings::set('branding.login_bg_blur', (int) $request->input('branding.login_bg_blur', 0));

        return redirect()
            ->route('settings.login-background')
            ->with('success', 'Login background settings saved.');
    }

    public function localization()
    {
        $settings = Settings::all([
            'locale.language' => 'en',
            'locale.date_format' => 'd M Y',
            'locale.time_format' => 'H:i',
            'locale.week_start' => 'monday',
            'locale.number_format' => '1,234.56',
        ]);

        return view('settings.localization', compact('settings'));
    }

    public function updateLocalization(Request $request)
    {
        $data = $request->validate([
            'locale.language' => 'required|in:en,fr,pt,sw,ny,zh,ar',
            'locale.date_format' => 'required|string|max:20',
            'locale.time_format' => 'required|in:H:i,h:i A',
            'locale.week_start' => 'required|in:monday,sunday',
            'locale.number_format' => 'required|in:1,234.56,1.234,56,1 234,56',
        ]);

        foreach ($data['locale'] as $key => $value) {
            Settings::set("locale.{$key}", $value);
        }

        return redirect()
            ->route('settings.localization')
            ->with('success', 'Localization settings saved.');
    }

    public function help()
    {
        return view('settings.help');
    }

    public function changelog()
    {
        return view('settings.changelog');
    }

    public function feedback()
    {
        $submitted = Settings::get('feedback.entries', []);

        if (!is_array($submitted)) {
            $submitted = [];
        }

        return view('settings.feedback', compact('submitted'));
    }

    public function submitFeedback(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|in:bug,feature,question,other',
            'subject' => 'required|string|max:150',
            'message' => 'required|string|min:10|max:2000',
        ]);

        $existing = Settings::get('feedback.entries', []);
        if (!is_array($existing)) {
            $existing = [];
        }

        $entry = [
            'id' => uniqid('fb_'),
            'type' => $data['type'],
            'subject' => $data['subject'],
            'message' => $data['message'],
            'user' => auth()->user()->name,
            'email' => auth()->user()->email,
            'created_at' => now()->toIso8601String(),
        ];

        array_unshift($existing, $entry);
        $existing = array_slice($existing, 0, 50);

        Settings::set('feedback.entries', $existing);

        return redirect()
            ->route('settings.feedback')
            ->with('success', 'Thank you - your feedback has been recorded.');
    }

    public function destroyFeedback(Request $request)
    {
        $id = $request->validate(['id' => 'required|string'])['id'];

        $existing = Settings::get('feedback.entries', []);
        if (!is_array($existing)) {
            $existing = [];
        }

        $existing = array_values(array_filter($existing, fn($e) => ($e['id'] ?? '') !== $id));

        Settings::set('feedback.entries', $existing);

        return back()->with('success', 'Feedback entry removed.');
    }

    public function status()
    {
        $status = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'environment' => app()->environment(),
            'debug_mode' => config('app.debug') ? 'On' : 'Off',
            'timezone' => config('app.timezone'),
            'session_driver' => config('session.driver'),
            'cache_driver' => config('cache.default'),
            'queue_driver' => config('queue.default'),
            'mail_mailer' => config('mail.default'),
        ];

        $writable = [
            'storage/app' => is_writable(storage_path('app')),
            'storage/framework' => is_writable(storage_path('framework')),
            'storage/logs' => is_writable(storage_path('logs')),
            'bootstrap/cache' => is_writable(bootstrap_path('cache')),
        ];

        try {
            \DB::connection()->getPdo();
            $dbOk = true;
            $dbVersion = \DB::connection()->getPdo()->getAttribute(\PDO::ATTR_SERVER_VERSION);
        } catch (\Throwable $e) {
            $dbOk = false;
            $dbVersion = null;
        }

        $logPath = storage_path('logs/laravel.log');
        $logSize = file_exists($logPath) ? filesize($logPath) : 0;

        return view('settings.status', compact('status', 'writable', 'dbOk', 'dbVersion', 'logSize'));
    }

    public function clearLog(Request $request)
    {
        $logPath = storage_path('logs/laravel.log');

        if (file_exists($logPath)) {
            file_put_contents($logPath, '');
        }

        return back()->with('success', 'Laravel log cleared.');
    }

    public function commentBanks()
    {
        $school = auth()->user()->school;

        $comments = CommentBank::forSchool($school->id)
            ->orderBy('category')
            ->orderBy('band')
            ->orderBy('subject')
            ->get();

        $grouped = $comments->groupBy('category');

        return view('settings.comment-banks', compact('comments', 'grouped'));
    }

    public function storeCommentBank(Request $request)
    {
        $school = auth()->user()->school;

        $data = $request->validate([
            'category' => 'required|string|in:strength,weakness,general,effort',
            'band' => 'nullable|string|in:excellent,good,average,poor',
            'subject' => 'nullable|string|max:100',
            'text' => 'required|string|min:10|max:500',
        ]);

        CommentBank::create([
            'school_id' => $school->id,
            'category' => $data['category'],
            'band' => $data['band'] ?? null,
            'subject' => $data['subject'] ?? null,
            'text' => $data['text'],
            'is_active' => true,
        ]);

        return back()->with('success', 'Comment added to bank.');
    }

    public function updateCommentBank(Request $request, CommentBank $comment)
    {
        abort_unless($comment->school_id === auth()->user()->school_id, 403);

        $data = $request->validate([
            'category' => 'required|string|in:strength,weakness,general,effort',
            'band' => 'nullable|string|in:excellent,good,average,poor',
            'subject' => 'nullable|string|max:100',
            'text' => 'required|string|min:10|max:500',
            'is_active' => 'nullable|boolean',
        ]);

        $comment->update([
            'category' => $data['category'],
            'band' => $data['band'] ?? null,
            'subject' => $data['subject'] ?? null,
            'text' => $data['text'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Comment updated.');
    }

    public function destroyCommentBank(CommentBank $comment)
    {
        abort_unless($comment->school_id === auth()->user()->school_id, 403);

        $comment->delete();

        return back()->with('success', 'Comment removed.');
    }

    public function emailTemplates()
    {
        $school = auth()->user()->school;

        $defaults = [
            'report_card' => [
                'name' => 'Report Card',
                'subject' => 'Report Card - {{student_name}}',
                'body' => "Dear Guardian,\n\nPlease find attached the report card for {{student_name}} ({{student_number}}).\n\nIf you have any questions, please contact the school office.\n\n{{school_name}}",
            ],
            'timetable' => [
                'name' => 'Class Timetable',
                'subject' => 'Timetable for {{class_name}}',
                'body' => "Dear {{guardian_name}},\n\nPlease find attached the class timetable for {{class_name}} for {{term}}, {{academic_year}}.\n\nIf you have any questions about your child's schedule, please contact the school office.\n\n{{school_name}}",
            ],
            'bulk_announcement' => [
                'name' => 'Bulk Announcement',
                'subject' => '{{school_name}}: Announcement',
                'body' => "Dear {{guardian_name}},\n\nPlease note the following announcement from {{school_name}}:\n\n{{message}}\n\nRegards,\n{{school_name}}",
            ],
            'guardian_update' => [
                'name' => 'Guardian Progress Update',
                'subject' => 'Progress update - {{student_name}}',
                'body' => "Dear Guardian,\n\nThis is a progress update regarding {{student_name}}.\n\n{{custom_message}}\n\n{{school_name}}",
            ],
            'password_reset' => [
                'name' => 'Password Reset',
                'subject' => 'Reset your password',
                'body' => "Hello,\n\nYou requested a password reset. Click the link below to set a new password.\n\n{{reset_link}}\n\nIf you did not request this, you can ignore this email.",
            ],
            'absence_alert' => [
                'name' => 'Absence Alert',
                'subject' => 'Absence notice - {{student_name}}',
                'body' => "Dear Guardian,\n\n{{student_name}} was marked absent today.\n\nIf this is an error, please contact the school office.",
            ],
            'intervention_reminder' => [
                'name' => 'Intervention Review Reminder',
                'subject' => 'Intervention review approaching - {{student_name}}',
                'body' => "Dear Staff,\n\nAn intervention for {{student_name}} is due for review on {{review_date}}.\n\nPlease review the intervention plan.",
            ],
        ];

        $stored = EmailTemplate::forSchool($school->id)->get()->keyBy('key');

        $templates = collect($defaults)->map(function ($default, $key) use ($stored) {
            $saved = $stored->get($key);

            return [
                'key' => $key,
                'name' => $saved->name ?? $default['name'],
                'subject' => $saved->subject ?? $default['subject'],
                'body' => $saved->body ?? $default['body'],
                'is_active' => $saved->is_active ?? true,
                'is_custom' => $saved !== null,
            ];
        });

        return view('settings.email-templates', compact('templates'));
    }

    public function updateEmailTemplate(Request $request)
    {
        $school = auth()->user()->school;

        $data = $request->validate([
            'key' => 'required|string|max:100',
            'subject' => 'required|string|max:255',
            'body' => 'required|string|min:10|max:5000',
            'is_active' => 'nullable|boolean',
        ]);

        $names = [
            'report_card' => 'Report Card',
            'timetable' => 'Class Timetable',
            'bulk_announcement' => 'Bulk Announcement',
            'guardian_update' => 'Guardian Progress Update',
            'password_reset' => 'Password Reset',
            'absence_alert' => 'Absence Alert',
            'intervention_reminder' => 'Intervention Review Reminder',
        ];

        EmailTemplate::updateOrCreate(
            [
                'school_id' => $school->id,
                'key' => $data['key'],
            ],
            [
                'name' => $names[$data['key']] ?? ucfirst(str_replace('_', ' ', $data['key'])),
                'subject' => $data['subject'],
                'body' => $data['body'],
                'is_active' => $request->boolean('is_active', true),
            ]
        );

        return back()->with('success', 'Email template saved.');
    }

    public function resetEmailTemplate(Request $request)
    {
        $school = auth()->user()->school;

        $key = $request->validate(['key' => 'required|string|max:100'])['key'];

        EmailTemplate::forSchool($school->id)
            ->where('key', $key)
            ->delete();

        return back()->with('success', 'Email template reset to default.');
    }

    public function audit(Request $request)
    {
        $school = auth()->user()->school;

        $query = AuditLog::forSchool($school->id)
            ->with('user')
            ->latest();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('user_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($action = $request->get('action')) {
            $query->where('action', $action);
        }

        if ($dateFrom = $request->get('from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo = $request->get('to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $logs = $query->paginate(50)->withQueryString();

        $actions = AuditLog::forSchool($school->id)
            ->distinct()
            ->pluck('action')
            ->filter()
            ->values();

        return view('settings.audit', compact('logs', 'actions'));
    }

    public function clearAuditLog(Request $request)
    {
        $school = auth()->user()->school;

        AuditLog::forSchool($school->id)->delete();

        \App\Services\AuditLogger::log('delete', null, null, 'Cleared audit log');

        return back()->with('success', 'Audit log cleared.');
    }

    public function staffMatrix()
    {
        $school = auth()->user()->school;

        $staff = \App\Models\User::where('school_id', $school->id)
            ->whereIn('role', ['teacher', 'school_admin', 'super_admin'])
            ->orderBy('name')
            ->get();

        $classrooms = \App\Models\Classroom::where('school_id', $school->id)
            ->orderBy('name')
            ->get();

        $subjects = \App\Models\Subject::where('school_id', $school->id)
            ->orderBy('name')
            ->get();

        $assignments = [];

        foreach ($classrooms as $classroom) {
            $assignments[$classroom->id] = [
                'classroom' => $classroom,
                'subjects' => [],
            ];

            foreach ($subjects as $subject) {
                $pivot = \DB::table('class_subject')
                    ->where('class_id', $classroom->id)
                    ->where('subject_id', $subject->id)
                    ->first();

                if ($pivot) {
                    $assignments[$classroom->id]['subjects'][] = [
                        'subject' => $subject,
                        'teacher_id' => $pivot->teacher_id,
                        'teacher' => $pivot->teacher_id
                            ? $staff->firstWhere('id', $pivot->teacher_id)
                            : null,
                    ];
                }
            }
        }

        return view('settings.staff-matrix', compact('staff', 'classrooms', 'subjects', 'assignments'));
    }

    public function assessmentTypes()
    {
        return redirect()->route('settings.policy');
    }

    public function reportCardTemplates()
    {
        return $this->roadmap(
            'Report Card Templates',
            'Choose between multiple report card layouts, or design your own with the visual builder.',
            [
                ['Classic', 'The current Pro Tech report card - formal, dense, print-ready.'],
                ['Modern', 'A lighter layout with more whitespace and larger type.'],
                ['Minimal', 'Compact single-page format for quick distribution.'],
                ['Custom', 'Build your own layout with the drag-and-drop designer.'],
            ],
            'In progress'
        );
    }

    public function reportBuilder()
    {
        return $this->roadmap(
            'Report Builder',
            'Build custom reports by choosing fields, filters, and groupings. Save them for reuse or share with other staff.',
            [
                ['Drag-and-drop fields', 'Pick any column from Students, Marks, Attendance, Interventions.'],
                ['Saved templates', 'Save a report definition and re-run it any time.'],
                ['Scheduled delivery', 'Email reports on a schedule - daily, weekly, or termly.'],
                ['Export options', 'Download as PDF, Excel, or CSV.'],
            ],
            'Planned'
        );
    }

    public function scheduledReports()
    {
        return $this->roadmap(
            'Scheduled Reports',
            'Set reports to run automatically and deliver by email to the right people at the right time.',
            [
                ['Weekly summaries', 'Class averages, at-risk counts, upcoming assessments.'],
                ['Termly report packs', 'Full report cards bundled and sent to guardians.'],
                ['Custom schedules', 'Daily, weekly, monthly, or tied to term events.'],
            ],
            'Planned'
        );
    }

    public function yearRollover()
    {
        return $this->roadmap(
            'Year Rollover',
            'Copy your classes, subjects, and structure into a new academic year in one step.',
            [
                ['Copy classes', 'Recreate all classes at their next grade level.'],
                ['Copy subjects', 'Assign the same subjects to the new classes.'],
                ['Archive old results', 'Keep prior year data safe and read-only.'],
                ['Promote students', 'Move eligible students forward, hold back those who need it.'],
            ],
            'Planned',
            'warning'
        );
    }

    public function termRollover()
    {
        return $this->roadmap(
            'Term Rollover',
            'Close the current term and open the next, carrying forward what should follow.',
            [
                ['Lock past term', 'Prevent further marks in the closing term.'],
                ['Open new term', 'Enable marking for the new term.'],
                ['Carry interventions', 'Continue active interventions into the next term.'],
            ],
            'Planned',
            'warning'
        );
    }

    public function customReports()
    {
        return $this->roadmap(
            'Custom Reports',
            'Reports you\'ve saved from the Report Builder appear here, ready to run in one click.',
            [
                ['Saved definitions', 'Every report you save from the builder.'],
                ['Quick run', 'Re-run a saved report with the same filters.'],
                ['Share with staff', 'Give a report to specific roles or individual users.'],
            ],
            'Planned'
        );
    }

    public function customFields()
    {
        return $this->roadmap(
            'Custom Fields',
            'Add your own columns to student, staff, or school records - House, Bus route, Medical notes, anything.',
            [
                ['Student fields', 'Add fields like House, Bus Route, Medical Notes.'],
                ['Staff fields', 'Add qualifications, certifications, contract types.'],
                ['Field types', 'Text, number, date, dropdown, yes/no.'],
                ['Visibility rules', 'Show a field only for certain roles.'],
            ],
            'Planned'
        );
    }

    public function campuses()
    {
        return $this->roadmap(
            'Multi-Campus',
            'Run multiple school branches under one account with shared staff, subjects, and reporting.',
            [
                ['Separate campuses', 'Each campus gets its own classes, students, and staff.'],
                ['Shared subjects', 'Common subject and grading structure across campuses.'],
                ['Cross-campus reporting', 'Compare performance across campuses.'],
                ['Role-based access', 'Admins see everything; staff see their own campus.'],
            ],
            'Planned'
        );
    }

    public function alumni()
    {
        return $this->roadmap(
            'Alumni Tracking',
            'Keep graduated students in a separate searchable archive, with their full academic history intact.',
            [
                ['Graduation flag', 'Mark students as alumni when they leave.'],
                ['Permanent record', 'Keep results, report cards, and comments indefinitely.'],
                ['Alumni search', 'Find a former student by name, year, or class.'],
                ['Career tracking', 'Optionally record where alumni went after graduation.'],
            ],
            'Planned'
        );
    }

    public function meetings()
    {
        return $this->roadmap(
            'Meeting Scheduler',
            'Let guardians request and book parent-teacher meeting slots without the back-and-forth.',
            [
                ['Available slots', 'Teachers publish when they\'re free.'],
                ['Guardian booking', 'Guardians see available slots and book instantly.'],
                ['Automatic reminders', 'Both sides get confirmation and reminder emails.'],
                ['Meeting notes', 'Teachers can record outcomes for future reference.'],
            ],
            'Planned'
        );
    }

    public function apiKeys()
    {
        return $this->roadmap(
            'API Access',
            'Give external tools secure read access to school data. Useful for custom dashboards, mobile apps, and integrations.',
            [
                ['Generate keys', 'Create a unique token per integration.'],
                ['Scoped permissions', 'Each key can read only the data it needs.'],
                ['Usage logs', 'See how often each key is used and from where.'],
                ['Revoke instantly', 'Kill any key in one click if it leaks.'],
            ],
            'Planned'
        );
    }

    private function roadmap(string $title, string $description, array $features = [], string $status = 'Planned', string $mode = 'info')
    {
        return view('settings._roadmap', compact('title', 'description', 'features', 'status', 'mode'));
    }

    private function placeholder(string $title)
    {
        return view('settings._placeholder', ['title' => $title]);
    }
}