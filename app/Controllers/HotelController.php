<?php

namespace App\Controllers;
use App\Request\Request;
use Exception;
use Libs\CurlHelper;
use Libs\Config;
use Libs\ResponseCode;
use App\Models\Hotel;
use App\Utilities\HotelSearchUtility;
/**
 * Hotel Controller
 *
 */
class HotelController extends BaseController
{
    /***
     * Search hotels
     *
     * @param Request $request
     * @return array
     */
    public function search(Request $request)
    {
        $response = ['message' => null, 'status' => true];
        try {
            $curlHelperObject = new CurlHelper();

            //Call api to get data
            $hotelAPIUrl = Config::get('HOTEL_API');
            $apiResponse = $curlHelperObject->getCall($hotelAPIUrl);
            //Populate hotel model from api data
            if($apiResponse['httpCode'] == ResponseCode::HTTP_OK) {
                $hotelModel = new Hotel();
                $hotelModelCollection = $hotelModel->setHotels($apiResponse['responseBody']);
                $response['data'] = $this->_filterHotelData($request, $hotelModelCollection);
                $noOfHotelsFound = count($response['data']);
                $response['message'] = "$noOfHotelsFound hotels found as per your search criteria";
            }
        } catch (Exception $exception) {
            $response = returnFriendlyErrorMessage($exception);
            parent::log($exception, __FILE__, __METHOD__);
        }
        return $response;
    }

    /***
     * Filter hotel data
     *
     * @param Request $request
     * @param array $hotelData
     *
     * @return array
     */
    private function _filterHotelData(Request $request, $hotelData)
    {
        $destination = $request->getQueryParams()->get('destination');
        $hotelName   = $request->getQueryParams()->get('name');
        $priceRange  = $request->getQueryParams()->get('price_range');
        $dateRange   = $request->getQueryParams()->get('date_range');

        $sortBy      = $request->getQueryParams()->get('sort_by');
        $sortOrder   = $request->getQueryParams()->get('sort_order', 'asc');
        //Create hotel search utility
        $filters = [
            'destination'  => $destination,
            'name'         => $hotelName,
            'priceRange'   => $priceRange,
            'dateRange'    => $dateRange,
        ];
        $hotelSearchUtility = new HotelSearchUtility($filters, $hotelData);
        $filteredHotelsData = $hotelSearchUtility->searchHotelsUsingFilters();
        //Sort filtered array
        if(!empty($sortBy)) {
            $filteredHotelsData = sortArrayByKey($filteredHotelsData, $sortBy, $sortOrder);
        }
        return $filteredHotelsData;
    }
}