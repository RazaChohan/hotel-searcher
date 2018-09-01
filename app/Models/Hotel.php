<?php
namespace App\Models;
/***
 * Class Hotel
 *
 */
class Hotel
{
    /***
     * Name of hotel
     *
     * @var string
     */
    public $name;
    /***
     * Destination of hotel (city)
     * @var string
     */
    public $destination;
    /***
     * Price of hotel accommodation
     * @var double
     */
    public $price;
    /***
     * Availability dates
     * @var array
     */
    public $availability;

    /***
     * Set hotel object
     *
     * @param $hotelRecord
     *
     * @return Hotel
     */
    public function setHotel($hotelRecord)
    {
        $hotel = new self();
        if(!empty($hotelRecord)) {
            $hotel->name = $hotelRecord->name;
            $hotel->price = $hotelRecord->price;
            $hotel->destination = $hotelRecord->city;
            $hotel->availability = $hotelRecord->availability;
        }
        return $hotel;
    }

    /***
     * Set hotels
     *
     * @param $dataCollection
     * @return array
     */
    public function setHotels($dataCollection)
    {
        $hotels = [];
        $dataCollection = $dataCollection->hotels;
        foreach($dataCollection as $hotelRecord) {
            $city = !empty($hotelRecord->city) ? strtolower($hotelRecord->city) : 'no-city';
            $hotels[ $city ] [] = $this->setHotel($hotelRecord);
        }
        return $hotels;
    }
}