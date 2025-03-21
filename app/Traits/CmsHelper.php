<?php

namespace App\Traits;

use App\Models\Section;

trait CmsHelper
{
    protected function getCmsSection($from, $slug)
    {
        return Section::where('section_for', $from)
            ->where('slug', $slug)
            ->with(['contents', 'media'])
            ->first();
    }
}
