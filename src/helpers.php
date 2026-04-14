<?php

if (! function_exists('form')) {
    /**
     * Get an instance of the form builder.
     *
     * @return \Souravmsh\Html\Services\FormBuilder
     */
    function form()
    {
        return app('form');
    }
}
