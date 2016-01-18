<?php
/*
 * Knee framework
 * Назначение: Обратка почтовых заголовков
 */

namespace Knee\Mail;

class Headers
{
	/**
	 * Массив установленых заголовков
	 */
	private $headers = array();

	/**
	 * Конструктор
	 */
	public function __construct()
	{
		$this->headers['MIME-Version'] = "1.0";
		$this->headers['X-Mailer'] = "Knee Framework (Mail script)";
		$this->headers['Content-Type'] = "text/plain; charset=utf-8";
		$this->headers['Content-Transfer-Encoding'] = "base64";
	}

	/**
	 * Тип контента
	 */
	public function content_type($type)
	{
		$this->headers['Content-Type'] = $type;

		return true;
	}

	/**
	 * Имя и адрес получателя
	 */
	public function to($address, $name = null)
	{
		$address = $this->secureHeader($address);

		if (!$this->is_email($address)) return false;

		if (!is_null($name)) {
			$this->headers['To'][$address] = $this->encodeHeader($name)." <".$address.">";
		} else {
			$this->headers['To'][$address] = $address;
		}

		return true;
	}

	/**
	 * Имя и адрес отправителя
	 */
	public function from($address, $name = null)
	{
		$address = $this->secureHeader($address);

		if (!$this->is_email($address)) return false;

		if (!is_null($name)) {
			$this->headers['From'] = $this->encodeHeader($name)." <".$address.">";
		} else {
			$this->headers['From'] = $address;
		}

		$this->headers['Reply-To'] = $this->headers['From'];
		$this->headers['Return-Path'] = $this->headers['From'];

		return true;
	}

	/**
	 * Тема письма
	 */
	public function subject($title)
	{
		$this->headers['Subject'] = $this->encodeHeader($title);

		return true;
	}

	/**
	 * Массив с заголовками
	 */
	public function headers()
	{
		return $this->headers;
	}

	/**
	 * Кодирует заголовки
	 */
	private function encodeHeader($str)
	{
		return mb_encode_mimeheader($this->secureHeader($str), "UTF-8", "B", "\r\n");
	}

	/**
	 * Убираем лишнии пробелы и переводы строк
	 */
	private function secureHeader($str)
	{
		$str = str_replace("\r", '', $str);
		$str = str_replace("\n", '', $str);

		return trim($str);
	}

	/**
	 * Простая проверка Email на валидность
	 */
	private function is_email($email)
	{
		return (filter_var($email, FILTER_VALIDATE_EMAIL) !== false) ? true : false;
	}
}

?>