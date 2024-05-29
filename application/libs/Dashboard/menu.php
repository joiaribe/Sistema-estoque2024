<?php

namespace Dashboard;

use Query as Query;

/**
 *
 * Classe que monta o menu proteje a página e ainda por cima ativa o css da página quase automaticamente,
 * Lembre-se Tem que usar em todas as páginas se não vai da merda !
 *
 * @author Bruno Ribeiro <bruno.espertinho@gmail.com>
 * @version 2.4.1
 * @copyright Divulgue Mania © 2014
 * @access public
 * @package menu
 * @example
 *
 * */
class menu extends menu_html {

    /**
     * Method Magic
     * @param String $filename
     */
    final public function __construct($filename) {
        $this->filename = $filename;
        $this->_loop_menu();
        $this->_load();
    }

    /**
     * load objects
     * @return mixed
     */
    private final function _load() {
        if ($this->_loop_menu() !== false) {
            return print $this->Build();
        }
        #die('loading menu error !');
    }

    /**
     * loop menu
     * @return boolean
     */
    private function _loop_menu() {
        $q = new Query;
        $q
                ->select()
                ->from('menu')
                ->where_equal_to(
                        array(
                            'status' => true
                        )
                )
                ->order_by('position asc')
                ->run();
        if (!($q->get_selected_count() > 0)) {
            return false;
        } else {
            $this->data = $q->get_selected();
            return true;
        }
    }

}

class menu_html {

    /**
     * Dados da consulta menu
     * @var Array
     */
    protected $data = array();

    /**
     * current url
     * @var String 
     */
    var $filename;

    /**
     * Pega todos os links
     * @param array $data
     * @return Array
     */
    private function check_links(array $data) {
        if ($data['sub'] == 1) {
            $q = new Query;
            $q
                    ->select()
                    ->from('menu_sub')
                    ->where_equal_to(
                            array(
                                'id_menu' => $data['id'],
                                'status' => true
                            )
                    )
                    ->run();
            $arr = array();
            if ($q->get_selected_count() > 0) {
                foreach ($q->get_selected() as $value) {
                    $arr[] = $value['link'];
                }
            }
            return $arr;
        }
    }

    /**
     * Verifica todas as páginas do submenu
     * @param array $data
     * @return string
     */
    private function check_active_submenu(array $data) {
        if (in_array($this->filename, $this->check_links($data))) {
            return ' class="active"';
        }
    }

    /**
     * Verifica página atual
     * @param String $url
     * @return string
     */
    private function check_active_menu($url) {
        if ($this->filename == $url) {
            return ' class="active"';
        }
    }

    /**
     * loop submenu
     * @param Integer $id
     * @return string|boolean
     */
    private function _loop_submenu($id, $title_sub) {
        $q = new Query;
        $q
                ->select()
                ->from('menu_sub')
                ->where_equal_to(
                        array(
                            'id_menu' => $id,
                            'status' => true
                        )
                )
                ->order_by('name asc')
                ->run();
        if (!($q->get_selected_count() > 0)) {
            return false;
        }
        $result = '';
        foreach ($q->get_selected() as $value) {
            if ($this->CheckVisible($value) == true) {
                $result.= '<li' . $this->check_active_menu($value['link']) . ' title="' . $title_sub . ' ' . $value['name'] . '"><a href="' . URL . $value['link'] . '">' . $value['name'] . '</a></li>';
            }
        }
        return $result;
    }

    /**
     * tag li submenu
     * @param array $dados
     * @return string
     */
    private function html_li_submenu(array $dados) {
        if ($this->CheckVisible($dados) == true) {
            $result = '<li class="sub-menu" title="' . $dados['name'] . '"><a ' . $this->check_active_submenu($dados) . ' href="' . URL . $dados ['link'] . '"><i class="fa ' . $dados['icone'] . '"></i><span>' . $dados['name'] . '</span></a>'
                    . '<ul class="sub">'
                    . $this->_loop_submenu($dados['id'], $dados['name'])
                    . '</ul></li>';
            return $result;
        }
    }

    /**
     * tag li menu
     * @param array $dados
     * @return String
     */
    private function html_li_menu(array $dados) {
        if ($this->CheckVisible($dados) == true) {
            return '<li title="' . $dados['name'] . '"><a' . $this->check_active_menu($dados['link']) . ' href="' . URL . $dados ['link'] . '"><i class="fa ' . $dados['icone'] . '"></i><span>' . $dados ['name'] . '</span></a></li>';
        }
    }

    /**
     * Verify access of account type
     * @param String $data Access Data Currenty
     * @return boolean
     */
    private function CheckVisible($data) {
        $myAccountType = \GetInfo::_user_cargo(NULL, FALSE);
        $total = \Func::_contarReg('menu_sub_access', array('id_sub_menu' => $data['id']));
        // is admin ?
        if ($myAccountType == 0) {
            return true;
        }
        // check access
        if ($total > 0) {
            return true;
        }
    }

    /**
     * html menu
     * @return Object
     */
    private function html_menu() {
        if (is_array($this->data)) {
            $result = '';
            $ids = array();
            foreach ($this->data as $dados) {
                $count = \Func::_contarReg("menu_sub", array("id_menu" => $dados["id"]));
                if ($dados['sub'] && $count > 0) {
                    $result.= $this->html_li_submenu($dados);
                } else {
                    $result.= $this->html_li_menu($dados);
                }
            }
            $this->data_id_menu = $ids;
            return $result;
        }
    }

    /**
     * Generate HTML menu
     * @param array $data Query
     * @param Object $element Description
     * @return String Menu HTMl
     */
    protected function Build() {
        return <<<EOFPAGE
<aside>
    <div id="sidebar" class="nav-collapse">
        <!-- sidebar menu start-->
        <div class="leftside-navigation">
            <ul class="sidebar-menu" id="nav-accordion">{$this->html_menu()}</ul>
        </div><!-- sidebar menu end-->
    </div>
</aside><!--sidebar end-->
EOFPAGE;
    }

}
