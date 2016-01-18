<?php

class Base_Auth
{
	/**
	 * Был ли вход
	 */
	private $is_login = false;
	
	/**
	 * Данные пользователя
	 */
	private $user_data = array();
	
	/**
	 * Проверка на вход в систему
	 */
	public function is_login()
	{
		return $this->is_login;
	}
	
	/**
	 * Данные пользователя
	 */
	public function user($key = 'user')
	{		
		return $this->user_data[$key];
	}
	
	/**
	 * Добавление данных пользователя
	 */
	public function add($name, $data)
	{
		$this->user_data[$name] = $data;
	}
	
	 /**
     * Авторизация
     */
    public function auth()
    {
		$cookie = Cookie::get('zzz');
		
        if(is_null($cookie)) return false;
		
		$SeSData = @unserialize(Crypter::decrypt($cookie));
		
        if(!is_array($SeSData)) return false;
		
		if(!isset($SeSData['user_id'])) return false;
		if(!isset($SeSData['user_key'])) return false;
		if(!isset($SeSData['user_token'])) return false;
		
		$user = false; // заглушка
		// Тут нужна реализованная модель пользователя
		//$user = Model::get('user')->getUserByID($SeSData['user_id']);
		
        try
        {
			if($user === false) {
                throw new Exception();
            }
			
			if($user['user_status'] != 'active') {
                throw new Exception();
            }
            
			if($SeSData['user_key'] != $user['user_key']) {
                throw new Exception();
            }
			
            if($SeSData['user_token'] != $user['user_token']) {
                throw new Exception();
            }
        }
        catch(Exception $e) 
        {
            $this->logout();
            return false;
        }
		
		$this->add('user', (object) $user);
		$this->add('vars', (new stdObject()));
		
		$this->is_login = true;
	
        return true;
    }
	
	/**
	 * Очистка и выход
	 */
	public function logout()
	{
		if(static::is_login())
		{
			Cookie::del('zzz', '/', null, false, true);
			Session::destroy();
			
			$this->is_login = false;
			$this->user_data = array();
		}
	}
}

?>