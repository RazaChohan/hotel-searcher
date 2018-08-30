<?php

namespace App\Controllers;
use Exception;

/**
 * Hotel Controller
 *
 */
class HotelController extends BaseController
{
    public function __construct()
    {

    }
    public function search() : array
    {
        $response = ['message' => null, 'status' => true];
        try {
            $response['message'] = "You have reached hotel search method";
        } catch (Exception $exception) {
            $response['status'] = false;
            parent::log($exception, __FILE__, __METHOD__);
        }
        return $response;
    }
}