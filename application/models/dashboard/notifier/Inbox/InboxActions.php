<?php

namespace notifier\Inbox;

use Dashboard\Call_JS as Call_JS;
use Developer\Tools\Url as Url;
use Query as Query;

/**
 * Ações
 */
class InboxActions extends InboxHTML {

    /**
     * checks will delete multiple checkbox by
     * @access private
     * @return void
     */
    protected function verify_broadcast_star() {
        $param = Url::getURL($this->URL_ACTION);
        if (isset($param) and $param == 'mark_all_star') {
            if (isset($_POST['delete']) or isset($_POST['checkbox'])) {
                //store the array of checkbox values
                $allCheckBoxId = $_POST['checkbox']; //filter_input(INPUT_POST, 'checkbox');
                $this->run_query_star($allCheckBoxId);
            } else {
                Call_JS::alerta('Selecione pelo menos um ' . $this->msg['singular']);
                Call_JS::retornar(URL . 'dashboard/Notifier/' . $this->page);
            }
        }
    }

    /**
     * monta query de delete
     * @access private
     * @return void
     */
    private function run_query_star($ids) {
        $url = Url::getURL($this->URL_ACTION + 1);
        $v = $url == true ? true : false;
        $q = new Query();
        $q
                ->update($this->table)
                ->set(
                        array(
                            'star' => $v
                        )
                )
                ->where_in(
                        array(
                            'id' => $ids
                        )
                )
                ->run();
        if ($q) {
            $a = count(filter_input(INPUT_POST, 'checkbox'));
            if ($v == true) {
                $m = ($a == 1) ? ' foi marcada com favorito' : ' forãm marcadas com favoritos';
            } else {
                $m = ($a == 1) ? ' foi marcadas como não favorito ' : ' forãm marcadas como não favoritos';
            }
            $mensagem = ($a == 1) ? $this->msg['singular'] . $m . ' ' : $a . $this->msg['plural'] . $m . ' com sucesso !';
            Call_JS::alerta($mensagem);
            Call_JS::retornar(URL . 'dashboard/Notifier/' . $this->page);
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
                Call_JS::retornar(URL . 'dashboard/Notifier/' . $this->page);
            }
        }
    }

    /**
     * monta query de delete
     * @access private
     * @return void
     */
    private function run_query($ids) {
        $url = Url::getURL($this->URL_ACTION + 1);
        $v = $url == true ? true : false;
        $m = $v == false ? ' não' : NULL;
        $q = new Query();
        $q
                ->update($this->table)
                ->set(
                        array(
                            'lida' => $v
                        )
                )
                ->where_in(
                        array(
                            'id' => $ids
                        )
                )
                ->run();
        if ($q) {
            $a = count(filter_input(INPUT_POST, 'checkbox'));
            $mensagem = ($a == 1) ? $this->msg['singular'] . ' marcada como' . $m . ' lida com sucesso !' : $a . $this->msg['plural'] . ' marcadas como ' . $m . ' lidas com sucesso !';
            Call_JS::alerta($mensagem);
            Call_JS::retornar(URL . 'dashboard/Notifier/' . $this->page);
        }
    }

    /**
     * checks will delete multiple checkbox by
     * @access private
     * @return void
     */
    protected function verify_broadcast_delete_permanent() {
        $param = Url::getURL($this->URL_ACTION);
        if (isset($param) and $param == 'delete_all_permanent') {
            if (isset($_POST['delete']) or isset($_POST['checkbox'])) {
                //store the array of checkbox values
                $allCheckBoxId = $_POST['checkbox']; //filter_input(INPUT_POST, 'checkbox');
                $this->run_query_delete_permanent($allCheckBoxId);
            } else {
                Call_JS::alerta('Selecione pelo menos um ' . $this->msg['singular']);
                Call_JS::retornar(URL . 'dashboard/Notifier/' . $this->page);
            }
        }
    }

    /**
     * monta query de delete
     * @access private
     * @return void
     */
    private function run_query_delete_permanent($ids) {
        $q = new Query();
        $q
                ->delete($this->table)
                ->where_in(
                        array(
                            'id' => $ids
                        )
                )
                ->run();
        if ($q) {
            $a = count(filter_input(INPUT_POST, 'checkbox'));
            $mensagem = ($a == 1) ? $this->msg['singular'] . ' removida permanentemente com sucesso !' : $a . $this->msg['plural'] . ' foram removidas permantentemente com sucesso !';
            Call_JS::alerta($mensagem);
            Call_JS::retornar(URL . 'dashboard/Notifier/' . $this->page);
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
                $this->run_query_delete($allCheckBoxId);
            } else {
                Call_JS::alerta('Selecione pelo menos um ' . $this->msg['singular']);
                Call_JS::retornar(URL . 'dashboard/Notifier/' . $this->page);
            }
        }
    }

    /**
     * monta query de delete
     * @access private
     * @return void
     */
    private function run_query_delete($ids) {
        $url = Url::getURL($this->URL_ACTION + 1);
        $v = $url == true ? true : false;
        $q = new Query();
        $q
                ->update($this->table)
                ->set(
                        array(
                            'trash' => $v
                        )
                )
                ->where_in(
                        array(
                            'id' => $ids
                        )
                )
                ->run();
        if ($q) {
            $a = count(filter_input(INPUT_POST, 'checkbox'));
            $mensagem = ($a == 1) ? $this->msg['singular'] . ' foi movido para lixeira com sucesso !' : $a . $this->msg['plural'] . ' foram movidos para lixeira com sucesso !';
            Call_JS::alerta($mensagem);
            Call_JS::retornar(URL . 'dashboard/Notifier/' . $this->page);
        }
    }

    /**
     * checks will delete multiple checkbox by
     * @access private
     * @return void
     */
    protected function verify_broadcast_spam() {
        $param = Url::getURL($this->URL_ACTION);
        if (isset($param) and $param == 'spam_all') {
            if (isset($_POST['delete']) or isset($_POST['checkbox'])) {
                //store the array of checkbox values
                $allCheckBoxId = $_POST['checkbox']; //filter_input(INPUT_POST, 'checkbox');
                $this->run_query_spam($allCheckBoxId);
            } else {
                Call_JS::alerta('Selecione pelo menos um ' . $this->msg['singular']);
                Call_JS::retornar(URL . 'dashboard/Notifier/' . $this->page);
            }
        }
    }

    /**
     * monta query de delete
     * @access private
     * @return void
     */
    private function run_query_spam($ids) {
        $url = Url::getURL($this->URL_ACTION + 1);
        $v = $url == true ? true : false;
        $q = new Query();
        $q
                ->update($this->table)
                ->set(
                        array(
                            'spam' => $v
                        )
                )
                ->where_in(
                        array(
                            'id' => $ids
                        )
                )
                ->run();
        if ($q) {
            $a = count(filter_input(INPUT_POST, 'checkbox'));
            $mensagem = ($a == 1) ? $this->msg['singular'] . ' marcada como spam com sucesso !' : $a . $this->msg['plural'] . ' foram marcadas como spam com sucesso !';
            Call_JS::alerta($mensagem);
            Call_JS::retornar(URL . 'dashboard/Notifier/' . $this->page);
        }
    }

    /**
     * checks will delete multiple checkbox by
     * @access private
     * @return void
     */
    protected function verify_broadcast_important() {
        $param = Url::getURL($this->URL_ACTION);
        if (isset($param) and $param == 'important_all') {
            if (isset($_POST['delete']) or isset($_POST['checkbox'])) {
                //store the array of checkbox values
                $allCheckBoxId = $_POST['checkbox']; //filter_input(INPUT_POST, 'checkbox');
                $this->run_query_important($allCheckBoxId);
            } else {
                Call_JS::alerta('Selecione pelo menos um ' . $this->msg['singular']);
                Call_JS::retornar(URL . 'dashboard/Notifier/' . $this->page);
            }
        }
    }

    /**
     * monta query de delete
     * @access private
     * @return void
     */
    private function run_query_important($ids) {
        $url = Url::getURL($this->URL_ACTION + 1);
        $v = $url == true ? true : false;
        $q = new Query();
        $q
                ->update($this->table)
                ->set(
                        array(
                            'important' => $v
                        )
                )
                ->where_in(
                        array(
                            'id' => $ids
                        )
                )
                ->run();
        if ($q) {
            $a = count(filter_input(INPUT_POST, 'checkbox'));
            $mensagem = ($a == 1) ? $this->msg['singular'] . ' marcada como spam com sucesso !' : $a . $this->msg['plural'] . ' foram marcadas como spam com sucesso !';
            Call_JS::alerta($mensagem);
            Call_JS::retornar(URL . 'dashboard/Notifier/' . $this->page);
        }
    }

    /**
     * monta query de delete
     * @access private
     * @return void
     */
    private function QueryReply() {
        $param = Url::getURL($this->URL_ACTION);
        if (isset($param) and $param == 'reply') {
            $msg = filter_input(INPUT_POST, 'message');
            $id = Url::getURL($this->URL_ACTION + 1);
            if (!isset($msg) || $msg == '') {
                Call_JS::alerta(' O campo mensagem não pode está vázio !');
                Call_JS::retornar(URL . 'dashboard/Notifier/' . $this->page . '/preview/' . $id);
                exit();
            }

            $q = new Query();
            $q
                    ->insert_into('mensagem_reply', array(
                        'id_user' => \Session::get('user_id'),
                        'id_message' => $id,
                        'text' => $msg
                            )
                    )
                    ->run();
            if ($q) {
                Call_JS::alerta(' Mensagem enviada com sucesso !');
                Call_JS::retornar(URL . 'dashboard/Notifier/' . $this->page . '/preview/' . $id);
                exit();
            } else {
                Call_JS::alerta(' Não foi possivel enviar uma mensagem !');
                Call_JS::retornar(URL . 'dashboard/Notifier/' . $this->page . '/preview/' . $id);
                exit();
            }
        }
    }

    public function __construct() {
        // check mark read or unread
        $this->verify_broadcast_mark();
        // check mark star or unstar
        $this->verify_broadcast_star();
        // check delete
        $this->verify_broadcast_delete();
        // check mark spam
        $this->verify_broadcast_spam();
        // check mark important
        $this->verify_broadcast_important();
        // check delete permantent
        $this->verify_broadcast_delete_permanent();
        // check reply message
        $this->QueryReply();
    }

}
