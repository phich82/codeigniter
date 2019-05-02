<?php
/**
* @author Huynh Phat <phat.nguyen@persol.co.jp>
* @license [v1]
*/
namespace App\Api\Helpers;

class ApiLog
{
    /**
     * Log by the info type
     *
     * @param string $message
     * @return void
     */
    public static function info($message = '')
    {
        self::log('info', $message);
    }

    /**
     * Log by the debug type
     *
     * @param string $message
     * @return void
     */
    public static function debug($message = '')
    {
        self::log('debug', $message);
    }

    /**
     * Log by the type
     *
     * @param string $type
     * @param string $message
     * @return void
     */
    public static function log($type = 'info', $message = '')
    {
        log_message($type, $message);
    }
}
