<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ArticleContributors extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'article_id',
        'contributor_role',
        'given_name',
        'family_name',
        'contact',
        'phone',
        'preferred_name',
        'affilation',
        'country',
        'img_url',
        'homepage_url',
        'orcid_id',
        'mailing_address',
        'bio_statement',
        'reviewing_interest',
        'principal_contact',
        'in_browse_list'
    ];

    protected $appends = [
        'name_formatted'
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

    public function getNameFormattedAttribute()
    {
        $preferredName = '';
        if (!empty($this->preferred_name) && $this->preferred_name !== 'null') {
            $preferredName = ' (' . $this->preferred_name . ') ';
        } else {
            $preferredName = '';
        }

        return $this->given_name . ' ' . $this->family_name . $preferredName .  ' - ' . $this->affilation;
    }
}
