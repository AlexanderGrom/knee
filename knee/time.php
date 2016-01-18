<?php
/*
 * Knee framework
 * Назначение: Работа с датой/временем
 */

namespace Knee;

class Time
{
	/**
	 * Установка временной зоны по умолчанию
	 */
	public static function setTimeZone($timezone)
	{
		return date_default_timezone_set($timezone);
	}

	/**
	 * Возвращает установленную временную зону
	 */
	public static function getTimeZone()
	{
		return date_default_timezone_get();
	}

	/**
	 * Смещение часового пояса относительно Гринвича
	 */
	public static function offset($timezone = null)
	{
		$timezone = (is_string($timezone)) ? new \DateTimeZone($timezone) : null;
		return (new \DateTime("now", $timezone))->getOffset();
	}

	/**
	 * Текущая временная отметка
	 */
	public static function now()
	{
		return (new \DateTime('now', new \DateTimeZone('UTC')))->getTimestamp();
	}

	/**
	 * Временная отметка начала сегодняшнего дня
	 */
	public static function today($timezone = null)
	{
		$timezone = (is_string($timezone)) ? new \DateTimeZone($timezone) : null;
		return (new \DateTime('today', $timezone))->getTimestamp();
	}

	/**
	 * Временная отметка начала вчерашнего дня
	 */
	public static function yesterday($timezone = null)
	{
		$timezone = (is_string($timezone)) ? new \DateTimeZone($timezone) : null;
		return (new \DateTime('yesterday', $timezone))->getTimestamp();
	}

	/**
	 * Временная отметка начала завтрашнего дня
	 */
	public static function tomorrow($timezone = null)
	{
		$timezone = (is_string($timezone)) ? new \DateTimeZone($timezone) : null;
		return (new \DateTime('tomorrow', $timezone))->getTimestamp();
	}

	/**
	 * Для совместимости
	 */
	public static function sec($second = 0)
	{
		return $second;
	}

	/**
	 * Перевод минут в секунды
	 */
	public static function min($minute = 0)
	{
		return $minute * 60;
	}

	/**
	 * Перевод часов в секунды
	 */
	public static function hour($hour = 0)
	{
		return $hour * 60 * 60;
	}

	/**
	 * Перевод дней в секунды
	 */
	public static function day($day = 0)
	{
		return $day * 60 * 60 * 24;
	}

	/**
	 * Перевод месяцев в секунды
	 */
	public static function month($month = 0, $timezone = null)
	{
		$_time = static::now();

		$date_obj = new \DateTime();

		if (is_string($timezone)) $date_obj->setTimezone(new \DateTimeZone($timezone));

		$date_obj->setTimestamp($_time);
		$date_obj->modify('+'.$month.' month');

		return $date_obj->getTimestamp() - $_time;
	}

	/**
	 * Перевод годов в секунды
	 */
	public static function year($year = 0, $timezone = null)
	{
		$_time = static::now();

		$date_obj = new \DateTime();

		if (is_string($timezone)) $date_obj->setTimezone(new \DateTimeZone($timezone));

		$date_obj->setTimestamp($_time);
		$date_obj->modify('+'.$year.' year');

		return $date_obj->getTimestamp() - $_time;
	}

	/**
	 * Время из строки
	 */
	public static function relative($format, $timezone = null)
	{
		$time = 0;
		if (preg_match('#^(?<sign>[+-]{1})?(?<number>\d+)[ ]*(?<value>sec|min|hour|day|month|year)$#is', trim($format), $match) != 0) {
			switch ($match['value']) {
				case 'sec':
					$time = Time::sec($match['number']);
					break;
				case 'min':
					$time = Time::min($match['number']);
					break;
				case 'hour':
					$time = Time::hour($match['number']);
					break;
				case 'day':
					$time = Time::day($match['number']);
					break;
				case 'month':
					$time = Time::month($match['number'], $timezone);
					break;
				case 'year':
					$time = Time::year($match['number'], $timezone);
					break;
			}

			if (isset($match['sign']) AND $match['sign'] == '-') {
				$time *= -1;
			}
		}

		return $time;
	}

	/**
	 * Проверяет относится ли временная отметка к сегодняшнему дню
	 */
	public static function is_today($time = 0, $timezone = null)
	{
		$date_obj = new \DateTime();

		if (is_string($timezone)) $date_obj->setTimezone(new \DateTimeZone($timezone));

		$date_obj->setTimestamp($time);
		$date_obj->setTime(0, 0, 0);

		$date_timestamp = $date_obj->getTimestamp();
		$today_timestamp = static::today($timezone);

		return ($date_timestamp == $today_timestamp) ? true : false;
	}

	/**
	 * Проверяет относится ли временная отметка ко вчерашнему дню
	 */
	public static function is_yesterday($time = 0, $timezone = null)
	{
		$date_obj = new \DateTime();

		if (is_string($timezone)) $date_obj->setTimezone(new \DateTimeZone($timezone));

		$date_obj->setTimestamp($time);
		$date_obj->setTime(0, 0, 0);

		$date_timestamp = $date_obj->getTimestamp();
		$yesterday_timestamp = static::yesterday($timezone);

		return ($date_timestamp == $yesterday_timestamp) ? true : false;
	}

	/**
	 * Проверяет относится ли временная отметка к завтрашнему дню
	 */
	public static function is_tomorrow($time = 0, $timezone = null)
	{
		$date_obj = new \DateTime();

		if (is_string($timezone)) $date_obj->setTimezone(new \DateTimeZone($timezone));

		$date_obj->setTimestamp($time);
		$date_obj->setTime(0, 0, 0);

		$date_timestamp = $date_obj->getTimestamp();
		$tomorrow_timestamp = static::tomorrow($timezone);

		return ($date_timestamp == $tomorrow_timestamp) ? true : false;
	}

	/**
	 * Проверяет дату и время на корректрость
	 */
	public static function check($foramt, $date)
	{
		$check = true;

		try {
			$date = \DateTime::createFromFormat($foramt, $date);

			if ($date === false) throw new \Exception();

			$date_error = $date->getLastErrors();

			if ($date_error['warning_count'] > 0) throw new \Exception();
		} catch (\Exception $e) {
			$check = false;
		}

		return $check;
	}

	/**
	 * Преобразует дату в метку времени
	 */
	public static function time($format, $date, $timezone = null)
	{
		if (static::check($format, $date) === false) return false;

		if (is_string($timezone)) {
			$date_obj = \DateTime::createFromFormat($format, $date, new \DateTimeZone($timezone));
		} else {
			$date_obj = \DateTime::createFromFormat($format, $date);
		}

		return $date_obj->getTimestamp();
	}

	/**
	 * Возвращает текущую дату
	 */
	public static function date($format, $time=0, $timezone = null)
	{
		$lang_month = Lang::get('system.time.date-month');

		$month_date_obj = new \DateTime();
		if (is_string($timezone)) $month_date_obj->setTimezone(new \DateTimeZone($timezone));
		$month_date_obj->setTimestamp($time);

		$month_index = $month_date_obj->format('n') - 1;

		if (is_array($lang_month) AND isset($lang_month[$month_index])) {
			$month_short = static::date_escape(mb_substr($lang_month[$month_index], 0, 3));
			$month_long = static::date_escape($lang_month[$month_index]);

			$format = preg_replace('#(?<!\\\)F#', $month_long, $format);
			$format = preg_replace('#(?<!\\\)M#', $month_short, $format);
		}

		$lang_week = Lang::get('system.time.date-week');

		$week_date_obj = new \DateTime();
		if (is_string($timezone)) $week_date_obj->setTimezone(new \DateTimeZone($timezone));
		$week_date_obj->setTimestamp($time);

		$week_index = $week_date_obj->format('N') - 1;

		if (is_array($lang_week) AND isset($lang_week[$week_index])) {
			$week_short = static::date_escape(mb_substr($lang_week[$week_index], 0, 3));
			$week_long = static::date_escape($lang_week[$week_index]);

			$format = preg_replace('#(?<!\\\)l#', $week_long, $format);
			$format = preg_replace('#(?<!\\\)D#', $week_short, $format);
		}

		$date_obj = new \DateTime();
		if (is_string($timezone)) $date_obj->setTimezone(new \DateTimeZone($timezone));
		$date_obj->setTimestamp($time);

		return $date_obj->format($format);
	}

	/**
	 * Возвращает разницу между двумя датами виде ассоциативного массива
	 */
	public static function diff($start_time = 0, $end_time = 0, $timezone = null)
	{
		$start_obj = new \DateTime();
		if (is_string($timezone)) $start_obj->setTimezone(new \DateTimeZone($timezone));
		$start_obj->setTimestamp($start_time);

		$end_obj = new \DateTime();
		if (is_string($timezone)) $end_obj->setTimezone(new \DateTimeZone($timezone));
		$end_obj->setTimestamp($end_time);

		$diff = $start_obj->diff($end_obj, true);

		$result = array();

		$result['year'] = $diff->format('%y');
		$result['month'] = $diff->format('%m');
		$result['day'] = $diff->format('%d');
		$result['hour'] = $diff->format('%h');
		$result['min'] = $diff->format('%i');
		$result['sec'] = $diff->format('%s');

		return $result;
	}

	/**
	 * Возвращает общую разницу между двумя датами виде ассоциативного массива
	 */
	public static function diff_total($start_time = 0, $end_time = 0, $timezone = null)
	{
		$_seconds = abs($start_time - $end_time);

		$start_obj = new \DateTime();
		if (is_string($timezone)) $start_obj->setTimezone(new \DateTimeZone($timezone));
		$start_obj->setTimestamp($start_time);

		$end_obj = new \DateTime();
		if (is_string($timezone)) $end_obj->setTimezone(new \DateTimeZone($timezone));
		$end_obj->setTimestamp($end_time);

		$diff = $start_obj->diff($end_obj, true);

		$result = array();

		$result['year'] = $diff->format('%y');
		$result['month'] = $diff->format('%m') + ($diff->format('%y') * 12);
		$result['day'] = floor($_seconds / 60 / 60 / 24);
		$result['hour'] = floor($_seconds / 60 / 60);
		$result['min'] = floor($_seconds / 60);
		$result['sec'] = $_seconds;

		return $result;
	}

	/**
	 * Экранирование спецсимволов в строке формата даты
	 */
	private static function date_escape($str)
	{
		$chars_list = preg_split('##su', $str, -1, PREG_SPLIT_NO_EMPTY);

		$string = "";
		foreach ($chars_list as $char) {
			$ord = mb_ord($char);
			if (($ord>=65 AND $ord<=90) OR ($ord>=97 AND $ord<=122)) {
				$string .= "\\".$char;
			} else {
				$string .= $char;
			}
		}

		return $string;
	}
}

?>