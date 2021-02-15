<?php
/** User: Sabo */

namespace sabosuke\bit_mvc_core\error_handler\exception;
use sabosuke\bit_mvc_core\Application;
use \Exception;

/** 
 * Class BaseException
 * 
 * @author Essam Abed <abedissam95@gmail.com>
 * @package sabosuke\bit_mvc_core\error_handler\exception
*/

class BaseException extends \Exception{
    
    protected $backTrace;
    
    /**
     * BaseException constructor
     *
     * @param integer $options_backTrace
     * @param integer $limit_backTrace
     */
    public function __construct(int $options_backTrace = DEBUG_BACKTRACE_PROVIDE_OBJECT, int $limit_backTrace = 0){
        $this->backTrace = debug_backtrace($options_backTrace, $limit_backTrace);
    }

    public function isResponseCodeExist(int $code = NULL){
        //if !function_exists('http_response_code')
        if ($code !== NULL) {
            switch ($code) {
                case 200: case 101: case 100: case 0: $text = 'OK'; break;
                case 201: $text = 'Created'; break;
                case 202: $text = 'Accepted'; break;
                case 203: $text = 'Non-Authoritative Information'; break;
                case 204: $text = 'No Content'; break;
                case 205: $text = 'Reset Content'; break;
                case 206: $text = 'Partial Content'; break;
                case 300: $text = 'Multiple Choices'; break;
                case 301: $text = 'Moved Permanently'; break;
                case 302: $text = 'Moved Temporarily'; break;
                case 303: $text = 'See Other'; break;
                case 304: $text = 'Not Modified'; break;
                case 305: $text = 'Use Proxy'; break;
                case 400: $text = 'Bad Request'; break;
                case 401: $text = 'Unauthorized'; break;
                case 402: $text = 'Payment Required'; break;
                case 403: $text = 'Forbidden'; break;
                case 404: $text = 'Not Found'; break;
                case 405: $text = 'Method Not Allowed'; break;
                case 406: $text = 'Not Acceptable'; break;
                case 407: $text = 'Proxy Authentication Required'; break;
                case 408: $text = 'Request Time-out'; break;
                case 409: $text = 'Conflict'; break;
                case 410: $text = 'Gone'; break;
                case 411: $text = 'Length Required'; break;
                case 412: $text = 'Precondition Failed'; break;
                case 413: $text = 'Request Entity Too Large'; break;
                case 414: $text = 'Request-URI Too Large'; break;
                case 415: $text = 'Unsupported Media Type'; break;
                case 500: $text = 'Internal Server Error'; break;
                case 501: $text = 'Not Implemented'; break;
                case 502: $text = 'Bad Gateway'; break;
                case 503: $text = 'Service Unavailable'; break;
                case 504: $text = 'Gateway Time-out'; break;
                case 505: $text = 'HTTP Version not supported'; break;
                case 507: $text = 'Insufficient Storage'; break;
                default:
                    exit('Unknown http status code "' . htmlentities($code) . '"');
                break;
            }

            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . $code . ' ' . $text);
            $GLOBALS['http_response_code'] = $code;
        } else {
            $code = (isset($GLOBALS['http_response_code']) ? $GLOBALS['http_response_code'] : 200);
        }
        return $code;
    }
    //debug_backtrace()
    function generateExceptionErrorMessage(Exception $e, ?string $customMessage = "") {
        $trace = $e->getTrace();
        $result =  'Exception: "'. $customMessage ?? $e->getMessage() . '" @ ';
        if($trace[0]['class'] != '') {
            $result .= $trace[0]['class'] . '->';
        }
        return $result . $trace[0]['function'] . '();';
    }

    public static $calls;

    public static function set_logger($message = null)
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