<?php

require '../config/config.php';
require '../libs/Session.php';
require '../libs/Func.php';
require '../libs/Request.php';
require '../../vendor/offboard/Class-Query/autoload.php';
#header('Content-type: application/json');

/**
 * Description of tasks
 *
 * @author offboard
 */
class tasks {

    /**
     * Magic metthod
     * 
     * @access public
     * @return json
     */
    public function __construct() {
        if (Func::isAjaxRequest()) {
            Session::init();
            return print $this->loadTask();
        }
        return print Func::badRequest();
    }

    /**
     * Do loop task
     * 
     * @return boolean or string
     */
    private function loadTask() {
       # $url = URL . FILENAME;
        $q = new Query;
        $q
                ->select()
                ->from('task')
                ->where_equal_to(
                        array(
                            'id_user' => Session::get('user_id')
                        )
                )
                ->run();
        $data = $q->get_selected();
        $count = $q->get_selected_count();
        if (!($data && $count > 0)) {
            return false;
        } else {
            $result = '';
            foreach ($data as $value) {
                $check = $value['status'] == true ? ' checked' : null;
                $class = $value['status'] == true ? ' line-through' : null;
                $this->dataTask[] = $value;
                $result .= <<<EOF
<form action="#" id="ftask{$value['id']}" method="post">
    <li class="clearfix">
        <span class="drag-marker"><i></i></span>
        <div class="todo-check pull-left">
            <input name="taskcheck" class="task_check" value="{$value["id"]}" type="checkbox"{$check}  id="todo-check{$value['id']}"/>
            <label for="todo-check{$value['id']}"></label>
        </div>
        <p class="todo-title{$class}">
            {$value['title']}
        </p>
        <div class="todo-actionlist pull-right clearfix">
            <button type="button" value="{$value["id"]}" class="todo-edit btn-xs btn-default task_edit"><i class="ico-pencil"></i></button>
            <button type="button" value="{$value["id"]}" class="todo-remove btn-xs btn-danger task_remove"><i class="ico-close"></i></button>
        </div>
    </li>
</form>
EOF;
            }
            return $result;
        }
    }

}

new tasks();
