<?php
use Libs\ResponseCode;

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
    $responseCode = ResponseCode::HTTP_INTERNAL_SERVER_ERROR;
    if($exceptionCode != ResponseCode::HTTP_INTERNAL_SERVER_ERROR) {
        $responseCode = $exceptionCode;
        $responseMessage = $exception->getMessage();
    }
    http_response_code($responseCode);
    $response = ['message' => $responseMessage, 'status' => false ];
    return json_encode($response);
}

/***
 * Check if string is valid
 * @param string $jsonString
 * @return bool
 */
function isValidateJsonString(string $jsonString)
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

/***
 * Log errors
 *
 * @param Exception $exception
 */
function logError(Exception $exception)
{
    //Log error in file
    error_log(date("[Y-m-d H:i:s]").
        "\t[" .$exception->getMessage() . "]" .
        "\t[" . $exception->getTraceAsString() . "]" .
        "\t[" . $_SERVER['REQUEST_URI'] . "]" .
        "\n\n\n", 3, "errors.log");
}

/***
 * Sort array by key
 *
 * @param $inputArray
 * @param $sortBy
 * @param $sortOrder
 * @return mixed
 */
function sortArrayByKey($inputArray, $sortBy, $sortOrder = 'asc') : array
{
    $sortKeyArray = [];
    foreach ($inputArray as $key => $row) {
        $sortKeyArray[ $key ] = $row->$sortBy;
    }
    $sortOrder = ($sortOrder == 'asc') ? SORT_ASC : SORT_DESC;
    array_multisort($sortKeyArray, $sortOrder, $inputArray);
    return $inputArray;
}
