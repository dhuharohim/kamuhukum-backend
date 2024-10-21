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
        'uuid',
        'user_id',
        'edition_id',
        'article_for',
        'prefix',
        'title',
        'subtitle',
        'section',
        'status',
        'comments_for_editor',
        'abstract',
        'slug',
        'viewed',
        'published_date'
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

    public function keywords(): HasMany
    {
        return $this->hasMany(ArticleKeyword::class, 'article_id', 'id');
    }

    public function references(): HasMany
    {
        return $this->hasMany(ArticleReference::class, 'article_id', 'id');
    }

    public function edition(): BelongsTo
    {
        return $this->belongsTo(Edition::class, 'edition_id', 'id');
    }

    public function authors(): HasMany
    {
        return $this->hasMany(ArticleContributors::class, 'article_id', 'id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(ArticleFile::class, 'article_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function viewedIncrease()
    {
        $this->viewed++;
        return $this->save();
    }

    public function comments(): HasMany
    {
        return $this->hasMany(ArticleComment::class, 'article_id', 'id');
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'editor_id', 'id');
    }
}
