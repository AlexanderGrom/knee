<?php

// Роуты

Route::get('/', function()
{
    return Controller::get('home')->home();
});

// Фильтры

Route::filter('is_ajax', function()
{
    return Request::is_ajax();
});

