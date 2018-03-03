<?php

namespace WPFluent\PostType;

use WPFluent\PostType\Traits\Content;
use WPFluent\PostType\Traits\Permalink;

class Page extends Native
{
    use Content,
        Permalink;

    public $post_type = 'page';

    public function getCacheableAccessors()
    {
        return array_merge(parent::getCacheableAccessors(), [
            'permalink',
        ]);
    }
}
