<?php

require '../config/config.php';
require '../libs/Session.php';
require '../libs/Func.php';
require '../libs/Request.php';
require '../../vendor/offboard/Class-Query/autoload.php';
header('Content-type: application/json');

/**
 * Description of check_task
 *
 * @author offboard
 */
class check_task {

    /**
     * Magic metthod
     * 
     * @access public
     * @return json
     */
    public function __construct() {
        if (Func::isAjaxRequest()) {
            Session::init();
            $id = Request::post('id');
            if (isset($id)) {
                return print $this->update($id);
            }
            return print Func::badRequest();
        }
        return print Func::badRequest();
    }

    /**
     * Update task
     * 
     * @param int $id
     * @return array
     */
    private function update($id) {
        $status = Request::post('status');
        $q = new Query();
        $q
                ->update("task", array(
                    "status" => $status
                ))
                ->where_equal_to(
                        array(
                            "id" => $id
                        )
                )
                ->limit(1)
                ->run();

        if ($q) {
            $word = $status ? "concluída" : "pendente";
            $response = array(
                "error" => false,
                "msg" => "Tarefa marcada como $word com sucesso !"
            );
        } else {
            $response = array(
                "error" => true,
                "msg" => "Error: Não foi possivel marcar a tarefa como $word !"
            );
        }
        return json_encode($response);
    }

}

new check_task();
