<?php
/** User: Sabo */

namespace sabosuke\bit_mvc_core\error_handler\exception;
use sabosuke\bit_mvc_core\Application;

/** 
 * Class MethodNotFoundException
 * 
 * @author Essam Abed <abedissam95@gmail.com>
 * @package sabosuke\bit_mvc_core\error_handler\exception
*/

class MethodNotFoundException extends BaseException{

    protected $message = 'Method not allowed or doesn\'t exist';
    protected $code = 405;
    public const ERROR  = "INVALID_METHOD";
    
    /**
     * MethodNotFoundException constructor
     *
     * @param string $message
     */
    public function __construct(string $message = ''){
        parent::__construct();
        $message === '' ?  null : $this->message = $message; 
    }    

}