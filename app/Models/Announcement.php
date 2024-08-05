<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Announcement extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'edition_id',
        'announcement_for',
        'title',
        'slug',
        'description',
        'submission_deadline_date',
        'published_date',
        'extend_submission_date',
    ];

    protected $appends = [
        'published_date_formatted',
        'submission_deadline_formatted',
        'created_at_formatted',
    ];
    /**
     * Activity log options.
     *
     * @return LogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll()->logOnlyDirty();
    }

    public function edition(): BelongsTo
    {
        return $this->belongsTo(Edition::class, 'edition_id', 'id');
    }

    public function getPublishedDateFormattedAttribute()
    {
        if (empty($this->published_date))
            return;

        return Carbon::parse($this->published_date)->format('d-m-Y');
    }

    public function getSubmissionDeadlineFormattedAttribute()
    {
        if (empty($this->submission_deadline_date))
            return;

        return Carbon::parse($this->submission_deadline_date)->format('d-m-Y');
    }

    public function getCreatedAtFormattedAttribute()
    {
        if (empty($this->created_at))
            return;

        return Carbon::parse($this->created_at)->format('d-m-Y');
    }

    public function criterias()
    {
        return $this->hasMany(AnnouncementCriteria::class);
    }
}
