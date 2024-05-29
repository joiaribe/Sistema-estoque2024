<?php

namespace Manager\Users;

use Query as Query;
use Developer\Tools\Url as Url;
use Dashboard\Call_JS as Call_JS;
use Func as Func;

class UsersUpdate extends UsersHTML {

    /**
     * Query data results
     * @var array 
     */
    var $query = array();

    /**
     * Query data results
     * @var array 
     */
    var $Avatar = NULL;

    /**
     * array collection columns and value to Update
     * @var array 
     */
    var $Update = array();

    /**
     * Builds page insert new registry and makes form HTML
     * @access private
     * @return object
     */
    private function _build() {
        $this->Query();
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
                            'user_id' => $param
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
             // Check demostration mode is active
            Func::CheckDemostrationMode();
            // insert avatar if exists
            $this->UploadAvatar();
            // check rules
            $this->CheckFields();
            // check occasion to insert data Update database 
            $this->_check_array_update();
            // update database
            $this->Update_On_Database();
        }
    }

    /**
     * handle occasion for update
     * @return Void
     */
    private function _check_array_update() {
        $c_pass = filter_input(INPUT_POST, 'c_pass');
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
            'user_account_type' => filter_input(INPUT_POST, 'business'),
        );

        if (isset($c_pass)) {
            $result['user_password_hash'] = $user_password_hash;
        }

        if ($this->Avatar !== NULL) {
            $result['user_has_avatar'] = true;
        }

        if (empty($lastname) || $lastname == '') {
            $result['user_last_name'] = NULL;
        } else {
            $result['user_last_name'] = $lastname;
        }

        $this->Update = $result;
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
            $pic = $path . $this->query['user_id'] . '.jpg';
            if (file_exists($pic)) {
                unlink($pic);
            }
            if ($handle->uploaded) {
                $handle->image_convert = 'jpg';
                $handle->file_new_name_body = $this->query['user_id'];
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
     * Check exist login
     * @param String $login
     * @return boolean
     */
    private function Check_login($login) {
        $q = new Query;
        $q
                ->select()
                ->from('users')
                ->where_equal_to(
                        array(
                            'user_name' => $login
                        )
                )
                ->where_not_equal_to(
                        array(
                            'user_id' => Url::getURL($this->URL_ACTION + 1)
                        )
                )
                ->run();
        #$q->show();
        $count = $q->get_selected_count();
        return !($count > 0) ? true : false;
    }

    /**
     * Check exist email
     * @param String $email
     * @return boolean
     */
    private function Check_email($email) {
        $q = new Query;
        $q
                ->select()
                ->from('users')
                ->where_equal_to(
                        array(
                            'user_email' => $email
                        )
                )
                ->where_not_equal_to(
                        array(
                            'user_id' => Url::getURL($this->URL_ACTION + 1)
                        )
                )
                ->run();
        $count = $q->get_selected_count();
        return !($count > 0) ? true : false;
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
        $c_pass = filter_input(INPUT_POST, 'c_pass');

        switch (true) {
            case(!isset($firstname)):
                $result = 'Insira um nome';
                break;
            case(isset($c_pass) && $password !== $confirm_password):
                $result = 'As senhas não estão iguais';
                break;
            case($this->Check_login($username) == false):
                $result = 'Esse login não está disponível';
                break;
            case($this->Check_email($email) == false):
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
     * Insert new registry
     * @access private
     * @return Query
     */
    private function Update_On_Database() {
        $param = Url::getURL($this->URL_ACTION + 1);
        $q = new Query;
        $q
                ->update($this->table)
                ->set($this->Update)
                ->where_equal_to(
                        array(
                            'user_id' => $param
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
     * Method Magic this script is used for buy my weed
     * @access public
     * @return main
     */
    public function __construct() {
        return $this->_build();
    }

}
