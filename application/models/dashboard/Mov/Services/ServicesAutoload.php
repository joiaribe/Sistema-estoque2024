<?php

namespace Mov\Services;

use Developer\Tools\Url as Url;
use Dashboard\breadcrumb as breadcrumb;

/**
 * auto load class
 */
class ServicesAutoload extends ServicesConfig {

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
                new ServicesPreview; // load module application for mode preview
                break;
            case 'add':
                new breadcrumb(FILENAME, $this->breadcrumbs[1]);
                new ServicesInsert; // load module application for mode add
                break;
            case 'alt':
                new breadcrumb(FILENAME, $this->breadcrumbs[1]);
                new ServicesUpdate; // load module application for mode add
                break;
            case 'del':
                new ServicesActions;
                break;
            case 'delete_all':
                new ServicesActions;
                break;
            case 'filter':
                new breadcrumb(FILENAME, $this->breadcrumbs[0]);
                new ServicesListingFilter; // load module application for default (listing)
                break;
            default :
                new breadcrumb(FILENAME, $this->breadcrumbs[0]);
                new ServicesListing; // load module application for default (listing)
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
