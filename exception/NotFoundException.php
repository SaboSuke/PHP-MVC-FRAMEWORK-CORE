<?php
/** User: Sabo */

namespace sabosuke\bit_mvc_core\exception;
use sabosuke\bit_mvc_core\Application;
use \Exception;

/** 
 * Class NotFoundException
 * 
 * @author Essam Abed <abedissam95@gmail.com>
 * @package sabosuke\bit_mvc_core\exception
*/

class NotFoundException extends \Exception{

    protected $message = 'Page not found';
    protected $code = 404;
}