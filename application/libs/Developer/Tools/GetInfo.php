<?php

namespace Developer\Tools;

use Session as Session;

class GetInfo {

    /**
     * user business accout type
     * @var array 
     */
    public static $names_acc_type = array(
        0 => array(
            'Name' => 'Administrador',
            'Badges' => 'badge bg-warning'
        ),
        1 => array(
            'Name' => 'Financeiro',
            'Badges' => 'badge bg-primary'
        ),
        2 => array(
            'Name' => 'Atendente',
            'Badges' => 'badge bg-info'
        ),
        3 => array(
            'Name' => 'Gerente',
            'Badges' => 'badge bg-success'
        )
    );

    /**
     * Name account type
     * @param Int $id
     * @return string
     */
    private static function _account_type_nane_maker($id) {
        switch ($id) {
            case 0:
                $result = 'Administrador';
                break;
            case 1:
                $result = 'Financeiro';
                break;
            case 2:
                $result = 'Atendente';
                break;
            case 3:
                $result = 'Gerente';
                break;
            default :
                $result = 'Unknow';
                break;
        }
        return $result;
    }

    /**
     * Check account type if is only admin
     * @return boolean
     */
    public static function _check_only_admin() {
        $info = $_SESSION['user_account_type'];
        if ($info !== 0) {
            return false;
        }
        return true;
    }

    /**
     * Check account type if is only suport
     * @return boolean
     */
    public static function _check_only_suport() {
        $info = $_SESSION['user_account_type'];
        if ($info !== 3) {
            return false;
        }
        return true;
    }

    /**
     * Check account type if is only client
     * @return boolean
     */
    public static function _check_only_client() {
        $info = $_SESSION['user_account_type'];
        if ($info !== 6) {
            return false;
        }
        return true;
    }

    /**
     * Show account type occupation
     * @param mixed $user
     * @param boolean $text
     * @return mixed
     */
    public static function _user_cargo($user = NULL, $text = TRUE) {
        if ($user !== NULL) {
            $info = \Func::array_table('users', array('user_id' => $user), 'user_account_type');
        } else {
            $info = $_SESSION['user_account_type'];
        }

        return $text == true ? self::_account_type_nane_maker($info) : $info;
    }

    /**
     * get user plan active, but when is customer
     * @param type $user
     * @return type
     */
    public static function _user_plan($user = NULL) {
        if ($user == NULL) {
            return array_table($_SESSION['user_id'], 'user_id', 'users', 'user_id_plan');
        }
        return array_table($user, 'user_id', 'users', 'user_id_plan');
    }

    /**
     * Show User name
     * @param Int $UserId User id
     * @param String $msg Message if not found user
     * @return String
     */
    public static function _name($UserId, $msg = '<span class="label label-danger label-mini">Desconhecido</span>') {
        $FirstName = \Func::array_table('users', array('user_id' => $UserId), 'user_first_name');
        $LastName = \Func::array_table('users', array('user_id' => $UserId), 'user_last_name');
        $name = $FirstName . ' ' . $LastName;
        // have User id ?
        if ($UserId == NULL XOR !isset($name)) {
            return $msg;
        }

        return \Func::FirstAndLastName($name);
    }

    /**
     * Show User name
     * @param Int $UserId User id
     * @param String $msg Message if not found user
     * @return String
     */
    public static function _email($UserId, $msg = '<span class="label label-danger label-mini">Desconhecido</span>') {
        $email = \Func::array_table('users', array('user_id' => $UserId), 'user_email');
        // have User id ?
        if ($UserId == NULL XOR !isset($email)) {
            return $msg;
        }

        return $email;
    }

    /**
     * Show avatar
     * @param Integer $UserId
     * @return string
     */
    public static function _foto($UserId = NULL) {
        $rules = array(
            'user_id' => $UserId,
            'user_has_avatar' => 1
        );
        if ($UserId == NULL) {
            try {
                $pic = Session::get('user_gravatar_image_url');
            } catch (Exception $ex) {
                if (isset($ex))
                    $pic = \Session::get('user_gravatar_image_url');
            }
        } else {
            $count = \Func::_contarReg('users', $rules);
            if ($count > 0) {
                $pic = URL . "public/avatars/$UserId.jpg";
            } else {
                $pic = URL . "public/avatars/default.jpg";
            }
        }
        return $pic;
    }

}
