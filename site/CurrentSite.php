<?php

namespace WPFluent\Site;

class CurrentSite extends Base
{
    public function newQuery()
    {
        return parent::newQuery()->site(get_current_blog_id());
    }
}
