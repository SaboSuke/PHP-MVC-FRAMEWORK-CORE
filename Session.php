<?php
/** User: Sabo */

namespace app\core;

/** 
 * Class Session
 * 
 * @author Essam Abed <abedissam95@gmail.com>
 * @package app\core
*/

class Session{

    protected const FLASH_KEY = 'flash_messages';

    /**
     * Session constructor
     * 
     */
    public function __construct(){
        session_start();
        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];
        foreach($flashMessages as $key => &$flashMessage){ //&flashMessage => modify the actual array not the copy of it = change on reload and url change
            //Mark to be removed at the end of the request 
            $flashMessage['remove'] = true;
        }

        $_SESSION[self::FLASH_KEY] = $flashMessages;
    }

    public function setFlash($key, $message){
        $_SESSION[self::FLASH_KEY][$key] = [
            'remove' => false,
            'value' => $message
        ];
    }

    public function getFlash($key){
        return $_SESSION[self::FLASH_KEY][$key]['value'] ?? false;
    }

    public function __destruct(){
        //Iterate over marked to removed flash messages and remove them
        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];
        foreach($flashMessages as $key => &$flashMessage){ 
            if($flashMessage['remove']){
                unset($flashMessages[$key]);
            } 
        }

        $_SESSION[self::FLASH_KEY] = $flashMessages;
    }

    public function set($key, $value){
        $_SESSION[$key] = $value;
    }

    public function get($key){
        return $_SESSION[$key] ?? false;
    }

    public function remove($key){
        unset($_SESSION[$key]);
    }

}