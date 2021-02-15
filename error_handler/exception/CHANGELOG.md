CHANGELOG
=========

1.0.8
-----

 * added `__getTrace()` method to all exception classes
 * you can now specify trace `$options` inside of any exception class constructor the default value for it is the same as `debug_backtrace()`
 * added a `BaseException` that checks if the http response exist, returns a well presented exception error message and 