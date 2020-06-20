<?php

interface Modules {

    /**
     * This function will be called from the ModulManager.
     */
    public function run() :void;
}