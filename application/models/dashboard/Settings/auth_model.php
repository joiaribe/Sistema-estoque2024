<?php

use Query as Query;
use Dashboard\Call_JS as Call_JS;

class authModel {

    /**
     * Current page
     * @var string 
     */
    var $page = 'auth';

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
                ->update('ConfigureMail')
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
     * Get value post metthod
     * @param mixed $param
     * @param string $returnMetthod Type data return
     * @access protected
     * @return boolean|mixed
     */
    protected static function GetParam($param, $returnMetthod = 'object') {
        $c = filter_input(INPUT_POST, $param);
        if ($c) {
            return ($returnMetthod !== 'object') ? true : $c;
        }
        return false;
    }

    /**
     * verifies that it is in the authentication mode
     * @param type $param
     * @return type
     */
    private static function isAuth($param) {
        if (self::GetParam('auth', 'bool')) {
            $data = self::GetParam($param, 'bool');
            return !($data) ? NULL : self::GetParam($param);
        }
        return NULL;
    }

    /**
     * Is HTML get correct name field
     * @param string $param Used to diff values
     * @return string
     */
    private static function isHTML($param = 'top') {
        if (self::GetParam('c_html', 'bool')) {
            if ($param == 'top') {
                return self::GetParam('assing_top_html');
            }
            return self::GetParam('assing_button_html');
        } else {
            if ($param == 'top') {
                return self::GetParam('assing_top');
            }
            return self::GetParam('assing_button');
        }
    }

    /**
     * handle occasion for update
     * @return Void
     */
    private function _check_array_update() {
        $result = array(
            'AUTH' => self::GetParam('auth', 'bool'),
            'SMTP' => self::GetParam('smtp'),
            'SMTP_SECURE' => self::isAuth('cripter'),
            'USER' => self::isAuth('email'),
            'PASS' => self::isAuth('password'),
            'PORT' => self::GetParam('port'),
            'CC' => self::GetParam('cc', NULL),
            'BCC' => self::GetParam('bcc', NULL),
            'HTML' => self::GetParam('c_html', 'bool'),
            'BUTTON_SIGNATURE' => self::isHTML('button'),
            'TOP_SIGNATURE' => self::isHTML()
        );

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
        if (self::GetParam('auth', 'bool')) {
            if (
                    !self::GetParam('smtp', 'bool') ||
                    !self::GetParam('email', 'bool') ||
                    !self::GetParam('password', 'bool') ||
                    !self::GetParam('port', 'bool')
            ) {
                return false;
            }
            return true;
        }
        return true;
    }

}
