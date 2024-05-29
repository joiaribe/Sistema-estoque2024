<?php

use OpenBoleto\Banco\Bradesco;
use OpenBoleto\Banco\Caixa;
use OpenBoleto\Banco\Itau;
use OpenBoleto\Agente;

require 'BilletHTML.php';

class GenerateBillet extends BilletHTML {

    /**
     * dir compĺeto do boleto em pdf
     * @var string
     */
    var $dir_billet = NULL;
    var $billet_html;

    /**
     * Método Mágico
     * @param array $data Resultado da consulta
     */
    public function __construct(array $data) {
        $this->SwithBanco($data);
    }

    /**
     * Qual banco é ? de onde eles vem ? o que eles comem ? onde eles vivem ?
     * 
     * @access private
     * @param array $data Resultado da consulta
     * @return void
     */
    private function SwithBanco($data) {
        $banco = Func::array_table('ConfigureFonts', array('id' => $data['id_font']), 'banco');
        switch ($banco) {
            case 'Banco Do Brasil':
                $this->generateBB($data);
                break;
            case 'Bradesco':
                $this->generateBradesco($data);
                break;
            case 'Caixa':
                $this->generateCaixa($data);
                break;
            case 'Itau':
                $this->generateItau($data);
                break;
            default:
                break;
        }
    }

    /**
     * Pega itens do receibo
     * @param type $id Id do recibo
     * @return array
     */
    private function pegaReciboItens($id) {
        $q = new Query();
        $q
                ->select()
                ->from('recibos_itens')
                ->where_equal_to(
                        array(
                            'id_recibo' => $id
                        )
                )
                ->run();
        $total = $q->get_selected_count();
        $data = $q->get_selected();

        if (!($data && $total > 0)) {
            die('Não foi possivel pega os itens do recibo');
        }
        $return = array();
        foreach ($data as $row) {
            $return[] = $row['nome'] . ' ' . $row['qnt'] . 'x';
        }
        return $return;
    }

    /**
     * Pega informações comerciais
     * @param mixed $param Coluna que irá retornar
     * @return mixed
     */
    private function GetInfo($param) {
        $q = new Query();
        $q
                ->select()
                ->from('ConfigureInfos')
                ->where_equal_to(
                        array(
                            'id' => 1
                        )
                )
                ->limit(1)
                ->run();
        $total = $q->get_selected_count();
        $data = $q->get_selected();

        if (!($data && $total > 0)) {
            die('não foi possivel achar as informações comerciais, verifique se existe um registro com o id 1');
        }

        return $data[$param];
    }

    /**
     * Gerar boleto para o Banco do Brasil
     * 
     * @access private
     * @param array $data Resultado da consulta
     * @return void
     */
    private function generateBB(array $data) {
        $interval = Func::array_table('recibos', array('id' => $data['id_receipt']), '_interval');
        $_interval_period = Func::array_table('recibos', array('id' => $data['id_receipt']), '_interval_period');

        if ($interval !== NULL) {
            $date = new DateTime($data['data_send']);
            $date->modify(sprintf("+%d %s", $interval, $_interval_period));
            $final_date = $date->format('Y-m-d');
        } else {
            $final_date = $data['data_send'];
        }

        $agence = Func::array_table('ConfigureFonts', array('id' => $data['id_font']), 'agencia');
        $conta = Func::array_table('ConfigureFonts', array('id' => $data['id_font']), 'conta');
        $carteira = Func::array_table('ConfigureFonts', array('id' => $data['id_font']), 'carteira');
        $codigoCliente = Func::array_table('ConfigureFonts', array('id' => $data['id_font']), 'codigoCliente');

        $cliente_nome = Func::array_table('clientes', array('id' => $data['id_client']), 'nome');
        $cliente_cep = Func::array_table('clientes', array('id' => $data['id_client']), 'Cep');
        $cliente_uf = Func::array_table('clientes', array('id' => $data['id_client']), 'UF');
        $client_cidade = Func::array_table('clientes', array('id' => $data['id_client']), 'Cidade');
        $client_end = Func::array_table('clientes', array('id' => $data['id_client']), 'End');
        $client_num = Func::array_table('clientes', array('id' => $data['id_client']), 'Num');
        $client_cpf = Func::array_table('clientes', array('id' => $data['id_client']), 'Cpf');

        $sacado = new Agente($cliente_nome, $client_cpf, $client_end . ' Nº ' . $client_num, $cliente_cep, $client_cidade, $cliente_uf);
        $cedente = new Agente(WEB_SITE_CEO_NAME, $this->GetInfo('CNPJ'), $this->GetInfo('End'), $this->GetInfo('Cep'), $this->GetInfo('Cidade'), $this->GetInfo('UF'));

        $boleto = new Itau(array(
            // Parâmetros obrigatórios
            'dataVencimento' => new DateTime($final_date),
            'valor' => $data['value'],
            'sequencial' => 12345678, // 8 dígitos
            'sacado' => $sacado,
            'cedente' => $cedente,
            'agencia' => $agence, // 4 dígitos
            'carteira' => $carteira, // 3 dígitos
            'conta' => $conta, // 5 dígitos
            // Parâmetro obrigatório somente se a carteira for
            // 107, 122, 142, 143, 196 ou 198
            'codigoCliente' => $codigoCliente, // 5 dígitos
            'numeroDocumento' => 1234567, // 7 dígitos
            // Parâmetros recomendáveis
            'logoPath' => URL . 'public/dashboard/images/recibo/' . $this->GetInfo('logo'), // Logo da sua empresa
            'contaDv' => 2,
            'agenciaDv' => 1,
            'descricaoDemonstrativo' => $this->pegaReciboItens($data['id_receipt']),
        ));
        $name = 'boleto_' . $data['id_receipt'] . '_' . $data['plots'] . '.pdf';
        $pdf_options = array(
            "source_type" => 'html',
            "source" => $boleto->getOutput(),
            "action" => 'save',
            "save_directory" => 'pdf',
            "file_name" => $name);
        $this->dir_billet = $name;
        // CALL THE phpToPDF FUNCTION WITH THE OPTIONS SET ABOVE
        phptopdf($pdf_options);
    }

    /**
     * Gerar boleto para o banco Bradesco
     * 
     * @access private
     * @param array $data Resultado da consulta
     * @return void
     */
    private function generateBradesco(array $data) {
        $interval = Func::array_table('recibos', array('id' => $data['id_receipt']), '_interval');
        $_interval_period = Func::array_table('recibos', array('id' => $data['id_receipt']), '_interval_period');

        if ($interval !== NULL) {
            $date = new DateTime($data['data_send']);
            $date->modify(sprintf("+%d %s", $interval, $_interval_period));
            $final_date = $date->format('Y-m-d');
        } else {
            $final_date = $data['data_send'];
        }

        $agence = Func::array_table('ConfigureFonts', array('id' => $data['id_font']), 'agencia');
        $conta = Func::array_table('ConfigureFonts', array('id' => $data['id_font']), 'conta');
        $carteira = Func::array_table('ConfigureFonts', array('id' => $data['id_font']), 'carteira');

        $cliente_nome = Func::array_table('clientes', array('id' => $data['id_client']), 'nome');
        $cliente_cep = Func::array_table('clientes', array('id' => $data['id_client']), 'Cep');
        $cliente_uf = Func::array_table('clientes', array('id' => $data['id_client']), 'UF');
        $client_cidade = Func::array_table('clientes', array('id' => $data['id_client']), 'Cidade');
        $client_end = Func::array_table('clientes', array('id' => $data['id_client']), 'End');
        $client_num = Func::array_table('clientes', array('id' => $data['id_client']), 'Num');
        $client_cpf = Func::array_table('clientes', array('id' => $data['id_client']), 'Cpf');

        $sacado = new Agente($cliente_nome, $client_cpf, $client_end . ' Nº ' . $client_num, $cliente_cep, $client_cidade, $cliente_uf);
        $cedente = new Agente(WEB_SITE_CEO_NAME, $this->GetInfo('CNPJ'), $this->GetInfo('End'), $this->GetInfo('Cep'), $this->GetInfo('Cidade'), $this->GetInfo('UF'));

        $boleto = new Bradesco(array(
            // Parâmetros obrigatórios
            'dataVencimento' => new DateTime($final_date),
            'valor' => $data['value'],
            'sequencial' => 75896452, // Até 11 dígitos
            'sacado' => $sacado,
            'cedente' => $cedente,
            'agencia' => $agence, // Até 4 dígitos
            'carteira' => $carteira, // 3, 6 ou 9
            'conta' => $conta, // Até 7 dígitos
            // Parâmetros recomendáveis
            'logoPath' => URL . 'public/dashboard/images/recibo/' . $this->GetInfo('logo'), // Logo da sua empresa
            'contaDv' => 2,
            'agenciaDv' => 1,
            'descricaoDemonstrativo' => $this->pegaReciboItens($data['id_receipt']),
        ));
        $name = 'boleto_' . $data['id_receipt'] . '_' . $data['plots'] . '.pdf';
        $pdf_options = array(
            "source_type" => 'html',
            "source" => $boleto->getOutput(),
            "action" => 'save',
            "save_directory" => __DIR__ . '/pdf',
            "file_name" => $name);
        $this->dir_billet = $name;
        // CALL THE phpToPDF FUNCTION WITH THE OPTIONS SET ABOVE
        phptopdf($pdf_options);
    }

    /**
     * Gerar boleto para o banco Caixa Econômica Federal
     * 
     * @access private
     * @param array $data Resultado da consulta
     * @return void
     */
    private function generateCaixa(array $data) {
        $interval = Func::array_table('recibos', array('id' => $data['id_receipt']), '_interval');
        $_interval_period = Func::array_table('recibos', array('id' => $data['id_receipt']), '_interval_period');

        if ($interval !== NULL) {
            $date = new DateTime($data['data_send']);
            $date->modify(sprintf("+%d %s", $interval, $_interval_period));
            $final_date = $date->format('Y-m-d');
        } else {
            $final_date = $data['data_send'];
        }

        $agence = Func::array_table('ConfigureFonts', array('id' => $data['id_font']), 'agencia');
        $conta = Func::array_table('ConfigureFonts', array('id' => $data['id_font']), 'conta');
        $carteira = Func::array_table('ConfigureFonts', array('id' => $data['id_font']), 'carteira');

        $cliente_nome = Func::array_table('clientes', array('id' => $data['id_client']), 'nome');
        $cliente_cep = Func::array_table('clientes', array('id' => $data['id_client']), 'Cep');
        $cliente_uf = Func::array_table('clientes', array('id' => $data['id_client']), 'UF');
        $client_cidade = Func::array_table('clientes', array('id' => $data['id_client']), 'Cidade');
        $client_end = Func::array_table('clientes', array('id' => $data['id_client']), 'End');
        $client_num = Func::array_table('clientes', array('id' => $data['id_client']), 'Num');
        $client_cpf = Func::array_table('clientes', array('id' => $data['id_client']), 'Cpf');

        $sacado = new Agente($cliente_nome, $client_cpf, $client_end . ' Nº ' . $client_num, $cliente_cep, $client_cidade, $cliente_uf);
        $cedente = new Agente(WEB_SITE_CEO_NAME, $this->GetInfo('CNPJ'), $this->GetInfo('End'), $this->GetInfo('Cep'), $this->GetInfo('Cidade'), $this->GetInfo('UF'));

        $boleto = new Caixa(array(
            // Parâmetros obrigatórios
            'dataVencimento' => new DateTime($final_date),
            'valor' => $data['value'],
            'sequencial' => 1234567,
            'sacado' => $sacado,
            'cedente' => $cedente,
            'agencia' => $agence, // Até 4 dígitos
            'carteira' => $carteira, // SR => Sem Registro ou RG => Registrada
            'conta' => $conta, // Até 6 dígitos
            // Parâmetros recomendáveis
            'logoPath' => URL . 'public/dashboard/images/recibo/' . $this->GetInfo('logo'), // Logo da sua empresa
            'contaDv' => 2,
            'agenciaDv' => 1,
            'descricaoDemonstrativo' =>
            $this->pegaReciboItens($data['id_receipt']),
        ));
        $name = 'boleto_' . $data['id_receipt'] . '_' . $data['plots'] . '.pdf';
        $pdf_options = array(
            "source_type" => 'html',
            "source" => $boleto->getOutput(),
            "action" => 'save',
            "save_directory" => __DIR__ . '/pdf',
            "file_name" => $name);
        $this->dir_billet = $name;
        $this->billet_html = $boleto->getOutput();

        // CALL THE phpToPDF FUNCTION WITH THE OPTIONS SET ABOVE
        phptopdf($pdf_options);
    }

    /**
     * Gerar boleto para o banco Itaú
     * 
     * @access private
     * @param array $data Resultado da consulta
     * @return void
     */
    private function generateItau(array $data) {
        $interval = Func::array_table('recibos', array('id' => $data['id_receipt']), '_interval');
        $_interval_period = Func::array_table('recibos', array('id' => $data['id_receipt']), '_interval_period');

        if ($interval !== NULL) {
            $date = new DateTime($data['data_send']);
            $date->modify(sprintf("+%d %s", $interval, $_interval_period));
            $final_date = $date->format('Y-m-d');
        } else {
            $final_date = $data['data_send'];
        }

        $agence = Func::array_table('ConfigureFonts', array('id' => $data['id_font']), 'agencia');
        $conta = Func::array_table('ConfigureFonts', array('id' => $data['id_font']), 'conta');
        $carteira = Func::array_table('ConfigureFonts', array('id' => $data['id_font']), 'carteira');
        $codigoCliente = Func::array_table('ConfigureFonts', array('id' => $data['id_font']), 'codigoCliente');

        $cliente_nome = Func::array_table('clientes', array('id' => $data['id_client']), 'nome');
        $cliente_cep = Func::array_table('clientes', array('id' => $data['id_client']), 'Cep');
        $cliente_uf = Func::array_table('clientes', array('id' => $data['id_client']), 'UF');
        $client_cidade = Func::array_table('clientes', array('id' => $data['id_client']), 'Cidade');
        $client_end = Func::array_table('clientes', array('id' => $data['id_client']), 'End');
        $client_num = Func::array_table('clientes', array('id' => $data['id_client']), 'Num');
        $client_cpf = Func::array_table('clientes', array('id' => $data['id_client']), 'Cpf');


        $sacado = new Agente($cliente_nome, $client_cpf, $client_end . ' Nº ' . $client_num, $cliente_cep, $client_cidade, $cliente_uf);
        $cedente = new Agente(WEB_SITE_CEO_NAME, $this->GetInfo('CNPJ'), $this->GetInfo('End'), $this->GetInfo('Cep'), $this->GetInfo('Cidade'), $this->GetInfo('UF'));

        $boleto = new Itau(array(
            // Parâmetros obrigatórios
            'dataVencimento' => new DateTime($final_date),
            'valor' => $data['value'],
            'sequencial' => 12345678, // 8 dígitos
            'sacado' => $sacado,
            'cedente' => $cedente,
            'agencia' => $agence, // 4 dígitos
            'carteira' => $carteira, // 3 dígitos
            'conta' => $conta, // 5 dígitos
            // Parâmetro obrigatório somente se a carteira for
            // 107, 122, 142, 143, 196 ou 198
            'codigoCliente' => $codigoCliente, // 5 dígitos
            'numeroDocumento' => 1234567, // 7 dígitos
            // Parâmetros recomendáveis
            'logoPath' => URL . 'public/dashboard/images/recibo/' . $this->GetInfo('logo'), // Logo da sua empresa
            'contaDv' => 2,
            'agenciaDv' => 1,
            'descricaoDemonstrativo' => $this->pegaReciboItens($data['id_receipt']),
            'instrucoes' => array(// Até 8
                'Após o dia 30/11 cobrar 2% de mora e 1% de juros ao dia.',
                'Não receber após o vencimento.',
            ),
        ));

        $name = 'boleto_' . $data['id_receipt'] . '_' . $data['plots'] . '.pdf';
        $pdf_options = array(
            "source_type" => 'html',
            "source" => $boleto->getOutput(),
            "action" => 'save',
            "save_directory" => __DIR__ . '/pdf',
            "file_name" => $name);
        $this->dir_billet = $name;
        // CALL THE phpToPDF FUNCTION WITH THE OPTIONS SET ABOVE
        phptopdf($pdf_options);
    }

}
