<?php

namespace Manager\Receipt;

use Developer\Tools\Url as Url;
use Dashboard\breadcrumb as breadcrumb;

/**
 * auto load class
 */
class ReceiptAutoload extends ReceiptConfig {

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
            case 'preview':
                new breadcrumb(FILENAME, $this->breadcrumbs[1]);
                new ReceiptPreview; // load module application for mode preview
                break;
            case 'del':
                new ReceiptActions;
                break;
            case 'delete_all':
                new ReceiptActions;
                break;
            default :
                new breadcrumb(FILENAME, $this->breadcrumbs[0]);
                new ReceiptListing; // load module application for default (listing)
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
