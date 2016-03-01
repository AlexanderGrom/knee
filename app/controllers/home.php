<?php

namespace App\Controllers;

use View;

class Home_Controller
{
	public function __construct() {}

	public function home()
	{
		$title = 'Knee Framework';
		$content = "Hello World";

		return View::make('main.content.home')
			->with('title', $title)
			->with('content', $content);
	}
}