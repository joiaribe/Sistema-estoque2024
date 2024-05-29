<?php

/**
 * Class Index
 * The index controller
 */
class Index extends Controller {

    /**
     * Construct this object by extending the basic Controller class
     * @param String $param 
     */
    function __construct() {
        parent::__construct();
        $this->loadModelDashboard('home');
    }

    /**
     * Handles what happens when user moves to URL/index/index, which is the same like URL/index or in this
     * case even URL (without any controller/action) as this is the default controller-action when user gives no input.
     */
    function index() {
        Auth::handleLogin();
        $this->loadModelDashboard('Tools/calendar');
        $this->view->render_dashboard('dashboard/index');
    }

}
