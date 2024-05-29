<?php

// autoload function call all classes from src folder
$autoloadManager = new autoloadManager(null, autoloadManager::SCAN_ONCE);
$autoloadManager->addFolder(__DIR__ . '/Finance/');
$autoloadManager->register();


#require 'chart/visits/VisitsGetBrowser.php';

use \chart\Finance\GetInventoryControl as GetInventoryControl;
use chart\Finance\GetServices as GetServices;
use chart\Finance\GetProducts as GetProducts;
use chart\Finance\GetReport as GetReport;
/**
 * Main Class
 */
class FinanceModel {

    public function __construct($action = NULL) {
        switch ($action) {
            case 'GetServices':
                $return = new GetServices();
                break;
            case 'GetProducts':
                $return = new GetProducts();
                break;
            case 'InventoryControl':
                $return = new GetInventoryControl();
                break;
            case 'Report':
                $return = new GetReport();
                break;
        }
        return $return;
    }

}
