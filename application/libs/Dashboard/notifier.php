<?php

namespace Dashboard;

use Query as Query;

/**
 *
 * Classe para as notificações
 *
 * @author Bruno Ribeiro
 * @version 1.1
 * @copyright AL Developer © 2013
 * @access public
 * @package Production
 * @subpackage submit
 * @example 
 *  new notifier('load_notifier');
 *
 */
class notifier {

    /**
     * Elemento que chama a ação
     * @var String
     */
    public $element = NULL;

    /**
     * Magic Method
     * @param type $elemenet
     * @return Void
     */
    public function __construct($elemenet) {
        $this->element = $elemenet;
        $this->_auto();
    }

    /**
     * Auto call function
     * @return Object
     */
    private function _auto() {
        switch ($this->element) {
            case 'load_inbox':
                $return = $this->_build_inbox();
                break;
            case 'load_notifier':
                $return = $this->_build_notifier();
                break;
            default :
                $return = false;
                break;
        }
        if ($return !== false) {
            return print $return;
        }
        return '';
    }

    /**
     * verifica se existe usuário remetente
     * @access private
     * @param Integer $dados dados obtidos 
     * @return string
     */
    private function verify_dados($dados, $return = NULL) {
        if ($return !== 'no_return') {
            return !$dados ? '<font color="red">Sistema</font>' : \Developer\Tools\GetInfo::_user_cargo($dados);
        }
    }

    /**
     * conta e verifica se existe mais de 1, caso não exista não será mostrando nada
     * @access private
     * @param Integer $total total
     * @return string
     */
    private function verify_notifier_count($total) {

        return ($total == NULL xor 0) ? false : '<span class="badge bg-warning">' . $total . '</span>';
    }

    /**
     * conta e verifica se existe mais de 1, caso não exista não será mostrando nada
     * @access private
     * @param Integer $total total
     * @return string
     */
    private function verify_inbox_count($total) {

        return ($total == NULL xor 0) ? FALSE : '<span class="badge bg-important">' . $total . '</span>';
    }

    /**
     * verifica se a notificação já foi lida e mostra classe css
     * @access private
     * @param Integer $lida valor dinâmico
     * @return string
     */
    private function verify_read($lida) {

        return $lida == 0 ? 'class="notifier_hover"' : false;
    }

    /**
     * faz o loop para inbox
     * @access private
     * @param string $pic foto
     * @param string $name nome
     * @param string $date data em formato unix
     * @param string $id id
     * @param string $text texto
     * @param string $lida lida
     * @return string
     */
    private function loop_inbox($pic, $name, $date, $id, $text, $lida) {
        // $name = $first . ' ' . $last;
        // $pic = isset($pic) ? $pic : 'avatar_small.png';

        return '
                     <li ' . $this->verify_read($lida) . '>
                        <a href="view_message/' . $id . '">
                            <span class="photo"><img alt="' . strip_tags($name) . '" title="' . strip_tags($name) . '" src="' . $pic . '"></span>
                            <span class="subject">
                                <span class="from">' . $name . '</span>
                                <span class="time">' . \makeNiceTime::MakeNew($date) . '</span>
                            </span>
                            <span class="message">
                                ' . \Func::str_truncate($text, 30) . '
                            </span>
                        </a>
                    </li> 
            ';
    }

    /**
     * faz o loop para notificações
     * @access private
     * @param array $param Array da consulta
     * @return string
     */
    private function loop_notifier(array $param) {

        $foto = \Developer\Tools\GetInfo::_foto($param['id_to']);
        $pic = !$foto ? 'avatar_small.png' : $foto;
        return '
                     <li ' . $this->verify_read($param['lida']) . '>
                        <a href="' . URL . 'dashboard/Notifier/notifier/preview/' . $param['id'] . '">
                           
                            <span class="subject">
                                <span class="from">' . \Func::str_truncate($param['title'], 30) . '</span>
                                <span class="time">' . \makeNiceTime::MakeNew($param['data']) . '</span>
                            </span>
                            <span class="message">
                                ' . \Func::str_truncate($param['text'], 30) . '
                            </span>
                        </a>
                    </li> 
            ';
    }

    /**
     * controi notificação
     * @access protected
     * @return HTML
     */
    private function _build_notifier() {
        $result = '                
                <li id="header_notification_bar" class="dropdown">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <i class="fa fa-bell-o"></i>';
        $result .= $this->verify_notifier_count($this->total_notifier());
        $result .= '</a>
                    <ul class="dropdown-menu extended inbox">
                        <li>
                            <p>Notificações</p>
                        </li>';
        $q = new Query;
        $q
                ->select()
                ->from('notifier')
                ->where_equal_to(
                        array(
                            'id_to' => \Session::get('user_id')
                        )
                )
                ->run();
        $users = $q->get_selected();
        foreach ($users as $user) {
            $result .= $this->loop_notifier($user);
        }
        $result .= '<li>
                            <a href="notifier_notifier">Ver Todas</a>
                        </li>
                    </ul>
                </li>
                <!-- notification dropdown end -->
            </ul>';
        return $result;
    }

    /**
     * verifica quantas mensagem tem e mostra mensagem apropriada
     * @access private
     * @return string
     */
    private function verify_message() {
        $total = $this->total_inbox();
        switch (true) {
            case($total == 0) :
                $result = "Você não tem mensagens";
                break;
            case($total == 1) :
                $result = "Você tem 1 mensagem";
                break;
            case($total > 1) :
                $result = "Você tem $total mensagens";
                break;
        }
        return $result;
    }

    /**
     * controi inbox
     * @access protected
     * @return HTML
     */
    private function _build_inbox() {
        $result = '                
                <li id="header_inbox_bar" class="dropdown">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <i class="fa fa-envelope-o"></i>';
        $result .= $this->verify_inbox_count($this->total_inbox());
        $result .= '</a>
                    <ul class="dropdown-menu extended inbox">
                        <li>
                            <p>' . $this->verify_message() . '</p>
                        </li>';
        $q = new Query;
        $q
                ->select()
                ->from('mensagem')
                ->where_equal_to(
                        array(
                            'id_to' => \Session::get('user_id'),
                            'trash' => FALSE
                        )
                )
                ->order_by(
                        array(
                            'lida ASC',
                            'id ASC'
                        )
                )
                ->run();
        $users = $q->get_selected();
        if ($users && $q->get_selected_count() > 0) {
            foreach ($users as $user) {
                $pic = \GetInfo::_foto($user['id_from']);
                $name = $user['id_from'] == NULL ? '<font color="red"><strong>Sistema</strong></font>' : \GetInfo::_name($user['id_from']);
                $result .= $this->loop_inbox($pic, $name, $user['data'], $user['id'], $user['text'], $user['lida']);
            }
        }

        $result .= '    <li>
                            <a href="manager_message">Ver Todas</a>
                        </li>
                    </ul>
                </li>';
        return $result;
    }

    /**
     * total de registros inbox
     * @access protected
     * @return integer
     */
    protected function total_inbox() {
        $q = new Query();
        $q
                ->select()
                ->from('mensagem')
                ->where_equal_to(
                        array(
                            'id_to' => $_SESSION['user_id'],
                            'lida' => FALSE
                        )
                )
                ->run();
        $total = $q->get_selected_count();

        return ($total == NULL or 0) ? 0 : $total;
    }

    /**
     * total de registros notificações
     * @access private
     * @return integer
     */
    private function total_notifier() {
        $q = new Query;
        $q
                ->select()
                ->from('notifier')
                ->where_equal_to(
                        array(
                            'id_to' => $_SESSION['user_id'],
                            'lida' => 0
                        )
                )
                ->run();
        $total = $q->get_selected_count();
        return ($total == NULL or 0) ? 0 : $total;
    }

}
