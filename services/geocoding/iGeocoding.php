<?php

interface Geocoding
{
    /**
     * Geocoding constructor.
     * @param $apiKey
     */
    public function __construct($apiKey);

    /**
     * @param $adresse is the address.
     * @param $stadt is the city.
     * @param $plz is the post code.
     * @return mixed
     */
    public function setAddress($address, $city, $postCode);

    /**
     * calculate the Coordinates. returns true if it was successful.
     * @return boolean
     */
    public function calculateCoordinates();

    /**
     * returns the latitude
     * @return mixed
     */
    public function getLat();

    /**
     * returns the longitude.
     * @return mixed
     */
    public function getLng();
}