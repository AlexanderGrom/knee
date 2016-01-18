<?php
/*
 * Knee framework
 * Назначение: Работа со строками
 */

namespace Knee;

class Str
{
	/**
	 * Лимит на число пробелов
	 */
	public static function limit_space($text, $limit)
	{
		if ($limit > 0) {
			$spaces = str_repeat(" ", $limit);
			$limit = $limit + 1;
			$text = preg_replace("#[ ]{".$limit.",}#is", $spaces, $text);
		} else {
			$text = preg_replace("#[ ]{1,}#is", "", $text);
		}

		return $text;
	}

	/**
	 * Лимит на число перевода строк
	 */
	public static function limit_nr($text, $limit)
	{
		$text = str_replace("\r", "", $text);

		if ($limit > 0) {
			$lines = str_repeat("\n", $limit);
			$limit = $limit + 1;
			$text = preg_replace("#[\n]{".$limit.",}#is", $lines, $text);
		} else {
			$text = preg_replace("#[\n]{1,}#is", " ", $text);
		}

		return $text;
	}

	/**
	 * Лимит на число символов в строке
	 */
	public static function limit_char($text, $limit, $cut = true, $end = true)
	{
		if (mb_strlen($text) > $limit) {
			$text = mb_substr($text, 0, $limit);

			if ($cut === false) {
				$text = explode(" ", $text);
				if ($end) array_pop($text);
				$text = implode(" ",$text);
			}

			$text = preg_replace('#[^\w]*$#isu', '', $text);

			$text .= ($end === true) ? "..." : "";
		}

		return $text;
	}

	/**
	 * Лимит на число байт в строке
	 */
	public static function limit_bytes($text, $limit, $cut = true, $end = true)
	{
		if (strlen($text) > $limit) {
			$text = mb_strcut($text, 0, $limit);

			if ($cut === false) {
				$text = explode(" ", $text);
				if ($end) array_pop($text);
				$text = implode(" ",$text);
			}

			$text = preg_replace('#[^\w]*$#isu', '', $text);

			$text .= ($end === true) ? "..." : "";
		}

		return $text;
	}

	/**
	 * Лимит на число выводимых строк
	 */
	public static function limit_line($text, $limit)
	{
		$text = str_replace("\r", "", $text);

		if ($limit > 0) {
			$lines = explode("\n", $text);
			$lines_limit = array_slice($lines, 0, $limit);
			$text = implode("\n", $lines_limit);
		} else {
			$text = "";
		}

		return $text;
	}

	/**
	 * Поиск в строке по "звездочке"
	 */
	public static function match($pattern, $string)
	{
		return (preg_match("#^".strtr(preg_quote($pattern, '#'),array('\*' => '.*'))."$#i", $string)) ? true : false;
	}

	/**
	 * Символ перевода строки в тег <br>
	 */
	public static function nr2br($text)
	{
		$text = str_replace("\r", "", $text);
		$text = str_replace("\n", "<br>", $text);
		return $text;
	}

	/**
	 * Тег <br> в символ перевода строки
	 */
	public static function br2nr($text)
	{
		return str_replace("<br>", "\n", $text);
	}

	/**
	 * Правильное окончание для русских слов
	 */
	public static function str_ending_rus($count, $str_1, $str_2, $str_3)
	{
		if ($count % 10 == 1 AND $count != 11) $str = $str_1;
		elseif (in_array($count % 10, array(2,3,4)) AND !in_array($count % 100, array(12,13,14))) $str = $str_2;
		else $str = $str_3;

		return $str;
	}

	/**
	 * Замена спец. символов
	 */
	public static function special($text)
	{
		$text = str_replace("&", "&#38;", $text);
		$text = str_replace('"', "&#34;", $text);
		$text = str_replace("'", "&#39;", $text);
		$text = str_replace("<", "&#60;", $text);
		$text = str_replace(">", "&#62;", $text);

		return $text;
	}

	/**
	 * Генерация случайной строки заданой длины
	 */
	public static function hash($length)
	{
		return substr(str_shuffle(str_repeat('abcdefghijklmnopqrstuvwxyz0123456789', 8)), 0, $length);
	}

	/**
	 * Генерация случайной строки заданой длины разного регистра
	 */
	public static function hash32($length)
	{
		return substr(str_shuffle(str_repeat('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', 8)), 0, $length);
	}

	/**
	 * "Подсветка" ссылок в строке
	 */
	public static function highlight_link($text)
	{
		$result = preg_replace_callback('#(?<=^|\s)(?P<href>(((http|https|ftp)://)|(www\.))[0-9a-z\-]+([\.][0-9a-z\-]+)+([/]?|[/](.+?)))(?=[\.\,\;\?\!]*(\s|$))#is', 'Str::highlight_link_build', $text);

		return (is_string($result)) ? $result : $text;
	}

	/**
	 * "Подсветка" ссылок в строке (build)
	 */
	private static function highlight_link_build($match)
	{
		$href = trim($match['href']);

		if (preg_match('#^(http|https|ftp)#is', $href) == 0) $href = "http://".$href;

		return "<a href=\"".$href."\">".$href."</a>";
	}

	/**
	 * Замена двойных кавычек на кавычки елочки
	 */
	public static function quote2quote($text)
	{
		$result = preg_replace('#"(.*?)"#u', '«$1»', $text);

		return (is_string($result)) ? $result : $text;
	}

	/**
	 * Замена короткого тире на длинное тире
	 */
	public static function dash2dash($text)
	{
		$result = preg_replace('#(?<=^|\s)-(?=\s|$)#', '–', $text);

		return (is_string($result)) ? $result : $text;
	}

	/**
	 * Вернет цифровую строку
	 */
	public static function digit()
	{
		return '0123456789';
	}

	/**
	 * Вернет буквеную строку
	 */
	public static function alpha()
	{
		return 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	}

	/**
	 * Вернет буквено-цифровую строку
	 */
	public static function alnum()
	{
		return 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	}
}

?>
