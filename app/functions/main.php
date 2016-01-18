<?php

/**
 * Knee framework
 * Назначение: Основные пользовательские функции
 */

function number62_encode($number)
{
    if(preg_match('#^[0-9]+$#iu', $number) == 0) return "";

	$out = "";
    $string = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";

	do {
		$key = gmp_mod($number, 62);
		$out = $string[$key].$out;
		$number = gmp_strval(gmp_div_q($number, 62, GMP_ROUND_ZERO));
	} while(gmp_cmp($number, 0) > 0);

    return $out;
}

function number62_decode($string)
{
	if(preg_match('#^[a-zA-Z0-9]+$#iu', $string) == 0) return 0;

	$out = 0;
	$length = mb_strlen($string);
	$array = array_flip(str_split("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"));

	for ($i = 0; $i < $length; $i++) {
		$out = gmp_add($out, gmp_mul($array[$string[$length - $i - 1]], gmp_pow(62, $i)));
	}

	$out = gmp_strval($out, 10);

    return $out;
}

function hotness($ups, $downs, $timestamp)
{
	$score = $ups - $downs;
	$order = log10(max(abs($score), 1));

	if ($score > 0) {
		$sign = 1;
	} else if ($score < 0) {
        $sign = -1;
	} else {
        $sign = 0;
	}

	$damping = 60 * 60 * 48; // затухание 48 часов
	$seconds = $timestamp - 1433102400; // 1433102400 - время начала "эпохи" сайта!

    return round($order * $sign + $seconds / $damping, 7);
}

?>
