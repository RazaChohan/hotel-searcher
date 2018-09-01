<?php

namespace App\Middlewares;

use App\Request\Request;
use Libs\Config;
use Exception;
use Libs\ResponseCode;

/***
 * Authentication request
 *
 * Class AuthMiddleware
 *
 */
class AuthMiddleware implements IMiddleware
{
    /***
     * Request object containing all the params of request
     *
     * @var Request
     */
    private $_request;
    /***
     * Response body
     *
     * @var array
     */
    private $_responseBody;
    /***
     * AuthMiddleware constructor.
     *
     * @param $requestObject Request
     * @param $responseBody array
     */
    public function __construct(Request &$requestObject, $responseBody)
    {
        $this->_request = $requestObject;
        $this->_responseBody = $responseBody;
    }

    /***
     * Handle middleware func. using the request object
     */
    public function handle()
    {
        $authToken = $this->_request->getQueryParams()->get('auth_token');
        // If auth token is not in query params check in post params
        if(!empty($authToken)) {
            //Check if auth token is correct
            if($authToken != Config::get('AUTH_TOKEN')) {
                throw new Exception("Auth token is not valid",
                        ResponseCode::HTTP_UNAUTHORIZED);
            }
        } else { //Auth token is not passed
            throw new Exception("Auth token is required", ResponseCode::HTTP_UNAUTHORIZED);
        }
    }
}