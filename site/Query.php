<?php

namespace WPFluent\Site;

use Illuminate\Support\Str;
use WPFluent\Support\Query as BaseQuery;
use InvalidArgumentException;
use WP_Site_Query;

class Query extends BaseQuery
{
    public function __construct(WP_Site_Query $query)
    {
        parent::__construct($query);
    }

    public function execute()
    {
        $sites = $this->query->get_sites();

        return $sites;
    }

    public function site($id)
    {
        if (is_int($id)) {
            $this->setQueryVar('ID', $id);
        } else {
            throw new InvalidArgumentException();
        }

        return $this;
    }

    public function sites(array $ids, $operator = 'IN')
    {
        switch ($operator) {
            case 'IN':
                $this->setQueryVar('site__in', $ids);
                break;
            case 'NOT IN':
                $this->setQueryVar('site__not_in', $ids);
                break;
            default:
                throw new InvalidArgumentException();
        }

        return $this;
    }

    public function number($number)
    {
        $this->setQueryVar('number', $number);

        return $this;
    }

    public function limit($limit)
    {
        return $this->number($limit);
    }

    public function offset($offset)
    {
        $this->setQueryVar('offset', $offset);

        return $this;
    }

    public function orderBy($orderby = 'id', $order = 'ASC')
    {
        $this->setQueryVar('orderby', $orderby);
        $this->setQueryVar('order', $order);

        return $this;
    }

    public function network($id)
    {
        if (is_int($id)) {
            $this->setQueryVar('network_id', $id);
        } else {
            throw new InvalidArgumentException();
        }

        return $this;
    }

    public function networks(array $ids, $operator = 'IN')
    {
        switch ($operator) {
            case 'IN':
                $this->setQueryVar('network__in', $ids);
                break;
            case 'NOT IN':
                $this->setQueryVar('network__not_in', $ids);
                break;
            default:
                throw new InvalidArgumentException();
        }

        return $this;
    }

    public function domain($domain)
    {
        if (is_string($domain)) {
            $this->setQueryVar('domain', $domain);
        } else {
            throw new InvalidArgumentException();
        }

        return $this;
    }

    public function domains(array $domains, $operator = 'IN')
    {
        switch ($operator) {
            case 'IN':
                $this->setQueryVar('domain__in', $domains);
                break;
            case 'NOT IN':
                $this->setQueryVar('domain__not_in', $domains);
                break;
            default:
                throw new InvalidArgumentException();
        }

        return $this;
    }

    public function path($path)
    {
        if (is_string($path)) {
            $this->setQueryVar('path', $path);
        } else {
            throw new InvalidArgumentException();
        }

        return $this;
    }

    public function paths(array $paths, $operator = 'IN')
    {
        switch ($operator) {
            case 'IN':
                $this->setQueryVar('path__in', $paths);
                break;
            case 'NOT IN':
                $this->setQueryVar('path__not_in', $paths);
                break;
            default:
                throw new InvalidArgumentException();
        }

        return $this;
    }

    public function archive($isArchive = true)
    {
        if (is_bool($isArchive)) {
            $this->setQueryVar('archive', ($isArchive ? '1' : '0'));
        } else {
            throw new InvalidArgumentException();
        }

        return $this;
    }

    public function mature($isMature = true)
    {
        if (is_bool($isMature)) {
            $this->setQueryVar('mature', ($isMature ? '1' : '0'));
        } else {
            throw new InvalidArgumentException();
        }

        $this->mature = $isMature ? '1' : '0';

        return $this;
    }

    public function spam($isSpam = true)
    {
        if (is_bool($isSpam)) {
            $this->setQueryVar('spam', ($isSpam ? '1' : '0'));
        } else {
            throw new InvalidArgumentException();
        }

        return $this;
    }

    public function deleted($isDeleted = true)
    {
        if (is_bool($isDeleted)) {
            $this->setQueryVar('deleted', ($isDeleted ? '1' : '0'));
        } else {
            throw new InvalidArgumentException();
        }

        return $this;
    }

    public function search($terms, array $columns = [])
    {
        $this->setQueryVar('search', $terms);
        $this->setQueryVar('search_columns', $columns);

        return $this;
    }

    public function updateSiteCache($enable = false)
    {
        if (is_bool($enable)) {
            $this->setQueryVar('update_site_cache', $enable);
        } else {
            throw new InvalidArgumentException();
        }

        return $this;
    }

    // TODO: Date Query implementation
    public function date()
    {
        return $this;
    }

    public function __call($method, array $parameters)
    {
        if (Str::is($method, 'public')) {
            $isPublic = array_shift($parameters);

            $isPublic = is_null($isPublic) ?: $isPublic;

            if (is_bool($isPublic)) {
                $this->setQueryVar('public', ($isPublic ? '1' : '0'));
            } else {
                throw new InvalidArgumentException();
            }

            return $this;
        }
    }
}
