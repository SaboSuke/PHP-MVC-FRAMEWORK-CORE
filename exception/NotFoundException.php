<?php
/** User: Sabo */

namespace sabosuke\sabophp_mvc_core\exception;
use sabosuke\sabophp_mvc_core\Application;
use \Exception;

/** 
 * Class NotFoundException
 * 
 * @author Essam Abed <abedissam95@gmail.com>
 * @package sabosuke\sabophp_mvc_core\exception
*/

class NotFoundException extends \Exception{

    protected $message = 'Page not found';
    protected $code = 404;
}