<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InterventionController;
use App\Http\Controllers\MarkingController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\SetupWizardController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// ---------- Guest ----------
Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('two-factor/verify', [TwoFactorController::class, 'show'])->name('two-factor.verify');
    Route::post('two-factor/verify', [TwoFactorController::class, 'verify'])
        ->middleware('throttle:5,1')
        ->name('two-factor.verify.store');
    Route::post('two-factor/resend', [TwoFactorController::class, 'resend'])
        ->middleware('throttle:3,1')
        ->name('two-factor.resend');

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

// ---------- Auth basics ----------
Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('password/change', [\App\Http\Controllers\Auth\ForcedPasswordChangeController::class, 'edit'])->name('password.change');
    Route::post('password/change', [\App\Http\Controllers\Auth\ForcedPasswordChangeController::class, 'update'])->name('password.change.store');

    Route::get('profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::put('profile/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('profile/avatar', [\App\Http\Controllers\ProfileController::class, 'destroyAvatar'])->name('profile.avatar.destroy');
});

// ---------- Setup Wizard ----------
Route::middleware(['auth', 'force.password.change'])->prefix('setup')->name('setup.')->group(function () {
    Route::get('step-1', [SetupWizardController::class, 'step1'])->name('step1');
    Route::post('step-1', [SetupWizardController::class, 'storeStep1'])->name('step1.store');
    Route::get('step-2', [SetupWizardController::class, 'step2'])->name('step2');
    Route::post('step-2', [SetupWizardController::class, 'storeStep2'])->name('step2.store');
    Route::get('step-3', [SetupWizardController::class, 'step3'])->name('step3');
    Route::post('step-3', [SetupWizardController::class, 'storeStep3'])->name('step3.store');
    Route::get('step-4', [SetupWizardController::class, 'step4'])->name('step4');
    Route::post('step-4', [SetupWizardController::class, 'storeStep4'])->name('step4.store');
    Route::get('step-5', [SetupWizardController::class, 'step5'])->name('step5');
    Route::post('step-5', [SetupWizardController::class, 'storeStep5'])->name('step5.store');
});

// ---------- Main app ----------
Route::middleware(['auth', 'session.policy', 'force.password.change', 'setup.completed'])->group(function () {

    // Dashboards
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('teacher/dashboard', [\App\Http\Controllers\TeacherDashboardController::class, 'index'])->name('teacher.dashboard');
    Route::get('parent/dashboard', [\App\Http\Controllers\ParentDashboardController::class, 'index'])->name('parent.dashboard');
    Route::get('student/dashboard', [\App\Http\Controllers\StudentPortalController::class, 'index'])->name('student.dashboard');

    // ---------- Admin-only ----------
    Route::middleware('role:school_admin,super_admin')->group(function () {

        Route::get('settings', [\App\Http\Controllers\SettingsController::class, 'overview'])->name('settings.overview');
        Route::get('settings/profile', [\App\Http\Controllers\SettingsController::class, 'profile'])->name('settings.profile');
        Route::post('settings/profile', [\App\Http\Controllers\SettingsController::class, 'updateProfile'])->name('settings.profile.update');

        Route::get('settings/policy', [\App\Http\Controllers\SettingsController::class, 'policy'])->name('settings.policy');
        Route::post('settings/policy', [\App\Http\Controllers\SettingsController::class, 'updatePolicy'])->name('settings.policy.update');
        Route::post('settings/policy/types', [\App\Http\Controllers\SettingsController::class, 'storeAssessmentType'])->name('settings.policy.types.store');
        Route::delete('settings/policy/types/{type}', [\App\Http\Controllers\SettingsController::class, 'destroyAssessmentType'])->name('settings.policy.types.destroy');

        Route::get('settings/weighting-templates', [\App\Http\Controllers\SettingsController::class, 'weightingTemplates'])->name('settings.weighting-templates');
        Route::post('settings/weighting-templates', [\App\Http\Controllers\SettingsController::class, 'storeWeightingTemplate'])->name('settings.weighting-templates.store');
        Route::put('settings/weighting-templates/{template}', [\App\Http\Controllers\SettingsController::class, 'updateWeightingTemplate'])->name('settings.weighting-templates.update');
        Route::delete('settings/weighting-templates/{template}', [\App\Http\Controllers\SettingsController::class, 'destroyWeightingTemplate'])->name('settings.weighting-templates.destroy');
        Route::post('settings/weighting-templates/{template}/set-default', [\App\Http\Controllers\SettingsController::class, 'setDefaultWeightingTemplate'])->name('settings.weighting-templates.setDefault');
        Route::post('settings/weighting-templates/{template}/apply', [\App\Http\Controllers\SettingsController::class, 'applyWeightingTemplate'])->name('settings.weighting-templates.apply');

        Route::get('settings/notifications', [\App\Http\Controllers\SettingsController::class, 'notifications'])->name('settings.notifications');
        Route::post('settings/notifications', [\App\Http\Controllers\SettingsController::class, 'updateNotifications'])->name('settings.notifications.update');

        Route::get('settings/grading', [\App\Http\Controllers\SettingsController::class, 'grading'])->name('settings.grading');
        Route::post('settings/grading', [\App\Http\Controllers\SettingsController::class, 'storeGradeBand'])->name('settings.grading.store');
        Route::put('settings/grading/{band}', [\App\Http\Controllers\SettingsController::class, 'updateGradeBand'])->name('settings.grading.update');
        Route::delete('settings/grading/{band}', [\App\Http\Controllers\SettingsController::class, 'destroyGradeBand'])->name('settings.grading.destroy');

        Route::get('settings/peer-comparison', [\App\Http\Controllers\SettingsController::class, 'peerComparison'])->name('settings.peer-comparison');
        Route::post('settings/peer-comparison', [\App\Http\Controllers\SettingsController::class, 'updatePeerComparison'])->name('settings.peer-comparison.update');

        Route::get('settings/retention', [\App\Http\Controllers\SettingsController::class, 'retention'])->name('settings.retention');
        Route::post('settings/retention', [\App\Http\Controllers\SettingsController::class, 'updateRetention'])->name('settings.retention.update');

        Route::get('settings/flags', [\App\Http\Controllers\SettingsController::class, 'flags'])->name('settings.flags');
        Route::post('settings/flags', [\App\Http\Controllers\SettingsController::class, 'updateFlags'])->name('settings.flags.update');

        Route::get('settings/comment-banks', [\App\Http\Controllers\SettingsController::class, 'commentBanks'])->name('settings.comment-banks');
        Route::post('settings/comment-banks', [\App\Http\Controllers\SettingsController::class, 'storeCommentBank'])->name('settings.comment-banks.store');
        Route::put('settings/comment-banks/{comment}', [\App\Http\Controllers\SettingsController::class, 'updateCommentBank'])->name('settings.comment-banks.update');
        Route::delete('settings/comment-banks/{comment}', [\App\Http\Controllers\SettingsController::class, 'destroyCommentBank'])->name('settings.comment-banks.destroy');

        Route::get('settings/email-templates', [\App\Http\Controllers\SettingsController::class, 'emailTemplates'])->name('settings.email-templates');
        Route::post('settings/email-templates', [\App\Http\Controllers\SettingsController::class, 'updateEmailTemplate'])->name('settings.email-templates.update');
        Route::delete('settings/email-templates', [\App\Http\Controllers\SettingsController::class, 'resetEmailTemplate'])->name('settings.email-templates.reset');

        Route::get('settings/assessment-types', [\App\Http\Controllers\SettingsController::class, 'assessmentTypes'])->name('settings.assessment-types');
        Route::get('settings/report-card-templates', [\App\Http\Controllers\SettingsController::class, 'reportCardTemplates'])->name('settings.report-card-templates');
        Route::get('settings/report-builder', [\App\Http\Controllers\SettingsController::class, 'reportBuilder'])->name('settings.report-builder');
        Route::get('settings/scheduled-reports', [\App\Http\Controllers\SettingsController::class, 'scheduledReports'])->name('settings.scheduled-reports');
        Route::get('settings/year-rollover', [\App\Http\Controllers\SettingsController::class, 'yearRollover'])->name('settings.year-rollover');
        Route::get('settings/term-rollover', [\App\Http\Controllers\SettingsController::class, 'termRollover'])->name('settings.term-rollover');
        Route::get('settings/custom-reports', [\App\Http\Controllers\SettingsController::class, 'customReports'])->name('settings.custom-reports');

        Route::get('settings/staff-matrix', [\App\Http\Controllers\SettingsController::class, 'staffMatrix'])->name('settings.staff-matrix');
        Route::get('settings/custom-fields', [\App\Http\Controllers\SettingsController::class, 'customFields'])->name('settings.custom-fields');

        Route::get('settings/student-portal', [\App\Http\Controllers\SettingsController::class, 'studentPortal'])->name('settings.student-portal');
        Route::post('settings/student-portal', [\App\Http\Controllers\SettingsController::class, 'updateStudentPortal'])->name('settings.student-portal.update');

        Route::get('settings/guardian-portal', [\App\Http\Controllers\SettingsController::class, 'guardianPortal'])->name('settings.guardian-portal');
        Route::post('settings/guardian-portal', [\App\Http\Controllers\SettingsController::class, 'updateGuardianPortal'])->name('settings.guardian-portal.update');

        Route::get('settings/campuses', [\App\Http\Controllers\SettingsController::class, 'campuses'])->name('settings.campuses');
        Route::get('settings/alumni', [\App\Http\Controllers\SettingsController::class, 'alumni'])->name('settings.alumni');

        Route::get('settings/meetings', [\App\Http\Controllers\SettingsController::class, 'meetings'])->name('settings.meetings');

        Route::get('settings/feedback', [\App\Http\Controllers\SettingsController::class, 'feedback'])->name('settings.feedback');
        Route::post('settings/feedback', [\App\Http\Controllers\SettingsController::class, 'submitFeedback'])->name('settings.feedback.submit');
        Route::delete('settings/feedback', [\App\Http\Controllers\SettingsController::class, 'destroyFeedback'])->name('settings.feedback.destroy');

        Route::get('settings/help', [\App\Http\Controllers\SettingsController::class, 'help'])->name('settings.help');
        Route::get('settings/changelog', [\App\Http\Controllers\SettingsController::class, 'changelog'])->name('settings.changelog');

        Route::get('settings/security', [\App\Http\Controllers\SettingsController::class, 'security'])->name('settings.security');
        Route::post('settings/security', [\App\Http\Controllers\SettingsController::class, 'updateSecurity'])->name('settings.security.update');

        Route::get('settings/password-policy', [\App\Http\Controllers\SettingsController::class, 'passwordPolicy'])->name('settings.password-policy');
        Route::post('settings/password-policy', [\App\Http\Controllers\SettingsController::class, 'updatePasswordPolicy'])->name('settings.password-policy.update');

        Route::get('settings/two-factor', [\App\Http\Controllers\SettingsController::class, 'twoFactor'])->name('settings.two-factor');
        Route::post('settings/two-factor', [\App\Http\Controllers\SettingsController::class, 'updateTwoFactor'])->name('settings.two-factor.update');

        Route::get('settings/sessions', [\App\Http\Controllers\SettingsController::class, 'sessions'])->name('settings.sessions');
        Route::post('settings/sessions', [\App\Http\Controllers\SettingsController::class, 'updateSessions'])->name('settings.sessions.update');

        Route::get('settings/api-keys', [\App\Http\Controllers\SettingsController::class, 'apiKeys'])->name('settings.api-keys');

        Route::get('settings/audit', [\App\Http\Controllers\SettingsController::class, 'audit'])->name('settings.audit');
        Route::delete('settings/audit', [\App\Http\Controllers\SettingsController::class, 'clearAuditLog'])->name('settings.audit.clear');

        Route::get('settings/status', [\App\Http\Controllers\SettingsController::class, 'status'])->name('settings.status');
        Route::post('settings/status/clear-log', [\App\Http\Controllers\SettingsController::class, 'clearLog'])->name('settings.status.clear-log');

        Route::get('settings/branding', [\App\Http\Controllers\SettingsController::class, 'branding'])->name('settings.branding');
        Route::get('settings/accent-color', [\App\Http\Controllers\SettingsController::class, 'accentColor'])->name('settings.accent-color');
        Route::get('settings/favicon', [\App\Http\Controllers\SettingsController::class, 'favicon'])->name('settings.favicon');
        Route::get('settings/logo', [\App\Http\Controllers\SettingsController::class, 'logo'])->name('settings.logo');
        Route::get('settings/signatures', [\App\Http\Controllers\SettingsController::class, 'signatures'])->name('settings.signatures');

        Route::get('settings/dark-mode', [\App\Http\Controllers\SettingsController::class, 'darkMode'])->name('settings.dark-mode');
        Route::post('settings/dark-mode', [\App\Http\Controllers\SettingsController::class, 'updateDarkMode'])->name('settings.dark-mode.update');

        Route::get('settings/login-background', [\App\Http\Controllers\SettingsController::class, 'loginBackground'])->name('settings.login-background');
        Route::post('settings/login-background', [\App\Http\Controllers\SettingsController::class, 'updateLoginBackground'])->name('settings.login-background.update');

        Route::get('settings/localization', [\App\Http\Controllers\SettingsController::class, 'localization'])->name('settings.localization');
        Route::post('settings/localization', [\App\Http\Controllers\SettingsController::class, 'updateLocalization'])->name('settings.localization.update');

        Route::get('settings/roles', [\App\Http\Controllers\RolePermissionController::class, 'edit'])->name('settings.roles');
        Route::post('settings/roles', [\App\Http\Controllers\RolePermissionController::class, 'update'])->name('settings.roles.update');

        Route::get('settings/academic-calendar', [\App\Http\Controllers\AcademicCalendarController::class, 'index'])->name('settings.calendar');
        Route::post('settings/academic-calendar/years', [\App\Http\Controllers\AcademicCalendarController::class, 'storeYear'])->name('settings.calendar.years.store');
        Route::post('settings/academic-calendar/years/{academicYear}/set-current', [\App\Http\Controllers\AcademicCalendarController::class, 'setCurrentYear'])->name('settings.calendar.years.setCurrent');
        Route::post('settings/academic-calendar/years/{academicYear}/terms', [\App\Http\Controllers\AcademicCalendarController::class, 'storeTerm'])->name('settings.calendar.terms.store');
        Route::post('settings/academic-calendar/terms/{term}/set-current', [\App\Http\Controllers\AcademicCalendarController::class, 'setCurrentTerm'])->name('settings.calendar.terms.setCurrent');

        Route::get('settings/timetable-periods', [\App\Http\Controllers\TimetablePeriodController::class, 'index'])->name('settings.timetable-periods');
        Route::post('settings/timetable-periods', [\App\Http\Controllers\TimetablePeriodController::class, 'store'])->name('settings.timetable-periods.store');
        Route::post('settings/timetable-periods/seed', [\App\Http\Controllers\TimetablePeriodController::class, 'seedDefaults'])->name('settings.timetable-periods.seed');
        Route::put('settings/timetable-periods/{period}', [\App\Http\Controllers\TimetablePeriodController::class, 'update'])->name('settings.timetable-periods.update');
        Route::delete('settings/timetable-periods/{period}', [\App\Http\Controllers\TimetablePeriodController::class, 'destroy'])->name('settings.timetable-periods.destroy');

        Route::post('settings/school/logo', [\App\Http\Controllers\SchoolProfileController::class, 'updateLogo'])->name('settings.school.logo');

        Route::get('settings/fees', [\App\Http\Controllers\FeeSetupController::class, 'index'])->name('settings.fees');
        Route::post('settings/fees/categories', [\App\Http\Controllers\FeeSetupController::class, 'storeCategory'])->name('settings.fees.categories.store');
        Route::delete('settings/fees/categories/{feeCategory}', [\App\Http\Controllers\FeeSetupController::class, 'destroyCategory'])->name('settings.fees.categories.destroy');
        Route::post('settings/fees/structures', [\App\Http\Controllers\FeeSetupController::class, 'storeStructure'])->name('settings.fees.structures.store');
        Route::delete('settings/fees/structures/{feeStructure}', [\App\Http\Controllers\FeeSetupController::class, 'destroyStructure'])->name('settings.fees.structures.destroy');

        Route::resource('staff', \App\Http\Controllers\StaffController::class)->only(['index', 'create', 'store']);

        Route::get('classes-subjects', [\App\Http\Controllers\AcademicStructureController::class, 'index'])->name('academic.index');
        Route::resource('classes', \App\Http\Controllers\ClassroomController::class)->except('index');
        Route::resource('subjects', \App\Http\Controllers\SubjectController::class)->except('index');

        Route::get('students-import', [\App\Http\Controllers\StudentImportController::class, 'create'])->name('students.import');
        Route::post('students-import', [\App\Http\Controllers\StudentImportController::class, 'store'])->name('students.import.store');
        Route::get('students-import/template', [\App\Http\Controllers\StudentImportController::class, 'template'])->name('students.import.template');
    });

    // ---------- Students ----------
    Route::middleware('can:students.view')->group(function () {
        Route::get('students', [StudentController::class, 'index'])->name('students.index');
        Route::get('students/create', [StudentController::class, 'create'])->name('students.create');
        Route::get('students/{student}', [StudentController::class, 'show'])->name('students.show');

        Route::post('students/{student}/photo', [\App\Http\Controllers\StudentPhotoController::class, 'update'])->name('students.photo.update');
        Route::delete('students/{student}/photo', [\App\Http\Controllers\StudentPhotoController::class, 'destroy'])->name('students.photo.destroy');
    });

    Route::middleware('can:students.edit')->group(function () {
        Route::post('students', [StudentController::class, 'store'])->name('students.store');

        Route::post('students/{student}/guardians', [\App\Http\Controllers\StudentGuardianController::class, 'store'])->name('students.guardians.store');
        Route::delete('students/{student}/guardians/{guardian}', [\App\Http\Controllers\StudentGuardianController::class, 'destroy'])->name('students.guardians.destroy');
        Route::post('students/{student}/portal-access', [\App\Http\Controllers\StudentPortalAccessController::class, 'store'])->name('students.portal.store');
        Route::delete('students/{student}/portal-access', [\App\Http\Controllers\StudentPortalAccessController::class, 'destroy'])->name('students.portal.destroy');
        Route::post('students/{student}/email-guardian', [\App\Http\Controllers\StudentGuardianEmailController::class, 'send'])->name('students.email-guardian');

        Route::get('students/{student}/edit', [StudentController::class, 'edit'])->name('students.edit');
        Route::put('students/{student}', [StudentController::class, 'update'])->name('students.update');
        Route::patch('students/{student}', [StudentController::class, 'update']);
        Route::delete('students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');
    });

    // ---------- Announcements ----------
    Route::prefix('announcements')->name('announcements.')->middleware('can:students.view')->group(function () {
        Route::get('/', [\App\Http\Controllers\AnnouncementController::class, 'index'])->name('index');
        Route::get('create', [\App\Http\Controllers\AnnouncementController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\AnnouncementController::class, 'store'])->name('store');
        Route::get('{announcement}', [\App\Http\Controllers\AnnouncementController::class, 'show'])->name('show');
        Route::delete('{announcement}', [\App\Http\Controllers\AnnouncementController::class, 'destroy'])->name('destroy');
    });

    // ---------- Communications ----------
    Route::prefix('communications')->name('communications.')->middleware('can:communications.send')->group(function () {
        Route::get('/', [\App\Http\Controllers\CommunicationsController::class, 'index'])->name('index');
        Route::get('preview', [\App\Http\Controllers\CommunicationsController::class, 'previewRecipients'])->name('preview');
        Route::get('search-guardians', [\App\Http\Controllers\CommunicationsController::class, 'searchGuardians'])->name('searchGuardians');
        Route::get('history', [\App\Http\Controllers\CommunicationsController::class, 'history'])->name('history');
        Route::get('history/detail', [\App\Http\Controllers\CommunicationsController::class, 'historyDetail'])->name('history.detail');
        Route::post('send', [\App\Http\Controllers\CommunicationsController::class, 'send'])->name('send');
    });

    // ---------- Assessments ----------
    Route::middleware('can:results.view')->group(function () {
        Route::get('assessments', [AssessmentController::class, 'index'])->name('assessments.index');
        Route::get('assessments/{assessment}', [AssessmentController::class, 'show'])->name('assessments.show');
    });

    Route::middleware('can:assessment.create')->group(function () {
        Route::get('assessments/create', [AssessmentController::class, 'create'])->name('assessments.create');
        Route::post('assessments', [AssessmentController::class, 'store'])->name('assessments.store');
    });

    Route::middleware('can:assessment.edit')->group(function () {
        Route::get('assessments/{assessment}/edit', [AssessmentController::class, 'edit'])->name('assessments.edit');
        Route::put('assessments/{assessment}', [AssessmentController::class, 'update'])->name('assessments.update');
        Route::patch('assessments/{assessment}', [AssessmentController::class, 'update']);
        Route::delete('assessments/{assessment}', [AssessmentController::class, 'destroy'])->name('assessments.destroy');
    });

    Route::middleware('can:results.enter')->group(function () {
        Route::get('assessments/{assessment}/marking', [MarkingController::class, 'edit'])->name('marking.edit');
        Route::post('assessments/{assessment}/marking', [MarkingController::class, 'store'])->name('marking.store');
        Route::post('assessments/{assessment}/lock', [MarkingController::class, 'lock'])->name('marking.lock');

        Route::get('marks/entry', [MarkingController::class, 'bulkEntry'])->name('marks.entry');
        Route::post('marks/entry', [MarkingController::class, 'bulkStore'])->name('marks.entry.store');
    });

    // ---------- Reports ----------
    Route::prefix('reports')->name('reports.')->middleware('can:reports.generate')->group(function () {
        Route::get('/', [\App\Http\Controllers\ReportController::class, 'index'])->name('index');
        Route::get('broadsheet', [\App\Http\Controllers\ReportController::class, 'broadsheet'])->name('broadsheet');
        Route::get('rankings', [\App\Http\Controllers\ReportController::class, 'rankings'])->name('rankings');
        Route::get('merit', [\App\Http\Controllers\ReportController::class, 'merit'])->name('merit');
        Route::get('pass-list', [\App\Http\Controllers\ReportController::class, 'passList'])->name('pass-list');
        Route::get('grade-analysis', [\App\Http\Controllers\ReportController::class, 'gradeAnalysis'])->name('grade-analysis');
        Route::get('subject-performance', [\App\Http\Controllers\ReportController::class, 'subjectPerformance'])->name('subject-performance');
        Route::get('statistics', [\App\Http\Controllers\ReportController::class, 'statistics'])->name('statistics');
        Route::get('export', [\App\Http\Controllers\ExportController::class, 'index'])->name('export');

        Route::get('{student}/report-card', [\App\Http\Controllers\ReportController::class, 'reportCard'])->name('report-card');
        Route::get('{student}/report-card/download', [\App\Http\Controllers\ReportController::class, 'downloadPdf'])->name('report-card.download');
        Route::post('{student}/report-card/email', [\App\Http\Controllers\ReportController::class, 'emailReportCard'])->name('report-card.email');
    });

    Route::middleware('can:reports.export')->group(function () {
        Route::get('reports/broadsheet/export', [\App\Http\Controllers\ReportController::class, 'broadsheetExport'])->name('reports.broadsheet.export');
        Route::get('reports/export/results-csv', [\App\Http\Controllers\ExportController::class, 'resultsCsv'])->name('reports.export.results-csv');
        Route::get('reports/export/report-cards-zip', [\App\Http\Controllers\ExportController::class, 'reportCardsZip'])->name('reports.export.report-cards-zip');
        Route::get('reports/export/full-results-xlsx', [\App\Http\Controllers\ExportController::class, 'fullResultsXlsx'])->name('reports.export.full-results-xlsx');
    });

    // ---------- Learning Flags ----------
    Route::prefix('learning')->name('learning.')->middleware('can:students.view')->group(function () {
        Route::get('flags', [\App\Http\Controllers\LearningFlagController::class, 'index'])->name('flags');
        Route::get('flags/{student}', [\App\Http\Controllers\LearningFlagController::class, 'show'])->name('flags.student');
        Route::post('recompute', [\App\Http\Controllers\LearningFlagController::class, 'recompute'])->name('recompute');
    });

    // ---------- Attendance ----------
    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::middleware('can:attendance.take')->group(function () {
            Route::get('/', [\App\Http\Controllers\AttendanceController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\AttendanceController::class, 'store'])->name('store');
        });

        Route::middleware('can:attendance.view')->group(function () {
            Route::get('summary', [\App\Http\Controllers\AttendanceController::class, 'summary'])->name('summary');
            Route::get('student/{student}', [\App\Http\Controllers\AttendanceController::class, 'student'])->name('student');
        });

        Route::middleware('role:school_admin,super_admin')->group(function () {
            Route::get('reasons', [\App\Http\Controllers\AttendanceController::class, 'reasons'])->name('reasons');
            Route::post('reasons', [\App\Http\Controllers\AttendanceController::class, 'storeReason'])->name('reasons.store');
            Route::delete('reasons/{reason}', [\App\Http\Controllers\AttendanceController::class, 'destroyReason'])->name('reasons.destroy');
        });
    });

    // ---------- Timetable ----------
    Route::prefix('timetable')->name('timetable.')->group(function () {
        Route::middleware('can:timetable.view')->group(function () {
            Route::get('/', [\App\Http\Controllers\TimetableController::class, 'index'])->name('index');
            Route::get('teacher', [\App\Http\Controllers\TimetableController::class, 'teacher'])->name('teacher');
            Route::get('teacher/{teacher}', [\App\Http\Controllers\TimetableController::class, 'teacher'])->name('teacher.show');
            Route::get('teacher/{teacher}/pdf', [\App\Http\Controllers\TimetableController::class, 'teacherPdf'])->name('teacher.pdf');

            Route::get('{classroom}', [\App\Http\Controllers\TimetableController::class, 'show'])->name('show');
            Route::get('{classroom}/pdf', [\App\Http\Controllers\TimetableController::class, 'downloadPdf'])->name('pdf');
            Route::get('{classroom}/xlsx', [\App\Http\Controllers\TimetableController::class, 'downloadXlsx'])->name('xlsx');
        });

        Route::middleware('can:timetable.edit')->group(function () {
            Route::post('{classroom}', [\App\Http\Controllers\TimetableController::class, 'save'])->name('save');
            Route::post('{classroom}/email', [\App\Http\Controllers\TimetableEmailController::class, 'send'])->name('email');
        });
    });

    // Classes overview
    Route::get('classes-overview', [\App\Http\Controllers\ClassroomController::class, 'overview'])
        ->middleware('can:students.view')
        ->name('classes.overview');

    // Interventions
    Route::middleware(['feature:interventions', 'can:students.edit'])->group(function () {
        Route::resource('interventions', InterventionController::class);
    });
});