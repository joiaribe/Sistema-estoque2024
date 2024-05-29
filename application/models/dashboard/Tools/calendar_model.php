<?php

use Developer\Tools\Url as Url;
use Query as Query;
use Dashboard\Buttons as Buttons;

class action {
    # header para algumas ações

    protected $loc_action = array(
        'add' => 'dashboard/calendar/add',
        'prev' => 'dashboard/calendar/pre/',
        'alt' => 'dashboard/calendar/alt/'
    );

    # plural e singular da página
    protected $msg = array(
        0 => 'Cliente',
        1 => 'Clientes',
        2 => 'Gerenciar' //palavra que vai aparecer em ferramentas seguido pelo nome da página
    );

    # tabela principal
    protected $table = 'agenda';

    /**
     * * verifica se vai deletar
     * * @access private
     * * @return void
     */
    protected function verify_del() {
        $param = Url::getURL(1);
        if (isset($param) AND $param == 'del') {
            $q = new Query();
            $q
                    ->delete($this->table)
                    ->where_equal_to(
                            array(
                                'id' => Url::getURL(2)
                            )
                    )
                    ->run();
            if ($q) {
                \Dashboard\Call_JS::alerta($this->msg[0] . ' excluido com sucesso !');
                \Dashboard\Call_JS::retornar('form', Url::getURL(0));
            }
        }
    }

    /**
     * * checks will delete multiple checkbox by
     * * @access private
     * * @return void
     */
    protected function verify_broadcast_delete() {
        $param = Url::getURL(1);
        if (isset($param) and $param == 'delete_all') {
            if (isset($_POST['delete']) or isset($_POST['checkbox'])) {
                //store the array of checkbox values
                $allCheckBoxId = filter_input(INPUT_POST, 'checkbox');
                //escaping all of them for a MySQL query using array_map
                array_map('mysql_real_escape_string', $allCheckBoxId);
                //implode will concatenate array values into a string divided by commas
                $ids = implode(",", $allCheckBoxId);
                $this->run_query($ids);
            } else {
                \Dashboard\Call_JS::alerta('Selecione pelo menos um ' . $this->msg[0]);
                \Dashboard\Call_JS::retornar('form', Url::getURL(0));
            }
        }
    }

    /**
     * * monta query de delete
     * * @access private
     * * @return void
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

            $a = count(filter_input(INPUT_POST, 'checkbox'));
            $mensagem = $a == 1 ? $this->msg[0] . ' excluido com sucesso !' : $a . $this->msg[1] . ' excluidos com sucesso !';
            \Dashboard\Call_JS::alerta($mensagem);
            \Dashboard\Call_JS::retornar('form', $this->location);
        }
    }

}

class html extends action {

    /**
     * * html header da tabela
     * * @access protected
     * * @return string
     */
    protected function body_table() {
        if (\Session::get('user_account_type') == 3 or 2) {
            return '<thead>
                    <tr>
                        <td class="center uniformjs"><input type="checkbox" name="checkbox[]" /></td>
                        <th>#ID</th>
                        <th>Cliente</th>
                        <th>Nome</th>
                        <th>Descrição</th>
                        <th>Data</th>
                        <th>Ação</th>
                    </tr>
                </thead>';
        } else {
            return '<thead>
                    <tr>
                        <td class="center uniformjs"><input type="checkbox" name="checkbox[]" /></td>
                        <th>#ID</th>
                        <th>Nome</th>
                        <th>Descrição</th>
                        <th>Data</th>
                        <th>Ação</th>
                    </tr>
                </thead>';
        }
    }

    /**
     * * verifica se existe um registro
     * * @access private
     * * @return string
     */
    private function verify_result($data) {
        return isset($data) ? $data : '<font style="color: red">Não Encontrado</font>';
    }

    /**
     * * html conteudo da tabela
     * * @access protected
     * * @return string
     */
    protected function contain_table(array $fetch) {
        if (\Session::get('user_account_type') == 3 or 2) {
            $name = 1; //GetInfo::_name($fetch['id_user']);
            return '        <tr class="gradeX">
                                <td class="center uniformjs"><input type="checkbox" name="checkbox[]" value="' . $fetch['id'] . '" /></td>
                                <td>#' . $fetch['id'] . '</td>
                                <td>' . $name . '</td>
                                <td>' . \Func::FirstAndLastName($fetch['titulo']) . '</td>
                                <td>' . $this->verify_result($fetch['description']) . '</td>
                                <td>' . strftime('%d de %B, %Y &aacute;s %H:%M:%S', strtotime($fetch['data'])) . '</td>
                                <td class="right actions">
                                ' . Buttons::button_ver($this->loc_action['prev'] . $fetch['id']) . '
                                ' . Buttons::button_alt($this->loc_action['alt'] . $fetch['id']) . '
                                ' . Buttons::button_delete($fetch['id']) . '                      
                                </td>
                            </tr>';
        } else {
            return '       <tr class="gradeX">
                                <td class="center uniformjs"><input type="checkbox" name="checkbox[]" value="' . $fetch['id'] . '" /></td>
                                <td>#' . $fetch['id'] . '</td>
                                <td>' . \Func::FirstAndLastName($fetch['title']) . '</td>
                                <td>' . $this->verify_result($fetch['descri']) . '</td>
                                <td>' . $this->verify_result($fetch['Fone']) . '</td>
                                <td class="right actions">
                                ' . Buttons::button_ver($this->loc_action['prev'] . $fetch['id']) . '
                                ' . Buttons::button_alt($this->loc_action['alt'] . $fetch['id']) . '
                                ' . Buttons::button_delete($fetch['id']) . '                  
                                </td>
                            </tr>';
        }
    }

    /**
     * faz um loop em ferramentas
     * @param array $ids
     * @return string
     */
    private function tools_call(array $ids) {
        $result = '';
        // for ($i = 0; $i <= count($ids); $i++) {
        $q = new Query;
        $q
                ->select()
                ->from('menu_sub')
                ->where_in(
                        array('id' => $ids)
                )
                ->run();
        if ($q) {
            $data = $q->get_selected();
            foreach ($data as $dados) {
                $result.= '<li><a href="' . $dados['link'] . '">' . $this->msg[2] . ' ' . $dados['name'] . '</a></li>';
            }
        }
        // }

        return $result;
    }

    protected function make_events(array $param, $last) {
        $data = strftime('%Y-%m-%d %H:%M:%S', strtotime($param['horario']));
        $date_formatted = strftime('%d de %B, %Y', strtotime($param['horario']));
        $time_formatted = strftime('%H:%M:%S', strtotime($param['horario']));
        $color = Func::array_table('clientes', array('id' => $param['id_cliente']), 'agenda');
        $color_f = Func::array_table('clientes', array('id' => $param['id_cliente']), 'agenda_cor');
        $name = Func::array_table('clientes', array('id' => $param['id_cliente']), 'nome');
        $c_name = !empty($name) ? $name : '<font color="red">Não Encontrado</font>';
        $descri = (isset($param['description'])) ? $param['description'] : '<font color="red">Sem Descrição</font>';
        $end = (isset($param['horario_end'])) ? "end: '" . strftime('%Y-%m-%d %H:%M:%S', strtotime($param['horario_end'])) . "'," : false;
        $v = $last !== true ? ',' : false;
        return <<<EOFPAGE
                {
                    title: '{$param['titulo']}',
                    start: '{$data}',{$end}
                    msg: '{$descri}',
                    date_formatted: '{$date_formatted}',
                    time_formatted: '{$time_formatted}',
                    backgroundColor: '{$color}',
                    textColor: '{$color_f}',
                    client: '{$c_name}'
                    

        
                }{$v}
EOFPAGE;
    }

    private function verify_label($i) {
        switch ($i) {
            case 0 :
                $result = 'label-primary';
                break;
            case 1 :
                $result = 'label-success';
                break;
            case 3 :
                $result = 'label-info';
                break;
            case 4 :
                $result = 'label-inverse';
                break;
            case 5 :
                $result = 'label-warning';
                break;
            case 6 :
                $result = 'label-danger';
                break;
            default :
                $result = 'label-default';
                break;
        }

        return $result;
    }

    protected function make_events_(array $param) {

        return "<div class='external-event label " . $this->verify_label(rand(0, 6)) . "'>" . $param['titulo'] . "</div>";
    }

    /**
     * monta o html do botão ferramentas
     * @return string
     */
    protected function make_tools() {
        $add = "location.href='" . Url::getURL(0) . "_" . $this->loc_action['add'] . "'";

        $return = '
            <form action="' . URL . 'dashboard/calendar?action=delete_all" id="delete_broadcast" method="POST">  
                <div class="panel-body">
                    <div class="adv-table">
                        <div class="clearfix">
                            <div class="btn-group">
                                <button id="editable-sample_new" onClick="' . $add . '" class="btn btn-primary">
                                    Add Novo <i class="fa fa-plus"></i>
                                </button>
                            </div>                          
                            <div class="btn-group pull-right">
                                <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">Ferramentas <i class="fa fa-angle-down"></i>
                                </button>
                                <ul class="dropdown-menu pull-right">
                                    <li><a href="' . Url::getURL(0) . '_' . $this->loc_action['add'] . '">Add Novo</a></li>
                                    <li class="divider"></li>
                                    ' . $this->tools_call(array(10)) . '
                                    <li class="divider"></li>
                                    <li><a href="javascript:del_all()">Excluir múltiplos ' . $this->msg[1] . '</a></li>
                                </ul>
                            </div>
                        </div>
              </form>';

        return $return;
    }

}

class calendarModel extends html {

    private $element = NULL;

    /**
     * * monta query caso seja cliente
     * * @access private
     * * @return array
     */
    private function query_cliente() {
        $q = new Query;
        $q
                ->select()
                ->from($this->table)
                ->where_equal_to(
                        array('id_user' => $_SESSION['user_id'])
                )
                ->order_by('data asc')
                ->run();
        $result = '';
        if ($q) {
            $users = $q->get_selected();
            foreach ($users as $user) {
                $result.= $this->contain_table($user);
            }
            return $result;
        }
    }

    /**
     * * monta query caso não seja cliente
     * * @access private
     * * @return array
     */
    private function query_root() {
        $q = new Query;
        $q
                ->select()
                ->from($this->table)
                ->order_by('data asc')
                ->run();

        $result = '';
        if ($q) {
            $users = $q->get_selected();
            foreach ($users as $user) {
                $result.= $this->contain_table($user);
            }
            return $result;
        }
    }

    /**
     * * monta query caso não seja cliente
     * * @access private
     * * @return array
     */
    private function query_root_events($type) {
        $q = new Query;
        $q
                ->select()
                ->from($this->table)
                ->order_by('data asc')
                ->run();

        $result = '';
        if ($q) {
            $users = $q->get_selected();
            $total = $q->get_selected_count();
            $i = 0;
            foreach ($users as $user) {
                $i++;
                $last = ($total == $i) ? true : false;
                if ($type == 1) {
                    $result.= $this->make_events($user, $last);
                } else {
                    $result.= $this->make_events_($user);
                }
// $result.= ($type == 1) ? $this->make_events($user) : $this->make_events_($user);
            }
            return $result;
        }
    }

    /**
     * * verifica qual query vai ser chamada dependendo do cargo
     * * @access protected
     * * @return object
     */
    protected function verify_query() {

        return(\Session::get('user_account_type') == 3 or 2) ? $this->query_root() : $this->query_cliente();
    }

    /**
     * * verifica qual elemento sera mostrado
     * * @access private
     * * @return object
     */
    private function verify_element() {

        switch ($this->element) {
            case 'body_table':
                $result = $this->body_table();
                break;
            case 'elements_table':
                $result = $this->verify_query();
                break;
            case 'tools':
                $result = $this->make_tools();
                break;
            case 'events_js':
                $result = $this->query_root_events(1);
                break;
            case 'events_li':
                $result = $this->query_root_events(2);
                break;
            default :
                $result = false;
                break;
        }

        return print $result;
    }

    /**
     * * controi classe método mágico
     * * @access public
     * * @return main
     */
    public function __construct($name) {
        $this->element = $name;
        $this->verify_del();
        $this->verify_broadcast_delete();

        return $this->verify_element();
    }

}
