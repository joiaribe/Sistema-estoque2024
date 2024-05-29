<?php

namespace Settings\Menu;

use Developer\Tools\Url as Url;
use Dashboard\breadcrumb as breadcrumb;

/**
 * auto load class
 */
class MenuAutoload extends MenuConfig {

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
            case 'alt':
                new breadcrumb(FILENAME, $this->breadcrumbs[1]);
                new MenuUpdate; // load module application for mode add
                break;
            case 'del':
                new MenuActions;
                break;
            case 'delete_all':
                new MenuActions;
                break;
            default :
                new breadcrumb(FILENAME, $this->breadcrumbs[0]);
                new MenuListing; // load module application for default (listing)
                break;
        }
    }

    /**
     * Magic Metthod
     * @return Void
     */
    public function __construct($breadcrumbs) {
        $this->breadcrumbs = $breadcrumbs;
        $this->swich_call();
    }

}
