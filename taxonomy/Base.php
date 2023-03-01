<?php

namespace WPFluent\Taxonomy;

use Exception;
use WPFluent\Support\Model;
use WP_Term;
use WP_Term_Query;

abstract class Base extends Model
{
    public $taxonomy;

    protected $term;

    public function __construct(WP_Term $term = null)
    {
        $this->term = $term ?: new WP_Term((object) [
            'taxonomy' => $this->taxonomy,
        ]);

        parent::__construct($this->term->to_array());
    }

    public function newQuery()
    {
        $query = (new Builder(new Query(new WP_Term_Query)))->setModel($this);

        return $query->taxonomy($this->taxonomy)->hideEmpty(false);
    }

    public function getCacheableAccessors()
    {
        return array_merge(parent::getCacheableAccessors(), [
            'permalink',
        ]);
    }

    public function getIdAttribute($value)
    {
        return (int)$value;
    }

    public function getPermalinkAttribute()
    {
        $permalink = get_term_link($this->term_id, $this->taxonomy);

        if (!is_wp_error($permalink)) {
            return $permalink;
        }
    }

    public function getUrlAttribute()
    {
        return $this->permalink;
    }

    public static function getTaxonomy()
    {
        $term = new static;

        $taxonomy = $term->taxonomy;

        return $taxonomy;
    }

    public static function in(array $ids)
    {
        return count(array_intersect($ids, self::get()->lists('term_id'))) > 0;
    }

    public static function registerHooks($class)
    {
        add_action('init', [$class, 'registerTaxonomy'], -PHP_INT_MAX);
    }

    public static function registerTaxonomy()
    {
        throw new Exception('A new taxonomy must override registerTaxonomy.');
    }
}
