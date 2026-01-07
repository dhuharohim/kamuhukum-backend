<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Services\StorageService;

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

        $storage = new StorageService();
        return $storage->cdnUrl($this->preview);
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
