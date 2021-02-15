<?php
/** User: Sabo */

namespace sabosuke\bit_mvc_core\error_handler\exception;
use sabosuke\bit_mvc_core\Application;

/** 
 * Class BaseForbiddenException
 * 
 * @author Essam Abed <abedissam95@gmail.com>
 * @package sabosuke\bit_mvc_core\error_handler\exception
*/

class BaseForbiddenException extends BaseException{

    protected $message = 'You don\'t have permission to access this page';
    protected $code = 403;
    public const ERROR  = "PERMISSION_DENIED";
    
    /**
     * BaseForbiddenException constructor
     *
     * @param string $message
     */
    public function __construct(string $message = ''){
        parent::__construct();
        $message === '' ?  null : $this->message = $message; 
    }    

}