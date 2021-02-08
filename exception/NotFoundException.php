<?php
/** User: Sabo */

namespace app\core\exception;
use app\core\Application;
use \Exception;

/** 
 * Class NotFoundException
 * 
 * @author Essam Abed <abedissam95@gmail.com>
 * @package app\core\exception
*/

class NotFoundException extends \Exception{

    protected $message = 'Page not found';
    protected $code = 404;
}