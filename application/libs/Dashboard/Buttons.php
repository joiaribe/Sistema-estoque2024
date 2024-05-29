<?php

namespace Dashboard;

class Buttons {

    public static function button_ver($location) {

        return '<a href="' . URL . $location . '" title="Visualizar Registro" class="btn-action glyphicons eye_open btn-default"><i></i></a>';
    }

    public static function button_alt($location) {

        return '<a href="' . URL . $location . '" title="Alterar Registro" class="btn-action glyphicons pencil btn-success"><i></i></a>';
    }

    public static function button_delete($id) {
        return '<a href="' . Call_JS::confirm_delete(URL . $id) . '" title="Deletar Registro" class="btn-action glyphicons remove_2 btn-danger"><i></i></a>';
    }

    public static function required_field($msg = 'Obrigatório') {
        return '<span title="' . $msg . '" id="field-required">*</span>';
    }

}
