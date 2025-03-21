<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Section extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'position', 'is_active', 'preview', 'section_for'];

    protected $appends = [
        'signed_preview_image'
    ];

    public function getSignedPreviewImageAttribute()
    {
        if (empty($this->preview))
            return;

        return config('app.url') . 'admin/storage/' . $this->preview;
    }

    public function contents(): HasMany
    {
        return $this->hasMany(SectionContent::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(SectionMedia::class);
    }
}
