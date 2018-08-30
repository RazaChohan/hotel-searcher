<?php
/***
 * Helper methods that can be used across the application
 */

/***
 * Return friendly error message
 *
 * @return string
 */
function returnFriendlyErrorMessage()
{
    http_response_code(500);
    $response = ['message' => 'Oh no! Something bad happened. Please come back later when we fixed that problem. Thanks'];
    return json_encode($response);
}