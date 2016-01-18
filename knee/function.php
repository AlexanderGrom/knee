<?php

/**
 * Knee framework
 * Назначение: Дополнительные функции ядра
 */


/**
 * Выводит информацию о значении
 */
function test($value, $dump = false)
{
	echo "<pre>";
	($dump) ? var_dump($value) : print_r($value);
	echo "</pre>";
}

/**
 * Проверяет являются ли значения валидными положительными числами
 */
function is_numbers()
{
	$args = func_get_args();

	$result = array();
	foreach ($args as $value) {
		$value = (!is_null($value)) ? (string) $value : '0';

		if (preg_match('#^([0-9]+)(\.[0-9]+)?$#', $value, $match) == 0) return false;

		$base = $match[1];

		if (mb_strlen($base) > 1 AND mb_substr($base, 0, 1) == '0') return false;
	}

	return true;
}

/**
 * Удаляет элементы массива с указанным значением
 */
function array_delete($array, $value)
{
	$value = (is_array($value)) ? $value : array($value);
	return array_diff($array, $value);
}

/**
 * Удаляет элементы массива с указанным ключом
 */
function array_delete_key($array, $value)
{
	$value = (is_array($value)) ? $value : array($value);
	return array_diff_key($array, array_flip($value));
}

/**
 * Добавляет элемент в массив в указанную позицию
 */
function array_insert(&$array, $position, $insert)
{
	$position = ($position > 0) ? ($position - 1) : 0;
	return array_splice($array, $position, 0, $insert);
}

/**
 * array_implode_multi
 */
function array_multi_implode($sep, $array)
{
	$_array = array();
	foreach ($array as $val) {
		if (is_array($val)) $_array[] = array_implode_multi($sep, $val);
		else $_array[] = $val;
	}

	return implode($sep, $_array);
}

/**
 * Хоть один элемент подходят под $mix
 */
function array_any($mix, $array)
{
	foreach ($array as $value) {
		if ($value === $mix) return true;
	}

	return false;
}

/**
 * Все ли элементы подходят под $mix
 */
function array_all($mix, $array)
{
	foreach ($array as $value) {
		if ($value !== $mix) return false;
	}

	return true;
}

/**
 * Сокращение дробной части без округления в большую сторону
 */
function number_fixed($number, $round=2)
{
	$tempd = $number*pow(10,$round);
	$tempd1 = floor($tempd);
	$number = $tempd1/pow(10,$round);
	return $number;
}

/**
 * Замена спец. символов (короткая запись Str::special)
 */
function s($string)
{
	return Str::special($string);
}

/**
 * Сброс и закрытие всего буфера
 */
function ob_end_flush_all()
{
	while (ob_get_level() > 0) {
		ob_end_flush();
	}
}

/**
 * Очистка и закрытие всего буфера
 */
function ob_end_clean_all()
{
	while (ob_get_level() > 0) {
		ob_end_clean();
	}
}

/**
 * mb_ucfirst
 */
if (!function_exists('mb_ucfirst')) {
	function mb_ucfirst($string, $encoding = null)
	{
		if ($encoding == null) $encoding = mb_internal_encoding();
		return mb_strtoupper(mb_substr($string, 0, 1, $encoding), $encoding).mb_substr($string, 1, mb_strlen($string, $encoding), $encoding);
	}
}

/**
 * mb_lcfirst
 */
if (!function_exists('mb_lcfirst')) {
	function mb_lcfirst($string, $encoding = null)
	{
		if ($encoding == null) $encoding = mb_internal_encoding();
		return mb_strtolower(mb_substr($string, 0, 1, $encoding), $encoding).mb_substr($string, 1, mb_strlen($string, $encoding), $encoding);
	}
}

/**
 * mb_ucwords
 */
if (!function_exists('mb_ucwords')) {
	function mb_ucwords($string, $encoding = null)
	{
		if ($encoding == null) $encoding = mb_internal_encoding();
		return mb_convert_case($string, MB_CASE_TITLE, $encoding);
	}
}

/**
 * mb_substr_replace
 */
if (!function_exists("mb_substr_replace")) {
	function mb_substr_replace($string, $replacement, $start, $length = null, $encoding = null) {
		if ($encoding == null) $encoding = mb_internal_encoding();
		if ($length == null) {
			return mb_substr($string, 0, $start, $encoding) . $replacement;
		} else {
			if ($length < 0) $length = mb_strlen($string, $encoding) - $start + $length;
			return
				mb_substr($string, 0, $start, $encoding) .
				$replacement .
				mb_substr($string, $start + $length, mb_strlen($string, $encoding), $encoding);
		}
	}
}

/**
 * mb_chunk_split
 */
if (!function_exists("mb_chunk_split")) {
	function mb_chunk_split($body, $chunklen = 76, $end = "\r\n")
	{
		return implode($end, preg_split('/(.{'.$chunklen.'})/us', $body, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE));
	}
}

/**
 * mb_str_shuffle
 */
if (!function_exists("mb_str_shuffle")) {
	function mb_str_shuffle($string, $encoding = null)
	{
		if ($encoding == null) $encoding = mb_internal_encoding();

		$array = preg_split("//u", $string, -1, PREG_SPLIT_NO_EMPTY);
		shuffle($array);

		return implode("", $array);
	}
}

/**
 * mb_preg_match_all
 */
if (!function_exists("mb_preg_match_all")) {
	function mb_preg_match_all($pattern, $subject, &$matches, $flags = PREG_PATTERN_ORDER, $offset = 0, $encoding = null)
	{
		if ($encoding == null) $encoding = mb_internal_encoding();

		$offset = strlen(mb_substr($subject, 0, $offset, $encoding));
		$result = preg_match_all($pattern, $subject, $matches, $flags, $offset);

		if ($result && ($flags & PREG_OFFSET_CAPTURE)) {
			foreach ($matches as &$match) {
				foreach ($match as &$match) {
					$match[1] = mb_strlen(substr($subject, 0, $match[1]), $encoding);
				}
			}
		}

		return $result;
	}
}

/**
 * mb_ord
 */
if (!function_exists("mb_ord")) {
	function mb_ord($chr, $encoding = null)
	{
		if ($encoding == null) $encoding = mb_internal_encoding();
		$result = unpack('N', mb_convert_encoding($chr, 'UCS-4BE', $encoding));
		return (is_array($result)) ? $result[1] : false;
	}
}

/**
 * mb_chr
 */
if (!function_exists("mb_chr")) {
	function mb_chr($dec, $encoding = null)
	{
		if ($encoding == null) $encoding = mb_internal_encoding();
		return mb_convert_encoding('&#'.intval($dec).';', $encoding, 'HTML-ENTITIES');
	}
}

?>