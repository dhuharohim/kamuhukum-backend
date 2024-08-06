<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ProfileAuthor extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'user_id',
        'author_type',
        'given_name',
        'family_name',
        'phone',
        'email',
        'preferred_name',
        'affilation',
        'country',
        'img_url',
        'homepage_url',
        'orcid_id',
        'mailing_address',
        'bio_statement',
        'reviewing_interest'
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
