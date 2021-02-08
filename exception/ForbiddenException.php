<?php
/** User: Sabo */

namespace app\core\exception;
use app\core\Application;
use \Exception;

/** 
 * Class ForbiddenException
 * 
 * @author Essam Abed <abedissam95@gmail.com>
 * @package app\core\exception
*/

class ForbiddenException extends \Exception{

    protected $message = 'You don\' have permission to access this page';
    protected $code = 403;

}