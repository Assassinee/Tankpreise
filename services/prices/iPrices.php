<?php

interface Prices
{
    public function __construct($apiKey);
    public function setData($lat, $lng, $radius);
    public function getStations();
    public function getPrice($tankstellen);
}