<?php

namespace Manager\Users;

use Query as Query;
use Developer\Tools\Url as Url;
use Dashboard\Call_JS as Call_JS;
use Func as Func;

/**
 * Classe para visualização
 */
class UsersInsert extends UsersHTML {

    /**
     * Query data results
     * @var array 
     */
    var $Avatar = NULL;

    /**
     * array collection columns and value to insert
     * @var array 
     */
    var $Insert = array();

    /**
     * return user id
     * @var int 
     */
    var $UserID;

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
            // Check demostration mode is active
            Func::CheckDemostrationMode();
            // insert temporary registry because the function insert pic need ID
            $this->Insert_On_DatabaseTMP();
            // insert avatar if exists
            $this->UploadAvatar();
            // check rules
            $this->CheckFields();
            // check occasion to insert data Update database 
            $this->_check_array_insert();
            // insert database
            $this->Insert_On_Database();
        }
    }

    /**
     * handle occasion for update
     * @return Void
     */
    private function _check_array_insert() {
        $password = filter_input(INPUT_POST, 'password');
        $lastname = filter_input(INPUT_POST, 'lname');
        // crypt the user's password with the PHP 5.5's password_hash() function, results in a 60 character
        // hash string. the PASSWORD_DEFAULT constant is defined by the PHP 5.5, or if you are using PHP 5.3/5.4,
        // by the password hashing compatibility library. the third parameter looks a little bit shitty, but that's
        // how those PHP 5.5 functions want the parameter: as an array with, currently only used with 'cost' => XX
        $hash_cost_factor = (defined('HASH_COST_FACTOR') ? HASH_COST_FACTOR : null);
        $user_password_hash = password_hash($password, PASSWORD_DEFAULT, array('cost' => $hash_cost_factor));

        $result = array(
            'user_name' => filter_input(INPUT_POST, 'username'),
            'user_first_name' => filter_input(INPUT_POST, 'firstname'),
            'user_email' => filter_input(INPUT_POST, 'email'),
            'user_password_hash' => $user_password_hash,
            'user_active' => 1,
            'user_account_type' => filter_input(INPUT_POST, 'business'),
            'user_provider_type' => 'DEFAULT'
        );

        if ($this->Avatar !== NULL) {
            $result['user_has_avatar'] = true;
        } else {
            $result['user_has_avatar'] = false;
        }

        if (empty($lastname) || $lastname == '') {
            $result['user_last_name'] = NULL;
        } else {
            $result['user_last_name'] = $lastname;
        }

        $this->Insert = $result;
    }

    /**
     * Insert new registry
     * @access private
     * @return void
     */
    private function Insert_On_Database() {
        $q = new Query();
        $q
                ->update($this->table, $this->Insert)
                ->where_equal_to(
                        array(
                            'user_id' => $this->UserID
                        )
                )
                ->run();

        if (!$q) {
            die('was not possible to insert a new ' . $this->msg['singular']);
        }
        Call_JS::alerta("Novo " . $this->msg['singular'] . " cadastrado com sucesso! ");
        Call_JS::retornar(URL . 'dashboard/Manager/' . $this->page);
    }

    /**
     * Insert new registry temporary
     * @access private
     * @return void
     */
    private function Insert_On_DatabaseTMP() {
        $f = filter_input(INPUT_POST, 'auto');
        $auto = (isset($f) AND $f !== '') ? true : false;
        $q = new Query();
        $q
                ->insert_into($this->table, array(
                    'user_name' => rand(5, 10),
                    'user_email' => rand(5, 30) . '@hotmail.com',
                    'user_active' => 1
                        )
                )
                ->run();

        if (!$q) {
            die('was not possible to insert a new ' . $this->msg['singular']);
        }
        $this->UserID = $q->get_insert_id();
    }

    /**
     * Check fields
     * @return Void
     */
    private function CheckFields() {
        $firstname = filter_input(INPUT_POST, 'firstname');
        $username = filter_input(INPUT_POST, 'username');
        $email = filter_input(INPUT_POST, 'email');
        $password = filter_input(INPUT_POST, 'password');
        $confirm_password = filter_input(INPUT_POST, 'confirm_password');

        switch (true) {
            case(!isset($firstname)):
                $result = 'Insira um nome';
                break;
            case($password !== $confirm_password):
                $result = 'As senhas não estão iguais';
                break;
            case(\Func::_contarReg($this->table, array('user_name' => $username)) > 0):
                $result = 'Esse login não está disponível';
                break;
            case(\Func::_contarReg($this->table, array('user_email' => $email)) > 0):
                $result = 'Esse email não está disponível';
                break;
            default :
                $result = false;
                break;
        }

        if ($result !== false) {
            Call_JS::alerta('Error : ' . $result);
            Call_JS::retornar(URL . 'dashboard/Settings/' . $this->page);
            die($result);
        }
    }

    /**
     * Upload Banner
     * @return boolean
     */
    private function UploadAvatar() {
        $file = $_FILES['img']['error'];
        if (!$file > 0) {
            $handle = new \Upload($_FILES['img']);
            $handle->file_overwrite = true;
            $path = __DIR__ . '/../../../../../public/avatars/';
            $pic = $path . $this->UserID . '.jpg';
            if (file_exists($pic)) {
                unlink($pic);
            }
            if ($handle->uploaded) {
                $handle->image_convert = 'jpg';
                $handle->file_new_name_body = $this->UserID;
                $handle->image_resize = true;
                $handle->image_x = 80;
                $handle->image_ratio_y = true;
                $handle->process($path);
                $nome_da_imagem = $handle->file_dst_name;
                if ($handle->processed) {
                    $this->Avatar = $nome_da_imagem;
                } else {
                    Call_JS::alerta('Error : ' . $handle->error);
                    Call_JS::retornar(URL . 'dashboard/Settings/' . $this->page);
                }
            }
            return true;
        }
        return false;
    }

    /**
     * Method Magic this script is used for buy my weed
     * @access public
     * @return main
     */
    public function __construct() {
        $this->Check_Insert();
        return $this->_build();
    }

}
