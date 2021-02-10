<?php
/** User: Sabo */

namespace sabosuke\bit_mvc_core\exception;
use sabosuke\bit_mvc_core\Application;
use \Exception;

/** 
 * Class ForbiddenException
 * 
 * @author Essam Abed <abedissam95@gmail.com>
 * @package sabosuke\bit_mvc_core\exception
*/

class ForbiddenException extends \Exception{

    protected $message = 'You don\' have permission to access this page';
    protected $code = 403;

}