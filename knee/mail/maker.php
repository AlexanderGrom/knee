<?php
/*
 * Knee framework
 * Назначение: Mail Maker (конструктор)
 */

namespace Knee\Mail;

class Maker
{
	/**
	 * Объект для работы с заголовками
	 */
	private $MailHeaders = null;

	/**
	 * Объект для работы с телом письма
	 */
	private $MailBody = null;

	/**
	 * Конструктор
	 */
	public function __construct()
	{
		$this->MailHeaders = new \Knee\Mail\Headers();
		$this->MailBody = new \Knee\Mail\Body();
	}

	/**
	 * Имя и адрес получателя
	 */
	public function to($address, $name = null)
	{
		$this->MailHeaders->to($address, $name);

		return $this;
	}

	/**
	 * Имя и адрес отправителя
	 */
	public function from($address, $name = null)
	{
		$this->MailHeaders->from($address, $name);

		return $this;
	}

	/**
	 * Тема письма
	 */
	public function subject($title)
	{
		$this->MailHeaders->subject($title);

		return $this;
	}

	/**
	 * Отправка письма в формате text
	 */
	public function text($text = '')
	{
		$this->MailHeaders->content_type('text/plain; charset=utf-8');
		$this->MailBody->message($text);

		return $this;
	}

	/**
	 * Отправка письма в формате html
	 */
	public function html($html = '')
	{
		$this->MailHeaders->content_type('text/html; charset=utf-8');
		$this->MailBody->message($html);

		return $this;
	}

	/**
	 * Отправка письма
	 */
	public function send()
	{
		$headers = $this->MailHeaders->headers();
		$message = $this->MailBody->body();

		if (!isset($headers['To'])) return false;
		if (!isset($headers['From'])) return false;
		if (!isset($headers['Subject'])) return false;

		$to = $headers['To'];
		$subject = $headers['Subject'];

		$headers = array_delete_key($headers, array('To', 'Subject'));

		$_headers = array();
		foreach ($headers as $k => $v) {
			$_headers[] = $k.": ".$v;
		}
		$headers = implode("\r\n", $_headers);

		foreach ($to as $address) {
			@mail($address, $subject, $message, $headers);
		}

		return true;
	}
}

?>