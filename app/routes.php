<?php

// Роуты

Route::get('/', function()
{
	return Controller::get('home')->home();
});

// Фильтры

Route::filter('is_ajax', function()
{
	if(Request::is_ajax()) return true;
	return false;
});

Route::filter('is_login', function()
{
	if(Auth::is_login()) return true;
	return false;
});
