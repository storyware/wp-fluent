<?php

namespace WPFluent\Role;

use WPFluent\Support\Model;
use WP_Role;

abstract class Base extends Model
{
    public $name;

    public $label;

    protected $role;

    public function __construct(WP_Role $role = null)
    {
        $this->role = $role ?: new WP_Role($this->name, $this->capabilities);

        parent::__construct((array) $this->role);
    }

    public function newQuery()
    {
        return (new Builder(new Query))->setModel($this)->name($this->name);
    }

    // TODO: Prevents access to WP_Role capabilities
    public function getCapabilitiesAttribute()
    {
        return [];
    }

    public function addCapability($capability)
    {
        $this->role->add_cap($capability);
    }

    public function removeCapability($capability)
    {
        $this->role->remove_cap($capability);
    }

    public static function registerHooks($class)
    {
        add_action('after_switch_theme', [$class, 'registerRole'], PHP_INT_MAX);
        add_action('after_switch_theme', [$class, 'registerCapabilities'], PHP_INT_MAX);
        add_action('switch_theme', [$class, 'deregisterCapabilities'], PHP_INT_MAX);
        add_action('switch_theme', [$class, 'deregisterRole'], PHP_INT_MAX);
    }

    public static function registerRole()
    {
        $role = new static();

        add_role($role->name, $role->label);
    }

    public static function deregisterRole()
    {
        $role = new static();

        remove_role($role->name);
    }

    public static function registerCapabilities()
    {
        $role = new static();

        foreach ($role->capabilities as $capability) {
            $role->addCapability($capability);
        }
    }

    public static function deregisterCapabilities()
    {
        $role = new static();

        foreach ($role->capabilities as $capability) {
            $role->removeCapability($capability);
        }
    }
}
