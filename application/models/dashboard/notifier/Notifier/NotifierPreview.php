<?php

namespace notifier\Notifier;

use Query as Query;
use Developer\Tools\Url as Url;
use Dashboard\Call_JS as Call_JS;

/**
 * Classe para visualização
 */
class NotifierPreview extends NotifierHTML {

    /**
     * verifica qual query vai chamar
     * @access private
     * @param Unknow $param nome da coluna na DB
     * @return Object
     */
    private function verify_query() {
        $id = Url::getURL($this->URL_ACTION + 1);
        $q = new Query;
        $q
                ->select()
                ->from($this->table)
                ->where_equal_to(
                        array(
                            'id' => $id,
                            'id_to' => \Session::get('user_id')
                        )
                )
                ->limit(1)
                ->run();
        $total = $q->get_selected_count();
        if (!($total > 0)) {
            Call_JS::alerta('Ocorreu um erro inesperado, talvez essa notificação não exista');
            Call_JS::retornar(URL . 'dashboard/Notifier/notifier');
            die('Ocorreu um erro');
        }
        return $q->get_selected();
    }

    /**
     * Mark notifier with read
     */
    private function marK_lida() {
        $where = array(
            'id' => Url::getURL(4),
            'id_to' => \Session::get('user_id')
        );
        $count = \Func::_contarReg($this->table, $where);
        if (!($count > 0)) {
            Call_JS::alerta('Ocorreu um erro inesperado, talvez essa notificação não exista');
            Call_JS::retornar(URL . 'dashboard/Notifier/notifier');
            die('Ocorreu um erro');
        }
        $q = new Query;
        $q
                ->update('notifier')
                ->set(array(
                    'lida' => 1
                        )
                )
                ->where_equal_to(
                        array(
                            'id' => Url::getURL(4)
                        )
                )
                ->run();
    }

    /**
     * controi e printa conteudo.
     * @access private
     * @return object
     */
    private function _build() {
        return print
                $this->_REQUIRED_PREVIEW_MODE() .
                $this->MAKE_PREVIEW_MODE($this->verify_query());
    }

    /**
     * método mágico controi a tabela
     * @access private
     * @return Main
     */
    public function __construct() {
        $this->marK_lida();
        return $this->_build();
    }

}
