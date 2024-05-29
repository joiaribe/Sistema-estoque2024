<?php

// autoload function call all classes from src folder
$autoloadManager = new autoloadManager(null, autoloadManager::SCAN_ONCE);
$autoloadManager->addFolder(__DIR__ . '/Doubts/');
$autoloadManager->register();

use Developer\Tools\Url as Url;
use guide\Doubts\DoubtsAutoload as DoubtsAutoload;

/**
 * Main Class
 */
class DoubtsModel extends DoubtsAutoload {

    private function _capture_name_widget_breadrumb_action() {
        $param = Url::getURL($this->URL_ACTION);
        $result = false;
        switch ($param) {
            case 'preview':
                $result = 'Visualizar';
                break;
            case 'Search':
                $result = 'Pesquisar';
                break;
        }

        return $result;
    }

    private function pre_configure_widget_breadcrumb_actions() {
        $names = array(
            'Guia do Usuário' => array(
                'link' => NULL,
                'icon' => NULL
            ),
            'Listar FAQs' => array(
                'link' => NULL,
                'icon' => NULL
            ),
            $this->_capture_name_widget_breadrumb_action() . ' FAQ ' => array(
                'link' => NULL,
                'icon' => NULL
            ),
        );
        return $names;
    }

    private function pre_configure_widget_breadcrumb() {
        $names = array(
            'Guia do Usuário' => array(
                'link' => NULL,
                'icon' => NULL
            ),
            'Listar FAQs' => array(
                'link' => NULL,
                'icon' => NULL
            )
        );
        return $names;
    }

    private function _load_breadcrumbs() {
        return array(
            $this->pre_configure_widget_breadcrumb(),
            $this->pre_configure_widget_breadcrumb_actions()
        );
    }

    public function __construct($action) {
        if ($action == 'loaded') {
            parent::__construct($this->_load_breadcrumbs());
        }
    }

}
