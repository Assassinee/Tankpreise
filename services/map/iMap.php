<?php

interface Map
{
    /**
     * Map constructor.
     * @param $apiKey is the apikey for the service.
     */
    public function __construct($apiKey);

    /**
     * This function sets data for the map.
     * @param $lat is the Latitude of the map.
     * @param $lng is the Longitude of the map.
     * @param $title is the title of the map.
     */
    public function setData($lat, $lng, $title): void;

    /**
     * This function adds a new marker to the map.
     * @param $lat is the Latitude of the marker.
     * @param $lng is the Longitude of the marker.
     * @param $title is the title of the marker.
     * @param $icon is the icon of the marker.
     */
    public function addMarker($lat, $lng, $title, $icon): void;

    /**
     * This function outputs the HTML-code for the map.
     * @return string
     */
    public function getMap(): string;

    /**
     * This function outputs the Javascript-code for the map.
     * @return string
     */
    public function getJS(): string;
}