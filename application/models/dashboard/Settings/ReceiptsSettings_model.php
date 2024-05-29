<?php

use Dashboard\Call_JS as Call_JS;
use Query as Query;
use Developer\Tools\Url as Url;
use Func as Func;

class ReceiptsSettingsModel {

    /**
     * Current page
     * @var string 
     */
    var $page = 'receipts';

    /**
     * Banner image
     * @var str 
     */
    var $banner = NULL;

    /**
     * Update array
     * @var array 
     */
    var $Update = array();

    /**
     * Magic Method
     * @return Void
     */
    public function __construct($action, $fetch = NULL) {
        if ($action == 'loaded') {
            return $this->FetchData($fetch);
        } else {
            $param = Url::getURL(3);
            if (isset($param) && $param == 'update') {
                // Check demostration mode is active
                Func::CheckDemostrationMode();
                // insert banner if exists
                $this->UploadBanner();
                // check occasion to insert data Update database 
                $this->_check_array_update();
                // Update database
                $this->UpdateOnDatabase();
            }
        }
    }

    /**
     * Get Info
     * @param string $fetch
     * @return mixed
     */
    private function FetchData($fetch) {
        return print Func::array_table('ConfigureInfos', array('id' => 1), $fetch);
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
     * Update global settings
     * @return Void
     */
    private function UpdateOnDatabase() {
        $q = new Query;
        $q
                ->update('ConfigureInfos')
                ->set($this->Update)
                ->where_equal_to(
                        array(
                            'id' => 1
                        )
                )
                ->run();
        if (!$q) {
            die('was not possible to update the global settings');
        }
        Call_JS::alerta(" Configurações de Recibos alteradas com sucesso ! ");
        Call_JS::retornar(URL . 'dashboard/Settings/' . $this->page);
    }

    /**
     * Upload Banner
     * @return boolean
     */
    private function UploadBanner() {
        $file = $_FILES['img']['error'];
        if (!$file > 0) {
            $path = 'public/dashboard/images/recibo/';
            $handle = new \Upload($_FILES['img']);
            if ($handle->uploaded) {
                $encript = substr(md5(microtime()), 0, 32);
                $handle->file_new_name_body = $encript;
                $handle->image_resize = false;
                $handle->image_ratio_y = false;
                $handle->process($path);
                $nome_da_imagem = $handle->file_dst_name;
                if ($handle->processed) {
                    $this->banner = $nome_da_imagem;
                    return true;
                } else {
                    Call_JS::alerta('Error : ' . $handle->error);
                    Call_JS::retornar(URL . 'dashboard/Settings/' . $this->page);
                }
            }
        }
        return false;
    }

    /**
     * Check Address field
     * @param mixed $data Name of field group address info
     * @return mixed
     */
    private function check_address($data) {
        $cep = filter_input(INPUT_POST, 'CEP');
        if (empty($cep)) {
            return NULL;
        }
        return filter_input(INPUT_POST, $data);
    }

    /**
     * handle occasion for update
     * @return Void
     */
    private function _check_array_update() {
        switch (true) {
            case ($this->banner == NULL):
                $result = array(
                    'CNPJ' => $this->check_field('cnpj'),
                    'Cep' => $this->check_field('CEP'),
                    'End' => $this->check_address('rua'),
                    'Num' => $this->check_field('numero'),
                    'Bairro' => $this->check_address('bairro'),
                    'Cidade' => $this->check_address('cidade'),
                    'UF' => $this->check_address('uf'),
                    'email' => $this->check_address('email'),
                    'Fone' => $this->check_field('tel'),
                );
                break;

            case ($this->banner !== NULL):
                $result = array(
                    'CNPJ' => $this->check_field('cnpj'),
                    'Cep' => $this->check_field('CEP'),
                    'End' => $this->check_address('rua'),
                    'Num' => $this->check_field('numero'),
                    'Bairro' => $this->check_address('bairro'),
                    'Cidade' => $this->check_address('cidade'),
                    'UF' => $this->check_address('uf'),
                    'email' => $this->check_address('email'),
                    'Fone' => $this->check_field('tel'),
                    'logo' => $this->banner
                );
                break;
        }

        $this->Update = $result;
    }

}
