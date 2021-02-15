<?php
/** User: Sabo */

namespace sabosuke\bit_mvc_core\error_handler;

/** 
 * Class Debug
 * 
 * @author Essam Abed <abedissam95@gmail.com>
 * @package sabosuke\bit_mvc_core\error_handler
*/

class Debug{
    
    /**
     * Debug constructor
     *
     * @param integer $options_backTrace
     * @param integer $limit_backTrace
     */
    public function __construct(){
        
    }

    private static $calls;

    public static function logger(string $message = null)
    {
        if(!is_array(self::$calls))
            self::$calls = array();

        $call = debug_backtrace(false);
        $call = (isset($call[1])) ? $call[1] : $call[0];

        $call['message'] = $message;
        array_push(self::$calls, $call);
    }
    
    public function whereCalled(int $level = 1) {
        $trace = debug_backtrace();
        $file   = $trace[$level]['file'];
        $line   = $trace[$level]['line'];
        $object = $trace[$level]['object'];
        if (is_object($object)) { $object = get_class($object); }
    
        return "Where called: line $line of $object \n(in $file)";
    }

    public function dump( $var ) {
        $result = var_export($var, true);
        $loc = $this->whereCalled();
        return "\n<pre>Dump: $loc\n$result</pre>";
    }

}