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
    public function setAddress($address, $city, $postCode): void;

    /**
     * calculate the Coordinates. returns true if it was successful.
     * @return bool
     */
    public function calculateCoordinates(): bool;

    /**
     * returns the latitude.
     * @return float|null
     */
    public function getLat(): ?float;

    /**
     * returns the longitude.
     * @return float|null
     */
    public function getLng(): ?float;
}