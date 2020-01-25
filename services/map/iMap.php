<?php

interface Map
{
    public function __construct($apiKey);
    public function setData($lat, $lng, $title);
    public function addMarker($lat, $lng, $title, $icon);
    public function getMap();
    public function getJS();
}