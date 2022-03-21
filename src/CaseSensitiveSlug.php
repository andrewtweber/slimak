<?php

namespace Slimak;

use Illuminate\Database\Eloquent\Builder;

/**
 * Trait CaseSensitiveSlug
 *
 * @package Slimak
 */
trait CaseSensitiveSlug
{
    /**
     * @param  Builder  $query
     * @param  string  $slug
     */
    public function scopeWhereSlug($query, $slug)
    {
        $query->whereRaw('BINARY `slug` = ?', [$slug]);
    }
}
