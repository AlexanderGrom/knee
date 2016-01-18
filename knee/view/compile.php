<?php
/*
 * Knee framework
 * Назначение: Компиляция шаблонов
 */

namespace Knee\View;

class Compile
{
	/**
	 * Данные шаблона
	 */
	private $data = null;

	/**
	 * Путь к шаблону
	 */
	private $path = null;

	/**
	 * Шаблон
	 */
	private $template = null;

	/**
	 * Кэш скомпилированых шаблонов
	 */
	private static $compiled = array();

	/**
	 * Компилируем это!
	 */
	private $compilers = array(
		'eval',
		'include',
		'if',
		'foreach',
		'for',
		'while',
		'echo',
	);

	/**
	 * Конструктор
	 */
	public function __construct($path, $data)
	{
		$this->path = $path;
		$this->data = $data;
		$this->template = $this->load($path);
	}

	/**
	 * Загрузка шаблона
	 */
	private function load($path)
	{
		if ($path == "") return null;

		$parse_path = explode(".", $path);

		$diff = array_diff($parse_path, array(''));
		if ((count($parse_path) - count($diff)) != 0) return null;

		$file_path = ROOT_PATH.'/app/views/'.implode("/", $parse_path).'.php';
		$cache_path = ROOT_PATH.'/cache/views/'.md5($file_path);

		if (isset(static::$compiled[$cache_path])) {
			return static::$compiled[$cache_path];
		}

		if (!is_file($file_path)) {
			return null;
		}

		if (!is_file($cache_path) OR (filemtime($file_path) > filemtime($cache_path))) {
			$view = file_get_contents($file_path);
			static::$compiled[$cache_path] = $this->compile_all($view);
			file_put_contents($cache_path, static::$compiled[$cache_path], LOCK_EX);
		} else {
			static::$compiled[$cache_path] = file_get_contents($cache_path);
		}

		return static::$compiled[$cache_path];
	}

	/**
	 * Компиляция произвольного php кода
	 */
	private function compile_eval($view)
	{
		$view = preg_replace('#\{%\s*eval\s*\(\s*(.*?)\s*\)\s*%\}#i', '<?php $1; ?>', $view);

		return $view;
	}

	/**
	 * Компиляция подгрузки шаблонов
	 */
	private function compile_include($view)
	{
		$view = preg_replace_callback('#\{%\s*include\s*\(\s*(.*?)\s*\)\s*%\}#i', function($match)
		{
			$params = explode(',', $match[1]);

			$path = (isset($params[0])) ? $params[0] : '';
			$data = (isset($params[1])) ? $params[1] : 'get_defined_vars()';

			return '<?php echo \Knee\View::make('.$path.', '.$data.'); ?>'.PHP_EOL;
		}, $view);

		return $view;
	}

	/**
	 * Компиляция условий if, esleif, else
	 */
	private function compile_if($view)
	{
		$view = preg_replace('#\{%\s*if\s*\(\s*(.*?)\s*\)\s*%\}#i', '<?php if($1): ?>', $view);
		$view = preg_replace('#\{%\s*elseif\s*\(\s*(.*?)\s*\)\s*%\}#i', '<?php elseif($1): ?>', $view);
		$view = preg_replace('#\{%\s*else\s*%\}#i', '<?php else: ?>', $view);
		$view = preg_replace('#\{%\s*endif\s*%\}#i', '<?php endif; ?>', $view);

		return $view;
	}

	/**
	 * Компиляция циклов foreach
	 */
	private function compile_foreach($view)
	{
		$view = preg_replace('#\{%\s*foreach\s*\(\s*(.*?)\s*\)\s*%\}#i', '<?php foreach($1): ?>', $view);
		$view = preg_replace('#\{%\s*endforeach\s*%\}#i', '<?php endforeach; ?>', $view);

		return $view;
	}

	/**
	 * Компиляция циклов for
	 */
	private function compile_for($view)
	{
		$view = preg_replace('#\{%\s*for\s*\(\s*(.*?)\s*\)\s*%\}#i', '<?php for($1): ?>', $view);
		$view = preg_replace('#\{%\s*endfor\s*%\}#i', '<?php endfor; ?>', $view);

		return $view;
	}

	/**
	 * Компиляция циклов while
	 */
	private function compile_while($view)
	{
		$view = preg_replace('#\{%\s*while\s*\(\s*(.*?)\s*\)\s*%\}#i', '<?php while($1): ?>', $view);
		$view = preg_replace('#\{%\s*endwhile\s*%\}#i', '<?php endwhile; ?>', $view);

		return $view;
	}

	/**
	 * Компиляция обычной печати echo
	 */
	private function compile_echo($view)
	{
		$view = preg_replace('#\{%\s*(.*?)\s*%\}#i', '<?php echo $1; ?>', $view);

		return $view;
	}

	/**
	 * Компиляция
	 */
	private function compile_all($view)
	{
		$result = '';
		$tokens = token_get_all($view);

		foreach ($tokens as $token) {
			if (is_array($token)) {
				list($token_id, $token_content) = $token;

				if ($token_id == T_INLINE_HTML) {
					foreach ($this->compilers as $compiler) {
						$method = "compile_".$compiler;
						$token_content = $this->$method($token_content);
					}
				}
				$result .= $token_content;
			} else {
				$result .= $token;
			}
		}

		return $result;
	}

	/**
	 * Рузультат работы
	 */
	public function result()
	{
		ob_start();

		$__viewData = $this->data;

		extract($__viewData, EXTR_SKIP);

		$__eval = @eval('?>'.$this->template);

		if ($__eval === false AND is_array(error_get_last())) {
			ob_end_clean_all();
			\Knee\Error::e503(\Knee\Lang::get('system.view.error-compile').'<br>View: '.$this->path);
		} else {
			return trim(ob_get_clean());
		}
	}
}

?>