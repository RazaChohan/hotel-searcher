<?php
namespace App\Utilities;

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
            $preparedDateRangeFilter['startDate'] = $startDate;
            $preparedDateRangeFilter['endDate'] = $endDate;
        } else {
            $preparedDateRangeFilter['onlyDate'] = date("d-m-Y", strtotime($dateRange[0]));
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
        $city = strtolower( $this->_filters['destination'] );

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
        $hotelName = $this->_filters['name'];
        //Filter on basis of hotel name
        if(isset($this->_filters) && !empty($hotelName)) {
            $matched = $this->_matchHotelName($hotelName, $item->name);
        }
        //Filter on the basis of price range
        if( $matched && isset($this->_filters['priceRange']) && !empty($this->_filters['priceRange'])) {
            $matched = $this->_matchPriceRange($this->_filters, $item->price);
        }

        //Filter on the basis of date range
        if( $matched && isset($this->_filters['dateRange']) && !empty($this->_filters['dateRange'])) {
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
    private function _matchHotelName(string $filterValue, string $recordValue) : bool
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
    private function _matchPriceRange($filters, $recordPrice) : bool
    {
        $matched = true;
        //Check for start price
        if( isset($filters['startPrice']) && !empty($filters['startPrice']) ) {
            $matched = ($filters['startPrice'] <= $recordPrice) ? true : false;
        }
        //Check for end price
        if ( isset($filters['endPrice']) && !empty($filters['endPrice']) && $matched ) {
            $matched = ($filters['endPrice'] >= $recordPrice) ? true : false;
        }
        //Price exact match
        if( isset($filters['onlyPrice']) && !empty($filters['onlyPrice'])) {
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
    private function _matchDateRange($filters, $recordAvailability) : bool
    {
        $matched = false;
        $compareWithDateRange = (isset($filters['onlyDate']) && !empty($filters['onlyDate'])) ? false : true;
        foreach( $recordAvailability as $dateItem)
        {
            if( $compareWithDateRange && isset($filters['startDate']) && !empty($filters['startDate'])
                                      && isset($filters['endDate']) && !empty($filters['endDate']) )  {

                if( $filters['startDate'] >= $dateItem->from && $dateItem->to >= $filters['endDate'] ) {
                    $matched = true;
                    break;
                }
            } else if(!$compareWithDateRange && isset($filters['onlyDate']) && !empty($filters['onlyDate'])) {

                if( $filters['onlyDate'] == $dateItem->from || $dateItem->to == $filters['onlyDate'] ) {
                    $matched = true;
                    break;
                }
            }

        }
        return $matched;
    }
}