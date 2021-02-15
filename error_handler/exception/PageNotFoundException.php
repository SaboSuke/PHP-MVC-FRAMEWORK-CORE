<?php
/** User: Sabo */

namespace sabosuke\bit_mvc_core\error_handler\exception;
use sabosuke\bit_mvc_core\Application;
use \Exception;

/** 
 * Class PageNotFoundException
 * 
 * @author Essam Abed <abedissam95@gmail.com>
 * @package sabosuke\bit_mvc_core\error_handler\exception
*/

class PageNotFoundException extends BaseException{

    protected $message = 'Page not found';
    protected $code = 404;
    public const ERROR = "NOT_FOUND";
    
    /**
     * BaseForbiddenException constructor
     *
     * @param string $message
     */
    public function __construct(string $message = ""){
        parent::__construct();
        $message === '' ?  null : $this->message = $message; 
    }
    
}