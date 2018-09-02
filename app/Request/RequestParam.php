<?php
namespace App\Request;

/***
 * Request get and post params
 *
 * Class Request Param
 */
class RequestParam
{
    /***
     * Request params array
     *
     * @var array
     */
    private $_params;

    /***
     * Initialize params as empty array
     *
     * RequestParam constructor.
     */
    public function __construct()
    {
        $this->_params = [];
    }

    /***
     * Get specific value for key from params
     *
     * @param $key
     * @param null $default
     * @return mixed|null
     */
    public function get($key, $default = null)
    {
        $value = $default;
        if( array_key_exists($key, $this->_params) ) {
              $value = $this->_params[$key];
        }
        return $value;
    }

    /***
     * Set passed value on
     *
     * @param $key
     * @param $value
     */
    public function set($key, $value)
    {
        $this->_params[$key] = $value;
    }

    /***
     * set params in bulk
     *
     * @param $params
     */
    public function setParamsBulk($params)
    {
        if(is_array($params) && count($params) > 0) {
            $this->_params = $params;
        }
    }

    /***
     * Append params array in Params attribute
     *
     * @param $params
     */
    public function appendParamsBulk($params)
    {
        if(is_array($params) && count($params) > 0) {
            $this->_params = array_merge($this->_params, $params);
        }
    }

    /***
     * Get params
     *
     * @return array
     */
    public function getParams()
    {
        return $this->_params;
    }
}