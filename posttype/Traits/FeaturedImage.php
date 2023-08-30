<?php

namespace WPFluent\PostType\Traits;

use WPFluent\PostType\Image;

trait FeaturedImage
{
    public function getFeaturedImageAttribute()
    {
        $id = get_post_thumbnail_id($this->ID);

        if (is_numeric($id)) {
            return Image::query()->find((int)$id);
        }
    }

    public function hasFeaturedImage()
    {
        return has_post_thumbnail($this->ID);
    }

    public function attachFeaturedImage($id)
    {
        return set_post_thumbnail($this->ID, $id);
    }

    public function detachFeaturedImage()
    {
        return delete_post_thumbnail($this->ID);
    }
}
