<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AnnouncementCriteria extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'announcement_criteria';
    protected $fillable = [
        'announcement_id',
        'name_criteria',
    ];

    public function criteria() : BelongsTo
    {
        return $this->belongsTo(Announcement::class, 'announcement_id', 'id');
    }
}
