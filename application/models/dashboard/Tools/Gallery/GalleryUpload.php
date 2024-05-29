<?php

include("../../../../config/config.php");
include("../../../../libs/Session.php");
include("../../../../../vendor/offboard/php-upload/lib/Upload.php");

$upload = new Upload('upl');
$upload
        ->file_name(true)
        ->upload_to('../../../../../public/Gallery/' . $_POST['id_user'] . "/")
        ->run();
if (!$upload->was_uploaded) {
    die('error : ' . $upload->error);
} else {

    try {
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        $mysqli->set_charset('utf8');
    } catch (Exception $e) {
        exit("Error : " . $e->message);
    }

    $res = $upload->file_width . 'x' . $upload->file_height;

    $sql = "INSERT INTO `gallery_pic` "
            . "(`id_cat`, `id_user`, `pic`, `type`, `Resolution`, `size`) "
            . "VALUES "
            . "("
            . "'" . $_POST['cat'] . "',"
            . "'" . $_POST['id_user'] . "',"
            . "'$upload->final_file_name',"
            . "'$upload->file_src_name_ext',"
            . "'$res',"
            . "'$upload->file_src_size'" .
            ")";
    $query = mysqli_query($mysqli, $sql);
}
