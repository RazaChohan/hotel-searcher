<?php

namespace App\Middlewares;
/***
 * Middleware interface
 *
 * Interface IMiddleware
 */
Interface IMiddleware
{
    /***
     * Handle middleware func. using the request object
     *
     * @return mixed
     */
    public function handle();
}