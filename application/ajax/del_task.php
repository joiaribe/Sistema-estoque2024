<?php

require '../config/config.php';
require '../libs/Session.php';
require '../libs/Func.php';
require '../libs/Request.php';
require '../../vendor/offboard/Class-Query/autoload.php';
header('Content-type: application/json');

/**
 * Description of del_task
 *
 * @author offboard
 */
class del_task {

    /**
     * Magic metthod
     * 
     * @access public
     * @return json
     */
    public function __construct() {
        if (Func::isAjaxRequest()) {
            Session::init();
            return print $this->delete();
        }
        return print Func::badRequest();
    }

    /**
     * Delete task
     * 
     * @return array
     */
    private function delete() {
        $q = new Query();
        $q
                ->delete("task")
                ->where_equal(
                        array(
                            "id_user" => Session::get('user_id'),
                            "id" => Request::get('id')
                        )
                )
                ->run();
        if ($q) {
            $response = array(
                "error" => false,
                "msg" => "Tarefa deletada com sucesso"
            );
        } else {
            $response = array(
                "error" => true,
                "msg" => "Error : Não foi possível deletar a tarefa"
            );
        }
        return json_encode($response);
    }

}

new del_task();
