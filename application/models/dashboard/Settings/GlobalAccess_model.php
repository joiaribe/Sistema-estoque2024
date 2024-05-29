<?php

use Query as Query;
use Dashboard\Call_JS as Call_JS;

/**
 * Main Class
 * @author Bruno Ribeiro <bruno.espertinho@gmail.com>
 * @version 0.2
 * @access public
 * @package GlobalAcessAux
 * */
class GlobalAccess extends GlobalAcessAux {

    /**
     * errors collection
     * @var array 
     */
    var $erros = array();

    /**
     * Magic Metthod
     * @param string $type Action type
     * @return void
     */
    public function __construct($type) {
        if ($type == 'loadElement') {
            parent::__construct();
        } else {
            $param = Url::getURL(3);
            if (isset($param) && $param == 'update') {
                // Check demostration mode is active
                Func::CheckDemostrationMode();
                $data = $this->DoNewAccessMenu();
                $this->InsertNewMenu($data);

                if (!in_array(false, $this->erros)) {
                    Call_JS::alerta('Acessos foram alterado com sucesso');
                    Call_JS::retornar(URL . 'dashboard/Settings/access');
                    exit;
                } else {
                    Call_JS::alerta('Não foi possivel modidificar os acessos do cargo ' . $this->GetAccountTypeAction());
                    Call_JS::retornar(URL . 'dashboard/Settings/access');
                    exit;
                }
            } elseif (isset($param) && $param == 'update_sub') {

                $data = $this->DoNewAccessSubMenu();
                $this->InsertNewSubMenu($data);
                $menu_name = Func::array_table('menu', array('id' => $_POST['id_menu']), 'name');
                if (!in_array(false, $this->erros)) {
                    Call_JS::alerta('Acessos do submenu "' . $menu_name . '" foram alterado com sucesso');
                    Call_JS::retornar(URL . 'dashboard/Settings/access');
                    exit;
                } else {
                    Call_JS::alerta('Não foi possivel modidificar os acessos do cargo ' . $this->GetAccountTypeAction());
                    Call_JS::retornar(URL . 'dashboard/Settings/access');
                    exit;
                }
            }
        }
    }

    /**
     * Insert reg on submenu table
     * @param array $data
     * @return boolean
     */
    private function InsertNewSubMenu(array $data) {
        if (self::DeleteAllSub($_POST['account_type']) !== false) {
            $erros = array();
            foreach ($data as $value) {
                $q = new Query;
                $q
                        ->insert_into(
                                'menu_sub_access', array(
                            'account_type' => $_POST['account_type'],
                            'id_sub_menu' => $value
                                )
                        )
                        ->run();
                $erros[] = !$q ? false : true;
            }
            $this->erros = $erros;
            return true;
        }
        return false;
    }

    /**
     * Delete all registry
     * @return boolean
     */
    private static function DeleteAll() {
        $q = new Query;
        $q
                ->delete_from('menu_access')
                ->run();
        return !$q ? false : true;
    }

    /**
     * Delete all registry
     * @return boolean
     */
    private static function DeleteAllSub($account_type) {
        $q = new Query;
        $q
                ->delete_from('menu_sub_access')
                ->where_equal_to(
                        array(
                            'account_type' => $account_type
                        )
                )
                ->run();
        return !$q ? false : true;
    }

    /**
     * Insert news regs
     * @param array $data
     * @return boolean
     */
    private function InsertNewMenu(array $data) {
        if (self::DeleteAll() !== false) {
            $erros = array();
            foreach ($data as $account_type => $value) {
                foreach ($value as $id_menu) {
                    echo $account_type . "|" . $id_menu . "<br>";
                    $q = new Query;
                    $q
                            ->insert_into(
                                    'menu_access', array(
                                'account_type' => $account_type,
                                'id_menu' => $id_menu
                                    )
                            )
                            ->run();
                    $erros[] = !$q ? false : true;
                }
            }
            $this->erros = $erros;
            return true;
        }
        return false;
    }

    /**
     * create a array for insert elements
     * @return array
     */
    private function DoNewAccessSubMenu() {
        $arr = array();
        $data = $_POST['access'];
        foreach ($data as $v) {
            $arr[] = $v;
        }
        return $arr;
    }

    /**
     * create a array for insert elements
     * @return array
     */
    private function DoNewAccessMenu() {
        $arr = array();
        for ($i = 1; $i <= 4; $i++) {
            $data = $_POST['business' . $i];
            foreach ($data as $v) {
                $arr[$i][] = $v;
            }
        }


        return $arr;
    }

}

/**
 * Build HTML of page
 * @author Bruno Ribeiro <bruno.espertinho@gmail.com>
 * @version 1
 * @access public
 * */
class GlobalAcessAux {

    /**
     * Verify menu is checked
     * @param string $id Id menu
     * @param integer $account_type Bussines type
     * @return string
     */
    private function check_checked($id, $account_type) {
        $where = array(
            'id_menu' => $id,
            'account_type' => $account_type
        );
        $total = Func::_contarReg('menu_access', $where);
        if ($total > 0) {

            return 'checked';
        }
    }

    /**
     * Verify submenu is checked
     * @param string $id Id submenu
     * @param integer $account_type Bussines type
     * @return string
     */
    private function check_checked_sub($id, $id_menu, $account_type) {
        $where = array(
            'id_sub_menu' => $id,
            'account_type' => $account_type
        );
        $total = Func::_contarReg('menu_sub_access', $where);
        if ($total > 0) {

            return 'checked';
        }
    }

    private function ModalSubMenu($account_type, $id) {
        $name = GetInfo::_account_type_nane_maker($account_type);
        $menu_name = Func::array_table('menu', array('id' => $id), 'name');
        $url = URL . FILENAME . DS;
        return <<<EOF
<div id="sub{$id}-{$account_type}" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <form action="{$url}update_sub" method="post" class="form-horizontal ">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Submenu {$menu_name} - {$name}</h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-striped table-condensed">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>Acesso</th>
                        </tr>
                        </thead>      
                        <tbody>
                         {$this->LoopSubmenu($id, $account_type)}
                        </tbody>
                    </table> 
                <input type="hidden" name="id_menu" value="{$id}">
                <input type="hidden" name="account_type" value="{$account_type}">
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" type="submit">Alterar</button>
                <button class="btn btn-primary" type="reset">Restaurar</button>
                <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Fechar</button>
            </div>
        </form>    
        </div>
    </div>
</div>
EOF;
    }

    /**
     * Added a button modal case this menu are checked
     * @param string $access Query resykt access
     * @param integer $account_type Bussines type
     * @param integer $id Id menu
     * @param string $return_type Type of element to returns
     * @return string
     */
    private function check_button($account_type, $id) {
        $where = array(
            'id_menu' => $id,
            'account_type' => $account_type
        );
        $total = Func::_contarReg('menu_access', $where);
        $is_sub = Func::array_table('menu', array('id' => $id), 'sub');
        if ($total > 0) {
            if ($is_sub) {
                return ' <a href="#sub' . $id . '-' . $account_type . '" title="Gerenciar acessos submenus" role="button" class="btn btn-xs btn-primary" data-toggle="modal"><i class="fa fa-cog"></i></a>';
            }
        }
    }

    private function LoopSubmenu($id_menu, $account_type) {
        $q = new Query();
        $q
                ->select()
                ->from('menu_sub')
                ->where_equal_to(
                        array(
                            'status' => true,
                            'id_menu' => $id_menu
                        )
                )
                ->run();
        $data = $q->get_selected();
        $result = '';
        foreach ($data as $k) {
            $result.= <<<EOF
<tr>
    <td>{$k['id']}</td>
    <td>{$k['name']}</td>
    <td><input name="access[]" value="{$k['id']}" type="checkbox" {$this->check_checked_sub($k['id'], $id_menu, $account_type)} data-on="success" data-off="danger"></td>
</tr>
EOF;
        }
        return $result;
    }

    /**
     * Loop all menu
     * @param string $return_type Type of element to returns
     * @return string
     */
    private function LoopModals() {
        $q = new Query();
        $q
                ->select()
                ->from('menu')
                ->where_equal_to(
                        array(
                            'status' => true,
                            'sub' => true
                        )
                )
                ->order_by('position asc')
                ->run();
        $data = $q->get_selected();
        $result = '';
        foreach ($data as $k) {
            $result.= $this->ModalSubMenu(1, $k['id'])
                    . $this->ModalSubMenu(2, $k['id'])
                    . $this->ModalSubMenu(3, $k['id'])
                    . $this->ModalSubMenu(4, $k['id']);
        }
        return $result;
    }

    /**
     * Loop all menu
     * @param string $return_type Type of element to returns
     * @return string
     */
    private function LoopMenu() {
        $q = new Query();
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
        $data = $q->get_selected();
        $result = '';
        foreach ($data as $k) {
            $result.= <<<EOF
                <tr>
                    <td>{$k['id']}</td>
                    <td>{$k['name']}</td>
                    <td><input name="business1[]" value="{$k['id']}" type="checkbox" {$this->check_checked($k['id'], 1)} data-on="success" data-off="danger">{$this->check_button(1, $k['id'])}</td>
                    <td><input name="business2[]" value="{$k['id']}" type="checkbox" {$this->check_checked($k['id'], 2)} data-on="success" data-off="danger">{$this->check_button(2, $k['id'])}</td>
                    <td><input name="business3[]" value="{$k['id']}" type="checkbox" {$this->check_checked($k['id'], 3)} data-on="success" data-off="danger">{$this->check_button(3, $k['id'])}</td>
                    <td><input name="business4[]" value="{$k['id']}" type="checkbox" {$this->check_checked($k['id'], 4)} data-on="success" data-off="danger">{$this->check_button(4, $k['id'])}</td>
                </tr>
EOF;
        }
        return $result;
    }

    /**
     * load elements
     * @param String $call
     * @return Object-String
     */
    public function __construct() {
        $url = URL . FILENAME . "/update";
        return print <<<EOF
<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">Alterar configurações de acesso
                <span class="tools pull-right">
                    <a class="fa fa-chevron-down" href="javascript:;"></a>
                    <a class="fa fa-times" href="javascript:;"></a>
                </span>
            </header>
            <div class="panel-body">
        <form class="cmxform form-horizontal" id="signupForm" method="post" action="{$url}">
                <section id="unseen">
                            <table class="table table-bordered table-striped table-condensed">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Título</th>
                                    <th>Atendente</th>
                                    <th>Funcionário</th>
                                    <th>Vendedor</th>
                                    <th>Gerente</th>
                                </tr>
                                </thead>      
                                <tbody>
                                 {$this->LoopMenu()}
                                </tbody>
                            </table>
                        </section>
                        <div class="clearfix"></div>
                        <div class="form-group">
                            <div class="col-lg-offset-3 col-lg-6">
                                <button class="btn btn-success" type="submit">Alterar</button>
                                <button class="btn btn-primary" type="reset">Restaurar</button>
                            </div>
                        </div>
                    </div>
                </section>
        </form>
        <!-- Modals Sub menu -->
        {$this->LoopModals()}
        <!-- end modals -->                         
            </div>
        </section>
    </div>
</div>
EOF;
    }

}
