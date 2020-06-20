<?php
require_once 'Telegram.php';

class ModuleManager {

    private $modules;

    public function __construct()
    {
        $this->modules = [
            'Telegram' => new Telegram()
        ];
    }

    public function runModules() {

        foreach ($this->modules as $module) {

            $module->run();
        }
    }
}
