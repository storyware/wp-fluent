<?php

namespace WPFluent\Role;

use WPFluent\Contracts\Support\Queryable;

class Query implements Queryable
{
    protected $names = [];

    public function execute()
    {
        $results = [];

        foreach ($this->names as $name) {
            $results[] = get_role($name);
        }

        return $results;
    }

    public function name($name)
    {
        $this->names[] = $name;

        return $this;
    }
}
