<?php

namespace App\Request;

use Exception;
/**
 * Request class containing all the request data
 *
 */
class Request
{
    /***
     * Request method (RequestMethod ENUM)
     * @var RequestMethod
     */
    private $_method;
    /***
     * Complete URL
     *
     * @var String
     */
    private $_fullUrl;
    /***
     * Query params
     *
     * @var RequestParam
     */
    private $_queryParams;
    /***
     * Post request fields
     *
     * @var RequestParam
     */
    private $_postFields;
    /***
     * Request headers
     *
     * @var RequestParam
     */
    private $_requestHeaders;
    /***
     * Response headers
     *
     * @var RequestParam
     */
    private $_responseHeaders;
    /***
     * Access object
     *
     * @var \stdClass
     */
    private $_accessObject;
    /***
     * Initialize all params with defaults
     *
     * Request constructor.
     */
    public function __construct()
    {
        $this->_method = null;
        $this->_fullUrl = null;
        $this->_queryParams = new RequestParam();
        $this->_postFields = new RequestParam();
        $this->_requestHeaders = new RequestParam();
        $this->_responseHeaders = new RequestParam();
    }

    /***
     * Populate request object
     */
    public function populate()
    {
        // Set request method name
        if( isset($_SERVER['REQUEST_METHOD']) ) {
            $this->setMethod($_SERVER['REQUEST_METHOD']);
        }

        //Set full Url
        if( isset($_SERVER['REQUEST_URI']) ) {
            $fullRequestUrl = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            $this->setFullUrl($fullRequestUrl);
        }
        // Set request headers
        $this->setRequestHeaders($this->_getAllHeaders());

        // Set query params (Get params)
        $this->setQueryParams($_GET);

        //Set post fields (if request is put or post)
        if( in_array($this->getMethod(), [RequestMethod::POST ,RequestMethod::PUT]) ) {
            $this->setPostParams($this->_getPostDataFromRequest());
        }
    }
    /***
     * Set method name
     *
     * @param $methodName
     */
    public function setMethod($methodName)
    {
        $this->_method = $methodName;
    }
    /***
     * Get request method
     *
     * @return RequestMethod
     */
    public function getMethod()
    {
        return $this->_method;
    }
    /***
     * get full request url
     *
     * @return null|String
     */
    public function getFullUrl()
    {
        return $this->_fullUrl;
    }
    /***
     * set full request url
     *
     * @param $fullUrl
     */
    public function setFullUrl($fullUrl)
    {
        $this->_fullUrl = $fullUrl;
    }
    /***
     * Get request headers
     *
     * @return RequestParam
     */
    public function getRequestHeaders()
    {
        return $this->_requestHeaders;
    }

    /***
     * Set request headers (If param is empty or not an array do not set)
     *
     * @param $requestHeaders
     */
    public function setRequestHeaders($requestHeaders)
    {
        if(is_array($requestHeaders) && count($requestHeaders) > 0) {
            $this->_requestHeaders->setParamsBulk($requestHeaders);
        }
    }

    /***
     * Set query params
     *
     * @param $queryParams
     */
    public function setQueryParams($queryParams)
    {
        if(is_array($queryParams) && count($queryParams)) {
            $this->_queryParams->setParamsBulk($queryParams);
        }
    }
    /***
     * Get query params
     *
     * @return RequestParam
     */
    public function getQueryParams()
    {
        return $this->_queryParams;
    }

    /***
     * Append query params
     *
     * @param $queryParams
     */
    public function appendQueryParams($queryParams)
    {
        if(is_array($queryParams) && count($queryParams)) {
            $this->_queryParams->appendParamsBulk($queryParams);
        }
    }
    /***
     * Set post params
     *
     * @param $postParams
     */
    public function setPostParams($postParams)
    {
        if(is_array($postParams) && count($postParams)) {
            $this->_postFields->setParamsBulk($postParams);
        }
    }
    /***
     * Get post params
     *
     * @return RequestParam
     */
    public function getPostParams()
    {
        return $this->_postFields;
    }

    /***
     * Set access object
     *
     * @param $accessObj
     */
    public function setAccessObject($accessObj)
    {
        $this->_accessObject = $accessObj;
    }

    /***
     * Get access object
     *
     * @return \stdClass
     */
    public function getAccessObject()
    {
        return $this->_accessObject;
    }
    /***
     * Get request header values from request
     *
     * @return array
     */
    private function _getAllHeaders() {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            //Remove HTTP from header param name
            $name = strtolower(substr($name, 0, 5) === 'HTTP_' ? substr($name, 5) : $name);
            $headers[ str_replace(' ', '_', $name) ] = $value;
        }
        return $headers;
    }

    /***
     * Get post data from request
     *
     * @return array|mixed
     * @throws Exception
     */
    private function _getPostDataFromRequest()
    {
        $postParams = [];
        $contentType = $this->_requestHeaders->get('content_type');
        switch($contentType) {
            case 'application/json':
                $postData = file_get_contents('php://input');
                //Check if json is valid if not throw exception
                if(isValidateJsonString($postData)) {
                    $postParams = json_decode($postData, true);
                } else {
                    throw new Exception("Invalid Json Body.");
                }
                break;
            default:
                $postParams = $_POST;
                break;
        }
        return $postParams;
    }
}