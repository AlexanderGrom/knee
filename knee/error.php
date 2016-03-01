<?php
/*
 * Завершение работы скрипта и вывод одной из страниц ошибок
 */

namespace Knee;

class Error
{
    /**
     * Выброс ошибки 403 - Доступ запрещен
     *
     * @param string|array $detals - опциональное сообщенние
     */
    public static function e403($detals = null)
    {
        Header::h403();

        ob_end_clean_all();

        if (!Request::is_ajax()) {
            echo View::make('error.e403')
                 ->with('detals', $detals)
                 ->compile();
        } else {
            $outputData = array();
            $outputData['status'] = '403 Forbidden';  
            if (!is_null($detals)) {
                $outputData['detals'] = $detals;
            }    
            echo json_encode($outputData, JSON_UNESCAPED_SLASHES);
        }

        exit();
    }

    /**
     * Выброс ошибки 404 - Страница не найдена
     *
     * @param string $detals - опциональное сообщенние
     */
    public static function e404($detals = null)
    {
        Header::h404();

        ob_end_clean_all();

        if (!Request::is_ajax()) {
            echo View::make('error.e404')
                 ->with('detals', $detals)
                 ->compile();
        } else {
            $outputData = array();
            $outputData['status'] = '404 Not Found';  
            if (!is_null($detals)) {
                $outputData['detals'] = $detals;
            }    
            echo json_encode($outputData, JSON_UNESCAPED_SLASHES);
        }
            
        exit();
    }

    /**
     * Выброс ошибки 410 - Страница удалена
     *
     * @param string $detals - опциональное сообщенние
     */
    public static function e410($detals = null)
    {
        Header::h410();

        ob_end_clean_all();

        if (!Request::is_ajax()) {
            echo View::make('error.e410')
                 ->with('detals', $detals)
                 ->compile();
        } else {
            $outputData = array();
            $outputData['status'] = '410 Gone';  
            if (!is_null($detals)) {
                $outputData['detals'] = $detals;
            }    
            echo json_encode($outputData, JSON_UNESCAPED_SLASHES);
        }
        
        exit();
    }

    /**
     * Выброс ошибки 503 - Сервис недоступен
     *
     * @param string $detals - опциональное сообщенние
     */
    public static function e503($detals = null)
    {
        Header::h503();

        ob_end_clean_all();

        if (!Request::is_ajax()) {
            echo View::make('error.e503')
                 ->with('detals', $detals)
                 ->compile();
        } else {
            $outputData = array();
            $outputData['status'] = '503 Service Unavailable';  
            if (!is_null($detals)) {
                $outputData['detals'] = $detals;
            }    
            echo json_encode($outputData, JSON_UNESCAPED_SLASHES);
        }
        
        exit();
    }
}
