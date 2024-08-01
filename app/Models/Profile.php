<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;

class Profile extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'affiliation',
        'country',
        'is_data_collected'
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
