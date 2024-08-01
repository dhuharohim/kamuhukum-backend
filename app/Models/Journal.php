<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;


class Journal extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $table = 'journals';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'upload_by',
        'abstract',
        'abstrak',
        'category_id',
        'view',
        'pdf_path'
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
}
