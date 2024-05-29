<?php
// autoload function call all classes from src folder
$autoloadManager = new autoloadManager(null, autoloadManager::SCAN_ONCE);
$autoloadManager->addFolder(__DIR__ . '/Notifier/');
$autoloadManager->register();

use Developer\Tools\Url as Url;
use notifier\Notifier\NotifierAutoload as NotifierAutoload;

class NotifierModel extends NotifierAutoload{
  
    private function _capture_name_widget_breadrumb_action() {
        $param = Url::getURL($this->URL_ACTION);
        $result = false;
        switch ($param) {
            case 'preview':
                $result = 'Visualizar';
                break;
            case 'add':
                $result = 'Adicionar';
                break;
            case 'alt':
                $result = 'Alterar';
                break;
        }

        return $result;
    }

    private function pre_configure_widget_breadcrumb_actions() {
        $names = array(
            'Gerenciamento' => array(
                'link' => NULL,
                'icon' => NULL
            ),
            'Gerenciar Notificações' => array(
                'link' => 'Manager/' . $this->page,
                'icon' => NULL
            ),
            $this->_capture_name_widget_breadrumb_action() . ' Notificação ' => array(
                'link' => NULL,
                'icon' => NULL
            ),
        );
        return $names;
    }

    private function pre_configure_widget_breadcrumb() {
        $names = array(
            'Gerenciamento' => array(
                'link' => NULL,
                'icon' => NULL
            ),
            'Gerenciar Notificações' => array(
                'link' => 'WebSite/about/Widget',
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
