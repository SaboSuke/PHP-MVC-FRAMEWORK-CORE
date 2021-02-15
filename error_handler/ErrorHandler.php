<?php

/**
 * not that this class contains some symfony code
 */

namespace sabosuke\bit_mvc_core\error_handler;

use \Throwable;
use sabosuke\bit_mvc_core\error_handler\error\OutOfMemoryError;
use sabosuke\bit_mvc_core\error_handler\error\UndefinedMethodError;
use sabosuke\bit_mvc_core\error_handler\error\UndefinedFunctionError;
use sabosuke\bit_mvc_core\error_handler\error\ClassNotFoundError;
use sabosuke\bit_mvc_core\error_handler\error\FatalError;
use sabosuke\bit_mvc_core\error_handler\exception\SilencedErrorContext;

use sabosuke\bit_mvc_core\error_handler\exception\BaseForbiddenException;
use sabosuke\bit_mvc_core\error_handler\exception\PageNotFoundException;

use \ErrorException;

/** 
 * Class ErrorHandler
 * 
 * @author Essam Abed <abedissam95@gmail.com>
 * @package sabosuke\bit_mvc_core\error_handler
*/

class ErrorHandler {
    
    private $levels = [
        \E_DEPRECATED => 'Deprecated',
        \E_USER_DEPRECATED => 'User Deprecated',
        \E_NOTICE => 'Notice',
        \E_USER_NOTICE => 'User Notice',
        \E_STRICT => 'Runtime Notice',
        \E_WARNING => 'Warning',
        \E_USER_WARNING => 'User Warning',
        \E_COMPILE_WARNING => 'Compile Warning',
        \E_CORE_WARNING => 'Core Warning',
        \E_USER_ERROR => 'User Error',
        \E_RECOVERABLE_ERROR => 'Catchable Fatal Error',
        \E_COMPILE_ERROR => 'Compile Error',
        \E_PARSE => 'Parse Error',
        \E_ERROR => 'Error',
        \E_CORE_ERROR => 'Core Error',
    ];

    private $loggers = [
        \E_DEPRECATED => 'LOG_INFO',
        \E_USER_DEPRECATED => 'LOG_INFO',
        \E_NOTICE => 'LOG_WARNING',
        \E_USER_NOTICE => 'LOG_WARNING',
        \E_STRICT => 'LOG_WARNING',
        \E_WARNING => 'LOG_WARNING',
        \E_USER_WARNING => 'LOG_WARNING',
        \E_COMPILE_WARNING => 'LOG_WARNING',
        \E_CORE_WARNING => 'LOG_WARNING',
        \E_USER_ERROR => 'LOG_CRITICAL',
        \E_RECOVERABLE_ERROR => 'LOG_CRITICAL',
        \E_COMPILE_ERROR => 'LOG_CRITICAL',
        \E_PARSE => 'LOG_CRITICAL',
        \E_ERROR => 'LOG_CRITICAL',
        \E_CORE_ERROR => 'LOG_CRITICAL'
    ];

    private $thrownErrors = 0x1FFF; // E_ALL - E_DEPRECATED - E_USER_DEPRECATED
    private $scopedErrors = 0x1FFF; // E_ALL - E_DEPRECATED - E_USER_DEPRECATED
    private $tracedErrors = 0x77FB; // E_ALL - E_STRICT - E_PARSE
    private $otherErrors = 0x55; // E_ERROR + E_CORE_ERROR + E_COMPILE_ERROR + E_PARSE
    private $loggedErrors = 0;
    private static $reservedMemory = 0;
    private $lastErrorLog;

    private $exceptionHandler;
    private $isRoot = false;
    
    private static $silencedErrorCache = [];
    private static $silencedErrorCount = 0;

    private const NOT_FOUND = "NOT_FOUND";
    private const PERMISSION_DENIED = "PERMISSION_DENIED";

    /**
     * ErrorHandler constructor
     *
     * @param boolean $debug
     */
    public function __construct(bool $debug = false){
        $traceReflector = new \ReflectionProperty(\Exception::class, 'trace');
        $traceReflector->setAccessible(true);
        $this->configureException = \Closure::bind(static function ($e, $trace, $file = null, $line = null) use ($traceReflector) {
            $traceReflector->setValue($e, $trace);
            $e->file = $file ?? $e->file;
            $e->line = $line ?? $e->line;
        }, null, new class() extends \Exception {
        });
        $this->debug = $debug;
    }

    /**
     * Register the error.
     *
     * @param self $handler
     * @param boolean $replace
     * @return void
     */
    public static function registerError(self $handler = null, bool $replace = true){
        if (self::$reservedMemory === null){
            self::$reservedMemory = str_repeat('x', 10240);
        }
        $handler = $handler ?? new static();
        //$prev = set_error_handler([$handler, 'handleError']) ?? null;
        $prev = null;
        if (null === $prev) {
            restore_error_handler();
            //set_error_handler([$handler, 'handleError'], $handler->thrownErrors | $handler->loggedErrors);
            $handler->isRoot = true;
        }
        if (is_array($prev) && $prev[0] instanceof self) {
            $handler = $prev[0];
            $replace = false;
        }
        if (!$replace && $prev) {
            restore_error_handler();
            $handlerIsRegistered = is_array($prev) && $handler === $prev[0];
        } else {
            $handlerIsRegistered = true;
        }

        if (is_array($prev = set_exception_handler([$handler, 'handleException'])) && $prev[0] instanceof self) {
            restore_exception_handler();
            if (!$handlerIsRegistered) {
                $handler = $prev[0];
            } elseif ($handler !== $prev[0] && $replace) {
                set_exception_handler([$handler, 'handleException']);
                $p = $prev[0]->setExceptionHandler(null);
                $handler->setExceptionHandler($p);
                $prev[0]->setExceptionHandler($p);
            }
        } else {
            $handler->setExceptionHandler($prev);
        }

        return $handler;
        
    }

    /**
     * Sets a user exception handler.
     *
     * @param callable(\Throwable $e)|null $handler
     * @return callable|null The previous exception handler
     */
    public function setExceptionHandler(?callable $handler): ?callable
    {
        $prev = $this->exceptionHandler;
        $this->exceptionHandler = $handler;

        return $prev;
    }

    /**
     * Cleans the trace by removing function arguments and the frames added by the error handler and DebugClassLoader.
     */
    private function cleanTrace(array $backtrace, int $type, string &$file, int &$line, bool $throw): array
    {
        $lightTrace = $backtrace;

        for ($i = 0; isset($backtrace[$i]); ++$i) {
            if (isset($backtrace[$i]['file'], $backtrace[$i]['line']) && $backtrace[$i]['line'] === $line && $backtrace[$i]['file'] === $file) {
                $lightTrace = \array_slice($lightTrace, 1 + $i);
                break;
            }
        }
        if (\E_USER_DEPRECATED === $type) {
            for ($i = 0; isset($lightTrace[$i]); ++$i) {
                if (!isset($lightTrace[$i]['file'], $lightTrace[$i]['line'], $lightTrace[$i]['function'])) {
                    continue;
                }
                if (!isset($lightTrace[$i]['class']) && 'trigger_deprecation' === $lightTrace[$i]['function']) {
                    $file = $lightTrace[$i]['file'];
                    $line = $lightTrace[$i]['line'];
                    $lightTrace = \array_slice($lightTrace, 1 + $i);
                    break;
                }
            }
        }
        
        return $lightTrace;
    }

    public function handleError(int $type, string $message, string $file, int $line): bool
    {
        if (\PHP_VERSION_ID >= 70300 && \E_WARNING === $type && '"' === $message[0]) {
            $type = \E_DEPRECATED;
        }

        // Level is the current error reporting level to manage silent error.
        $level = error_reporting();
        $silenced = 0 === ($level & $type);

        // Strong errors are not authorized to be silenced.
        $level |= \E_RECOVERABLE_ERROR | \E_USER_ERROR | \E_DEPRECATED | \E_USER_DEPRECATED;
        $log = $this->loggedErrors & $type;
        $throw = $type & $level;
        $type &= $level | $this->otherErrors;

        // Never throw on warnings triggered by assert()
        if (\E_WARNING === $type && 'a' === $message[0] && 0 === strncmp($message, 'assert(): ', 10)) {
            $throw = 0;
        }

        if (!$throw && !($type & $level)) {
            if (!isset(self::$silencedErrorCache[$id = $file.':'.$line])) {
                $lightTrace = $this->tracedErrors & $type ? $this->cleanTrace(debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS, 5), $type, $file, $line, false) : [];
                $errorAsException = new SilencedErrorContext($type, $file, $line, isset($lightTrace[1]) ? [$lightTrace[0]] : $lightTrace);
            } elseif (isset(self::$silencedErrorCache[$id][$message])) {
                $lightTrace = null;
                $errorAsException = self::$silencedErrorCache[$id][$message];
                ++$errorAsException->count;
            } else {
                $lightTrace = [];
                $errorAsException = null;
            }

            if (100 < ++self::$silencedErrorCount) {
                self::$silencedErrorCache = $lightTrace = [];
                self::$silencedErrorCount = 1;
            }
            if ($errorAsException) {
                self::$silencedErrorCache[$id][$message] = $errorAsException;
            }
            if (null === $lightTrace) {
                return true;
            }
        }else {
            $errorAsException = new \ErrorException($message, 0, $type, $file, $line);

            if ($throw || $this->tracedErrors & $type) {
                $backtrace = $errorAsException->getTrace();
                $lightTrace = $this->cleanTrace($backtrace, $type, $file, $line, $throw);
                ($this->configureException)($errorAsException, $lightTrace, $file, $line);
            } else {
                ($this->configureException)($errorAsException, []);
                $backtrace = [];
            }
        }

        if ($throw) {
            if (\PHP_VERSION_ID < 70400 && \E_USER_ERROR & $type) {
                for ($i = 1; isset($backtrace[$i]); ++$i) {
                    if (isset($backtrace[$i]['function'], $backtrace[$i]['type'], $backtrace[$i - 1]['function'])
                        && !isset($backtrace[$i - 1]['class'])
                        && ('trigger_error' === $backtrace[$i - 1]['function'] || 'user_error' === $backtrace[$i - 1]['function'])
                    ) {
                        $context = 4 < \func_num_args() ? (func_get_arg(4) ?: []) : [];
                        foreach ($context as $e) {
                            if ($e instanceof \Throwable && $e->__toString() === $message) {
                                return true;
                            }
                        }

                        // Display the original error message instead of the default one.
                        $this->handleException($errorAsException);

                        // Stop the process by giving back the error to the native handler.
                        return false;
                    }
                }
            }

            throw $errorAsException;
        }

        return $type;
    }

    /**
     * Handles errors by filtering then logging them.
     *
     * @return bool Returns false when no handling happens so that the PHP engine can handle the error itself
     * @throws \ErrorException When $this->thrownErrors requests so
     */
    protected function getLastErrorLog(){
        return $this->lastErrorLog;
    }

    protected function formatCurrentDate(){
        return '['.date('Y-m-d H:i:s').'] - ';
    }

    /**
     * logger
     *
     * @param string $message
     * @param array|null $params
     * @return void
     */
    protected function log(string $message, ?array $params = []) {
        $exception = $params['exception'];
        if($exception){
            return $exception->getCode(). ' - ' . $message . $exception->getMessage() .PHP_EOL;
            $this->lastErrorLog = $this->formatCurrentDate(). $exception->getCode(). ' - ' . $message . $exception->getMessage() .PHP_EOL;
        }elseif($$params['type'] instanceof OutOfMemoryError){
            return 'Out Of Memory Exception: ' . $message . PHP_EOL;
            $this->lastErrorLog = $this->formatCurrentDate(). 'Out Of Memory Exception: ' . $message . PHP_EOL;
        }else{
            return $exception->getCode(). ' - ' . $message . $exception->getMessage() .PHP_EOL;
            $this->lastErrorLog = $this->formatCurrentDate() . $exception->getCode(). ' - ' . $message . $exception->getMessage() .PHP_EOL;
        }
    }

    /**
     * Handles an exception by logging.
     *
     * @internal
     */
    public function handleException(\Throwable $exception)
    {
        $handlerException = null;

        if (!$this->loggedErrors) {
            if (false !== strpos($message = $exception->getMessage(), "@anonymous\0")) {
                $message = "Anonymous Class Exception"; //call class not found exception here
            }

            if ($exception::ERROR == self::PERMISSION_DENIED) 
                $message = self::PERMISSION_DENIED . '<br>';
            elseif($exception::ERROR == self::NOT_FOUND)
                $message = self::NOT_FOUND . '<br>';
            if (strpos($exception->getMessage(), "FatalError")) {
                $message .= '<br>Fatal Error: ';
            } elseif ($exception instanceof \Error) {
                $message .= '<br>Uncaught Error: ';
            } elseif ($exception instanceof ErrorException) {
                $message .= '<br>Uncaught ';
            } elseif ($exception instanceof BaseForbiddenException) {
                $message .= 'Permission Error: ';
            }elseif($exception instanceof PageNotFoundException){
                $message .= 'Not Found Error: ';
            } else{
                $message .= '<br>Uncaught Exception: ';
            }

            try {
                $this->log($message, ['exception' => $exception]);
            } catch (\Throwable $handlerException) {
                echo $handlerException->getMessage();
            }
        }

        if ($exception instanceof OutOfMemoryError) {
            $this->log($exception->getMessage(), ['exception' => $exception, 'type' => 'OutOfMemoryError']);
        }
        $loggedErrors = $this->loggedErrors;
        $this->loggedErrors = $exception === $handlerException ? 0 : $this->loggedErrors;
    }

    /**
     * Calls a function and turns any PHP error into ErrorException.
     *
     * @return mixed What $function(...$arguments) returns
     * @throws ErrorException When $function(...$arguments) triggers a PHP error
     */
    public static function call(callable $function, ...$arguments)
    {
        set_error_handler(static function (int $type, string $message, string $file, int $line) {
            if (__FILE__ === $file) {
                $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
                $file = $trace[2]['file'] ?? $file;
                $line = $trace[2]['line'] ?? $line;
            }

            throw new ErrorException($message, 0, $type, $file, $line);
        });

        try {
            return $function(...$arguments);
        } finally {
            restore_error_handler();
        }
    }

    /**
     * Gets sequential array of all previously-chained errors
     *
     * @param Throwable $error
     * @return Throwable[]
     */
    public function getChain(Throwable $error) : array
    {
        $chain = [];

        do {
            $chain[] = $error;
        } while ($error = $error->getPrevious());

        return $chain;
    }
    
}