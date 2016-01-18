<?php
/*
 * Knee framework
 * Назначение: Пагинация (Make)
 */

namespace Knee\Page;
use Request;

class Make
{
	/**
	 * Массив с результатами работы метода make
	 */
	private $result = array();

	/**
	 * Конструктор
	 */
	public function __construct($result)
	{
		$this->result = $result;
	}

	/**
	 * Пагинация в query string (/?page=2)
	 */
	public function query($name = 'page')
	{
		$query_string = Request::query();
		mb_parse_str($query_string, $query_array);

		$path_string = Request::path();

		$list_array = array();
		foreach ($this->result['list'] as $number) {
			$outputList = array();

			$outputList['number'] = $number;

			if ($number == 1) {
				$query_array = array_delete_key($query_array, $name);
				$query_string = http_build_query($query_array);

				$outputList['url'] = ($query_string != "") ? $path_string."?".$query_string : $path_string;
			} else {
				$query_array[$name] = $number;
				$query_string = http_build_query($query_array);

				$outputList['url'] = $path_string."?".$query_string;
			}

			$list_array[] = $outputList;
		}
		$this->result['list'] = $list_array;

		//-----

		if ($this->result['current'] == 1 OR $this->result['prev'] == 1) {
			$query_array = array_delete_key($query_array, $name);
			$query_string = http_build_query($query_array);

			$this->result['prev_url'] = ($query_string != "") ? $path_string."?".$query_string : $path_string;
		} else {
			$query_array[$name] = $this->result['prev'];
			$query_string = http_build_query($query_array);

			$this->result['prev_url'] = $path_string."?".$query_string;
		}

		//-----

		if ($this->result['current'] == 1) {
			$query_array = array_delete_key($query_array, $name);
			$query_string = http_build_query($query_array);

			$this->result['current_url'] = ($query_string != "") ? $path_string."?".$query_string : $path_string;
		} else {
			$query_array[$name] = $this->result['current'];
			$query_string = http_build_query($query_array);

			$this->result['current_url'] = $path_string."?".$query_string;
		}

		//-----

		if ($this->result['current'] == $this->result['total']) {
			$query_array[$name] = $this->result['current'];
			$query_string = http_build_query($query_array);
		} else {
			$query_array[$name] = $this->result['next'];
			$query_string = http_build_query($query_array);
		}

		$this->result['next_url'] = $path_string."?".$query_string;

		//-----

		$query_array = array_delete_key($query_array, $name);
		$query_string = http_build_query($query_array);

		$this->result['first_url'] = ($query_string != "") ? $path_string."?".$query_string : $path_string;

		if ($this->result['total'] > 1) {
			$query_array[$name] = $this->result['total'];
			$query_string = http_build_query($query_array);

			$this->result['last_url'] = $path_string."?".$query_string;
		} else {
			$this->result['last_url'] = $this->result['first_url'];
		}

		//-----

		return $this->result;
	}

	/**
	 * Пагинация в path (/name/page2/)
	 */
	public function path($name = 'page')
	{
		$query_string = Request::query();
		$query_string = ($query_string != "") ? "?".$query_string : "";

		$path_string = ($this->result['exists'] == 0) ? rtrim(Request::path(), "/") : Request::dir();

		$list_array = array();
		foreach ($this->result['list'] as $number) {
			$outputList = array();
			$outputList['number'] = $number;

			if ($number == 1) {
				$outputList['url'] = $path_string."/".$query_string;
			}
			else {
				$outputList['url'] = $path_string."/".$name."".$number."/".$query_string;
			}

			$list_array[] = $outputList;
		}
		$this->result['list'] = $list_array;

		//-----

		if ($this->result['current'] == 1 OR $this->result['prev'] == 1) {
			$this->result['prev_url'] = $path_string."/".$query_string;
		} else {
			$this->result['prev_url'] = $path_string."/".$name."".$this->result['prev']."/".$query_string;
		}

		//-----

		if ($this->result['current'] == 1) {
			$this->result['current_url'] = $path_string."/".$query_string;
		} else {
			$this->result['current_url'] = $path_string."/".$name."".$this->result['current']."/".$query_string;
		}

		//-----

		if ($this->result['current'] == $this->result['total']) {
			$this->result['next_url'] = $path_string."/".$name."".$this->result['current']."/".$query_string;
		} else {
			$this->result['next_url'] = $path_string."/".$name."".$this->result['next']."/".$query_string;
		}

		//-----

		$this->result['first_url'] = $path_string."/".$query_string;

		if ($this->result['total'] > 1) {
			$this->result['last_url'] = $path_string."/".$name."".$this->result['total']."/".$query_string;
		} else {
			$this->result['last_url'] = $this->result['first_url'];
		}

		//-----

		return $this->result;
	}

	/**
	 * Пагинация в file (/name/file-2.html)
	 */
	public function file()
	{
		$query_string = Request::query();
		$query_string = ($query_string != "") ? "?".$query_string : "";

		$path_string = Request::dir();

		list($file_name, $file_ext) = explode('.', Request::base(), 2);

		if ($this->result['exists'] == 1) $file_name = preg_replace('#\-[0-9]+$#is', '', $file_name);

		$list_array = array();
		foreach ($this->result['list'] as $number) {
			$outputList = array();

			$outputList['number'] = $number;

			if ($number == 1) {
				$outputList['url'] = $path_string."/".$file_name.".".$file_ext."".$query_string;
			} else {
				$outputList['url'] = $path_string."/".$file_name."-".$number.".".$file_ext."".$query_string;
			}

			$list_array[] = $outputList;
		}
		$this->result['list'] = $list_array;

		//-----

		if ($this->result['current'] == 1 OR $this->result['prev'] == 1) {
			$this->result['prev_url'] = $path_string."/".$file_name.".".$file_ext."".$query_string;
		} else {
			$this->result['prev_url'] = $path_string."/".$file_name."-".$this->result['prev'].".".$file_ext."".$query_string;
		}

		//-----

		if ($this->result['current'] == 1) {
			$this->result['current_url'] = $path_string."/".$file_name.".".$file_ext."".$query_string;
		} else {
			$this->result['current_url'] = $path_string."/".$file_name."-".$this->result['current'].".".$file_ext."".$query_string;
		}

		//-----

		if ($this->result['current'] == $this->result['total']) {
			$this->result['next_url'] = $path_string."/".$file_name."-".$this->result['current'].".".$file_ext."".$query_string;
		} else {
			$this->result['next_url'] = $path_string."/".$file_name."-".$this->result['next'].".".$file_ext."".$query_string;
		}

		//-----

		$this->result['first_url'] = $path_string."/".$file_name.".".$file_ext."".$query_string;

		if ($this->result['total'] > 1) {
			$this->result['last_url'] = $path_string."/".$file_name."-".$this->result['total'].".".$file_ext."".$query_string;
		} else {
			$this->result['last_url'] = $this->result['first_url'];
		}

		//-----

		return $this->result;
	}
}

?>