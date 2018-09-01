<?php

namespace App\Controllers;
use App\Request\Request;
use Exception;
use Libs\CurlHelper;
use Libs\Config;
use Libs\ResponseCode;
use App\Models\Hotel;
/**
 * Hotel Controller
 *
 */
class HotelController extends BaseController
{
    public function __construct()
    {

    }

    /***
     * Search hotels
     *
     * @param Request $request
     * @return array
     */
    public function search(Request $request) : array
    {
        $response = ['message' => null, 'status' => true];
        try {
            $curlHelperObject = new CurlHelper();
            $destination = $request->getQueryParams()->get('destination');
            $hotelName   = $request->getQueryParams()->get('name');
            $priceRange  = $request->getQueryParams()->get('price_range');
            $dateRange   = $request->getQueryParams()->get('date_range');
            $sortBy      = $request->getQueryParams()->get('sort_by');
            $sortOrder   = $request->getQueryParams()->get('sort_order', 'asc');

            //Call api to get data
            $hotelAPIUrl = Config::get('HOTEL_API');
            $apiResponse = $curlHelperObject->getCall($hotelAPIUrl);
            if($apiResponse['httpCode'] == ResponseCode::HTTP_OK) {
                $hotelModel = new Hotel();
                $response['data'] = $hotelModel->setHotels($apiResponse['responseBody']);
            }
            $response['message'] = "You have reached hotel search method";
        } catch (Exception $exception) {
            $response = returnFriendlyErrorMessage($exception);
            parent::log($exception, __FILE__, __METHOD__);
        }
        return $response;
    }
}