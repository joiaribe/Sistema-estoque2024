<?php

require '../config/config.php';
require '../libs/Session.php';
require '../libs/Func.php';
require '../libs/Request.php';
require '../../vendor/offboard/Class-Query/autoload.php';
header('Content-type: application/json');

/**
 * Description of update_task
 *
 * @author offboard
 */
class update_task {

    /**
     * Magic metthod
     * 
     * @access public
     * @return json
     */
    public function __construct() {
        if (Func::isAjaxRequest()) {
            Session::init();
            $action = Request::post('action');
            $id = Request::post('id');
            if (isset($action) && $action == 'form') {
                return print $this->getTitle($id);
            } else {
                return print $this->update($id);
            }
        }
        return print Func::badRequest();
    }

    /**
     * Get title to update
     * 
     * @param int $id
     * @return array
     */
    private function getTitle($id) {
        $q = new Query();
        $q
                ->select()
                ->from("task")
                ->where_equal_to(
                        array(
                            "id" => $id
                        )
                )
                ->limit(1)
                ->run();
        $data = $q->get_selected();
        if ($q && $q->get_selected_count() > 0) {
            $response = array(
                "error" => false,
                "title" => $data["title"]
            );
        } else {
            $response = array(
                "error" => true,
                "title" => ''
            );
        }
        return json_encode($response);
    }

    /**
     * Update task
     * 
     * @param int $id
     * @return array
     */
    private function update($id) {
        $q = new Query();
        $q
                ->update("task", array(
                    "title" => Request::post("message")
                ))
                ->where_equal_to(
                        array(
                            "id" => $id
                        )
                )
                ->limit(1)
                ->run();

        if ($q) {
            $response = array(
                "error" => false,
                "msg" => "Tarefa alterada com sucesso !"
            );
        } else {
            $response = array(
                "error" => true,
                "msg" => 'Error: Não foi possivel alterar a tarefa !'
            );
        }
        return json_encode($response);
    }

}

new update_task();
