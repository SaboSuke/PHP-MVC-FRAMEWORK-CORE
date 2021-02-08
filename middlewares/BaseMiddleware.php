<?php
/** User: Sabo */

namespace app\core\middlewares;

/** 
 * Class BaseMiddleware
 * 
 * @author Essam Abed <abedissam95@gmail.com>
 * @package app\core\middlewares
*/

abstract class BaseMiddleware{

    /**
     * BaseMiddleware constructor
     * 
     */
    public function __construct(){
        //
    }

    abstract public function execute();
    
}