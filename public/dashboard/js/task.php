<?php

require '../../../application/config/config.php';
require '../../../application/libs/Session.php';


$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$mysqli->set_charset('utf8');

$id = $_POST['UserId'];
$status = isset($_POST['status']) ? true : false;

$query = "UPDATE `task` SET `status`='$status')";
$q = mysqli_query($mysqli, $query);

function loop($id) {
    global $mysqli;
    $query = "SELECT * FROM `task` WHERE `id_user`=$id";
    $q = mysqli_query($mysqli, $query);
    $result = '';
    $url = URL . FILENAME;
    while ($value = mysqli_fetch_array($q)) {
        $check = $value['status'] == true ? ' checked' : null;
        $class = $value['status'] == true ? ' line-through' : null;
        $result .= <<<EOF
                            
                            <li class="clearfix">
                                <span class="drag-marker"><i></i></span>
                                <div class="todo-check pull-left">
                                    <input type="checkbox"{$check} value="None" id="todo-check{$value['id']}"/>
                                    <label for="todo-check{$value['id']}"></label>
                                </div>
                                <p class="todo-title{$class}">
                                    {$value['title']}
                                </p>
                                <div class="todo-actionlist pull-right clearfix">
                                    <a href="{$url}#alt_task{$value['id']}" data-toggle="modal" class="todo-edit"><i class="ico-pencil"></i></a>
                                    <a href="{$url}/action/delete_task/{$value['id']}" class="todo-remove"><i class="ico-close"></i></a>
                                </div>
                            </li>
EOF;
    }
    return $result;
}

if (isset($id)) {
    if ($q) {
        echo loop($id); // 'Nota Adicionada com sucesso !';
    } else {
        echo 'Ocorreu um erro !';
    }
}