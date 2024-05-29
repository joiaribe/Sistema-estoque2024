<?php

use Query as Query;
use Dashboard\Call_JS as Call_JS;
use Developer\Tools\Notifier as Notifier;
use Func as Func;

class PerfilSettingsModel extends Notifier {

    /**
     * Current page
     * @var string 
     */
    var $page = 'perfil';

    /**
     * Avatar image
     * @var str 
     */
    var $Avatar = NULL;

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
            // insert avatar if exists
            $this->UploadAvatar();
            // check rules
            $this->CheckFields();
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
                ->update('users')
                ->set($this->Update)
                ->where_equal_to(
                        array(
                            'user_id' => Session::get('user_id')
                        )
                )
                ->run();
        if (!$q) {
            die('was not possible to update the global settings');
        }
        $now = strftime('%A, %d de %B de %Y ás %H:%M', strtotime('now'));
        $Text = 'Você alterou informações do seu perfil ' . $now . ' <br> <p> Essa é uma notificação de segurança</p>';
        $this->InsertNotifierById('Perfil alterado', $Text);
        Call_JS::alerta(" Perfil alterado com sucesso ! ");
        Call_JS::retornar(URL . 'dashboard/Settings/' . $this->page);
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
            $path = __DIR__ . '/../../../public/avatars/';
            $pic = $path . Session::get('user_id') . '.jpg';
            if (file_exists($pic)) {
                unlink($pic);
            }
            if ($handle->uploaded) {
                $handle->image_convert = 'jpg';
                $handle->file_new_name_body = Session::get('user_id');
                $handle->image_resize = false;
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
                            'user_id' => Session::get('user_id')
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
                            'user_id' => Session::get('user_id')
                        )
                )
                ->run();
        $count = $q->get_selected_count();
        return !($count > 0) ? true : false;
    }

    /**
     * Check password hash
     * @param String $pass Password
     * @param Int $idUser IdUser, use NULL for get id own
     * @return boolean
     */
    private function Check_pass($pass, $idUser = NULL) {
        $idUser = $idUser == NULL ? Session::get('user_id') : $idUser;
        $q = new Query;
        $q
                ->select()
                ->from('users')
                ->where_equal_to(
                        array(
                            'user_id' => Session::get('user_id')
                        )
                )
                ->limit(1)
                ->run();
        $count = $q->get_selected_count();
        $data = $q->get_selected();

        if (!($data && $count > 0)) {
            return false;
        }
        // check if hash of provided password matches the hash in the database
        if (password_verify($pass, $data['user_password_hash'])) {
            return true;
        } else {
            return false;
        }
    }

    private function CheckFields() {
        $firstname = filter_input(INPUT_POST, 'firstname');
        $username = filter_input(INPUT_POST, 'username');
        $email = filter_input(INPUT_POST, 'email');
        $c_pass = filter_input(INPUT_POST, 'c_pass');
        $pass = filter_input(INPUT_POST, 'pass');
        $password = filter_input(INPUT_POST, 'password');
        $confirm_password = filter_input(INPUT_POST, 'confirm_password');

        // crypt the user's password with the PHP 5.5's password_hash() function, results in a 60 character
        // hash string. the PASSWORD_DEFAULT constant is defined by the PHP 5.5, or if you are using PHP 5.3/5.4,
        // by the password hashing compatibility library. the third parameter looks a little bit shitty, but that's
        // how those PHP 5.5 functions want the parameter: as an array with, currently only used with 'cost' => XX
        $hash_cost_factor = (defined('HASH_COST_FACTOR') ? HASH_COST_FACTOR : null);
        $old_pass = password_hash($pass, PASSWORD_DEFAULT, array('cost' => $hash_cost_factor));
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
            case(isset($c_pass) && $this->Check_pass($pass) == false):
                $result = 'Senha atual está errada.';
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
        );

        if ($this->Avatar !== NULL) {
            $result['user_has_avatar'] = 1;
        }

        if (empty($lastname) || $lastname == '') {
            $result['user_last_name'] = NULL;
        } else {
            $result['user_last_name'] = $lastname;
        }

        if (isset($c_pass)) {
            $result['user_password_hash'] = $user_password_hash;
        }

        $this->Update = $result;
    }

}
