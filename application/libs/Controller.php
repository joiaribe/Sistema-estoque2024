<?php

/**
 * This is the "base controller class". All other "real" controllers extend this class.
 * Whenever a controller is created, we also
 * 1. initialize a session
 * 2. check if the user is not logged in anymore (session timeout) but has a cookie
 * 3. create a database connection (that will be passed to all models that need a database connection)
 * 4. create a view object
 */
class Controller {

    function __construct() {
        Session::init();

        // user has remember-me-cookie ? then try to login with cookie ("remember me" feature)
        if (!isset($_SESSION['user_logged_in']) && isset($_COOKIE['rememberme'])) {
            header('location: ' . URL . 'login/loginWithCookie');
        }

        // create database connection
        try {
            $this->db = new Database();
        } catch (PDOException $e) {
            die(PAGINATION_TEXT_DB_NAME);
        }

        // create a view object (that does nothing, but provides the view render() method)
        $this->view = new View();
    }

    /**
     * loads the model with the given name.
     * @param $name string name of the model
     */
    public function loadModel($name, $strtolower = false) {
        #$is = ($strtolower !== false) ? strtolower($name) : $name;
        $path = MODELS_PATH . strtolower($name) . '_model.php'; // no remove strtolower possible bug
        // check models in models folder
        
        if (file_exists($path)) {
            require $path;
            // The "Model" has a capital letter as this is the second part of the model class name,
            // all models have names like "LoginModel"
            $modelName = $name . 'Model';
            // return the new model object while passing the database connection to the model
            return new $modelName($this->db);
        }
    }

    /**
     * loads the model with the given name.
     * @param $name string name of the model
     */
    public function loadModelDashboard($name, $subPath = false) {
        if ($subPath !== false) {
            $subPath = $subPath . DS;
        }
        $path_dashboard = MODELS_PATH . 'dashboard' . DS . $subPath . $name . '_model.php';
        // check models in models/dashboard folder
        if (!file_exists($path_dashboard)) {
            throw new Exception("The file $path_dashboard is missing in folder.");
        } else {
            require $path_dashboard;
            #$modelName = $name . 'Model';
        }
    }

}
