<?php

namespace sabosuke\bit_mvc_core\error_handler\error;

/** 
 * Class UndefinedFunctionError
 * 
 * @author Essam Abed <abedissam95@gmail.com>
 * @package sabosuke\bit_mvc_core\error_handler\error
*/

class UndefinedFunctionError extends \Error{
    
    /**
     * handles any undefined function error
     *
     * @param string $message
     * @param \Throwable $previous
     */
    public function __construct(string $message, \Throwable $previous)
    {
        parent::__construct($message, $previous->getCode(), $previous->getPrevious());

        foreach ([
            'file' => $previous->getFile(),
            'line' => $previous->getLine(),
            'trace' => $previous->getTrace(),
        ] as $property => $value) {
            $refl = new \ReflectionProperty(\Error::class, $property);
            $refl->setAccessible(true);
            $refl->setValue($this, $value);
        }
    }
    
}