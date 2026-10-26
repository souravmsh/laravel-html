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

if (! function_exists('html')) {
    /**
     * Get an instance of the html builder.
     *
     * @return \Souravmsh\Html\Services\HtmlBuilder
     */
    function html()
    {
        return app('html');
    }
}
