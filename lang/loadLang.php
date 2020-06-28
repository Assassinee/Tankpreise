<?php

if (array_key_exists($language, $availableLanguages))
{
    require_once $language.'.php';
} else {

    require_once 'EN.php';
}