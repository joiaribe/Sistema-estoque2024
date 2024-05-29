<?php

namespace Dashboard;

use Query as Query;
use Developer\Tools\Url as Url;

class breadcrumb {

    /**
     * @access public
     * @todo define configuração do breadcrumbs
     * @category Menu Map
     */
    public $breadcrumbs = false;

    /**
     * Elements
     * @var String
     */
    private $elements;

    /**
     * Current action
     * @var String 
     */
    private $filename;

    /**
     * ùltimo elemento guarda o nome do penultimo
     * @var String
     */
    var $last_name = NULL;

    /**
     * Magic Method
     * @param string $filename
     * @param mixed $method
     */
    public function __construct($filename = NULL, $method = 'auto') {
        $this->filename = $filename;
        if ($method == 'auto') {
            $this->load_auto();
        } else {
            $this->breadcrumbs = $method;
            $this->load_custom();
        }
        $this->HTML();
    }

    /**
     * Breadcrumbs main HTML
     * @return string
     */
    private function HTML() {
        return print '
                <!--main content start-->
                <section id="main-content">
                <section class="wrapper">
                <div class="row">
                    <div class="col-md-12">
                        <!--breadcrumbs start -->
                        <ul class="breadcrumb">
                        ' . $this->elements . '
                        </ul>
                        <!--breadcrumbs end -->
                    </div>
                </div>';
    }

    private function html_item() {
        return '
                        <li><a href="#">Dashboard</a></li>
                        <li class="active">Current page</li>';
    }

    private function _load_auto_sub() {
        $q = new Query;
        $q
                ->select()
                ->from('menu_sub')
                ->where_equal_to(
                        array(
                            'link' => $this->filename,
                            'status' => true
                        )
                )
                ->run();
        $result = '';
        if (!($q->get_selected_count() > 0)) {
            $result = false;
        } else {
            foreach ($q->get_selected() as $dados) {
                $result.= '<li><a href="' . URL . $dados['link'] . '">' . $dados['nome'] . '</a></li>';
                $p = Url::getURL(3);
                if (isset($p)) {
                    $this->last_name = $p;
                } else {
                    $this->last_name = $dados['nome'];
                }
                #$result.= '<li class="active">' . $dados['nome'] . '</li>';
            }
        }
        return $result;
    }

    private function check_url() {
        switch (Url::getURL(4)) {
            case 'preview':
                $result = '<li class="active">Visualizar ' . $this->last_name . '</li>';
                break;
            case 'alter':
                $result = '<li class="active">Alterar ' . $this->last_name . '</li>';
                break;
            case 'add':
                $result = '<li class="active">Adicionar ' . $this->last_name . '</li>';
                break;
            default :
                $result = false;
                break;
        }
        return $result;
    }

    /**
     * Auto load breadcrumbs
     * @return mixed
     */
    private function load_auto() {
        $q = new Query();
        $q
                ->select()
                ->from('menu')
                ->where_equal_to(
                        array(
                            'link' => $this->filename,
                            'status' => true
                        )
                )
                ->run();

        $result = '<li><a href="' . URL . 'dashboard/index"><i class="fa fa-home"></i> Principal</a></li>';
        if (!($q->get_selected_count() > 0)) {
            if ($this->check_url() !== false) {
                $result.= $this->_load_auto_sub()
                        . $this->check_url();
            } else {
                $result.= $this->_load_auto_sub();
            }
        } else {
            foreach ($q->get_selected() as $dados) {
                $result.= '<li><a href="' . URL . '">' . $dados['nome'] . '</a></li>';
                #$result.= '<li class="active">' . $dados['nome'] . '</li>';
            }
        }
        $this->elements = $result;
    }

    /**
     * load custom mode
     * @return Void
     */
    private function load_custom() {
        $result = '<li><a href="' . URL . 'dashboard/index"><i class="fa fa-home"></i> Principal</a></li>';
        $id = 0;
        if (is_array($this->breadcrumbs) && $this->breadcrumbs !== false) {
            foreach ($this->breadcrumbs as $k => $v) {
                $link = ($v['link'] == NULL) ? FILENAME . '#' : $v['link'];
                if (count($this->breadcrumbs) == ++$id) {
                    $result.= '<li class="active">' . $k . '</li>';
                } else {
                    $dash = (preg_match('/dashboard/', $link)) ? false : 'dashboard' . DS;
                    $result.= '<li><a href="' . URL . $dash . $link . '">' . $k . '</a></li>';
                }
            }
            $this->elements = $result;
        } else {
            die('no was possible load breadcrubs in mode custom');
        }
    }

}
