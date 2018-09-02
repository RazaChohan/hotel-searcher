<?php

namespace App\Middlewares;
use Exception;
/***
 * Middleware Service provider applies a one or more
 * middlewares of a specific route
 *
 * Class MiddlewareServiceProvider
 */

use App\Request\Request;
use Libs\ResponseCode;

class MiddlewareServiceProvider implements IMiddleware
{
    /***
     * Middleware constant
     */
    const MIDDLEWARE = 'Middleware';
    /***
     * Middleware namespace constant for middleware instantiation
     */
    const MIDDLEWARE_NAMESPACE = 'App\Middlewares\\';
    /***
     * Middleware folder
     */
    const MIDDLEWARE_FOLDER = 'app/Middlewares/';
    /***
     * Request object containing all the params of request
     *
     * @var Request
     */
    private $_request;
    /***
     * Comma Middlewares that should be applied on the request
     *
     * @var array
     */
    private $_middlewares;
    /***
     * Response body returned from controller
     *
     * @var array
     */
    private $_responseBody;

    /***
     * MiddlewareServiceProvider constructor.
     *
     * @param $middlewares
     * @param $request
     * @param $responseBody
     */
    public function __construct(string $middlewares, Request &$request, &$responseBody)
    {
        $this->_request = $request;
        $this->_middlewares = explode(',', $middlewares);
        $this->_responseBody = $responseBody;
    }

    /***
     * Execute middlewares passed
     *
     */
    public function handle()
    {
        if(count($this->_middlewares) > 0) {
            foreach($this->_middlewares as $middleware) {
                $middleware = $this->_snakeCaseToCamelCase($middleware) . MiddlewareServiceProvider::MIDDLEWARE;
                if($this->_checkIfMiddlewareExists($middleware)) {
                    $middleware = MiddlewareServiceProvider::MIDDLEWARE_NAMESPACE . $middleware;
                    $middlewareObj = new $middleware($this->_request, $this->_responseBody);
                    $middlewareObj->handle();
                } else {
                    throw new Exception("$middleware Middleware does not exists.",
                                        ResponseCode::HTTP_INTERNAL_SERVER_ERROR);
                }
            }
        }
    }

    /***
     * Require middleware file
     *
     * @param $middlewareName
     * @return boolean
     */
    private function _checkIfMiddlewareExists($middlewareName) : bool
    {
        $file =  self::MIDDLEWARE_FOLDER . $middlewareName . '.php';
        $middlewareExists = false;

        //Test if the middleware file exists
        if (file_exists($file)) {
            $middlewareExists = true;
        }
        return $middlewareExists;
    }

    /***
     * Convert snake case to Pascal case
     *
     * @param $string
     * @return mixed
     */
    private function _snakeCaseToCamelCase($string) : string
    {
        if(!empty($string)) {
            $string = str_replace(' ', '',
                        ucwords(str_replace('_', ' ', $string)));
        }
        return $string;
    }
}