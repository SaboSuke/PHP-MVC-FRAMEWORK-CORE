<?php
/** User: Sabo */

namespace sabosuke\bit_mvc_core\error_handler\exception;
use sabosuke\bit_mvc_core\Application;
use \Exception;

/** 
 * Class QueryUnfinishedException
 * 
 * @author Essam Abed <abedissam95@gmail.com>
 * @package sabosuke\bit_mvc_core\error_handler\exception
*/

class QueryUnfinishedException extends BaseException{

    protected $message = 'Your should finish the query before getting the result || ';
    protected $code = 404;
    public const ERROR = "QUERY_UNFINISHED";
    
    /**
     * QueryUnfinishedException constructor
     *
     * @param string $message
     */
    public function __construct(string $message = ""){
        parent::__construct();
        $message === '' ?  null : $this->message = $message; 
    }
    
}