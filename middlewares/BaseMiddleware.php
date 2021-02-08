<?php
/** User: Sabo */

namespace sabosuke\sabophp_mvc_core\middlewares;

/** 
 * Class BaseMiddleware
 * 
 * @author Essam Abed <abedissam95@gmail.com>
 * @package sabosuke\sabophp_mvc_core\middlewares
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