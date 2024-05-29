<?php

use Query as Query;
use Dashboard\Call_JS as Call_JS;

class GlobalSettingsModel {

    /**
     * Current page
     * @var string 
     */
    var $page = 'global';

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
    public function __construct() {
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

    /**
     * Update global settings
     * @return Void
     */
    private function UpdateOnDatabase() {
        $q = new Query;
        $q
                ->update('Configure')
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
        Call_JS::alerta(" Configurações Globais alteradas com sucesso ! ");
        Call_JS::retornar(URL . 'dashboard/Settings/' . $this->page);
    }

    /**
     * Upload Banner
     * @return boolean
     */
    private function UploadBanner() {
        $file = $_FILES['img']['error'];
        if (!$file > 0) {
            $path = 'public/dashboard/images/logo/';
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
                } else {
                    Call_JS::alerta('Error : ' . $handle->error);
                    Call_JS::retornar(URL . 'dashboard/Settings/' . $this->page);
                }
            }
            return true;
        }
        return false;
    }

    private static function GetPost($param) {
        return filter_input(INPUT_POST, $param);
    }

    /**
     * handle occasion for update
     * @return Void
     */
    private function _check_array_update() {
        $d = self::GetPost('status');
        $d_close = self::GetPost('c_day_close');
        $debug = isset($d) ? true : false;
        $s_day_close = !isset($d_close) ? false : true;
        $day_close = !isset($d_close) ? NULL : self::GetPost('day_close');
        $result = array(
            'DEBUG' => $debug,
            'url' => self::GetPost('url'),
            'INTERFACE' => self::GetPost('interface'),
            'NAME' => self::GetPost('name'),
            'COOKIE_RUNTIME' => self::GetPost('time_cookie'),
            'COOKIE_DOMAIN' => self::GetPost('cookie_domain'),
            'ACCOUNT_TYPE_FOR_SALLER' => self::GetPost('beautician'),
            'DAY_CLOSE_COMISSION' => $day_close,
            'STATUS_DAY_CLOSE' => $s_day_close
        );

        if ($this->banner !== NULL) {
            $result['LOGO'] = $this->banner;
        }

        if ($this->Check_Fields() !== true) {
            Call_JS::alerta('Error : Todos os campos com * são obrigatórios !');
            Call_JS::retornar(URL . 'dashboard/Settings/' . $this->page);
        } else {
            $this->Update = $result;
        }
    }

    /**
     * Validate fields
     * @return boolean
     */
    private function Check_Fields() {
        $name = filter_input(INPUT_POST, 'name');
        $url = filter_input(INPUT_POST, 'url');
        $interface = filter_input(INPUT_POST, 'interface');
        $time_cookie = filter_input(INPUT_POST, 'time_cookie');
        $cookie_domain = filter_input(INPUT_POST, 'cookie_domain');
        if (!isset($name) || !isset($url) || !isset($interface) || !isset($time_cookie) || !isset($cookie_domain)) {
            return false;
        } else {
            return true;
        }
    }

}
