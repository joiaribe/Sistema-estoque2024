<?php

require '../../../application/config/config.php';
require '../../../application/libs/Session.php';


$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$mysqli->set_charset('utf8');
$data = $_POST['DataText'];
$id = $_POST['UserId'];

$query = "INSERT INTO `notes`(`id_user`, `text`) VALUES ('$id','$data')";
$q = mysqli_query($mysqli, $query);

function loop($id) {
    global $mysqli;
    $query = "SELECT * FROM `notes` WHERE `id_user`=$id";
    $q = mysqli_query($mysqli, $query);
    $result = '';
    while ($value = mysqli_fetch_array($q)) {
        $result.= '<li>' . $value['text'] . '<a href="' . URL . 'dashboard/index/action/remove_note/' . $value['id'] . '" class="event-close"> <i class="ico-close2"></i> </a> </li>';
    }
    return $result;
}

if (isset($data)) {
    if ($q) {
        echo loop($id); // 'Nota Adicionada com sucesso !';
    } else {
        echo 'Ocorreu um erro !';
    }
}