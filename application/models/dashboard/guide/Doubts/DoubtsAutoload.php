<?php

namespace guide\Doubts;

use Developer\Tools\Url as Url;
use Dashboard\breadcrumb as breadcrumb;

/**
 * auto load class
 */
class DoubtsAutoload extends DoubtsConfig {

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
                new DoubtsPreview; // load module application for mode preview
                break;
            case 'Search':
                new breadcrumb(FILENAME, $this->breadcrumbs[1]);
                new DoubtsListingSearch; // load module application for mode preview
                break;
            default :
                new breadcrumb(FILENAME, $this->breadcrumbs[0]);
                new DoubtsListing; // load module application for default (listing)
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
