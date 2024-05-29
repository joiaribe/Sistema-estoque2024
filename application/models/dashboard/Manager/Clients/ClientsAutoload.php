<?php

namespace Manager\Clients;

use Developer\Tools\Url as Url;
use Dashboard\breadcrumb as breadcrumb;

/**
 * auto load class
 */
class ClientsAutoload extends ClientsConfig {

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
                new ClientsPreview; // load module application for mode preview
                break;
            case 'add':
                new breadcrumb(FILENAME, $this->breadcrumbs[1]);
                new ClientsInsert; // load module application for mode add
                break;
            case 'alt':
                new breadcrumb(FILENAME, $this->breadcrumbs[1]);
                new ClientsUpdate; // load module application for mode add
                break;
            case 'del':
                new ClientsActions;
                break;
            case 'delete_all':
                new ClientsActions;
                break;
            default :
                new breadcrumb(FILENAME, $this->breadcrumbs[0]);
                new ClientsListing; // load module application for default (listing)
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