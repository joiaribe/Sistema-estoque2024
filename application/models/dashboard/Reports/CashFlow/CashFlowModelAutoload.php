<?php

namespace Reports\CashFlow;

use Developer\Tools\Url as Url;
use Dashboard\breadcrumb as breadcrumb;
use Manager\CashFlow\ClientsModel as ClientsModel;

/**
 * auto load class
 */
class CashFlowAutoload extends CashFlowConfig {

    /**
     * Breadcrumbs
     * @var array 
     */
    var $breadcrumbs = array();

    /**
     * what action should load the objects ?
     * @return Void
     */
    private function swich_call() {
        $param = Url::getURL($this->URL_ACTION);
        switch ($param) {
            // manager
            case 'clients':
                new breadcrumb(FILENAME, $this->breadcrumbs[1]);
                new ClientsModel; // load module application for mode add
                break;






            // mov
            case 'income':
                new breadcrumb(FILENAME, $this->breadcrumbs[1]);
                new CashFlowUpdate; // load module application for mode add
                break;
            case 'manager_expense':
                new CashFlowActions;
                break;
            case 'manager_products':
                new CashFlowActions;
                break;
            case 'manager_services':
                new breadcrumb(FILENAME, $this->breadcrumbs[0]);
                new CashFlowListing; // load module application for default (listing)
            default :
                new breadcrumb(FILENAME, $this->breadcrumbs[0]);
                new CashFlowMain; // load module application for default (listing)
                break;
        }
    }

    /**
     * Check
     * @return Void
     */
    public function __construct($breadcrumbs) {
        $this->breadcrumbs = $breadcrumbs;
        $this->swich_call();
    }

}
