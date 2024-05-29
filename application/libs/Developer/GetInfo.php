<?php

class GetInfo {

    public static $names_acc_type = array(
        0 => 'Administrador',
        1 => 'Atendente',
        2 => 'Funcionário',
        3 => 'Vendedor',
        4 => 'Gerente'
    );

    public static function _account_type_nane_maker($id) {
        switch ($id) {
            case 0:
                $result = 'Administrador';
                break;
            case 1:
                $result = 'Atendente';
                break;
            case 2:
                $result = 'Funcionário';
                break;
            case 3:
                $result = 'Vendedor';
                break;
            case 4 :
                $result = 'Gerente';
                break;
        }
        return $result;
    }

    /**
     * Return account type of user
     * @param Integer $UserId Use NULL to catch himself
     * @return String
     */
    public static function _user_cargo($user = NULL, $text = TRUE) {
        if ($user !== NULL) {
            $info = Func::array_table('users', array('user_id' => $user), 'user_account_type');
        } else {
            $info = $_SESSION['user_account_type'];
        }

        return $text == true ? self::_account_type_nane_maker($info) : $info;
    }

    /**
     * Return formatted name of user
     * @param Integer $UserId Use NULL to catch himself
     * @return String
     */
    public static function _name($UserId) {
        $FirstName = Func::array_table('users', array('user_id' => $UserId), 'user_first_name');
        $LastName = Func::array_table('users', array('user_id' => $UserId), 'user_last_name');
        if (isset($FirstName, $LastName)) {
            return $FirstName . ' ' . $LastName;
        } elseif (isset($FirstName)) {
            return $FirstName;
        } elseif (isset($LastName)) {
            return $LastName;
        } else {
            return 'Sem Nome';
        }
    }

    /**
     * Return firstname of user
     * @param Integer $UserId Use NULL to catch himself
     * @return String
     */
    public static function FirstName($UserId = NULL) {
        if ($UserId !== NULL) {
            $FirstName = Func::array_table('users', array('user_id' => $UserId), 'user_first_name');
        } else {
            $FirstName = Func::array_table('users', array('user_id' => Session::get('user_id')), 'user_first_name');
        }
        if (isset($FirstName)) {
            return $FirstName;
        } else {
            return NULL;
        }
    }

    /**
     * Return lastname of user
     * @param Integer $UserId Use NULL to catch himself
     * @return String
     */
    public static function LastName($UserId = NULL) {
        if ($UserId !== NULL) {
            $LastName = Func::array_table('users', array('user_id' => $UserId), 'user_last_name');
        } else {
            $LastName = Func::array_table('users', array('user_id' => Session::get('user_id')), 'user_last_name');
        }
        if (isset($LastName)) {
            return $LastName;
        } else {
            return NULL;
        }
    }

    /**
     * Return email of user
     * @param Integer $UserId Use NULL to catch himself
     * @return String
     */
    public static function Email($UserId = NULL) {
        if ($UserId !== NULL) {
            $email = Func::array_table('users', array('user_id' => $UserId), 'user_email');
        } else {
            $email = Func::array_table('users', array('user_id' => Session::get('user_id')), 'user_email');
        }
        if (isset($email)) {
            return $email;
        } else {
            return NULL;
        }
    }

    /**
     * Return login of user
     * @param Integer $UserId Use NULL to catch himself
     * @return String
     */
    public static function Login($UserId = NULL) {
        if ($UserId !== NULL) {
            $Login = Func::array_table('users', array('user_id' => $UserId), 'user_name');
        } else {
            $Login = Func::array_table('users', array('user_id' => Session::get('user_id')), 'user_name');
        }
        if (isset($Login)) {
            return $Login;
        } else {
            return NULL;
        }
    }

    /**
     * Return full path pic of user
     * @param Integer $UserId Use NULL to catch himself
     * @return String
     */
    public static function _foto($UserId = NULL) {
        if ($UserId == NULL) {
            $UserId = Session::get('user_id');
        }
        $count = Func::_contarReg('users', array(
                    'user_id' => $UserId,
                    'user_has_avatar' => 1
                        )
        );
        if ($count > 0) {
            $pic = URL . "public/avatars/$UserId.jpg";
        } else {
            $pic = URL . "public/avatars/default.jpg";
        }

        return $pic;
    }

}
