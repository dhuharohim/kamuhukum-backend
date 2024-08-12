<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ArticleFile extends Model
{
    use HasFactory, LogsActivity;
    protected $fillable = [
        'article_id',
        'file_name',
        'file_path',
        'type',
    ];

    protected $appends = [
        'signed_file_path'
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

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class, 'article_id', 'id');
    }

    public function getSignedFilePathAttribute()
    {
        if (empty($this->file_path))
            return;

        return config('app.url') . 'admin/storage/' . $this->file_path;
    }
}
