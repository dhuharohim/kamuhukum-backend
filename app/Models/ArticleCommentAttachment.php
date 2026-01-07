<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\StorageService;

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

        $storage = new StorageService();
        return $storage->cdnUrl($this->file_path);
    }
}
