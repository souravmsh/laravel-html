<?php

namespace Souravmsh\Html;

use Illuminate\Support\ServiceProvider;
use Souravmsh\Html\Services\FormBuilder;
use Souravmsh\Html\Services\HtmlBuilder;

class HtmlServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('form', function ($app) {
            return new FormBuilder();
        });

        $this->app->singleton('html', function ($app) {
            return new HtmlBuilder();
        });

        $this->app->booting(function() {
            if (!class_exists('Form')) {
                class_alias(\Souravmsh\Html\Facades\Form::class, 'Form');
            }

            if (!class_exists('Html')) {
                class_alias(\Souravmsh\Html\Facades\Html::class, 'Html');
            }
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
