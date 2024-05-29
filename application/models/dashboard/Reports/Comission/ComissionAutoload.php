<?php

namespace Reports\Comission;

use Developer\Tools\Url as Url;
use Dashboard\breadcrumb as breadcrumb;
use Reports\Employee\ComissionActions as ComissionActions;
use Reports\Comission\ComissionMain as ComissionMain;
use Reports\Comission\ComissionPreview as ComissionPreview;

/**
 * auto load class
 */
class ComissionAutoload extends ComissionConfig {

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
                new ComissionPreview; // load module application for mode preview
                break;
            case 'mark':
                new ComissionActions();
                break;
             case 'MarkSingle':
                new ComissionActions();
                break;
            case 'del':
                new ComissionActions();
                break;
            case 'PreviewListing':
                new breadcrumb(FILENAME, $this->breadcrumbs[0]);
                new ComissionListing();
                break;
            default :
                new breadcrumb(FILENAME, $this->breadcrumbs[0]);
                new ComissionMain(); // load module application for default (listing)
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
