<?php

namespace WPFluent\PostType\Traits;

use WPFluent\Support\Filter;

trait Permalink
{
    use CachePermalink;

    public function getPermalinkAttribute()
    {
        $permalink = get_permalink($this->ID);

        if (Filter::isUrl($permalink)) {
            return $permalink;
        }
    }

    public function getUrlAttribute()
    {
        return $this->permalink;
    }
}
