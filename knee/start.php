<?php

/**
 * Старт
 *
 * Запускает ядро
 */

namespace Knee;

ini_set('request_order', 'GP');

header('Content-Type: text/html; charset=utf-8');

setlocale(LC_ALL, 'en_US.utf8');

mb_internal_encoding("UTF-8");

chdir(ROOT_PATH);

ob_start();

/**
 * Дополнительные функции ядра
 */
require(ROOT_PATH."/knee/function.php");

/**
 * Подключение загрузчика скриптов
 */
require(ROOT_PATH."/knee/loader.php");

/**
 * Автоматическая загрузка системных классов
 */
Loader::autoload();

/**
 * Регистрация функции планового завершения работы сессии
 */
register_shutdown_function(function()
{
	Session::end();
});

/**
 * Регистрация функций управления незапланированым завершением работы скрипта
 */
Debug::register();

/**
 * Подключение слушателей событий
 */
Loader::path(ROOT_PATH."/app/events.php");

/**
 * Подключение дополнительных пользовательских настроек
 */
Loader::path(ROOT_PATH."/app/settings.php");

/**
 * Подключение ручной загрузки скриптов
 */
Loader::path(ROOT_PATH."/app/loaders.php");

/**
 * Подключение стартов app
 */
Loader::path(ROOT_PATH."/app/starts.php");

/**
 * Старт маршрутизации
 */
Route::start();

/**
 * Сброс буфера в браузер
 */
ob_end_flush_all();
