<?php

namespace App\Controllers;
use Exception;

/**
 * Base Controller
 */
class BaseController
{
    /***
     * Log errors
     *
     * @param Exception $exception
     * @param string $filename
     * @param string $methodName
     */
    protected function log(Exception $exception, string $filename, string $methodName)
    {
        error_log(date("[Y-m-d H:i:s]").
                    "\t[" .$exception->getMessage() . "]" .
                    "\t[" . $exception->getTraceAsString() . "]" .
                    "\t[" . $filename . "]" .
                    "\t[" . $methodName . "]" .
                    "\n\n\n", 3, "errors.log");
    }
}