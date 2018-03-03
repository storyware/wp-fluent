<?php

namespace WPFluent\PostType;

use Exception;
use WPFluent\Support\Model;
use WP_Post;
use WP_Query;

abstract class Base extends Model
{
    public $post_type;

    public $post_status;

    protected $post;

    public function __construct(WP_Post $post = null)
    {
        $this->post = $post ?: new WP_Post((object) [
            'post_status' => $this->post_status,
            'post_type'   => $this->post_type,
        ]);

        parent::__construct($this->post->to_array());
    }

    public function newQuery()
    {
        $query = (new Builder(new Query(new WP_Query)))->setModel($this);

        return $query->type($this->post_type)->paginate(false);
    }

    public function getCacheableAccessors()
    {
        return array_merge(parent::getCacheableAccessors(), [
            'next_post',
            'password_form',
            'previous_post',
        ]);
    }

    public function getPostAuthorAttribute($value)
    {
        if (is_numeric($value)) {
            $value = (int)$value;
        }

        return $value;
    }

    public function getCommentCountAttribute($value)
    {
        if (is_numeric($value)) {
            $value = (int)$value;
        }

        return $value;
    }

    public function getNextPostAttribute()
    {
        return $this->getNextPost();
    }

    public function hasNextPost()
    {
        return !is_null($this->next_post);
    }

    public function getPreviousPostAttribute()
    {
        return $this->getPreviousPost();
    }

    public function hasPreviousPost()
    {
        return !is_null($this->previous_post);
    }

    public function getPasswordFormAttribute()
    {
        if ($this->isPasswordRequired()) {
            return get_the_password_form($this->post);
        }
    }

    public function isPasswordRequired()
    {
        return post_password_required($this->post);
    }

    public function insert()
    {
        return wp_insert_post($this->getDirty());
    }

    public function update()
    {
        wp_update_post(array_merge(
            $this->getDirty(),
            ['ID' => $this->ID]
        ));
    }

    public function delete()
    {
        wp_trash_post($this->ID);
    }

    public function getMeta($key, $single = false)
    {
        return get_post_meta($this->ID, $key, $single);
    }

    public function addMeta($key, $value, $unique = false)
    {
        return add_post_meta($this->ID, $key, $value, $unique);
    }

    public function updateMeta($key, $value, $previousValue = '')
    {
        return update_post_meta($this->ID, $key, $value, $previousValue);
    }

    public function deleteMeta($key, $value = '')
    {
        return delete_post_meta($this->ID, $key, $value);
    }

    public function replaceTerms(array $terms, $taxonomy)
    {
        return $this->saveTerms($terms, $taxonomy);
    }

    public function saveTerms(array $terms, $taxonomy, $append = false)
    {
        return wp_set_object_terms($this->ID, $terms, $taxonomy, $append);
    }

    protected function getNextPost($inSameTerm = false, array $excludedTerms = [], $taxonomy = 'category')
    {
        return $this->getAdjacentPost($inSameTerm, $excludedTerms, false, $taxonomy);
    }

    protected function getPreviousPost($inSameTerm = false, array $excludedTerms = [], $taxonomy = 'category')
    {
        return $this->getAdjacentPost($inSameTerm, $excludedTerms, true, $taxonomy);
    }

    protected function getAdjacentPost($inSameTerm = false, array $excludedTerms = [], $previous = true, $taxonomy = 'category')
    {
        global $post;

        $globalPost = $post;

        $post = get_post($this->post);

        $adjacentPost = get_adjacent_post($inSameTerm, $excludedTerms, $previous, $taxonomy);

        $post = get_post($globalPost);

        if ($adjacentPost instanceof WP_Post) {
            return self::make($adjacentPost);
        }
    }

    public static function getPostType()
    {
        static $postType;

        if (is_null($postType)) {
            $post = new static;

            $postType = $post->post_type;
        }

        return $postType;
    }

    public static function doDeletePost($id)
    {
        static::doPostTypeAction('delete_post', get_post_type($id), compact('id'));
    }

    public static function doRefreshRewrites($id = 0)
    {
        $type = get_post_type($id);

        if (static::doPostTypeFilter('refreshRewrites', $type, compact('id'))) {
            parent::doRefreshRewrites($id, $type);
        }
    }

    public static function doSavePost($id, WP_Post $post)
    {
        static::doPostTypeAction('save_post', $post->post_type, compact('id', 'post'));
    }

    public static function doTrashPost($id)
    {
        static::doPostTypeAction('trash_post', get_post_type($id), compact('id'));
    }

    public static function doGetMetaData($value, $id, $key = '', $single = false)
    {
        return static::doPostTypeFilter('get_post_metadata', get_post_type($id), compact('value', 'id', 'key', 'single'));
    }

    public static function doGetPostThumbnailId($value, $id, $key = '_thumbnail_id', $single = true, $filter = true)
    {
        if (str_is($key, '_thumbnail_id') && $single) {
            remove_filter('get_post_metadata', [__CLASS__, 'doGetPostThumbnailId'], 10, 4);

            $value = get_post_thumbnail_id($id);

            add_filter('get_post_metadata', [__CLASS__, 'doGetPostThumbnailId'], 10, 4);

            if ($filter) {
                $value = static::doPostTypeFilter('get_post_thumbnail_id', get_post_type($id), compact('value', 'id'));
            }

            $value = is_numeric($value) ? (int)$value : null;
        }

        return $value;
    }

    public static function doPostTypeLink($url, WP_Post $post, $leavename = false, $sample = false)
    {
        return static::doPostTypeFilter('post_type_link', $post->post_type, compact('url', 'post', 'leavename', 'sample'));
    }

    public static function doThePosts($posts, WP_Query $query)
    {
        if ($postType = $query->get('post_type')) {
            $posts = static::doPostTypeFilter('the_posts', $postType, compact('posts', 'query'));
        }

        return $posts;
    }

    public static function getPostThumbnailId($id)
    {
        return static::doGetPostThumbnailId(null, $id, '_thumbnail_id', true, false);
    }

    public static function registerHooks($class)
    {
        add_action('delete_post', [__CLASS__, 'doDeletePost'], 10, 1);
        add_action('delete_post', [__CLASS__, 'doRefreshRewrites'], PHP_INT_MAX, 1);
        add_action('save_post', [__CLASS__, 'doSavePost'], 10, 3);
        add_action('save_post', [__CLASS__, 'doRefreshRewrites'], PHP_INT_MAX, 1);
        add_action('trash_post', [__CLASS__, 'doTrashPost'], 10, 1);
        add_action('trash_post', [__CLASS__, 'doRefreshRewrites'], PHP_INT_MAX, 1);
        add_filter('get_post_metadata', [__CLASS__, 'doGetMetaData'], 10, 4);
        add_filter('get_post_metadata', [__CLASS__, 'doGetPostThumbnailId'], 10, 4);
        add_filter('post_type_link', [__CLASS__, 'doPostTypeLink'], 10, 4);
        add_filter('the_posts', [__CLASS__, 'doThePosts'], PHP_INT_MAX, 2);

        add_action('init', [$class, 'registerPostType'], -PHP_INT_MAX, 0);
    }

    public static function registerPostType()
    {
        throw new Exception('A post type must override registerPostType.');
    }

    protected static function doPostTypeAction($action, $type, array $args = [])
    {
        static::doAction(implode('/', [$type, $action]), $args);
    }

    protected static function doPostTypeFilter($filter, $type, array $args = [])
    {
        return static::doFilter(implode('/', [$type, $filter]), $args);
    }
}
