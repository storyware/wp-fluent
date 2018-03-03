<?php

namespace WPFluent\PostType\Traits;

use WPFluent\Support\Filter;

trait Archive
{
    public function getArchiveUrlAttribute()
    {
        $archiveUrl = get_post_type_archive_link($this->post_type);

        if (Filter::isUrl($archiveUrl)) {
            return $archiveUrl;
        }
    }

    public static function getArchiveUrl()
    {
        $instance = (new static);

        return $instance->archive_url;
    }
}
