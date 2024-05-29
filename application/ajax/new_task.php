<?php

require '../config/config.php';
require '../libs/Session.php';
require '../libs/Func.php';
require '../libs/Request.php';
require '../../vendor/offboard/Class-Query/autoload.php';
header('Content-type: application/json');

/**
 * Description of new_task
 *
 * @author offboard
 */
class new_task {

    /**
     * Magic metthod
     * 
     * @access public
     * @return json
     */
    public function __construct() {
        if (Func::isAjaxRequest()) {
            Session::init();
            return print $this->insert();
        }
        return print Func::badRequest();
    }

    /**
     * Insert new task
     * 
     * @return array
     */
    private function insert() {
        $q = new Query();
        $q
                ->insert("task", array(
                    "id_user" => Session::get('user_id'),
                    "title" => Request::post('message')
                        )
                )
                ->run();
        if ($q) {
            $response = array(
                "error" => false,
                "msg" => "Nova terefa cadastrada com sucesso"
            );
        } else {
            $response = array(
                "error" => true,
                "msg" => "Error : Não foi possível cadastrar nova tarefa"
            );
        }
        return json_encode($response);
    }

}

new new_task();
