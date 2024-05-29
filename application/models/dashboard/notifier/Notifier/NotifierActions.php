<?php

namespace notifier\Notifier;

use Developer\Tools\Url as Url;
use Query as Query;
use Dashboard\Call_JS as Call_JS;

/**
 * Ações
 */
class NotifierActions extends notifierHTML {

    /**
     * verifica se mudou o estado da mensagem para lida ou não lida
     * @access protected
     * @return void
     */
    protected function verify_mark() {
        $param = Url::getURL($this->URL_ACTION);
        $result = (Url::getURL($this->URL_ACTION + 2) == 'unread') ? 0 : 1;
        $msg = ($result == 1) ? ' marcada como lida' : ' marcada como não lida';
        if (isset($param) AND $param == 'mark') {
            $q = new Query;
            $q
                    ->update($this->table)
                    ->set(
                            array(
                                'lida' => $result
                            )
                    )
                    ->where_equal_to(
                            array(
                                'id' => Url::getURL($this->URL_ACTION + 1)
                            )
                    )
                    ->limit(1)
                    ->run();
            if ($q) {
                Call_JS::alerta($this->msg['singular'] . $msg);
                Call_JS::retornar(URL . 'dashboard/Notifier/notifier');
            }
        }
    }

    /**
     * checks will delete multiple checkbox by
     * @access private
     * @return void
     */
    protected function verify_broadcast_mark() {
        $param = Url::getURL($this->URL_ACTION);
        if (isset($param) and $param == 'mark_all') {
            if (isset($_POST['delete']) or isset($_POST['checkbox'])) {
                //store the array of checkbox values
                $allCheckBoxId = $_POST['checkbox']; //filter_input(INPUT_POST, 'checkbox');
                $this->run_query($allCheckBoxId);
            } else {
                Call_JS::alerta('Selecione pelo menos um ' . $this->msg['singular']);
                Call_JS::retornar(URL . 'dashboard/Notifier/notifier');
            }
        }
    }

    /**
     * monta query de delete
     * @access private
     * @return void
     */
    private function run_query($ids) {

        $q = new Query();
        $q
                ->update($this->table)
                ->set(
                        array(
                            'lida' => Url::getURL($this->URL_ACTION + 1)
                        )
                )
                ->where_in(
                        array('id' => $ids)
                )
                ->run();
        if ($q) {
            $a = count(filter_input(INPUT_POST, 'checkbox'));
            $mensagem = ($a == 1) ? $this->msg['singular'] . ' marcada como lida com sucesso !' : $a . $this->msg['plural'] . ' marcadas como lidas com sucesso !';
            Call_JS::alerta($mensagem);
            Call_JS::retornar(URL . 'dashboard/Notifier/notifier');
        }
    }

    public function __construct() {
        $this->verify_broadcast_mark();
        $this->verify_mark();
    }

}
