<?php
namespace App\Utilities;

use DateTime;
/***
 * Hotel search utility
 *
 * Class HotelSearchUtility
 */
Class HotelSearchUtility
{
    /***
     * Filters to apply while searching
     *
     * @var array
     */
    private $_filters;
    /***
     * Data containing hotels
     *
     * @var array
     */
    private $_data;

    /***
     * HotelSearchUtility constructor.
     *
     * @param $filters array
     * @param $hotelData array
     */
    public function __construct($filters, $hotelData)
    {
        $this->setData($hotelData);
        $this->setFilters($filters);
    }
    /***
     * Set filters
     *
     * @param $filters
     */
    public function setFilters($filters)
    {
        //Prepare date range filter
        if(isset($filters['dateRange'])) {
            $dateRangePreparedFilters = $this->_prepareDateRangeFilters($filters['dateRange']);
            $filters = array_merge($filters, $dateRangePreparedFilters);
        }
        //Prepare price range filter
        if(isset($filters['priceRange'])) {
            $priceRangePreparedFilters = $this->_preparePriceRangeFilters($filters['priceRange']);
            $filters = array_merge($filters, $priceRangePreparedFilters);
        }
        $this->_filters = $filters;
    }

    /***
     * Set data
     *
     * @param $data
     */
    public function setData($data)
    {
        $this->_data = $data;
    }

    /***
     * Prepare date range filters
     *
     * @param $dateRangeFilter
     * @return array
     */
    private function _prepareDateRangeFilters($dateRangeFilter)
    {
        $preparedDateRangeFilter = [];
        $dateRange = explode(':', $dateRangeFilter);
        if(count($dateRange) == 2) {
            $startDate = date("d-m-Y", strtotime($dateRange[0]));
            $endDate   = date("d-m-Y", strtotime($dateRange[1]));

            //Swap dates if start date is greater than end date
            if($startDate > $endDate) {
                $temp = $startDate;
                $startDate = $endDate;
                $endDate = $temp;
            }
            $preparedDateRangeFilter['startDate'] = new DateTime($startDate);
            $preparedDateRangeFilter['endDate'] = new DateTime($endDate);
        } else {
            $preparedDateRangeFilter['onlyDate'] = new DateTime(date("d-m-Y", strtotime($dateRange[0])));
        }
        return $preparedDateRangeFilter;
    }
    /***
     * Prepare price range filters
     *
     * @param $priceRangeFilter
     * @return array
     */
    private function _preparePriceRangeFilters($priceRangeFilter)
    {
        $preparedPriceRangeFilter = [];
        $priceRange = explode(':', str_replace('$','', $priceRangeFilter) );
        if(count($priceRange) == 2) {
            $startPrice = $priceRange[0];
            $endPrice   = $priceRange[1];

            //Swap dates if start date is greater than end date
            if($startPrice > $endPrice) {
                $temp = $startPrice;
                $startPrice = $endPrice;
                $endPrice = $temp;
            }
            $preparedPriceRangeFilter['startPrice'] = $startPrice;
            $preparedPriceRangeFilter['endPrice'] = $endPrice;
        } else {
            $preparedPriceRangeFilter['onlyPrice'] = $priceRange[0];
        }
        return $preparedPriceRangeFilter;
    }
    /***
     * Search hotels using filters
     *
     * @return array $filteredData
     */
    public function searchHotelsUsingFilters()
    {
        $city = isset($this->_filters['destination']) ? strtolower( $this->_filters['destination'] ) : null;

        if(!empty($city)) {
            if( isset($this->_data[ $city ]) ) {
                $filteredData = array_filter($this->_data[$city], [$this, 'matchCriteria']);
            } else {
                $filteredData = [];
            }
        } else {
            $filteredData = [];
            foreach($this->_data as $cityData) {
                $cityData = array_filter($cityData , [$this, 'matchCriteria']);
                $filteredData = array_merge($filteredData, $cityData);
            }
        }
        return $filteredData;
    }
    /***
     * Receives each Hotel as $item and applies filters supplied
     * by the user if test passes then returns true otherwise false
     *
     * @param $item
     * @return bool|int
     */
    public function matchCriteria($item)
    {
        $matched = true;
        $hotelName = isset($this->_filters['name']) ? $this->_filters['name'] : null;
        //Filter on basis of hotel name
        if(isset($this->_filters) && !empty($hotelName)) {
            $matched = $this->_matchHotelName($hotelName, $item->name);
        }
        //Filter on the basis of price range
        if( $matched && isset($this->_filters['priceRange']) ) {
            $matched = $this->_matchPriceRange($this->_filters, $item->price);
        }

        //Filter on the basis of date range
        if( $matched && isset($this->_filters['dateRange']) ) {
            $matched = $this->_matchDateRange($this->_filters, $item->availability);
        }
        return $matched;
    }

    /***
     * Match hotel name
     *
     * @param $filterValue
     * @param $recordValue
     *
     * @return boolean
     */
    private function _matchHotelName($filterValue, $recordValue)
    {
        return preg_match("/$filterValue/i" , $recordValue);
    }

    /***
     * Match price Range
     * @param $filters
     * @param $recordPrice
     *
     * @return bool
     */
    private function _matchPriceRange($filters, $recordPrice)
    {
        $matched = true;
        //Check for start price
        if( isset($filters['startPrice']) ) {
            $matched = ($filters['startPrice'] <= $recordPrice) ? true : false;
        }
        //Check for end price
        if ( isset($filters['endPrice']) && $matched ) {
            $matched = ($filters['endPrice'] >= $recordPrice) ? true : false;
        }
        //Price exact match
        if( isset($filters['onlyPrice'])) {
            $matched = ($filters['onlyPrice'] == $recordPrice) ? true : false;
        }
        return $matched;
    }

    /***
     * Match date Range
     *
     * @param $filters
     * @param $recordAvailability
     * @return bool
     */
    private function _matchDateRange($filters, $recordAvailability)
    {
        $matched = false;
        $compareWithDateRange = (isset($filters['onlyDate']) ) ? false : true;
        foreach( $recordAvailability as $dateItem)
        {
            $recordAvailabilityTo = new DateTime($dateItem->to);
            $recordAvailabilityFrom = new DateTime($dateItem->from);
            if( $compareWithDateRange && isset($filters['startDate'])
                                      && isset($filters['endDate']) )  {

                if( $filters['startDate'] <= $recordAvailabilityTo && $filters['endDate'] >= $recordAvailabilityFrom ) {
                    $matched = true;
                    break;
                }
            } else if(!$compareWithDateRange && isset($filters['onlyDate']) ) {

                if( $filters['onlyDate'] <= $recordAvailabilityTo
                    && $filters['onlyDate'] >= $recordAvailabilityFrom) {
                    
                    $matched = true;
                    break;
                }
            }

        }
        return $matched;
    }
}