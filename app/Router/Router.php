<?php

namespace App\Router;

use AltoRouter;
use Exception;
use App\Request\Request;
use Libs\ResponseCode;
use App\Middlewares\MiddlewareServiceProvider;
use App\Middlewares\MiddlewareType;
/**
 * Router Class
 *
 */
class Router
{
    /***
     * Controller namespace
     */
    CONST CONTROLLERS_NAMESPACE = "App\Controllers";
    /***
     * Controllers folder
     */
    const CONTROLLERS_FOLDER = 'app/Controllers/';
    /***
     * Altorouter object
     *
     * @var AltoRouter
     */
    private $_altoRouter;
    /***
     * Defined routes
     *
     * @var array
     */
    private $_definedRoutes;

    /***
     * Router constructor.
     */
    public function __construct()
    {
        $this->_altoRouter = new AltoRouter();
        $this->_definedRoutes = include "routes.php";
    }

    /***
     * Registers defined routes on altorouter
     *
     * @throws Exception
     */
    public function registerRoutes()
    {
        foreach($this->_definedRoutes as $key => $route) {
            //Get controller and action method names
            $controllerAndMethod = explode('.', $key);
            if(count($controllerAndMethod) == 2) {
                $controller = $controllerAndMethod[0];
                $method = $controllerAndMethod[1];
                //Map routes on alto router
                if (!empty($controller) && !empty($method) && isset($route['path'])
                    && isset($route['method'])) {
                    $this->_altoRouter->map($route['method'], $route['path'], $controller, $key);
                }
            } else {
                throw new Exception("Invalid route $key defined in routes.php");
            }
        }
    }

    /***
     * Match incoming request with registered routes
     *
     */
    public function matchRequest() : array
    {
        $matchedRoute = $this->_altoRouter->match();
        //Check is route is matched with registered routes
        if($matchedRoute) {
            //Populate request object
            $request = new Request();
            $request->populate();
            $request->appendQueryParams($matchedRoute['params']);

            //Apply pre middlewares
            $this->_callMiddlewares($request, $matchedRoute['name'],
                                    $this->_definedRoutes, MiddlewareType::PRE);

            $methodName = $this->_getMethodNameFromRouteName($matchedRoute['name']);

            //Check if controller and action method exists
            $controllerFileName = $matchedRoute['target'];
            $this->_checkControllerAndActionMethod($controllerFileName, $methodName);

            $controllerFullName = self::CONTROLLERS_NAMESPACE . '\\' .
                                  $this->_getControllerFullName($controllerFileName);

            //Instantiate Controller
            $controllerObj = new $controllerFullName();
            return $controllerObj->{$methodName}($request);
        }
        //api layer logic
        else {
            throw new Exception("Sorry, request could not be found.",
                            ResponseCode::HTTP_NOT_FOUND);
        }
    }

    /***
     * Get controller method name from route name
     *
     * @param string $routeName
     *
     * @return string $methodName
     */
    private function _getMethodNameFromRouteName(string $routeName) : string
    {
        $explodedRouteName = explode('.', $routeName);
        return end($explodedRouteName);
    }

    /***
     * Check existence of controller and action method
     *
     * @param string $controllerName
     * @param string $methodName
     * @throws Exception
     *
     */
    private function _checkControllerAndActionMethod(string $controllerName, string $methodName)
    {
        $controllerFullName = $this->_getControllerFullName($controllerName);
        $controllerFilePath = self::CONTROLLERS_FOLDER . $controllerFullName . '.php';
        //Test if the controller file exists - otherwise return exception
        if (file_exists($controllerFilePath)) {
            //Check if method exists in controller
            if(!method_exists(self::CONTROLLERS_NAMESPACE . '\\' . $controllerFullName, $methodName)) {
                throw new Exception("Method call '{$methodName}' does not exists in {$controllerFullName} controller");
            }
        } else {
            throw new Exception("Controller {$controllerFullName} does not exists");
        }
    }

    /***
     * Get Controller full name
     *
     * @param $controllerName
     * @return string
     */
    private function _getControllerFullName(string $controllerName) : string
    {
        return ucfirst($controllerName) . 'Controller';
    }
    /***
     * Call middlewares
     *
     * @param $request
     * @param $routeKey
     * @param $routes
     * @param $middlewareType
     * @param $responseBody
     */
    private function _callMiddlewares(&$request, $routeKey, $routes, $middlewareType, &$responseBody = null)
    {
        if( isset($routes[$routeKey]) && isset($routes[$routeKey]['middlewares'][$middlewareType]) &&
            !empty($routes[$routeKey]['middlewares'][$middlewareType])) {
            $middleServiceProvider = new MiddlewareServiceProvider($routes[$routeKey]['middlewares'][$middlewareType],
                                                                   $request, $responseBody);
            $middleServiceProvider->handle();
        }
    }
}