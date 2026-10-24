<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Collection;

class TeacherScopeService
{
    /** Roles that can see everything in their school, no scoping applied */
    private array $unrestrictedRoles = ['super_admin', 'school_admin', 'academic_director', 'head_of_department'];

    public function isUnrestricted(User $user): bool
    {
        return in_array($user->role, $this->unrestrictedRoles, true);
    }

    /**
     * Classroom IDs this user is allowed to touch. Null means "no restriction, see everything in the school"
     * (used for admins), an empty collection means genuinely nothing (a teacher assigned to no classes yet).
     */
    public function accessibleClassroomIds(User $user): ?Collection
    {
        if ($this->isUnrestricted($user)) {
            return null;
        }

        if ($user->role !== 'teacher') {
            return collect(); // parents/students never reach these staff routes, but fail closed just in case
        }

        $homeroom = Classroom::where('school_id', $user->school_id)
            ->where('homeroom_teacher_id', $user->id)
            ->pluck('id');

        $subjectTaught = \DB::table('class_subject')
            ->join('classes', 'classes.id', '=', 'class_subject.class_id')
            ->where('classes.school_id', $user->school_id)
            ->where('class_subject.teacher_id', $user->id)
            ->pluck('class_subject.class_id');

        return $homeroom->merge($subjectTaught)->unique()->values();
    }

    public function canAccessClassroom(User $user, int $classroomId): bool
    {
        if ($this->isUnrestricted($user)) {
            return true;
        }

        return $this->accessibleClassroomIds($user)->contains($classroomId);
    }

    public function canAccessStudent(User $user, Student $student): bool
    {
        if ($this->isUnrestricted($user)) {
            return true;
        }

        $classroom = $student->currentClassroom();

        return $classroom && $this->canAccessClassroom($user, $classroom->id);
    }

    public function canAccessAssessment(User $user, Assessment $assessment): bool
    {
        if ($this->isUnrestricted($user)) {
            return true;
        }

        return $this->canAccessClassroom($user, $assessment->class_id);
    }
}