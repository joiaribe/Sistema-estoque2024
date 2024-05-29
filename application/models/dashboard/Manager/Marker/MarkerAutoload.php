<?php

namespace Manager\Marker;

use Developer\Tools\Url as Url;
use Dashboard\breadcrumb as breadcrumb;

/**
 * auto load class
 */
class MarkerAutoload extends MarkerConfig {

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
            case 'add':
                new breadcrumb(FILENAME, $this->breadcrumbs[1]);
                new MarkerInsert; // load module application for mode add
                break;
            case 'alt':
                new breadcrumb(FILENAME, $this->breadcrumbs[1]);
                new MarkerUpdate; // load module application for mode add
                break;
            case 'del':
                new MarkerActions;
                break;
            case 'delete_all':
                new MarkerActions;
                break;
            default :
                new breadcrumb(FILENAME, $this->breadcrumbs[0]);
                new MarkerListing; // load module application for default (listing)
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
