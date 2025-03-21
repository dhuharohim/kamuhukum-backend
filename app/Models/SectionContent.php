<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SectionContent extends Model
{
    use HasFactory;

    protected $fillable = ['section_id', 'key', 'value', 'type'];

    protected $appends = ['value_image_url'];


    public function getValueImageUrlAttribute()
    {
        if ($this->type !== 'image' && $this->value) return;

        return config('app.url') . 'admin/storage/' . $this->value;
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }
}
