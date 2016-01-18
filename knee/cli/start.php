<?php
/*
 * Knee framework
 * Назначение: Работа с командной строкой
 */

namespace Knee\CLI;

try
{
	Command::run($_SERVER['argv']);
}
catch (\Exception $e)
{
	echo $e->getMessage();
}

echo PHP_EOL;

?>