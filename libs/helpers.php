<?php
/***
 * Helper methods that can be used across the application
 */

/***
 * Return friendly error message
 *
 * @param Exception $exception
 * @return string
 */
function returnFriendlyErrorMessage(Exception $exception)
{
    $exceptionCode = $exception->getCode();
    $responseMessage = "Oh no! Something bad happened. Please come back later when we fixed that problem. Thanks";
    $responseCode = 500;
    if($exceptionCode == 404) {
        $responseCode = 404;
        $responseMessage = $exception->getMessage();
    }
    http_response_code($responseCode);
    $response = ['message' => $responseMessage];
    return json_encode($response);
}

/***
 * Check if string is valid
 * @param string $jsonString
 * @return bool
 */
function isValidateJsonString(string $jsonString) : bool
{
    $isValidJson = true;
    if (is_object($jsonString)) {
        $isValidJson = false;
    } else {
        json_decode($jsonString);
        $isValidJson = (json_last_error() == JSON_ERROR_NONE);
    }
    return $isValidJson;
}