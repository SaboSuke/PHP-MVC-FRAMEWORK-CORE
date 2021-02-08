<?php
/** User: Sabo */

namespace sabosuke\sabophp_mvc_core;

/** 
 * Class Response
 * 
 * @author Essam Abed <abedissam95@gmail.com>
 * @package sabosuke\sabophp_mvc_core
*/

class Response{

    /**
     * Response constructor
     * 
     */
    public function __construct(){
        //
    }

    public function setStatusCode(int $code){
        http_response_code($code);
    }

    public function redirect(string $url){
        header('Location: '. $url);
    }

}