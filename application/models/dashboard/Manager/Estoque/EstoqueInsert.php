<?php

namespace Manager\Estoque;

use Query as Query;
use Developer\Tools\Url as Url;
use Dashboard\Call_JS as Call_JS;

/**
 * Classe para visualização
 */
class EstoqueInsert extends EstoqueHTML {

    /**
     * Query data results
     * @var array 
     */
    var $pic = NULL;

    /**
     * main table
     * @var string 
     */
    var $table = 'produtos';

    /**
     * Builds page insert new registry and makes form HTML
     * @access private
     * @return object
     */
    private function _build() {
        return print
                $this->HTML_Insert_New() .
                $this->_LOAD_REQUIRED_INSERT();
    }

    /**
     * Check param for inset a new registry
     * @return Void
     */
    private function Check_Insert() {
        $param = Url::getURL($this->URL_ACTION + 1);
        if ($param == 'new') {
            $this->Insert_banner();
            $this->Insert_On_Database();
        }
    }

    /**
     * Insert new banner
     * @return String
     */
    private function Insert_banner() {
        $file = $_FILES['img']['error'];
        if (!$file > 0) {
            $path = 'public/images/produtos/';
            $handle = new \Upload($_FILES['img']);
            if ($handle->uploaded) {
                $encript = substr(md5(microtime()), 0, 32) . '_n';
                $handle->file_new_name_body = $encript;
                $handle->image_resize = false;
                $handle->image_ratio_y = false;
                $handle->process($path);
                $nome_da_imagem = $handle->file_dst_name;
                if ($handle->processed) {
                    $this->pic = $nome_da_imagem;
                } else {
                    Call_JS::alerta('Error : ' . $handle->error);
                    Call_JS::retornar(URL . 'dashboard/Manager/' . $this->page);
                }
            }
        } else {
            $this->pic = NULL;
        }
    }

    /**
     * Check card name
     * @return mixed
     */
    private function check_card_name() {
        $metthod = filter_input(INPUT_POST, 'metthod');
        $card = filter_input(INPUT_POST, 'card_name');
        if ($metthod !== 'Cartão de Crédito' && $metthod !== 'Débito Automático') {
            return NULL;
        } else {
            return $card;
        }
    }

    /**
     * Insert Expense in table out_product
     * @param integer $idproduct ID of product added
     * @return Void
     */
    private function InsertExpense($idproduct) {
        $value = \Func::RealToFloat(filter_input(INPUT_POST, 'valor'));
        $qnt = filter_input(INPUT_POST, 'qnt');
        $where = array('id' => $idproduct);
        $s = filter_input(INPUT_POST, 'status');
        $status = isset($s) ? true : false;

        $q = new Query();
        $q
                ->insert_into(
                        'output_product', array(
                    'id_product' => $idproduct,
                    'name' => \Func::array_table($this->table, $where, 'nome'),
                    'descri' => \Func::array_table($this->table, $where, 'descri'),
                    'qnt' => $qnt,
                    'value' => $value * $qnt,
                    'metthod' => filter_input(INPUT_POST, 'metthod'),
                    'status' => $status,
                    'card_name' => $this->check_card_name('card_name'),
                    'card_agence' => $this->check_field('agencia'),
                    'card_number' => $this->check_field('card_number'),
                    'cheque_number' => $this->check_field('cheque_number')
                        )
                )
                ->run();
        if (!$q) {
            die('was not possible to insert the expense');
        }
    }

    /**
     * Insert new registry
     * @access private
     * @return void
     */
    private function Insert_On_Database() {
        $f = filter_input(INPUT_POST, 'auto');
        $auto = (isset($f) AND $f !== '') ? true : false;
        $q = new Query();
        $q
                ->insert_into(
                        $this->table, array(
                    'autoexpense' => $auto,
                    'id_fornecedor' => filter_input(INPUT_POST, 'for'),
                    'nome' => filter_input(INPUT_POST, 'name'),
                    'valor' =>  \Func::RealToFloat(\Request::post('money')),
                    'valor_original' => \Func::RealToFloat(\Request::post('valor')),
                    'marcador' => filter_input(INPUT_POST, 'mar'),
                    'quantidade' => filter_input(INPUT_POST, 'qnt'),
                    'descri' => $this->check_field('comment'),
                    'foto' => $this->pic
                        )
                )
                ->run();
        if (!$q) {
            die('was not possible to insert a new ' . $this->msg['singular']);
        }
        if ($auto == true) {
            $this->InsertExpense($q->get_insert_id());
        }
        Call_JS::alerta("Novo " . $this->msg['singular'] . " cadastrado com sucesso! ");
        Call_JS::retornar(URL . 'dashboard/Manager/' . $this->page);
    }

    /**
     * really ? I need explain ? 
     * @param mixed $value
     * @return mixed
     */
    private function check_field($value) {
        $field = filter_input(INPUT_POST, $value);
        return (isset($field) AND $field !== '') ? $field : NULL;
    }

    /**
     * check link is null
     * @param mixed $value
     * @return mixed
     */
    private function check_field_target($value) {
        $lin = filter_input(INPUT_POST, 'question');
        if (isset($lin) AND $lin !== '') {
            return filter_input(INPUT_POST, $value);
        } else {
            return NULL;
        }
    }

    /**
     * Check Address field
     * @param mixed $data Name of field group address info
     * @return mixed
     */
    private function check_address($data) {
        $cep = filter_input(INPUT_POST, 'cep');
        if (empty($cep)) {
            return NULL;
        }
        return filter_input(INPUT_POST, $data);
    }

    /**
     * Method Magic this script is used for buy my weed
     * @access public
     * @return main
     */
    public function __construct() {
        $this->loop_fornecedor();
        $this->loop_marcadores();
        $this->Check_Insert();
        return $this->_build();
    }

}
