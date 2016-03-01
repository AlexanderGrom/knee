<?php
/*
 * Маршрутизатор
 */
 
namespace Knee;

use Closure;

class Route
{
    /**
     * True если стар уже дан
     *
     * @var boolean
     */
    protected static $start = false;

    /**
     * Массив экземпляров служебных объектов Route
     *
     * @var array
     */
    protected static $objects = array();

    /**
     * Массив фильтров
     *
     * @var array
     */
    protected $filters = array();

    /**
     * Массив зарегистрированных действия
     *
     * @var array
     */
    protected $actions = array();

    /**
     * Найденый экшен
     *
     * @var string
     */
    protected static $action = null;

    /**
     * Массив параметров к найденному экшену
     */
    protected static $match = array();

    /**
     * Текущий хост
     *
     * @var string
     */
    protected static $host = null;

    /**
     * Текущий путь запроса
     *
     * @var string
     */
    protected static $path = null;

    /**
     * Текущие параметры запроса
     *
     * @var string
     */
    protected static $query = null;

    /**
     * Текущий метод запроса
     *
     * @var string
     */
    protected static $method = null;

    /**
     * True если маршрут найден и делать больше ничего не нужно
     *
     * @var boolean
     */
    protected static $found = false;

    /**
     * Старт/Стоп регистрации действий
     *
     * @var boolean
     */
    protected static $record = false;

    /**
     * Старт маршрутизации
     */
    public static function start()
    {
        if (static::$start) {
            return false;
        } else {
            static::$start = true;
        }
        static::$record = true;
        static::$host = Request::host();
        static::$path = Request::path();
        static::$query = Request::query();
        static::$method = Request::method();
        if (preg_match('#^/index.php#i', static::$path)) {
            Error::e404();
        }
        if (preg_match('#^/public/#i', static::$path)) {
            Error::e404();
        }
        static::running();
        if (!static::$found) {
            if (mb_substr(static::$path, -1, 1) != '/') {
                $extension = pathinfo(static::$path, PATHINFO_EXTENSION);
                if ($extension == "") {
                    static::$path .= "/";
                    static::running();
                    if (static::$found) {
                        Redirect::r302(static::$path.((static::$query!='')?'?'.static::$query:''));
                    }
                }
            }
        }
        static::$record = false;
        if (static::$found) {
            static::render();
        } else {
            Error::e404();
        }
    }

    /**
     * Пробег по маршрутам
     */
    protected static function running()
    {
        $obj = new Route();
        array_push(static::$objects, $obj);
        $file_path = ROOT_PATH.'/app/routes.php';
        if (is_file($file_path)) {
            require($file_path);
        } else {
            return false;
        }
        $obj->routing();
        array_pop(static::$objects);
    }

    /**
     * Групировка маршрутов по хосту
     *
     * @param string $host
     * @param Closure $action
     * @param string $filter
     * @return boolean
     */
    public static function host($host, Closure $action, $filter = null)
    {
        if (!static::$record) {
            return false;
        }
        $params = array();
        $params['name'] = 'host';
        $params['host'] = $host;
        $params['action'] = $action;
        $params['filter'] = $filter;
        $obj = end(static::$objects);
        $obj->registr($params);
        return true;
    }

    /**
     * Групировка маршрутов по части uri
     *
     * @param string $path
     * @param Closure $action
     * @param string $filter
     * @return boolean
     */
    public static function group($path, Closure $action, $filter = null)
    {
        if (!static::$record) {
            return false;
        }
        $params = array();
        $params['name'] = 'group';
        $params['path'] = $path;
        $params['action'] = $action;
        $params['filter'] = $filter;
        $obj = end(static::$objects);
        $obj->registr($params);
        return true;
    }

    /**
     * GET запросы к маршруту
     *
     * @param string $path
     * @param Closure $action
     * @param string $filter
     * @return boolean
     */
    public static function get($path, Closure $action, $filter = null)
    {
        if (!static::$record) {
            return false;
        }
        $params = array();
        $params['name'] = 'get';
        $params['path'] = $path;
        $params['action'] = $action;
        $params['filter'] = $filter;
        $obj = end(static::$objects);
        $obj->registr($params);
        return true;
    }

    /**
     * POST запросы к маршруту
     *
     * @param string $path
     * @param Closure $action
     * @param string $filter
     * @return boolean
     */
    public static function post($path, Closure $action, $filter = null)
    {
        if (!static::$record) {
            return false;
        }
        $params = array();
        $params['name'] = 'post';
        $params['path'] = $path;
        $params['action'] = $action;
        $params['filter'] = $filter;
        $obj = end(static::$objects);
        $obj->registr($params);
        return true;
    }

    /**
     * PUT запросы к маршруту
     *
     * @param string $path
     * @param Closure $action
     * @param string $filter
     * @return boolean
     */
    public static function put($path, Closure $action, $filter = null)
    {
        if (!static::$record) {
            return false;
        }
        $params = array();
        $params['name'] = 'put';
        $params['path'] = $path;
        $params['action'] = $action;
        $params['filter'] = $filter;
        $obj = end(static::$objects);
        $obj->registr($params);
        return true;
    }

    /**
     * DELETE запросы к маршруту
     *
     * @param string $path
     * @param Closure $action
     * @param string $filter
     * @return boolean
     */
    public static function delete($path, Closure $action, $filter = null)
    {
        if (!static::$record) {
            return false;
        }
        $params = array();
        $params['name'] = 'delete';
        $params['path'] = $path;
        $params['action'] = $action;
        $params['filter'] = $filter;
        $obj = end(static::$objects);
        $obj->registr($params);
        return true;
    }

    /**
     * GET/POST/PUT/DELETE запросы к маршруту
     *
     * @param string $path
     * @param Closure $action
     * @param string $filter
     * @return boolean
     */
    public static function any($path, Closure $action, $filter = null)
    {
        if (!static::$record) {
            return false;
        }
        $params = array();
        $params['name'] = 'any';
        $params['path'] = $path;
        $params['action'] = $action;
        $params['filter'] = $filter;
        $obj = end(static::$objects);
        $obj->registr($params);
        return true;
    }

    /**
     * Фильтры к маршрутам
     *
     * @param string $name
     * @param Closure $action
     * @return boolean
     */
    public static function filter($name, Closure $action)
    {
        if (!static::$record) {
            return false;
        }
        $obj = end(static::$objects);
        $obj->filters[$name] = $action;
        return true;
    }

    /**
     * Регистрация действий для данного объекта
     *
     * @param array $params
     */
    protected function registr($params)
    {
        $this->actions[] = $params;
    }

    /**
     * Проверка зарегистрированных роутов
     */
    protected function routing()
    {
        foreach ($this->actions as $params) {
            $name = $params['name'];
            unset($params['name']);
            call_user_func_array(array($this, $name.'_action'), $params);
        }
    }

    /**
     * Групировка маршрутов по хосту
     *
     * @param string $host
     * @param Closure $action
     * @param string $filter
     * @return boolean
     */
    protected function host_action($host, Closure $action, $filter = null)
    {
        $pattern = $this->host_patterns($host);
        if (preg_match("#^".$pattern."$#", static::$host, $match)) {
            if (!is_null($filter)) {
                $filters = $this->filters_result($filter);
                if (!array_all(true, $filters)) {
                    return false;
                }
            }
            $obj = new Route();
            $obj->filters = end(static::$objects)->filters;
            array_push(static::$objects, $obj);
            call_user_func($action);
            $obj->routing();
            array_pop(static::$objects);
            return true;
        }
        return false;
    }

    /**
     * Групировка маршрутов по части uri
     *
     * @param string $path
     * @param Closure $action
     * @param string $filter
     * @return boolean
     */
    protected function group_action($path, Closure $action, $filter = null)
    {
        $pattern = $this->group_patterns($path);
        if (preg_match("#^".$pattern."$#", static::$path, $match)) {
            if (!is_null($filter)) {
                $filters = $this->filters_result($filter);
                if (!array_all(true, $filters)) {
                    return false;
                }
            }
            $obj = new Route();
            $obj->filters = end(static::$objects)->filters;
            array_push(static::$objects, $obj);
            call_user_func($action);
            $obj->routing();
            array_pop(static::$objects);
            return true;
        }
        return false;
    }

    /**
     * GET запросы к маршруту
     *
     * @param string $path
     * @param Closure $action
     * @param string $filter
     * @return boolean
     */
    protected function get_action($path, Closure $action, $filter = null)
    {
        if (static::$found) {
            return false;
        }
        if (mb_strtolower(static::$method) != 'get') {
            return false;
        }
        $pattern = $this->route_patterns($path);
        if (preg_match("#^".$pattern."$#", static::$path, $match)) {
            if (!is_null($filter)) {
                $filters = $this->filters_result($filter);
                if (!array_all(true, $filters)) {
                    return false;
                }
            }
            $params = $this->params($action, $match);
            static::$action = $action;
            static::$match = $params;
            static::$found = true;
            return true;
        }
        return false;
    }

    /**
     * POST запросы к маршруту
     *
     * @param string $path
     * @param Closure $action
     * @param string $filter
     * @return boolean
     */
    protected function post_action($path, Closure $action, $filter = null)
    {
        if (static::$found) {
            return false;
        }
        if (mb_strtolower(static::$method) != 'post') {
            return false;
        }
        $path = $this->route_patterns($path);
        if (preg_match("#^".$path."$#", static::$path, $match)) {
            if (!is_null($filter)) {
                $filters = $this->filters_result($filter);
                if (!array_all(true, $filters)) {
                    return false;
                }
            }
            $params = $this->params($action, $match);
            static::$action = $action;
            static::$match = $params;
            static::$found = true;
            return true;
        }
        return false;
    }

    /**
     * PUT запросы к маршруту
     *
     * @param string $path
     * @param Closure $action
     * @param string $filter
     * @return boolean
     */
    protected function put_action($path, Closure $action, $filter = null)
    {
        if (static::$found) {
            return false;
        }
        if (mb_strtolower(static::$method) != 'put') {
            return false;
        }
        $path = $this->route_patterns($path);
        if (preg_match("#^".$path."$#", static::$path, $match)) {
            if (!is_null($filter)) {
                $filters = $this->filters_result($filter);
                if (!array_all(true, $filters)) {
                    return false;
                }
            }
            $params = $this->params($action, $match);
            static::$action = $action;
            static::$match = $params;
            static::$found = true;
            return true;
        }
        return false;
    }

    /**
     * DELETE запросы к маршруту
     *
     * @param string $path
     * @param Closure $action
     * @param string $filter
     * @return boolean
     */
    protected function delete_action($path, Closure $action, $filter = null)
    {
        if (static::$found) {
            return false;
        }
        if (mb_strtolower(static::$method) != 'delete') {
            return false;
        }
        $path = $this->route_patterns($path);
        if (preg_match("#^".$path."$#", static::$path, $match)) {
            if (!is_null($filter)) {
                $filters = $this->filters_result($filter);
                if (!array_all(true, $filters)) {
                    return false;
                }
            }
            $params = $this->params($action, $match);
            static::$action = $action;
            static::$match = $params;
            static::$found = true;
            return true;
        }
        return false;
    }

    /**
     * GET/POST/PUT/DELETE запросы к маршруту
     *
     * @param string $path
     * @param Closure $action
     * @param string $filter
     * @return boolean
     */
    protected function any_action($path, Closure $action, $filter = null)
    {
        if (static::$found) {
            return false;
        }
        $path = $this->route_patterns($path);
        if (preg_match("#^".$path."$#", static::$path, $match)) {
            if (!is_null($filter)) {
                $filters = $this->filters_result($filter);
                if (!array_all(true, $filters)) {
                    return false;
                }
            }
            $params = $this->params($action, $match);
            static::$action = $action;
            static::$match = $params;
            static::$found = true;
            return true;
        }
        return false;
    }

    /**
     * Именованные параметры
     *
     * @param Closure $action
     * @param array $match
     * @return array
     */
    protected function params($action, $match)
    {
        $named = array();
        foreach ($match as $key => $value) {
            if (is_string($key)) {
                $named[$key] = $value;
            }
        }
        $reflector = new \ReflectionFunction($action);
        $parameters = $reflector->getParameters();
        $result = array();
        foreach ($parameters as $param) {
            $name = $param->getName();
            $value = ($param->isDefaultValueAvailable()) ? $param->getDefaultValue() : null;
            if (array_key_exists($name, $named)) {
                if ($named[$name] === "") {
                    $result[$name] = $value;
                } else {
                    $result[$name] = $named[$name];
                }
            } else {
                $result[$name] = $value;
            }
        }
        return $result;
    }

    /**
     * Массив результатов работы фильтров
     *
     * @param string|array $filter - фильтры
     * @return array
     */
    protected function filters_result($filter)
    {
        $filters = array();
        if (is_array($filter)) {
            foreach ($filter as $name) {
                $filters = array_merge($filters, $this->filters_result($name));
            }
        } else {
            static::$record = false;
            $filters[] = (array_key_exists($filter, $this->filters)) ? (bool) call_user_func($this->filters[$filter]) : false;
            static::$record = true;
        }
        return $filters;
    }

    /**
     * Патерны для роутов
     *
     * @param string $route - паттерн
     * @return string
     */
    protected function route_patterns($route)
    {
        $route = preg_quote($route);
        $route = preg_replace('#\\\\\<\@(.+?)\\\\\>#', '(?<$1>[a-zA-Z0-9\-]+)', $route);
        $route = preg_replace('#\\\\\<\#(.+?)\\\\\>#', '(?<$1>[0-9]+)', $route);
        $route = preg_replace('#\\\\\<\\\\\*(.+?)\\\\\>#', '(?<$1>[^/]+)', $route);
        $route = preg_replace('#\\\\\{(.+?)\\\\\}\\\\\?(\/)?#', '(?:$1$2)?', $route);
        $route = preg_replace_callback('#\\\\\((.+?)\\\\\)\\\\\?#', function($match){
            return (mb_strpos($match[1], "\\|") !== false) ? '(?:'.str_replace("\\|", "|", $match[1]).')' : '(?:'.$match[1].')?';
        }, $route);
        return $route;
    }

    /**
     * Патерны для хоста
     *
     * @param string $host - паттерн
     * @return string
     */
    protected function host_patterns($host)
    {
        $host = strtr(preg_quote($host, '#'), array('\*' => '([a-zA-Z0-9\-\.]*)'));
        return $host;
    }

    /**
     * Патерны для групп
     *
     * @param string $route - паттерн
     * @return string
     */
    protected function group_patterns($route)
    {
        $route = strtr(preg_quote($route, '#'), array('\*' => '(.*?)'));
        return $route;
    }

    /**
     * Параметры экшена
     *
     * @param string $name - имя параметра
     * @return mixed
     */
    public static function match($name)
    {
        return (array_key_exists($name, static::$match)) ? static::$match[$name] : null;
    }

    /**
     * Доступ к переменным маршрута
     */
    protected static function render()
    {
        if (static::$action instanceof Closure) {
            echo call_user_func_array(static::$action, static::$match);
        }
    }
}
