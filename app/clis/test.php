<?php

class Test_Cli
{
	public function __construct() {}
	
	public function test()
	{
		for ($i = 1; $i <= 5; $i++)
		{
			echo $i.PHP_EOL;
			sleep(1);
		}
	}
}

?>