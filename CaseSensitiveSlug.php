<?php namespace Slimak;

trait CaseSensitiveSlug
{
    /**
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $slug
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereSlug($query, $slug)
    {
        return $query->whereRaw('BINARY `slug` = ?', [$slug]);
    }
}

