<?php

namespace WPFluent\PostType;

use WPFluent\PostType\Traits\Content;
use WPFluent\PostType\Traits\Excerpt;
use WPFluent\PostType\Traits\Permalink;

class Post extends Native
{
    use Content,
        Excerpt,
        Permalink;

    public $post_type = 'post';

    public function getCacheableAccessors()
    {
        return array_merge(parent::getCacheableAccessors(), [
            'permalink',
        ]);
    }
}
