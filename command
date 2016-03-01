<?php

/**
 * Работа из командной строки
 */

namespace Knee;
 
define('ROOT_PATH', __DIR__);

chdir(ROOT_PATH);

error_reporting(E_ALL);

setlocale(LC_ALL, 'en_US.utf8');

mb_internal_encoding("UTF-8");

require(ROOT_PATH."/knee/function.php");

require(ROOT_PATH."/knee/loader.php");

Loader::autoload();

require(ROOT_PATH."/knee/cli/start.php");

?>