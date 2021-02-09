<?php
/** User: Sabo */

namespace sabosuke\bit_mvc_core\middlewares;

/** 
 * Class BaseMiddleware
 * 
 * @author Essam Abed <abedissam95@gmail.com>
 * @package sabosuke\bit_mvc_core\middlewares
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