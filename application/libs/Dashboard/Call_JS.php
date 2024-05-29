<?php

namespace Dashboard;

class Call_JS {

    public static function delete_aviso($id) {
        return "javascript:aviso('" . URL . $id . "')";
    }

    public static function alerta($msg) {
        return print "<script type='text/javascript'> alert('" . $msg . "');</script>";
    }

    public static function retornar($pagina, $type = 'form') {
        if ($type == 'form') {
            return print "<script>window.location='" . $pagina . "';</script>";
        } else {
            return print "<script>window.location='../../" . $pagina . "';</script>";
        }
    }

    public static function confirm_delete($page) {
        return "javascript:aviso('" . $page . "')";
    }

}
