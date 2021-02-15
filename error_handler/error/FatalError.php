<?php

namespace sabosuke\bit_mvc_core\error_handler\error;

/** 
 * Class FatalError
 * 
 * @author Essam Abed <abedissam95@gmail.com>
 * @package sabosuke\bit_mvc_core\error_handler\error
*/

class FatalError extends \Error{
    
    /**
     * handles any fatal error
     *
     * @param string $message
     * @param int $code
     * @param array $error 
     */
    public function __construct(string $message, int $code, array $error)
    {
        parent::__construct($message, $code);

        $this->error = $error;
    }
    
}