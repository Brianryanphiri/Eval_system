<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    use BelongsToSchool;
    protected $table = 'announcements';

    protected $fillable = [
        'school_id',
        'author_id',
        'title',
        'body',
        'audience',
        'audience_class_id',
        'priority',
        'publish_at',
        'expires_at',
        'is_published',
    ];

    protected $casts = [
        'publish_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_published' => 'boolean',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class, 'audience_class_id');
    }

    /**
     * Only published announcements — not scheduled in the future.
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true)
            ->where(function ($q) {
                $q->whereNull('publish_at')
                  ->orWhere('publish_at', '<=', now());
            });
    }

    /**
     * Not expired.
     */
    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>=', now());
        });
    }

    /**
     * Announcements visible to a specific role. Falls back to role-specific
     * scoping: admins/teachers see everything, students/parents see audience
     * announcements only.
     */
    public function scopeVisibleTo($query, string $role)
    {
        if (in_array($role, ['school_admin', 'super_admin'], true)) {
            return $query;
        }

        if (in_array($role, ['teacher'], true)) {
            return $query->whereIn('audience', ['all', 'staff', 'teachers']);
        }

        if ($role === 'student') {
            return $query->whereIn('audience', ['all', 'students']);
        }

        if ($role === 'parent') {
            return $query->whereIn('audience', ['all', 'guardians']);
        }

        return $query->where('audience', 'all');
    }

    /**
     * Sort by priority (urgent first), then recency.
     */
    public function scopeByImportance($query)
    {
        return $query->orderByRaw("FIELD(priority, 'urgent', 'high', 'normal', 'low')")
            ->orderByDesc('publish_at')
            ->orderByDesc('created_at');
    }

    /**
     * Label for priority.
     */
    public function getPriorityLabelAttribute(): string
    {
        return match ($this->priority) {
            'urgent' => 'Urgent',
            'high' => 'High',
            'low' => 'Low',
            default => 'Normal',
        };
    }

    /**
     * Color hint for the priority badge.
     */
    public function getPriorityColorAttribute(): string
    {
        return match ($this->priority) {
            'urgent' => 'red',
            'high' => 'amber',
            'low' => 'grey',
            default => 'blue',
        };
    }

    /**
     * Label for audience.
     */
    public function getAudienceLabelAttribute(): string
    {
        if ($this->audience === 'class' && $this->classroom) {
            return 'Class: ' . $this->classroom->name;
        }

        return match ($this->audience) {
            'staff' => 'Staff',
            'teachers' => 'Teachers',
            'students' => 'Students',
            'guardians' => 'Guardians',
            'class' => 'Class',
            default => 'Everyone',
        };
    }
}