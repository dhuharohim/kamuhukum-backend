<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Edition extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'name_edition',
        'edition_for',
        'volume',
        'issue',
        'description',
        'publish_date',
        'status',
        'slug',
        'year',
        'img_path',
        'pdf_path'
    ];

    protected $appends = [
        'signed_edition_image',
        'signed_edition_pdf',
        'publish_date_formatted'
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

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class, 'edition_id', 'id');
    }

    public function getSignedEditionImageAttribute()
    {
        if (empty($this->img_path))
            return;

        return config('app.url') . '/storage/' . $this->img_path;
    }

    public function getSignedEditionPdfAttribute()
    {
        if (empty($this->img_path))
            return;

        return config('app.url') . '/storage/' . $this->pdf_path;
    }

    public function getPublishDateFormattedAttribute()
    {
        if (empty($this->publish_date))
            return;

        return Carbon::parse($this->publish_date)->format('d-m-Y');
    }
}
