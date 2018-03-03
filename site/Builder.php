<?php

namespace WPFluent\Site;

use WPFluent\Support\Builder as BaseBuilder;

class Builder extends BaseBuilder
{
    public function find($id)
    {
        $site = $this->site($id)->first();

        if (is_null($site)) {
            throw new SiteNotFoundException();
        }

        return $site;
    }

    public function findByDomain($domain)
    {
        $site = $this->search($domain, ['domain'])->first();

        if (is_null($site)) {
            throw new SiteNotFoundException();
        }

        return $site;
    }

    public function findByPath($path)
    {
        $site = $this->search($path, ['path'])->first();

        if (is_null($site)) {
            throw new SiteNotFoundException();
        }

        return $site;
    }

    public function findAll(array $ids)
    {
        return $this->sites($ids)->get();
    }

    public function first()
    {
        return $this->limit(1)->get()->first();
    }
}
