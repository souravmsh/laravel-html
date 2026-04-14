<?php

namespace Souravmsh\Html;

use Illuminate\Support\ServiceProvider;
use Souravmsh\Html\Services\FormBuilder;

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

        $this->app->booting(function() {
            if (!class_exists('Form')) {
                class_alias(\Souravmsh\Html\Facades\Form::class, 'Form');
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
