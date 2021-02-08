<?php
/** User: Sabo */

namespace sabosuke\sabophp_mvc_core\exception;
use sabosuke\sabophp_mvc_core\Application;
use \Exception;

/** 
 * Class ForbiddenException
 * 
 * @author Essam Abed <abedissam95@gmail.com>
 * @package sabosuke\sabophp_mvc_core\exception
*/

class ForbiddenException extends \Exception{

    protected $message = 'You don\' have permission to access this page';
    protected $code = 403;

}