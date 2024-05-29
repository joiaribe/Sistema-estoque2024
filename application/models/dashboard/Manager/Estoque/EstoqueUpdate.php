<?php

namespace Manager\Estoque;

use Query as Query;
use Developer\Tools\Url as Url;
use Dashboard\Call_JS as Call_JS;

class EstoqueUpdate extends EstoqueHTML {

    /**
     * Query data results
     * @var array 
     */
    var $query = array();

    /**
     * product image
     * @var string 
     */
    var $pic = NULL;

    /**
     * Builds page insert new registry and makes form HTML
     * @access private
     * @return object
     */
    private function _build() {
        $this->Query();
        $this->loop_fornecedor();
        $this->loop_marcadores();
        $this->Check_Update();
        return print
                $this->HTML_Update($this->query) .
                $this->_LOAD_REQUIRED_UPDATE($this->query);
    }

    /**
     * Build Query
     * @return Void
     */
    private function Query() {
        $param = Url::getURL($this->URL_ACTION + 1);
        $q = new Query();
        $q
                ->select()
                ->from($this->table)
                ->where_equal_to(
                        array(
                            'id' => $param
                        )
                )
                ->limit(1)
                ->run();
        $data = $q->get_selected();
        $total = $q->get_selected_count();
        if (!$total > 0) {
            Call_JS::alerta('Erro na consulta');
            Call_JS::retornar(URL . 'dashboard/Mov/' . $this->page);
        } else {
            $this->query = $data;
        }
    }

    /**
     * Check param for inset a new registry
     * @return Void
     */
    private function Check_Update() {
        $param = Url::getURL($this->URL_ACTION + 2);
        if ($param == 'update') {
            $this->Insert_banner();
            $this->Update_On_Database();
        }
    }

    /**
     * Check if pic was changed
     * @return type
     */
    private function CheckColumnsUpdate() {
        $arr = array(
            'id_fornecedor' => filter_input(INPUT_POST, 'for'),
            'nome' => filter_input(INPUT_POST, 'name'),
            'valor' => \Func::RealToFloat(\Request::post('money')),
            'valor_original' => \Func::RealToFloat(\Request::post('valor')),
            'marcador' => filter_input(INPUT_POST, 'mar'),
            'quantidade' => filter_input(INPUT_POST, 'qnt'),
            'descri' => $this->check_field('comment'),
        );
        if ($this->pic !== NULL) {
            $arr['foto'] = $this->pic;
        }
        return $arr;
    }

    /**
     * Insert new registry
     * @access private
     * @return Query
     */
    private function Update_On_Database() {
        $param = Url::getURL($this->URL_ACTION + 1);


        $q = new Query;
        $q
                ->update($this->table)
                ->set($this->CheckColumnsUpdate())
                ->where_equal_to(
                        array(
                            'id' => $param
                        )
                )
                ->run();
        if (!$q) {
            die('was not possible to insert a new ' . $this->msg['singular']);
        }
        Call_JS::alerta($this->msg['singular'] . " alterado com sucesso! ");
        Call_JS::retornar(URL . 'dashboard/Manager/' . $this->page);
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
     * really ? I need explain ? 
     * @param mixed $value
     * @return mixed
     */
    private function check_field($value) {
        $field = filter_input(INPUT_POST, $value);
        return (isset($field) AND $field !== '') ? $field : NULL;
    }

    /**
     * Method Magic this script is used for buy my weed
     * @access public
     * @return main
     */
    public function __construct() {
        return $this->_build();
    }

}
