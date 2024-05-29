<?php

namespace Manager\Fornecedor;

use Developer\Tools\Url as Url;
use Dashboard\breadcrumb as breadcrumb;

/**
 * auto load class
 */
class FornecedorAutoload extends FornecedorConfig {

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
                new FornecedorPreview; // load module application for mode preview
                break;
            case 'add':
                new breadcrumb(FILENAME, $this->breadcrumbs[1]);
                new FornecedorInsert; // load module application for mode add
                break;
            case 'alt':
                new breadcrumb(FILENAME, $this->breadcrumbs[1]);
                new FornecedorUpdate; // load module application for mode add
                break;
            case 'del':
                new FornecedorActions;
                break;
            case 'delete_all':
                new FornecedorActions;
                break;
            default :
                new breadcrumb(FILENAME, $this->breadcrumbs[0]);
                new FornecedorListing; // load module application for default (listing)
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
