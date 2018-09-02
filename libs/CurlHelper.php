<?php
namespace Libs;

/***
 * Curl helper for API calls
 *
 * Class CurlHelper
 */
class CurlHelper
{
    /***
     * Get call
     *
     * @param $url
     * @param bool $decodeJson
     * @return array
     */
    public function getCall($url, $decodeJson = true)
    {
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        $responseBody = curl_exec($ch);
        $httpCode = $this->_getHttpCode($ch);
        curl_close($ch);
        if($httpCode == ResponseCode::HTTP_OK) {
            $responseBody = ($decodeJson) ? json_decode($responseBody) : $responseBody;
        }
        return [ 'httpCode' => $httpCode , 'responseBody' => $responseBody ];
    }
    /***
     * Get Http code from response of curl request
     *
     * @param $curlObj
     * @return mixed
     */
    private function _getHttpCode($curlObj)
    {
        return curl_getinfo($curlObj, CURLINFO_HTTP_CODE);
    }
}