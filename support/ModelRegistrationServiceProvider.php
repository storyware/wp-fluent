<?php

namespace WPFluent\Support;

use Illuminate\Support\ServiceProvider;

class ModelRegistrationServiceProvider extends ServiceProvider
{
    public function register()
    {
        $models = config('models');

        $this->registerModels($models['options']);
        $this->registerModels($models['roles']);
        $this->registerModels($models['users']);
        $this->registerModels($models['taxonomies']);
        $this->registerModels($models['posttypes']);
        $this->registerModels($models['other']);
    }

    private function registerModels($models)
    {
        foreach ($models as $model) {
            $this->registerModel($model);
        }
    }

    private function registerModel($model)
    {
        call_user_func([$model, 'register']);
    }
}
