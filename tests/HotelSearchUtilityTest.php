<?php

namespace Tests;

use App\Utilities\HotelSearchUtility;
use Libs\Config;
use Libs\ResponseCode;
use App\Models\Hotel;

class HotelSearchUtilityTest extends BaseTestCase
{
    /***
     * Hotel search utility object
     *
     * @var HotelSearchUtility
     */
    private $_hotelSearchUtility;
    /***
     * Hotel data
     *
     * @var array
     */
    private $_hotelData;
    /***
     * Filters array
     *
     * @var array
     */
    private $_filters;
    /***
     * Hotel API url
     *
     * @var string
     */
    private $_hotelAPIUrl;
    /***
     * Test case set up method
     */
    public function setUp()
    {
        $this->_filters = [];
        $this->_hotelData = [];
        $this->_hotelSearchUtility = new HotelSearchUtility($this->_filters, $this->_hotelData);
        $this->_hotelAPIUrl = Config::get('HOTEL_API');
        parent::setUp();
    }

    /***
     * Test data fetch from API
     */
    public function testDataFetchFromAPI()
    {
        $apiResponse = $this->curlHelper->getCall($this->_hotelAPIUrl);
        //Populate hotel model from api data
        if($apiResponse['httpCode'] == ResponseCode::HTTP_OK) {
            $hotelModel = new Hotel();
            $this->_hotelData = $hotelModel->setHotels($apiResponse['responseBody']);
        }
        $this->assertTrue( count($this->_hotelData) > 0);
    }
    /***
     * Test city filter
     *
     */
    public function testCityFilter()
    {
        $isSuccessful = false;
        $this->_setHotelData();
        //Check if hotel data exists
        if(count($this->_hotelData) > 0) {
            //Check if first city data
            $cityName = key($this->_hotelData);
            $filters['destination'] = $cityName;
            $filteredData = $this->_getFilteredData($filters);
            foreach ($filteredData as $hotelRecord) {
                if ($hotelRecord->destination == $filters['destination']) {
                    $isSuccessful = true;
                    break;
                }
            }
        }
        $this->assertTrue($isSuccessful);
    }

    /***
     * Test city name filter
     */
    public function testHotelNameFilter()
    {
        $isSuccessful = false;
        $this->_setHotelData();
        //Check if hotel data exists
        if(count($this->_hotelData) > 0) {
            //Check if first city data has more than zero city records
            $firstCityHotelsData = current($this->_hotelData);
            if(count($firstCityHotelsData) > 0) {
                $firstHotelData = current($firstCityHotelsData);
                $filters['name'] = $firstHotelData->name;
                $filteredData = $this->_getFilteredData($filters);
                foreach ($filteredData as $hotelRecord) {
                    if ($hotelRecord->name == $filters['name']) {
                        $isSuccessful = true;
                        break;
                    }
                }
            }
        }
        $this->assertTrue($isSuccessful);
    }
    /***
     * Test price range filter
     */
    public function testPriceRangeFilter()
    {
        $isSuccessful = false;
        $this->_setHotelData();
        //Check if hotel data exists
        if(count($this->_hotelData) > 0) {
            //Check if first city data has more than zero city records
            $firstCityHotelsData = current($this->_hotelData);
            if(count($firstCityHotelsData) > 0) {
                $firstHotelData = current($firstCityHotelsData);
                $price = $firstHotelData->price;
                $filters['priceRange'] = "$" . ($price - 10) . ":" . "$" . ($price + 100);
                $filteredData = $this->_getFilteredData($filters);
                foreach ($filteredData as $hotelRecord) {
                    if (($price - 10) <= $hotelRecord->price && $hotelRecord->price <= ($price + 100)) {
                        $isSuccessful = true;
                        break;
                    }
                }
            }
        }
        $this->assertTrue($isSuccessful);
    }
    /***
     * Test price swap filter
     */
    public function testPriceSwapFilter()
    {
        $isSuccessful = false;
        $this->_setHotelData();
        //Check if hotel data exists
        if(count($this->_hotelData) > 0) {
            //Check if first city data has more than zero city records
            $firstCityHotelsData = current($this->_hotelData);
            if(count($firstCityHotelsData) > 0) {
                $firstHotelData = current($firstCityHotelsData);
                $price = $firstHotelData->price;
                $filters['priceRange'] = "$" . ($price + 100) . ":" . "$" . ($price - 10);
                $filteredData = $this->_getFilteredData($filters);
                foreach ($filteredData as $hotelRecord) {
                    if (($price - 10) <= $hotelRecord->price && $hotelRecord->price <= ($price + 100)) {
                        $isSuccessful = true;
                        break;
                    }
                }
            }
        }
        $this->assertTrue($isSuccessful);
    }
    /***
     * Test price range filter
     */
    public function testExactPriceFilter()
    {
        $isSuccessful = false;
        $this->_setHotelData();
        //Check if hotel data exists
        if(count($this->_hotelData) > 0) {
            //Check if first city data has more than zero city records
            $firstCityHotelsData = current($this->_hotelData);
            if(count($firstCityHotelsData) > 0) {
                $firstHotelData = current($firstCityHotelsData);
                $filters['priceRange'] = $firstHotelData->price;
                $filteredData = $this->_getFilteredData($filters);
                foreach ($filteredData as $hotelRecord) {
                    if ($hotelRecord->price == $filters['priceRange']) {
                        $isSuccessful = true;
                        break;
                    }
                }
            }
        }
        $this->assertTrue($isSuccessful);
    }
    /***
     * Test date range filter
     */
    public function testDateRangeFilter()
    {
        $isSuccessful = false;
        $this->_setHotelData();
        //Check if hotel data exists
        if(count($this->_hotelData) > 0) {
            //Check if first city data has more than zero city records
            $firstCityHotelsData = current($this->_hotelData);
            if(count($firstCityHotelsData) > 0) {
                $firstHotelData = current($firstCityHotelsData);
                $filters['dateRange'] = $this->_prepareDateRangeFilter($firstHotelData->availability);
                $filteredData = $this->_getFilteredData($filters);
                foreach ($filteredData as $hotelRecord) {
                    $availability = count($hotelRecord->availability) ? $hotelRecord->availability[0] : null;
                    if(!is_null($availability)) {
                        $availability = $availability->from . ':' . $availability->to;
                    }
                    if ($availability == $filters['dateRange']) {
                        $isSuccessful = true;
                        break;
                    }
                }
            }
        }
        $this->assertTrue($isSuccessful);
    }
    /***
     * Test date range filter
     */
    public function testDateRangeSwapFilter()
    {
        $isSuccessful = false;
        $this->_setHotelData();
        //Check if hotel data exists
        if(count($this->_hotelData) > 0) {
            //Check if first city data has more than zero city records
            $firstCityHotelsData = current($this->_hotelData);
            if(count($firstCityHotelsData) > 0) {
                $firstHotelData = current($firstCityHotelsData);
                $filters['dateRange'] = $this->_prepareDateRangeFilter($firstHotelData->availability, true);
                $filteredData = $this->_getFilteredData($filters);
                foreach ($filteredData as $hotelRecord) {
                    $availability = count($hotelRecord->availability) ? $hotelRecord->availability[0] : null;
                    if(!is_null($availability)) {
                        $availability =  $availability->to . ':' . $availability->from;
                    }
                    if ($availability == $filters['dateRange']) {
                        $isSuccessful = true;
                        break;
                    }
                }
            }
        }
        $this->assertTrue($isSuccessful);
    }

    /***
     * Test match exact date filter
     */
    public function testMatchExactDateFilter()
    {
        $isSuccessful = false;
        $this->_setHotelData();
        //Check if hotel data exists
        if(count($this->_hotelData) > 0) {
            //Check if first city data has more than zero city records
            $firstCityHotelsData = current($this->_hotelData);
            if(count($firstCityHotelsData) > 0) {
                $firstHotelData = current($firstCityHotelsData);
                $filters['dateRange'] = count($firstHotelData->availability) > 0 ? $firstHotelData->availability[0] : null;
                if(!is_null($filters['dateRange'])) {
                    $filters['dateRange'] =  $filters['dateRange']->to;
                }
                $filteredData = $this->_getFilteredData($filters);
                foreach ($filteredData as $hotelRecord) {
                    $availability = count($hotelRecord->availability) ? $hotelRecord->availability[0] : null;

                    if ( $availability <= $filters['dateRange'] || $availability >= $filters['dateRange']) {
                        $isSuccessful = true;
                        break;
                    }
                }
            }
        }
        $this->assertTrue($isSuccessful);
    }

    /***
     * Set hotel data from hotel search utility
     *
     */
    private function _setHotelData()
    {
        $apiResponse = $this->curlHelper->getCall($this->_hotelAPIUrl);
        //Populate hotel model from api data
        if($apiResponse['httpCode'] == ResponseCode::HTTP_OK) {
            $hotelModel = new Hotel();
            $this->_hotelData = $hotelModel->setHotels($apiResponse['responseBody']);
            reset($this->_hotelData);
        }
    }

    /***
     * Get filtered data
     *
     * @param $filters
     *
     * @return array
     */
    private function _getFilteredData($filters)
    {
        $this->_hotelSearchUtility->setFilters($filters);
        $this->_hotelSearchUtility->setData($this->_hotelData);
        return $this->_hotelSearchUtility->searchHotelsUsingFilters();
    }

    /***
     * Prepare date range filter
     *
     * @param $availabilityArray
     * @param $swapDate
     *
     * @return null|string
     */
    private function _prepareDateRangeFilter($availabilityArray, $swapDate = false)
    {
        $dateRangeFilter = null;
        $dateRangeFilter = count($availabilityArray) > 0 ? $availabilityArray[0] : null;

        if(!is_null($dateRangeFilter)) {
            $dateRangeFilter = ($swapDate) ? ($dateRangeFilter->to . ':' . $dateRangeFilter->from) :
                                        ($dateRangeFilter->from . ':' . $dateRangeFilter->to);
        }
        return $dateRangeFilter;
    }

}