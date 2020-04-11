<?php

interface Prices
{
    /**
     * Prices constructor.
     * @param $apiKey is the apikey for the service.
     */
    public function __construct($apiKey);

    /**
     * This function sets data for the data query.
     * @param $lat is the Latitude of the map.
     * @param $lng is the Longitude of the map.
     * @param $radius is the search radius.
     */
    public function setData($lat, $lng, $radius): void;

    /**
     * This function returns the gas stations in the area.
     * @return array
     */
    public function getStations(): array;

    /**
     * This function returns the current petrol prices.
     * @param $stations are the petrol stations from which the price is sought.
     * @return array
     */
    public function getPrice($stations): array;
}