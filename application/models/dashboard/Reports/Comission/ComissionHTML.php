<?php

namespace Reports\Comission;

use Dashboard\Buttons as Buttons;
use Developer\Tools\GetInfo as GetInfo;
use Dashboard\Call_JS as Call_JS;
use Query as Query;
use Developer\Tools\Url as Url;
use DateTime as DateTime;
use Func as Func;

/**
 * HTML used in all class
 */
class ComissionHTML extends requireds {

    /**
     * html header of table
     * @return string
     */
    protected function body_table() {
        return '<thead>
                    <tr>
                        <th>#ID</th>
                        <th>Serviço</th>
                        <th>Cliente</th>
                        <th>Funcionário</th>
                        <th>Valor</th>
                        <th>Estado</th>
                        <th>Ação</th>
                    </tr>
                </thead>';
    }

    /**
     * check what button call
     * @param String $value Value array loc_action
     * @param Integer $id ID reg
     * @return Object
     */
    private function check_buttons($value, $id) {
        $recover_data_param = '?preview_type=' . Url::getURL(4) . '&preview_id=' . Url::getURL(5);
        $result = false;
        switch ($value) {
            case 'prev':
                $result = Buttons::button_ver(FILENAME . $this->loc_action['prev'] . $id . DS . $recover_data_param);
                break;
            case 'alt':
                $result = Buttons::button_alt(FILENAME . $this->loc_action['alt'] . $id);
                break;
            case 'del':
                $result = Buttons::button_delete(FILENAME . $this->loc_action['del'] . $id);
                break;
        }
        return $result;
    }

    /**
     * Build Buttons actions
     * @param type $id
     * @return String
     */
    private function build_buttons($id) {
        $result = '';
        foreach ($this->loc_action as $value => $key) {
            if ($key !== false) {
                $result.= ' ' . $this->check_buttons($value, $id) . ' ';
            }
        }
        return $result;
    }

    /**
     * Get Client name
     * @param integer $id
     * @return string
     */
    private function Client($id, $return = 'full') {
        if (isset($id)) {
            $name = Func::array_table('clientes', array('id' => $id), 'nome');
            if ($name == false) {
                $name = '<span class="label label-danger">Não Encontrado !</span>';
            } else {
                $name = $return == false ? $name : Func::FirstAndLastName($name);
            }
        } else {
            $name = '<span class="label label-danger">Desconhecido</span>';
        }
        return $name;
    }

    /**
     * Get employee name
     * @param integer $id
     * @return string
     */
    private function Employee($id, $return = 'full') {
        if (isset($id)) {
            $name = Func::array_table('funcionarios', array('id' => $id), 'nome');
            if ($name == false) {
                $name = '<span class="label label-danger">Não Encontrado !</span>';
            } else {
                $name = $return == false ? $name : Func::FirstAndLastName($name);
            }
        } else {
            $name = '<span class="label label-danger">Desconhecido</span>';
        }
        return $name;
    }

    /**
     * html conteudo da tabela
     * @access protected
     * @return string
     */
    protected function contain_table(array $fetch) {
        $cur_url = URL . FILENAME . DS;
        $recover_data_param = '?preview_type=' . Url::getURL(4) . '&preview_id=' . Url::getURL(5) . DS;
        $status = $fetch['status'] == true ? '<a href="' . $cur_url . 'MarkSingle/' . $fetch['id'] . '/0/' . $recover_data_param . '" title="Marca como não pago"><span class="badge bg-success">Pago</span></a>' : '<a href="' . $cur_url . 'MarkSingle/' . $fetch['id'] . '/1/' . $recover_data_param . '" title="Marca como pago"><span class="badge bg-important">Não Pago</span</a>';
        $valor = number_format($fetch['value'], 2, '.', ',');
        return <<<EOFPAGE
        <tr class="gradeX">
            <td>#{$fetch['id']}</td>
            <td>{$fetch['name']}</td>
            <td>{$this->Client($fetch['id_client'])}</td>
            <td>{$this->Employee($fetch['id_employee'])}</td>
            <td>R$: {$valor}</td>
            <td>{$status}</td>
            <td class="right actions">{$this->build_buttons($fetch['id'])}</td>
        </tr>
EOFPAGE;
    }

    /**
     * build tools for listing mode
     * @return string
     */
    protected function make_tools() {
        $return = '<form action="' . URL . FILENAME . '/delete_all" id="delete_broadcast" method="POST">';
        return $return;
    }

    /**
     * last day in month
     * @param string $newData Date
     * @param string $output Output format
     * @return integer
     */
    private static function ultimoDiaMes($newData, $output = 'd/m/Y') {
        /* Desmembrando a Data */
        list($newDia, $newMes, $newAno) = explode("/", $newData);
        return date($output, mktime(0, 0, 0, $newMes + 1, 0, $newAno));
    }

    /**
     * Calculate percent between start and end of month
     * @return float
     */
    private static function calculatePercent() {
        $today = date('d');
        $percent = round(($today / self::ultimoDiaMes(date('d/m/Y'), 'd')) * 100, 1);
        $result = str_replace(",", ".", $percent);
        return $result;
    }

    /**
     * show comission text appropriate per user
     * @param array $data Query result
     * @return string
     */
    private function CalculateComissionPerUser(array $data) {
        $criter = array(
            'MONTH(data)' => date('m'),
            'YEAR(data)' => date('Y'),
            'id_employee' => $data['id_employee']
        );
        $totalComission = Func::_contarReg($this->table, $criter);

        if ($totalComission == 0) {
            return "Sem comissões";
        }

        if ($totalComission == 1) {
            return "1 comissão";
        }
        return sprintf("%d comissões", $totalComission);
    }

    /**
     * show comisssion text appropriate per service
     * @param array $data Query result
     * @return string
     */
    private function CalculateComissionPerService(array $data) {
        $criter = array(
            'MONTH(data)' => date('m'),
            'YEAR(data)' => date('Y'),
            'id_service' => $data['id_service']
        );
        $totalComission = Func::_contarReg($this->table, $criter);

        if ($totalComission == 0) {
            return "Sem comissões";
        }

        if ($totalComission == 1) {
            return "1 comissão";
        }
        return sprintf("%d comissões", $totalComission);
    }

    /**
     * show comisssion text appropriate per client
     * @param array $data Query result
     * @return string
     */
    private function CalculateComissionPerClient(array $data) {
        $criter = array(
            'MONTH(data)' => date('m'),
            'YEAR(data)' => date('Y'),
            'id_client' => $data['id_client']
        );
        $totalComission = Func::_contarReg($this->table, $criter);

        if ($totalComission == 0) {
            return "Sem comissões";
        }

        if ($totalComission == 1) {
            return "1 comissão";
        }
        return sprintf("%d comissões", $totalComission);
    }

    /**
     * calculate amount value per user
     * @param array $data
     * @return string
     */
    private function CalculateAmoutPerUser(array $data, $action = false) {
        $criter = array(
            'MONTH(data)' => date('m'),
            'YEAR(data)' => date('Y'),
            'id_employee' => $data['id_employee']
        );
        $total = Func::_sum_values($this->table, 'value', $criter);
        $formatted = number_format($total, 2, ",", ".");
        $result = "R$: " . $formatted;
        if ($action == 'title') {
            if ($total == 1) {
                return 'Valor total da comissão ' . $result;
            }
            return 'Valor total das comissões ' . $result;
        }
        return $result;
    }

    /**
     * calculate amount value per service
     * @param array $data
     * @return string
     */
    private function CalculateAmoutPerService(array $data, $action = false) {
        $criter = array(
            'MONTH(data)' => date('m'),
            'YEAR(data)' => date('Y'),
            'id_service' => $data['id_service']
        );
        $total = Func::_sum_values($this->table, 'value', $criter);
        $formatted = number_format($total, 2, ",", ".");
        $result = "R$: " . $formatted;
        if ($action == 'title') {
            if ($total == 1) {
                return 'Valor total da comissão ' . $result;
            }
            return 'Valor total das comissões ' . $result;
        }
        return $result;
    }

    /**
     * calculate amount value per client
     * @param array $data
     * @return string
     */
    private function CalculateAmoutPerClient(array $data, $action = false) {
        $criter = array(
            'MONTH(data)' => date('m'),
            'YEAR(data)' => date('Y'),
            'id_client' => $data['id_client']
        );
        $total = Func::_sum_values($this->table, 'value', $criter);
        $formatted = number_format($total, 2, ",", ".");
        $result = "R$: " . $formatted;
        if ($action == 'title') {
            if ($total == 1) {
                return 'Valor total da comissão ' . $result;
            }
            return 'Valor total das comissões ' . $result;
        }
        return $result;
    }

    /**
     * [Gambiarra] Get total comission in currenty monthly only the payed
     * for some reason neither SQL nor the class is doing with where equal to
     * @param array $data
     * @return Int
     */
    private function GetTotalPerUserStatus(array $data) {
        $q = new Query();
        $q
                ->select()
                ->from($this->table)
                ->where_equal_to(
                        array(
                            'YEAR(data)' => date('Y'),
                            'MONTH(data)' => date('m'),
                        )
                )
                ->run();
        $count = 0;
        foreach ($q->get_selected() as $v) {
            if ($v['status'] == true && $v['id_employee'] == $data['id_employee']) {
                $count = $count + 1;
            }
        }
        return $count;
    }

    /**
     * [Gambiarra] Get total comission in currenty monthly only the payed
     * for some reason neither SQL nor the class is doing with where equal to
     * @param array $data
     * @return Int
     */
    private function GetTotalPerServiceStatus(array $data) {
        $q = new Query();
        $q
                ->select()
                ->from($this->table)
                ->where_equal_to(
                        array(
                            'YEAR(data)' => date('Y'),
                            'MONTH(data)' => date('m'),
                        )
                )
                ->run();
        $count = 0;
        foreach ($q->get_selected() as $v) {
            if ($v['status'] == true && $v['id_service'] == $data['id_service']) {
                $count = $count + 1;
            }
        }
        return $count;
    }

    /**
     * Get status of comission per user
     * @param array $data
     * @return string
     */
    private function GetStatusPerUser(array $data, $return = false) {
        $criter = array(
            'MONTH(data)' => date('m'),
            'YEAR(data)' => date('Y'),
            'id_employee' => $data['id_employee']
        );
        $total = Func::_contarReg($this->table, $criter);
        $total_per_status = $this->GetTotalPerUserStatus($data);
        switch (true) {
            case ($total == $total_per_status):
                $result = ($return == false) ? '<span class="label label-success"> Completo</span>' : TRUE;
                break;
            case ($total_per_status == 0):
                $result = ($return == false) ? '<span class="label label-danger"> Pendente</span>' : FALSE;
                break;
            default:
                $result = ($return == false) ? '<span class="label label-warning"> Incompleto</span>' : FALSE;
                break;
        }
        return $result;
    }

    /**
     * Get status of comission per user
     * @param array $data
     * @return string
     */
    private function GetStatusPerService(array $data, $return = false) {
        $criter = array(
            'MONTH(data)' => date('m'),
            'YEAR(data)' => date('Y'),
            'id_service' => $data['id_service']
        );
        $total = Func::_contarReg($this->table, $criter);
        $total_per_status = $this->GetTotalPerServiceStatus($data);
        switch (true) {
            case ($total == $total_per_status):
                $result = ($return == false) ? '<span class="label label-success"> Completo</span>' : TRUE;
                break;
            case ($total_per_status == 0):
                $result = ($return == false) ? '<span class="label label-danger"> Pendente</span>' : FALSE;
                break;
            default:
                $result = ($return == false) ? '<span class="label label-warning"> Incompleto</span>' : FALSE;
                break;
        }
        return $result;
    }

    /**
     * Get status of comission per user
     * @param array $data
     * @return string
     */
    private function GetStatusPerClient(array $data, $return = false) {
        $criter = array(
            'MONTH(data)' => date('m'),
            'YEAR(data)' => date('Y'),
            'id_client' => $data['id_client']
        );
        $total = Func::_contarReg($this->table, $criter);
        $total_per_status = $this->GetTotalPerServiceStatus($data);
        switch (true) {
            case ($total == $total_per_status):
                $result = ($return == false) ? '<span class="label label-success"> Completo</span>' : TRUE;
                break;
            case ($total_per_status == 0):
                $result = ($return == false) ? '<span class="label label-danger"> Pendente</span>' : FALSE;
                break;
            default:
                $result = ($return == false) ? '<span class="label label-warning"> Incompleto</span>' : FALSE;
                break;
        }
        return $result;
    }

    /**
     * Checks the params value for any action
     * @return array
     */
    private static function ActionCriterFilter() {
        $criter = array();
        // Check month
        if (self::GetParam('month')) {
            $criter['month'] = array(
                'STATUS' => TRUE,
                'VALUE' => self::GetParam('month')
            );
        }
        // Check year
        if (self::GetParam('year')) {
            $criter['year'] = array(
                'STATUS' => TRUE,
                'VALUE' => self::GetParam('year')
            );
        }
        // Check employee
        if (self::GetParam('employee') !== 'all' && self::GetParam('employee')) {
            $criter['employee'] = array(
                'STATUS' => TRUE,
                'VALUE' => self::GetParam('employee')
            );
        }
        // Check Service
        if (self::GetParam('service') !== 'all' && self::GetParam('service')) {
            $criter['service'] = array(
                'STATUS' => TRUE,
                'VALUE' => self::GetParam('service')
            );
        }

        $i = 0;
        // checks if there is at least one element
        if (in_array(TRUE, $criter)) {
            $result = NULL;
            // builds parameters sequence 
            foreach ($criter as $k => $v) {
                $i++;
                // that param is first ?
                if ($i == 1) {
                    $result.= '?' . $k . '=' . $v['VALUE'];
                } else {
                    $result.= '&' . $k . '=' . $v['VALUE'];
                }
            }
        } else {
            $result = false;
        }

        return $result;
    }

    /**
     * which verifies call tracer
     * change message to an appropriate
     * @param array $data Query result
     * @return string
     */
    private function CheckTracerTocall(array $data, $return_action = NULL, $location = NULL) {
        $action = ($return_action == NULL) ? NULL : $data[$return_action];
        $url = URL . FILENAME . DS;
        $pending = "javascript:CustomWarning('" . $url . 'mark' . DS . $location . DS . 'pending' . DS . $action . DS . self::ActionCriterFilter() . "',' Deseja realmente prosseguir com a operação ? ')";
        $paid = "javascript:CustomWarning('" . $url . 'mark' . DS . $location . DS . 'paid' . DS . $action . DS . self::ActionCriterFilter() . "', ' Deseja realmente prosseguir com a operação ? ')";
        if (STATUS_DAY_CLOSE == true) {
            // checks can close the month
            if (DAY_CLOSE_COMISSION == date('d')) {
                if ($this->GetStatusPerUser($data, 'bool')) {
                    return <<<EOF
<a class="btn" title="Abrir o mês ! marca todos como pendente." href="{$pending}">
    <i class="fa fa-check"></i>
</a>
EOF;
                } else {
                    return <<<EOF
<a class="btn" title="Fechar o mês ! marca todos como pago." href="{$paid}">
    <i class="fa fa-check"></i>
</a>
EOF;
                }
            }
        }

        if ($this->GetStatusPerUser($data, 'bool')) {
            return <<<EOF
<a class="btn" title="Marcar todos como pendente" href="{$pending}">
    <i class="fa fa-check"></i>
</a>
EOF;
        } else {
            return <<<EOF
<a class="btn" title="Marcar todos como pago" href="{$paid}">
    <i class="fa fa-check"></i>
</a>
EOF;
        }
    }

    /**
     * HTML itens per user
     * @param array $data Query result
     * @return string
     */
    protected function PerUser(array $data) {
        $dateNice = \makeNiceTime::MakeNew($data['data']);
        $date = strftime('%d de %B, %Y ás %H:%m:%S', strtotime($data['data']));
        $name = Func::FirstAndLastName(Func::array_table('funcionarios', array('id' => $data['id_employee']), 'nome'));
        $mark_comission = $this->CheckTracerTocall($data, 'id_employee', 'MarkPerUser');
        $my_url = URL . FILENAME . DS;
        $url = "javascript:aviso('" . $my_url . 'del' . DS . 'DeletePerUser' . DS . $data['id_employee'] . DS . self::ActionCriterFilter() . "')";
        $pre_url = $my_url . 'PreviewListing' . DS . 'PreviewPerUser' . DS . $data['id_employee'] . DS . self::ActionCriterFilter();
        return <<<EOF
<li>
    <div class="todo-actions">
        <div class="padding-horizontal-5">
            <div class="block space5">
                <span class="desc">{$name}</span> {$this->GetStatusPerUser($data)}
            </div>
            <div class="block">
                <span class="desc text-small text-light" title="Ùltima comissão foi feita em : {$date}"><i class="fa fa-clock-o"></i> {$dateNice} </span>
                <span class="desc text-small text-light" title="{$this->CalculateAmoutPerUser($data, 'title')}"><i class="fa fa-usd"></i>  {$this->CalculateAmoutPerUser($data)}</span>
                 <span class="desc text-small text-light" title="Com o total de {$this->CalculateComissionPerUser($data)}"><i class="fa fa-book"></i>  {$this->CalculateComissionPerUser($data)}</span>
                <div class="btn-group btn-group-sm todo-tools">
                    {$mark_comission}
                    <a class="btn" title="Ver comissões" href="{$pre_url}">
                        <i class="fa fa-eye"></i>
                    </a>
                    <a class="btn" title="Deletar comissões" href="{$url}">
                        <i class="fa fa-trash-o"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</li>
EOF;
    }

    /**
     * HTML itens per client
     * @param array $data Query result
     * @return string
     */
    protected function PerClient(array $data) {
        $dateNice = \makeNiceTime::MakeNew($data['data']);
        $date = strftime('%d de %B, %Y ás %H:%m:%S', strtotime($data['data']));
        $name = Func::FirstAndLastName(Func::array_table('clientes', array('id' => $data['id_client']), 'nome'));
        $mark_comission = $this->CheckTracerTocall($data, 'id_client', 'MarkPerClient');
        $my_url = URL . FILENAME . DS;
        $url = "javascript:aviso('" . $my_url . 'del' . DS . 'DeletePerClient' . DS . $data['id_client'] . DS . self::ActionCriterFilter() . "')";
        $pre_url = $my_url . 'PreviewListing' . DS . 'PreviewPerClient' . DS . $data['id_client'] . DS . self::ActionCriterFilter();
        return <<<EOF
<li>
    <div class="todo-actions">
        <div class="padding-horizontal-5">
            <div class="block space5">
                <span class="desc">{$name}</span> {$this->GetStatusPerClient($data)}
            </div>
            <div class="block">
                <span class="desc text-small text-light" title="Ùltima comissão foi feita em : {$date}"><i class="fa fa-clock-o"></i> {$dateNice} </span>
                <span class="desc text-small text-light" title="{$this->CalculateAmoutPerClient($data, 'title')}"><i class="fa fa-usd"></i>  {$this->CalculateAmoutPerClient($data)}</span>
                 <span class="desc text-small text-light" title="Com o total de {$this->CalculateComissionPerClient($data)}"><i class="fa fa-book"></i> {$this->CalculateComissionPerClient($data)}</span>
                <div class="btn-group btn-group-sm todo-tools">
                    {$mark_comission}
                    <a class="btn" title="Ver comissões" href="{$pre_url}">
                        <i class="fa fa-eye"></i>
                    </a>
                    <a class="btn" title="Deletar comissões" href="{$url}">
                        <i class="fa fa-trash-o"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</li>
EOF;
    }

    /**
     * HTML itens per service
     * @param array $data Query result
     * @return string
     */
    protected function PerService(array $data) {
        $dateNice = \makeNiceTime::MakeNew($data['data']);
        $date = strftime('%d de %B, %Y ás %H:%m:%S', strtotime($data['data']));
        $mark_comission = $this->CheckTracerTocall($data, 'id_service', 'MarkPerService');
        $my_url = URL . FILENAME . DS;
        $url = "javascript:aviso('" . $my_url . 'del' . DS . 'DeletePerService' . DS . $data['id_service'] . DS . self::ActionCriterFilter() . "')";
        $pre_url = $my_url . 'PreviewListing' . DS . 'PreviewPerService' . DS . $data['id_service'] . DS . self::ActionCriterFilter();
        return <<<EOF
<li>
    <div class="todo-actions">
        <div class="padding-horizontal-5">
            <div class="block space5">
                <span class="desc">{$data['name']}</span> {$this->GetStatusPerService($data)}
            </div>
            <div class="block">
                <span class="desc text-small text-light" title="Ùltima comissão foi feita em : {$date}"><i class="fa fa-clock-o"></i> {$dateNice} </span>
                <span class="desc text-small text-light" title="{$this->CalculateAmoutPerService($data, 'title')}"><i class="fa fa-usd"></i>  {$this->CalculateAmoutPerService($data)}</span>
                 <span class="desc text-small text-light" title="Com o total de {$this->CalculateComissionPerService($data)}"><i class="fa fa-book"></i> {$this->CalculateComissionPerService($data)}</span>
                <div class="btn-group btn-group-sm todo-tools">
                    {$mark_comission}
                    <a class="btn" title="Ver comissões" href="{$pre_url}">
                        <i class="fa fa-eye"></i>
                    </a>
                    <a class="btn" title="Deletar comissões" href="{$url}">
                        <i class="fa fa-trash-o"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</li>
EOF;
    }

    /**
     * Check if day are enabled
     * @return string
     */
    private function CheckDayCloseDay() {
        $day_close = DAY_CLOSE_COMISSION;
        $today = date('d');
        if (STATUS_DAY_CLOSE == true) {
            $result = '<span class="background-dark padding-5 radius-5 text-small inline-block" title="Dia do mês que fecha o caixa"><i class="fa fa-desktop"></i> ' . $day_close . '</span>';
        } else {
            $result = '<span class="background-dark padding-5 radius-5 text-small inline-block" title="Dia atual"><i class="fa fa-desktop"></i> ' . $today . '</span>';
        }
        return $result;
    }

    /**
     * show all months in year
     * @return string
     */
    private static function ShowAllMonths() {
        $result = '<select id="e9" name="month" class="populate " style="width: 120px;">';
        $p_month = self::GetParam('month');
        for ($i = 1; $i <= 12; $i++) {
            $month = strftime("%B", mktime(0, 0, 0, $i));
            if (isset($p_month) && $p_month == $i) {
                $result.= sprintf("<option value='%d' selected>%s</option>", $i, ucfirst($month));
            } else {
                $result.= sprintf("<option value='%d'>%s</option>", $i, ucfirst($month));
            }
        }

        $result.= '</select>';
        return $result;
    }

    /**
     * show last 5 years 
     * @return string
     */
    private static function ShowLast5Years() {
        $result = '<select id="e10" name="year" class="populate " style="width: 80px;">';
        $p_year = self::GetParam('year');
        for ($i = 0; $i <= 5; $i++) {
            $year = date('Y') - $i;
            if (isset($p_year) && $p_year == $year) {
                $result.= sprintf("<option value='%d' selected>%d</option>", $year, $year);
            } else {
                $result.= sprintf("<option value='%d'>%d</option>", $year, $year);
            }
        }

        $result.= '</select>';
        return $result;
    }

    /**
     * GET metthod get value of param
     * @param string $name Name param
     * @return mixed
     */
    protected static function GetParam($name) {
        $f = filter_input(INPUT_GET, $name);
        return $f;
    }

    /**
     * Modal HTML 
     * @param string $services Services select
     * @param string $users Users select
     * @return string
     */
    protected function ModalListing($services, $users) {
        $url = URL . FILENAME;
        $months = self::ShowAllMonths();
        $years = self::ShowLast5Years();
        return <<<EOF
<!-- Modal -->      
<div class="modal fade" id="filter" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<form action="{$url}/filter" method="get" id="form_filter" class="form-horizontal ">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Filtrar Comissões</h4>
            </div>
            <div class="modal-body">

                <div class="form-group">
                    <label class="control-label col-md-3">Período</label>
                    <div class="col-md-6">
                        <div class="input-group input-large">
                             {$months}
                            
                           {$years}
                        </div>
                        <span class="help-block">Escolha o mês e o ano para filtrar</span>
                    </div>
                </div>
                <div class="form-group">
                        <label class="control-label col-md-3">Usuário</label>
                        <div class="col-md-6">
                            <div class="input-group input-large">
                                   <select id="e1" name="employee" class="populate " style="width: 250px;">
                                         {$users}
                                    </select>
                            </div>
                            <span class="help-block">Usuário que recebeu a comissão</span>
                        </div>
                </div>
                <div class="form-group">
                        <label class="control-label col-md-3">Serviço</label>
                        <div class="col-md-6">
                            <div class="input-group input-large">
                                    <select id="e8" name="service" class="populate " style="width: 250px;">
                                          {$services}
                                    </select>
                            </div>
                        </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" type="submit">Filtrar</button>
                <button data-dismiss="modal" class="btn btn-default" type="button">Fechar</button>
            </div>
        </div>
    </div>
</form>
</div>
<!-- modal -->
EOF;
    }

    /**
     * Make row HTML listing mode
     * @param array $Object
     * @return String
     */
    protected function MAKE_LISTING_MODE(array $Object) {
        return '<div class="row">
                    <div class="col-sm-12">
                        <section class="panel">
                            <header class="panel-heading">
                                ' . $this->msg['manager'] . ' ' . $this->msg['plural'] . '
                                <span class="tools pull-right">
                                    <a href="javascript:;" class="fa fa-chevron-down"></a>
                                    <a href="javascript:;" class="fa fa-times"></a>
                                </span>
                            </header>
                            <div class="panel-body">
                                <div class="adv-table">
                                ' . $Object['tools'] . '
                                    <table  class="display table table-bordered table-striped table-condensed checkboxs js-table-sortable" id="dynamic-table">
                                        ' . $Object['body_table'] . '
                                        <tbody>' . $Object['elements_table'] . '</tbody>
                                    </table>
                                </form>    
                                </div>
                            </div>
                        </section>';
    }

    /**
     * Make row HTML listing mode
     * @param array $Object
     * @return String
     */
    protected function MAKE_MAIN(array $Object) {
        $p_month = self::GetParam('month');
        $p_year = self::GetParam('year');
        // check params
        if (isset($p_month, $p_year)) {
            $m = strftime("%B", mktime(0, 0, 0, self::GetParam('month')));
            $month = ucfirst($m) . ' ' . self::GetParam('year');
        } else {
            $month = ucfirst(strftime('%B %Y'));
        }

        $url = URL . FILENAME;
        $percent = self::calculatePercent();

        return <<<EOF
<div class="col-sm-12">
        <header class="panel-heading">
            {$this->msg['manager']} {$this->msg['plural']}
            <span class="tools pull-right">
                <a href="javascript:;" class="fa fa-chevron-down"></a>
                <a href="javascript:;" class="fa fa-times"></a>
            </span>
        </header>
        <div class="panel panel-green">
        <div class="panel-body no-padding">
            <div class="row no-margin">
                <div class="padding-10 col-md-12">
                    <div class="progress progress-striped active progress-sm">
                        <div class="progress-bar progress-bar-success"  role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: {$percent}%"></div> 
                    </div>
                    <span class="text-extra-small" title="porcentagem referente ao fechamento das comissões mensais.">{$percent}% progresso</span>
                </div>
                <div class="padding-10 col-md-12">
                    <div class="pull-left">
                        {$this->CheckDayCloseDay()}
                        <span class="background-dark padding-5 radius-5 text-small inline-block" title="Mês e ano das comissões listadas"><i class="fa fa-calendar-o"></i> {$month}</span>
                    </div>
                    <div class="pull-right">
                        <a href="{$url}#filter" data-toggle="modal" class="btn btn-sm btn-transparent-white"><i class="fa fa-sort-alpha-desc"></i> Filtrar Comissões </a>
                    </div>
                </div>
            </div>
            <div class="tabbable no-margin no-padding partition-dark">
                <ul class="nav nav-tabs" id="myTab2">
                    <li class="active">
                        <a data-toggle="tab" href="#todo_tab_example1">
                            Por Usuário
                        </a>
                    </li>
                    <li class="">
                        <a data-toggle="tab" href="#todo_tab_example2">
                            Por Serviço
                        </a>
                    </li>
                    <li class="">
                        <a data-toggle="tab" href="#todo_tab_example3">
                            Por Cliente
                    </a>
                    </li>
                </ul>
                <div class="tab-content partition-white">
                    <div id="todo_tab_example1" class="tab-pane padding-bottom-5 active">
                        <div class="panel-scroll height-180">
                            <ul class="todo">{$Object['PerUser']}</ul>
                        </div>
                    </div>
                    <div id="todo_tab_example2" class="tab-pane padding-bottom-5">
                        <div class="panel-scroll height-180">
                            <ul class="todo">{$Object['PerService']}</ul>
                        </div>
                    </div>
                   <div id="todo_tab_example3" class="tab-pane padding-bottom-5">
                        <div class="panel-scroll height-180">
                            <ul class="todo">{$Object['PerClient']}</ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>  
EOF;
    }

    /**
     * Tables preview mode
     * @param array $param Query result
     * @return String
     */
    private function _make_list_mode_tables(array $param) {
        $recover_data_param = '?preview_type=' . self::GetParam('preview_type') . '&preview_id=' . self::GetParam('preview_id');
        $status = $param['status'] == true ? '<a href="' . URL . 'dashboard/Reports/commission/MarkSingle/' . $param['id'] . '/0/' . $recover_data_param . '" title="Marca como não pago"><span class="badge bg-success">Pago</span></a>' : '<a href="' . URL . 'dashboard/Reports/commission/MarkSingle/' . $param['id'] . '/1/' . $recover_data_param . '" title="Marca como pago"><span class="badge bg-important">Não Pago</span</a>';
        $valor = number_format($param['value'], 2, '.', ',');
        return '            
            <tr>
                <th>#ID:</th>
                <td>#' . $param['id'] . '</td>
            </tr>
            <tr>
                <th>Estado:</th>
                <td>' . $status . '</td>
            </tr>
            <tr>
                <th>Serviço:</th>
                <td>' . $param['name'] . '</td>
            </tr>
            <tr>
                <th>Cliente:</th>
                <td>' . $this->Client($param['id_client'], false) . '</td>
            </tr>
            <tr>
                <th>Funcionário:</th>
                <td>' . $this->Employee($param['id_employee'], false) . '</td>
            </tr>
            <tr>
                <th>Valor:</th>
                <td>' . $valor . '</td>
            </tr>
            <tr>
                <th>Cadastrado:</th>
                <td>' . strftime('%d de %B, %Y &aacute;s %H:%M:%S', strtotime($param['data'])) . '</td>
            </tr>';
    }

    /**
     * Make row HTML listing mode
     * @param array $data Data Query
     * @return String
     */
    protected function MAKE_PREVIEW_MODE(array $data) {
        $id = Url::getURL($this->URL_ACTION + 1);
        return '
<div class="row">
    <div class="col-sm-12">
        <section class="panel">
            <header class="panel-heading">
                Visualizar ' . $this->msg['singular'] . '
                <span class="tools pull-right">
                    <a href="javascript:;" class="fa fa-chevron-down"></a>
                    <a href="javascript:;" class="fa fa-times"></a>
                </span>
            </header>
            <div class="panel-body">
                <div class="adv-table">


                    <!-- Content Area -->
                    <div id="da-content-area">
                        <div class="grid_4">
                            <div class="da-panel collapsible">
                                <div class="da-panel-header">
                                    <span class="da-panel-title">' . \Func::array_table('notifier', array('id' => $id), 'title') . '</span>
                                </div>

                                <div class="da-panel-content">
                                    <table class="da-table da-detail-view">
                                        <tbody>' . $this->_make_list_mode_tables($data) . '</tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>  
                </div>	
            </div>
            <!-- // Content END -->
    </div>
</div>';
    }

    /**
     * format the date format for the timestamp
     * @access protected
     * @param DateTime $date Date in format dd/mm/YYYY
     * @param array $rep Replace rules
     * @return Timestamp
     * */
    protected function verify_data($date, $rep = array('/', '-')) {
        try {
            $final = str_replace($rep[0], $rep[1], $date);
            $dateTime = new DateTime($final);
            return $dateTime->format('Y-m-d H:i:s');
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * isso é pra enviar um bug com a mascara jquery
     * então verifica se existe . que no caso é float (fração) caso não ache acressenta 00
     * @access private
     * @param $num
     * @return integer
     */
    private function tratar_numero($num) {
        return (strpos($num, '.') == false) ? $num . '00' : $num;
    }

}

/**
 * Class Required CSS and Javascript
 * @todo put in array the path files
 */
class requireds extends ComissionConfig {

    /**
     * Load JS for page listing mode
     * @return string
     */
    private function JS_REQUIRED_LISTING() {
        return <<<EOF
<!-- Placed js at the end of the document so the pages load faster -->
<!--Core js-->
<script src="js/jquery.js"></script>
<script src="bs3/js/bootstrap.min.js"></script>
<script class="include" type="text/javascript" src="js/jquery.dcjqaccordion.2.7.js"></script>
<script src="js/jquery.scrollTo.min.js"></script>
<script src="js/jQuery-slimScroll-1.3.0/jquery.slimscroll.js"></script>
<script src="js/jquery.nicescroll.js"></script>
<!--Easy Pie Chart-->
<script src="js/easypiechart/jquery.easypiechart.js"></script>
<!--Sparkline Chart-->
<script src="js/sparkline/jquery.sparkline.js"></script>
<!--jQuery Flot Chart-->
<script src="js/flot-chart/jquery.flot.js"></script>
<script src="js/flot-chart/jquery.flot.tooltip.min.js"></script>
<script src="js/flot-chart/jquery.flot.resize.js"></script>
<script src="js/flot-chart/jquery.flot.pie.resize.js"></script>

<!--dynamic table-->
<script type="text/javascript" language="javascript" src="js/advanced-datatable/js/jquery.dataTables.js"></script>
<script type="text/javascript" src="js/data-tables/DT_bootstrap.js"></script>
<!--common script init for all pages-->
<script src="js/scripts.js"></script>

<!--dynamic table initialization -->
<script src="js/dynamic_table_init.js"></script>
EOF;
    }

    /**
     * Load CSS for page listing mode
     * @return string
     */
    private function CSS_REQUIRED_LISTING() {
        return <<<EOF
<!--dynamic table-->
<link href="js/advanced-datatable/css/demo_page.css" rel="stylesheet" />
<link href="js/advanced-datatable/css/demo_table.css" rel="stylesheet" />
<link rel="stylesheet" href="js/data-tables/DT_bootstrap.css" />
<link href="css/glyphicons.css" rel="stylesheet" />
<link href="css/estilo.css" rel="stylesheet" />
EOF;
    }

    private function JS_REQUIRED_MAIN() {
        return <<<EOF
<!--Core js-->
<script src="js/jquery.js"></script>
<script src="js/jquery-1.8.3.min.js"></script>
<script src="bs3/js/bootstrap.min.js"></script>
<script src="js/jquery-ui-1.9.2.custom.min.js"></script>
<script class="include" type="text/javascript" src="js/jquery.dcjqaccordion.2.7.js"></script>
<script src="js/jquery.scrollTo.min.js"></script>
<script src="js/easypiechart/jquery.easypiechart.js"></script>
<script src="js/jQuery-slimScroll-1.3.0/jquery.slimscroll.js"></script>
<script src="js/jquery.nicescroll.js"></script>
<script src="js/jquery.nicescroll.js"></script>

<script src="js/bootstrap-switch.js"></script>

<script type="text/javascript" src="js/jquery-multi-select/js/jquery.multi-select.js"></script>
<script type="text/javascript" src="js/jquery-multi-select/js/jquery.quicksearch.js"></script>

<script src="js/jquery-tags-input/jquery.tagsinput.js"></script>

<script src="js/select2/select2.js"></script>
<script src="js/select-init.js"></script>

<!--common script init for all pages-->
<script src="js/scripts.js"></script>

<script src="js/advanced-form.js"></script>
<!--Easy Pie Chart-->
<script src="js/easypiechart/jquery.easypiechart.js"></script>
<!--Sparkline Chart-->
<script src="js/sparkline/jquery.sparkline.js"></script>
<!--jQuery Flot Chart-->
<script src="js/flot-chart/jquery.flot.js"></script>
<script src="js/flot-chart/jquery.flot.tooltip.min.js"></script>
<script src="js/flot-chart/jquery.flot.resize.js"></script>
<script src="js/flot-chart/jquery.flot.pie.resize.js"></script>

        
<!-- start: MAIN JAVASCRIPTS -->
<script src="assets/plugins/perfect-scrollbar/src/jquery.mousewheel.js"></script>
<script src="assets/plugins/perfect-scrollbar/src/perfect-scrollbar.js"></script>
<script src="assets/plugins/jquery.scrollTo/jquery.scrollTo.min.js"></script>
<script src="assets/plugins/ScrollToFixed/jquery-scrolltofixed-min.js"></script>
<script src="assets/js/main.js"></script>


        
<script>
    jQuery(document).ready(function () {
        Main.init();
        SVExamples.init();
        Index.init();
    });
</script>        

EOF;
    }

    private function CSS_REQUIRED_MAIN() {
        return <<<EOF
<link rel="stylesheet" type="text/css" href="js/select2/select2.css" />
<link rel="stylesheet" type="text/css" href="js/jquery-multi-select/css/multi-select.css" />
<!-- start: MAIN CSS -->
<link rel="stylesheet" href="assets/plugins/perfect-scrollbar/src/perfect-scrollbar.css">

<!-- start: CORE CSS -->
<link rel="stylesheet" href="assets/css/styles.css">
<link rel="stylesheet" href="assets/css/styles-responsive.css">
EOF;
    }

    /**
     * Load all JS for page Preview mode
     * @return string
     */
    private function JS_REQUIRED_PREVIEW() {
        return <<<EOF
                <!--Core js-->
                <script src="js/jquery.js"></script>
                <script src="bs3/js/bootstrap.min.js"></script>
                <script class="include" type="text/javascript" src="js/jquery.dcjqaccordion.2.7.js"></script>
                <script src="js/jquery.scrollTo.min.js"></script>
                <script src="js/jQuery-slimScroll-1.3.0/jquery.slimscroll.js"></script>
                <script src="js/jquery.nicescroll.js"></script>
                <!--Easy Pie Chart-->
                <script src="js/easypiechart/jquery.easypiechart.js"></script>
                <!--Sparkline Chart-->
                <script src="js/sparkline/jquery.sparkline.js"></script>
                <!--jQuery Flot Chart-->
                <script src="js/flot-chart/jquery.flot.js"></script>
                <script src="js/flot-chart/jquery.flot.tooltip.min.js"></script>
                <script src="js/flot-chart/jquery.flot.resize.js"></script>
                <script src="js/flot-chart/jquery.flot.pie.resize.js"></script>

                <!--common script init for all pages-->
                <script src="js/scripts.js"></script>
EOF;
    }

    /**
     * Load CSS for page Preview mode
     * @return string
     */
    private function CSS_REQUIRED_PREVIEW() {
        return <<<EOF
                <link href="css/preview.css" rel="stylesheet" />
EOF;
    }

    /**
     * load required files for page main
     * @return Object
     */
    protected function _LOAD_REQUIRED_MAIN() {
        return $this->JS_REQUIRED_MAIN() . $this->CSS_REQUIRED_MAIN();
    }

    /**
     * Load required files for page listing
     * @return Object
     */
    protected function _LOAD_REQUIRED_LISTING() {
        return $this->JS_REQUIRED_LISTING() . $this->CSS_REQUIRED_LISTING();
    }

    /**
     * Load JS for page preview mode
     * @return string
     */
    protected function _REQUIRED_PREVIEW_MODE() {
        return $this->JS_REQUIRED_PREVIEW() . $this->CSS_REQUIRED_PREVIEW();
    }

}
