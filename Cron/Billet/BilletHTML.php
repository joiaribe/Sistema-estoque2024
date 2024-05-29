<?php

class BilletHTML {

    /**
     * main table
     * 
     * @access public
     * @var string 
     */
    var $table = 'ProgrammerBillet';

    /**
     * Days of week
     * 
     * @access public
     * @var array 
     */
    var $dias_da_semana = array(
        'Domingo',
        'Segunda-Feira',
        'Terça-Feira',
        'Quarta-Feira',
        'Quinta-Feira',
        'Sexta-Feira',
        'Sábado'
    );

    /**
     * Generate Body Mail
     * @param array $fetch Query Result
     * @return boolean|string
     */
    protected function generateOutstandingInstallments($fetch) {
        $q = new Query();
        $q
                ->select()
                ->from($this->table)
                ->where_equal_to(
                        array(
                            'id_receipt' => $fetch['id_receipt']
                        )
                )
                ->order_by('id desc')
                ->run();

        $total = $q->get_selected_count();
        $data = $q->get_selected();

        if (!($data && $total > 0)) {
            return false;
        }

        $result = $this->bodyMailHTML($fetch);
        foreach ($data as $row) {
            $result .= $this->ItenInstallments($row);
        }

        $result.= "</tbody></table><br>";
        return $result;
    }

    /**
     * HTML itens
     * @param array $param
     * @return type
     */
    protected function ItenInstallments(array $param) {
        $bgcolor = !$param['status'] ? 'f5eae9' : 'eeeeee';
        $status = !$param['status'] ? 'Pendente' : 'Pago';
        $date = date('d/m/Y', strtotime($param['data_send']));
        $value = number_format($param['value'], 2, ",", ".");
        return <<<EOF
<tr>
    <td align="center" height="30" bgcolor="#{$bgcolor}"><font style="font-size:14px" face="Helvetica, Arial, sans-serif" color="#4f4f4f">Nº {$param['plots']}x</font></td>
    <td align="center" bgcolor="#{$bgcolor}"><font style="font-size:14px" face="Helvetica, Arial, sans-serif" color="#4f4f4f">R$ {$value}</font></td>
    <td align="center" bgcolor="#{$bgcolor}"><font style="font-size:14px" face="Helvetica, Arial, sans-serif" color="#cc3333">{$status}</font></td>
    <td align="center" bgcolor="#{$bgcolor}"><font style="font-size:14px" face="Helvetica, Arial, sans-serif" color="#4f4f4f">{$date}</font></td>
</tr> 
EOF;
    }

    /**
     * Create personal title
     * @param array $data Query Result
     * @return string
     */
    private function Check_Style(array $data) {
        $total_itens = Func::_contarReg('recibos_itens', array('id_recibo' => $data['id_receipt']));
        $style = Func::array_table('recibos', array('id' => $data['id_receipt']), 'style');

        if ($style == 'Products') {
            $s = $total_itens == 1 ? 'produto' : 'produtos';
            return sprintf("Você comprou %d %s", $total_itens, $s);
        }

        $s = $total_itens == 1 ? 'serviço' : 'serviços';
        return sprintf("Você contratou %d %s", $total_itens, $s);
    }

    /**
     * body table mail
     * 
     * @access protected
     * @param integer $id_receipt ID receipt
     * @param integer $plot Current plot for payment
     * @return string
     */
    protected function bodyMailHTML(array $data) {
        $sex_client = Func::array_table('clientes', array('id' => $data['id_client']), 'Sexo');
        $name_client = Func::array_table('clientes', array('id' => $data['id_client']), 'nome');
        $date = strtotime(Func::array_table('recibos', array('id' => $data['id_receipt']), 'data'));

        $date_formated = $this->dias_da_semana[date('w', $date)] . ", " . strftime("%d/%m/%Y ás %H:%M", $date);

        $pay = date('d/m/Y', strtotime($data['data_send']));
        $sigle = $sex_client == 'F' ? 'Sra' : 'Sr';
        $plot_value = Func::FloatToReal($data['value']);
        return <<<EOF
<table width="600" border="0" cellpadding="0" cellspacing="0">
    <tbody><tr>
            <td width="40">&nbsp;</td>
            <td><font style="font-size:14px" face="Arial, Helvetica, sans-serif"><font color="#00558c"><font color="#4f4f4f"><strong>Olá, $sigle $name_client </strong><br>
                <br>
                <strong> Pagamento da parcela:</strong> Nº {$data['plots']}x <br>
                <strong> Valor: </strong> R$ $plot_value<br>
                <strong> Vencimento: </strong>{$pay} <br> 
                <br>
                {$this->Check_Style($data)} no Sistema Financeiro com o valor total de R$ 1.000,00 <br>
                na {$date_formated}.<br>

                <br>Os números são referentes ao recibo <strong>#{$data['id_receipt']}</strong>.<br>
                <br>
                </font></font></font>
                <table width="520" border="0" cellpadding="0" cellspacing="1">
<tbody>
EOF;
    }

}
