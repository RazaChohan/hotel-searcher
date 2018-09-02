<?php

namespace Tests;

use App\Controllers\HotelController;
use App\Request\Request;
use Exception;
use Libs\ResponseCode;

/***
 * Hotel Controller Tests
 *
 * Class HotelControllerTest
 * @package tests
 */
class HotelControllerTest extends BaseTestCase
{
    /***
     * Hotel search
     */
    CONST HOTEL_SEARCH = '/hotel/search';
    /***
     * Hotel controller object
     * @var HotelController
     */
    private $_hotelController;

    /***
     * Test case set up method
     */
    public function setUp()
    {
        $this->_hotelController = new HotelController();
        parent::setUp();
    }

    /***
     * Test to check api is returning valid response
     */
    public function testSearchDataFound()
    {
        $isSuccessful = false;
        $dateRange = '01-01-2015:31-12-2050';
        $requestObject = $this->_getRequestObject(null, null, $dateRange,
                                                  null, null);
        $response = $this->_hotelController->search($requestObject);
        if($response['status'] === TRUE && count($response['data']) > 0) {
            $isSuccessful = true;
        }
        $this->assertTrue($isSuccessful);
    }

    /***
     * Test sort by name
     *
     */
    public function testSortByName()
    {
        $dateRange = '01-01-2015:31-12-2050';
        $requestObject = $this->_getRequestObject(null, null, $dateRange,
                                                  null, 'name');
        $response = $this->_hotelController->search($requestObject);
        $sortedResponse = $response['data'];
        $sortedArrayFromHelper = sortArrayByKey($response['data'], 'name', 'asc');
        $this->assertEquals($sortedArrayFromHelper, $sortedResponse);
    }
    /***
     * Test catch exception
     *
     */
    public function testCatchException()
    {
        $this->expectException(Exception::class);
        $mock = $this->createMock(HotelController::class);
        $mock->expects($this->once())
            ->method('search')
            ->will($this->throwException(
                new Exception('Expected Exception was thrown'))
            );
        $mock->search(new Request());
        $this->fail();
    }

    /***
     * Test search by city
     */
    public function testSearchByCity()
    {
        $isSuccessful = false;
        $city = 'dubai';
        $requestObject = $this->_getRequestObject($city, null, null,
                                                  null, null);

        $response = $this->_hotelController->search($requestObject);
        if($response['status'] === TRUE && count($response['data']) > 0) {
            $isSuccessful = true;
            foreach($response['data'] as $hotelData) {
                if($hotelData->destination != $city) {
                    $isSuccessful = false;
                    break;
                }
            }
        }

        $this->assertTrue($isSuccessful);
    }

    /***
     * Search by invalid city
     */
    public function testSearchByInvalidCity()
    {
        $isSuccessful = false;
        $city = 'abc';
        $requestObject = $this->_getRequestObject($city, null, null,
                                                    null, null);

        $response = $this->_hotelController->search($requestObject);
        if($response['status'] == TRUE && count($response['data']) == 0) {
            $isSuccessful = true;
        }
        $this->assertTrue($isSuccessful);
    }

    /***
     * Test search by price range
     *
     */
    public function testSearchByPriceRange()
    {
        $isSuccessful = true;
        //Setting price range for price range test
        $priceRange = "$10:$1000";
        $minPrice = 10;
        $maxPrice = 1000;
        $requestObject = $this->_getRequestObject(null, null, null,
                                                  $priceRange, null);

        $response = $this->_hotelController->search($requestObject);
        if($response['status'] == TRUE && count($response['data']) > 0) {
            if( count($response['data']) > 0 ) {
                foreach( $response['data'] as $hotel ) {
                    $isSuccessful = ( $minPrice <= $hotel->price && $hotel->price <= $maxPrice );
                    //If any record is out of range break the loop
                    if(!$isSuccessful) {
                        break;
                    }
                }
            }
        }
        $this->assertTrue($isSuccessful);
    }
    /***
     * Get request object for hotel controller
     *
     * @param $destination
     * @param $hotelName
     * @param $dateRange
     * @param $priceRange
     * @param $sortBy
     *
     * @return Request
     */
    private function _getRequestObject($destination, $hotelName, $dateRange, $priceRange, $sortBy)
    {
        $request = new Request();
        $request->getQueryParams()->set('destination', $destination);
        $request->getQueryParams()->set('name', $hotelName);
        $request->getQueryParams()->set('date_range', $dateRange);
        $request->getQueryParams()->set('price_range', $priceRange);
        $request->getQueryParams()->set('sort_by', $sortBy);
        return $request;
    }
}