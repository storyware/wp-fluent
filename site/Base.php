<?php

namespace WPFluent\Site;

use Closure;
use WPFluent\Support\Model;
use WP_Site;
use WP_Site_Query;

abstract class Base extends Model
{
    protected $site;

    public function __construct(WP_Site $site = null)
    {
        $this->site = $site ?: new WP_Site((object) []);

        parent::__construct($this->site->to_array());
    }

    public function newQuery()
    {
        $query = (new Builder(new Query(new WP_Site_Query)))->setModel($this);

        return $query;
    }

    public function getBlogIdAttribute($value)
    {
        return (int)$value;
    }

    public function getSiteIdAttribute($value)
    {
        return (int)$value;
    }

    public function toggle(Closure $callback, array $parameters = [])
    {
        return static::toggleSite($this, $callback, $parameters);
    }

    public function __get($key)
    {
        if (is_null($value = parent::__get($key))) {
            $value = $this->site->$key;
        }

        return $value;
    }

    public static function toggleSite(Base $site, Closure $callback, array $parameters = [])
    {
        array_unshift($parameters, $site);

        switch_to_blog($site->blog_id);

        $result = call_user_func_array($callback, $parameters);

        restore_current_blog();

        return $result;
    }
}
