<?php

namespace WPFluent\Taxonomy;

use WPFluent\PostType\Post;
use WPFluent\Support\Builder as BaseBuilder;
use InvalidArgumentException;

class Builder extends BaseBuilder
{
    public function find($id)
    {
        return $this->term($id)->first();
    }

    public function findAll(array $ids)
    {
        return $this->terms($ids)->get();
    }

    public function findBySlug($slug)
    {
        return $this->term($slug)->first();
    }

    public function first()
    {
        return $this->limit(1)->get()->first();
    }

    public function post($postId)
    {
        if (is_int($postId)) {
            $this->posts([$postId]);
        } else {
            throw new InvalidArgumentException();
        }

        return $this;
    }

    public function posts(array $postIds)
    {
        $ids = wp_get_object_terms($postIds, $this->model->taxonomy, ['fields' => 'ids']);

        return $this->terms($ids);
    }

    public function postType($postType)
    {
        $ids = Post::query()->type($postType)->get()->lists('ID');

        return $this->posts($ids);
    }
}
