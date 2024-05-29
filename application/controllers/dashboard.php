<?php

$autoloadManager = new autoloadManager(NULL, autoloadManager::SCAN_ONCE);
$autoloadManager->addFolder(LIBS_DEV_PATH);
$autoloadManager->addFolder(LIBS_DEV_PATH_DASHBOARD);
$autoloadManager->register();

use Url as Url;

/**
 * Class Dashboard
 * This is a demo controller that simply shows an area that is only visible for the logged in user
 * because of Auth::handleLogin(); in line 19.
 */
class Dashboard extends Controller {

    /**
     * Construct this object by extending the basic Controller class
     */
    function __construct() {

        parent::__construct();
        // this controller should only be visible/usable by logged in users, so we put login-check here
        Auth::handleLogin();
    }

    /**
     * This method controls what happens when you move to /dashboard/index in your app.
     */
    function index() {
        // Auth::handleLogin() makes sure that only logged in users can use this action/method and see that page
        Auth::handleLogin();
        define('FILENAME', 'dashboard/index');
        $this->loadModelDashboard('home');
        $this->loadModelDashboard('Tools/calendar');
        $this->view->render_dashboard('dashboard/index');
    }

    /**
     * This method controls what happens when you move to /dashboard/index in your app.
     */
    function systems() {
        // Auth::handleLogin() makes sure that only logged in users can use this action/method and see that page
        Auth::handleLogin();
        $this->view->render_dashboard('dashboard/systems/academia');
    }

    /**
     * load pages when the page are in submenu
     * @param String $render
     * @param mixed $model When the value is null, not load model
     * @param Boolean $render_header Render dashboard header
     */
    private function _check_sub_menu_item($render, $model = null, $render_header = false, $model_type = 'dashboard') {
        // Auth::handleLogin() makes sure that only logged in users can use this action/method and see that page
        Auth::handleLogin();
        define('FILENAME', $render);
        if (isset($model) || $model !== null) {
            if ($model_type == 'dashboard')
                $this->loadModelDashboard($model);
            else
                $this->loadModel($model);
        }
        $this->view->render_dashboard($render, $render_header);
    }

    /**
     * Category Page System
     */
    function System() {
        switch (Url::getURL(2)) {
            case 'lock_screen':
                $render = 'dashboard/System/lock_screen';
                $model = 'Login';
                $this->_check_sub_menu_item($render, $model, true, 'no_dashboard');
                break;
            case 'Changerlog':
                $render = 'dashboard/System/Changerlog';
                $model = NULL;
                $this->_check_sub_menu_item($render, $model, false, 'no_dashboard');
                break;
            default:
                break;
        }
    }

    /**
     * Category Page Manager
     */
    function Manager() {
        switch (Url::getURL(2)) {
            case 'fonts':
                $render = 'dashboard/Manager/fonts';
                $model = 'Manager/Fonts';
                break;
            case 'clients':
                $render = 'dashboard/Manager/clients';
                $model = 'Manager/Clients';
                break;
            case 'fornecedores':
                $render = 'dashboard/Manager/fornecedores';
                $model = 'Manager/Fornecedor';
                break;
            case 'estoque':
                $render = 'dashboard/Manager/estoque';
                $model = 'Manager/Estoque';
                break;
            case 'servicos':
                $render = 'dashboard/Manager/servicos';
                $model = 'Manager/Servico';
                break;
            case 'agenda':
                $render = 'dashboard/Manager/agenda';
                $model = 'Manager/agenda';
                break;
            case 'marker':
                $render = 'dashboard/Manager/marker';
                $model = 'Manager/Marker';
                break;
            case 'users':
                $render = 'dashboard/Manager/users';
                $model = 'Manager/Users';
                break;
            case 'employee':
                $render = 'dashboard/Manager/employee';
                $model = 'Manager/Employee';
                break;
            default:
                break;
        }
        $this->_check_sub_menu_item($render, $model);
    }

    /**
     * Category Page Overhead costs
     */
    function OverheadCosts() {
        switch (Url::getURL(2)) {
            case 'income':
                $render = 'dashboard/OverheadCosts/income';
                $model = 'OverheadCosts/Income';
                break;
            case 'expense':
                $render = 'dashboard/OverheadCosts/expense';
                $model = 'OverheadCosts/Expense';
                break;

            default:
                break;
        }
        $this->_check_sub_menu_item($render, $model);
    }

    /**
     * Category Page Manager
     */
    function Mov() {
        switch (Url::getURL(2)) {
            case 'products':
                $render = 'dashboard/Mov/products';
                $model = 'Mov/Products';
                break;
            case 'services':
                $render = 'dashboard/Mov/services';
                $model = 'Mov/Service';
                break;
            case 'income':
                $render = 'dashboard/Mov/income';
                $model = 'Mov/Income';
                break;
            case 'expense':
                $render = 'dashboard/Mov/expense';
                $model = 'Mov/Expense';
                break;
            case 'Checkout':
                $render = 'dashboard/Mov/Checkout';
                $model = 'Mov/Checkout';
                break;
            case 'CheckoutServices':
                $render = 'dashboard/Mov/CheckoutServices';
                $model = 'Mov/CheckoutServices';
                break;
            default:
                break;
        }
        $this->_check_sub_menu_item($render, $model);
    }

    /**
     * Category Page Guide
     */
    function Guide() {
        switch (Url::getURL(2)) {
            case 'doubts':
                $render = 'dashboard/Guide/doubts';
                $model = 'guide/Doubts';
                break;
            default:
                break;
        }
        $this->_check_sub_menu_item($render, $model);
    }

    /**
     * Category Page Tools
     */
    function Tools() {
        switch (Url::getURL(2)) {
            case 'calendar':
                $render = 'dashboard/Tools/calendar';
                $model = 'Tools/calendar';
                break;
            case 'chart_bandwidth':
                $render = 'dashboard/Tools/chart_bandwidth';
                $model = null;
                break;
            case 'gallery':
                $render = 'dashboard/Tools/gallery';
                $model = 'Tools/Gallery';
                break;
            case 'receipt':
                $render = 'dashboard/Tools/receipt';
                $model = 'Tools/Receipt';
                break;

            default:
                break;
        }
        $this->_check_sub_menu_item($render, $model);
    }

    /**
     * Category Page Reports
     */
    function Reports() {
        switch (Url::getURL(2)) {
            case 'commission':
                $render = 'dashboard/Reports/commission';
                $model = 'Reports/Comission';
                break;
            case 'cash_flow':
                $render = 'dashboard/Reports/cash_flow';
                $model = 'Reports/CashFlow';
                break;
            case 'receipt':
                $render = 'dashboard/Reports/receipt';
                $model = 'Reports/Receipt';
                break;

            default:
                break;
        }
        $this->_check_sub_menu_item($render, $model);
    }

    /**
     * Category Page Charts
     */
    function Charts() {
        switch (Url::getURL(2)) {
            case 'comissions':
                $render = 'dashboard/Charts/comissions';
                $model = 'chart/Comission';
                break;
            case 'finance':
                $render = 'dashboard/Charts/finance';
                $model = 'chart/Finance';
                break;
            default:
                break;
        }
        $this->_check_sub_menu_item($render, $model);
    }

    /**
     * Category Page Settings
     */
    function Settings() {
        switch (Url::getURL(2)) {
            case 'Menu':
                $render = 'dashboard/Settings/Menu';
                $model = 'Settings/MenuSettings';
                break;
            case 'global':
                $render = 'dashboard/Settings/global';
                $model = 'Settings/GlobalSettings';
                break;
            case 'auth':
                $render = 'dashboard/Settings/auth';
                $model = 'Settings/auth';
                break;
            case 'access':
                $render = 'dashboard/Settings/access';
                $model = 'Settings/GlobalAccess';
                break;
            case 'perfil':
                $render = 'dashboard/Settings/perfil';
                $model = 'Settings/PerfilSettings';
                break;
            case 'receipts':
                $render = 'dashboard/Settings/receipts';
                $model = 'Settings/ReceiptsSettings';
                break;
            default:
                break;
        }
        $this->_check_sub_menu_item($render, $model);
    }

    /**
     * Category Page Notifier
     */
    function Notifier() {
        switch (Url::getURL(2)) {
            case 'notifier':
                $render = 'dashboard/Notifier/notifier';
                $model = 'notifier/Notifier';
                break;
            case 'inbox':
                $render = 'dashboard/Notifier/inbox';
                $model = 'notifier/Inbox';
                break;
            default:
                break;
        }
        $this->_check_sub_menu_item($render, $model);
    }

    /**
     * The login action, when you do login/login
     */
    function login() {
        // run the login() method in the login-model, put the result in $login_successful (true or false)
        $login_model = $this->loadModel('Login');
        // perform the login method, put result (true or false) into $login_successful
        $login_successful = $login_model->login();

        // check login status
        if ($login_successful) {
            // if YES, then move user to dashboard/index (btw this is a browser-redirection, not a rendered view!)
            header('location: ' . URL . 'dashboard/index');
        } else {
            // if NO, then move user to login/index (login form) again
            header('location: ' . URL . 'dashboard/System/lock_screen');
        }
    }

}
