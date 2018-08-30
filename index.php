<?php
/***
 * Main index file for application
 *
 * @package Hotel search
 * @version 1.0
 * @author Muhammad Raza <razachohan@live.com>
 */

// Require vendor autoloader
require_once __DIR__ . '/vendor/autoload.php';
use App\Router\Router;

try {
    //Creating router object and passing request to router
    $routerObject = new Router();

    //initialize bootstrapping process
    $routerObject->registerRoutes();

    //match incoming request with registered routes
    $response =  $routerObject->matchRequest();
    if($response['status']) {
        $responseToEcho = json_encode($response);
    } else {
        $responseToEcho = returnFriendlyErrorMessage($exception);
    }

    echo $responseToEcho;

} catch(Exception $exception) {
    //Log error in file
    error_log(date("[Y-m-d H:i:s]").
        "\t[" .$exception->getMessage() . "]" .
        "\t[" . $exception->getTraceAsString() . "]" .
        "\t[" . $_SERVER['REQUEST_URI'] . "]" .
        "\n\n\n", 3, "errors.log");

    echo returnFriendlyErrorMessage($exception);
}