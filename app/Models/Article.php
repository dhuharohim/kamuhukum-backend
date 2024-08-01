<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Article extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'edition_id',
        'article_title',
        'author',
        'affiliation',
        'country',
        'keywords',
        'abstract',
        'reference',
        'path',
        'slug',
        'viewed',
    ];

     protected $appends = [
        'signed_article_pdf',
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

    public function keywords() : HasMany
    {
        return $this->hasMany(ArticleKeyword::class, 'article_id', 'id');
    }

    public function references() : HasMany
    {
        return $this->hasMany(ArticleReference::class, 'article_id', 'id');
    }

    public function edition() : BelongsTo
    {
        return $this->belongsTo(Edition::class, 'edition_id', 'id');
    }

    public function getSignedArticlePdfAttribute() {
        if(empty($this->path))
            return;

        return config('app.url').$this->path;
    }

    public function viewedIncrease() {
        $this->viewed++;
        return $this->save();
    }
}
