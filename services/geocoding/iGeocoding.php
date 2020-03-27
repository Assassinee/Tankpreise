<?php

interface Geocoding
{
    /**
     * Geocoding constructor.
     * @param $apiKey is the apikey for the service.
     */
    public function __construct($apiKey);

    /**
     * This function set the address for the calculation.
     * @param $address is the address.
     * @param $city is the city.
     * @param $postCode is the post code
     * @return void
     */
    public function setAddress($address, $city, $postCode): void;

    /**
     * This function calculate the Coordinates.
     * returns true if it was successful.
     * @return bool
     */
    public function calculateCoordinates(): bool;

    /**
     * This function returns the latitude.
     * @return float|null
     */
    public function getLat(): ?float;

    /**
     * This function returns the longitude.
     * @return float|null
     */
    public function getLng(): ?float;
}