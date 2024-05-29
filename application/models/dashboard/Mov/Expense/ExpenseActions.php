<?php

namespace Mov\Expense;

use Dashboard\Call_JS as Call_JS;
use Developer\Tools\Url as Url;
use Query as Query;

/**
 * Ações
 */
class ExpenseActions extends ExpenseHTML {

    /**
     * verifica se vai deletar
     * @access protected
     * @return void
     */
    protected function verify_del() {
        $param = Url::getURL($this->URL_ACTION);
        if (isset($param) AND $param == 'del') {
            $q = new Query();
            $q
                    ->delete($this->table)
                    ->where_equal_to(
                            array(
                                'id' => Url::getURL($this->URL_ACTION + 1)
                            )
                    )
                    ->run();
            if ($q) {
                Call_JS::alerta($this->msg['singular'] . ' excluido com sucesso !');
                Call_JS::retornar(URL . 'dashboard/Mov/' . $this->page);
            }
        }
    }

    /**
     * checks will delete multiple checkbox by
     * @access private
     * @return void
     */
    protected function verify_broadcast_delete() {
        $param = Url::getURL($this->URL_ACTION);
        if (isset($param) and $param == 'delete_all') {
            if (isset($_POST['delete']) or isset($_POST['checkbox'])) {
                //store the array of checkbox values
                $allCheckBoxId = $_POST['checkbox']; //filter_input(INPUT_POST, 'checkbox');
                $this->run_query($allCheckBoxId);
            } else {
                Call_JS::alerta('Selecione pelo menos um ' . $this->msg['singular']);
                Call_JS::retornar(URL . 'dashboard/Mov/' . $this->page);
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
                ->delete($this->table)
                ->where_in(
                        array('id' => $ids)
                )
                ->run();
        if ($q) {
            $total = count($ids);
            $mensagem = ($total == 1) ? '1 ' . $this->msg['singular'] . ' excluido(a) com sucesso !' : $total . ' ' . $this->msg['plural'] . ' excluidos(as) com sucesso !';
            Call_JS::alerta($mensagem);
            Call_JS::retornar(URL . 'dashboard/Mov/' . $this->page);
        }
    }

    public function __construct() {
        $this->verify_del();
        $this->verify_broadcast_delete();
    }

}
