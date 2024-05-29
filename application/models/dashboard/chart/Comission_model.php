<?php

// autoload function call all classes from src folder
$autoloadManager = new autoloadManager(null, autoloadManager::SCAN_ONCE);
$autoloadManager->addFolder(__DIR__ . '/Comission/');
$autoloadManager->register();

use chart\Comission\GetEmployee as GetEmployee;
use chart\Comission\GetSallers as GetSallers;
use chart\Comission\GetReport as GetReport;
/**
 * Main Class
 */
class ComissionModel {

    public function __construct($action = NULL) {
        switch ($action) {
            case 'GetEmployee':
                $return = new GetEmployee();
                break;
            case 'GetSallers':
                $return = new GetSallers();
                break;
            case 'Report':
                $return = new GetReport();
                break;
        }
        return $return;
    }

}
