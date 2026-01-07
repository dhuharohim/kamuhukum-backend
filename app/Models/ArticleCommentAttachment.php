<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleCommentAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'article_comment_id',
        'file_path',
        'file_name',
    ];

    protected $appends = [
        'signed_file_path',
    ];

    public function comment()
    {
        return $this->belongsTo(ArticleComment::class, 'article_comment_id');
    }

    public function getSignedFilePathAttribute()
    {
        if (empty($this->file_path))
            return;

        return config('app.url') . 'storage/' . $this->file_path;
    }
}
