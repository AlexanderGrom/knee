<?php

/**
 * Knee framework
 * Назначение: Ручная загрузка скриптов. Тех, кому не нужен Autoload
*/

namespace Knee;

/**
 * Ручная загрузка скриптов
 */
Loader::path(ROOT_PATH.'/app/functions/main.php');
?>