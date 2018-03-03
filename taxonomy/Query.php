<?php

namespace WPFluent\Taxonomy;

use Closure;
use WPFluent\Support\Query as BaseQuery;
use InvalidArgumentException;
use WP_Term_Query;

class Query extends BaseQuery
{
    public function __construct(WP_Term_Query $query)
    {
        parent::__construct($query);
    }

    public function getExcludeQueryVar($value)
    {
        if (is_null($value)) {
            $value = [];
        }

        return $value;
    }

    public function getIncludeQueryVar($value)
    {
        if (is_null($value)) {
            $value = [];
        }

        return $value;
    }

    public function getMetaQueryQueryVar($value)
    {
        if (is_null($value)) {
            $value = [];
        }

        return $value;
    }

    public function execute()
    {
        $terms = $this->query->get_terms();

        return $terms;
    }

    public function terms(array $ids, $operator = 'IN')
    {
        $ids = count($ids) > 0 ? $ids : [PHP_INT_MAX];

        switch ($operator) {
            case 'IN':
                $exclude = $this->getQueryVar('exclude');
                $this->setQueryVar('exclude', array_diff($exclude, $ids));
                $this->setQueryVar('include', $ids);
                break;
            case 'NOT IN':
                $include = $this->getQueryVar('include');
                $this->setQueryVar('include', array_diff($include, $ids));
                $this->setQueryVar('exclude', $ids);
                break;
        }

        return $this;
    }

    public function term($id)
    {
        if (is_int($id)) {
            $ids = [];

            if ($id > 0) {
                $ids[] = $id;
            }

            $this->terms($ids);
        } elseif (is_string($id)) {
            $this->slug($id);
        } else {
            throw new InvalidArgumentException();
        }

        return $this;
    }

    public function taxonomy($taxonomy)
    {
        $this->setQueryVar('taxonomy', $taxonomy);

        return $this;
    }

    public function orderBy($orderby = 'name', $order = 'ASC')
    {
        $this->setQueryVar('orderby', $orderby);
        $this->setQueryVar('order', $order);

        return $this;
    }

    public function hideEmpty($empty = true)
    {
        if (is_bool($empty)) {
            $this->setQueryVar('hide_empty', $empty);
        } else {
            throw new InvalidArgumentException();
        }

        return $this;
    }

    public function number($number)
    {
        if (is_int($number)) {
            $this->setQueryVar('number', $number);
        } else {
            throw new InvalidArgumentException();
        }

        return $this;
    }

    public function limit($number)
    {
        return $this->number($number);
    }

    public function slug($slug)
    {
        if (is_string($slug)) {
            $this->setQueryVar('slug', $slug);
        } else {
            throw new InvalidArgumentException();
        }

        return $this;
    }

    public function slugs(array $slugs)
    {
        $this->setQueryVar('slug', $slugs);

        return $this;
    }

    public function childOf($id)
    {
        if (is_int($id)) {
            $this->setQueryVar('child_of', $id);
        } else {
            throw new InvalidArgumentException();
        }

        return $this;
    }

    public function parent($id)
    {
        if (is_int($id)) {
            $this->setQueryVar('parent', $id);
        } else {
            throw new InvalidArgumentException();
        }

        return $this;
    }

    public function metaCompare($compare)
    {
        $this->setQueryVar('meta_compare', $compare);

        return $this;
    }

    public function metaKey($key)
    {
        $this->setQueryVar('meta_key', $key);

        return $this;
    }

    public function metaType($type)
    {
        $this->setQueryVar('meta_type', $type);

        return $this;
    }

    public function metaValue($value)
    {
        $this->setQueryVar('meta_value', $value);

        return $this;
    }

    public function meta($key, $value, $compare = '=', $type = 'CHAR')
    {
        $meta_query = compact('key', 'value', 'compare', 'type');

        $meta_query = array_merge($this->getQueryVar('meta_query'), [$meta_query]);

        $this->setQueryVar('meta_query', $meta_query);

        return $this;
    }

    public function metas(Closure $callback, $relation = 'AND')
    {
        call_user_func($callback, $this);

        $meta_query = $this->getQueryVar('meta_query');

        if (count($meta_query) > 1) {

            $meta_query = array_merge(['relation' => $relation], $meta_query);

            $this->setQueryVar('meta_query', $meta_query);
        }

        return $this;
    }

    public function metasSub(Closure $callback, $relation = 'AND')
    {
        $query = (new static(new WP_Term_Query));

        $query->metas($callback, $relation);

        $meta_query = $query->getQueryVar('meta_query');

        $meta_query = array_merge($this->getQueryVar('meta_query'), [$meta_query]);

        $this->setQueryVar('meta_query', $meta_query);

        return $this;
    }
}
