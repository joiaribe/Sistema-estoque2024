<?php

require '../../../application/config/config.php';
require '../../../application/libs/Session.php';
require '../../../application/libs/Developer/GetInfo.php';
require '../../../application/libs/Func.php';

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$mysqli->set_charset('utf8');
$data = $_POST['Text'];
$id = $_POST['UserId'];

$query = "INSERT INTO `global_chat`(`id_user`, `msg`) VALUES ('$id','$data')";
$q = mysqli_query($mysqli, $query);

function loop($id) {
    global $mysqli;
    $query = "SELECT * FROM `global_chat`";
    $q = mysqli_query($mysqli, $query);
    $result = '';
    while ($value = mysqli_fetch_array($q)) {
        $class = ($value['id_user'] !== $id) ? ' odd' : NULL;
        $date = strftime('%d de %B, %Y &aacute;s %H:%M', strtotime($value['data']));
        $nice_date = makeNiceTime($value['data']);
        $avata = _foto($value['id_user']);
        $name = _name($value['id_user']);
        $result.= <<<EOF
<li class="clearfix{$class}">
    <div class="chat-avatar">
        <img src="{$avata}" width="42" height="42" alt="{$name}">
    </div>
    <div class="conversation-text">
        <div class="ctext-wrap">
                <i title="{$name}">{$name}</i>
                <p>{$value['msg']}</p>
                <i title="enviado {$date}"> {$nice_date}</i> 
        </div>
    </div>
</li>
EOF;
    }
    return $result;
}

function makeNiceTime($datetime) {
    $etime = time() - strtotime($datetime);

    if ($etime < 1) {
        return 'Agora mesmo';
    }

    $a = array(12 * 30 * 24 * 60 * 60 => array('ano', 'anos'), 30 * 24 * 60 * 60 => array('mês', 'meses'), 24 * 60 * 60 => array('dia', 'dias'), 60 * 60 => array('hora', 'horas'), 60 => array('minuto', 'minutos'), 1 => array('segundo', 'segundos'));

    foreach ($a as $secs => $str) {
        $d = $etime / $secs;
        if ($d >= 1) {
            $r = round($d);
            $result = ($r > 1) ? $str[1] : $str[0];
            return sprintf("há %d %s atrás", $r, $result);
        }
    }
}


function _name($UserId) {
    $FirstName = array_table('users', array('user_id', $UserId), 'user_first_name');
    $LastName = array_table('users', array('user_id', $UserId), 'user_last_name');
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

function _foto($UserId = NULL) {
    $count = _contarReg('users', array(
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

function _contarReg($table, array $where) {
    global $mysqli;
    $array = '';
    $i = 1;
    foreach ($where as $key => $value) {
        if ($i++ == count($where)) {
            $array.= sprintf($key . ' = "%s"', $value);
        } else {
            $array.= sprintf($key . ' = "%s" AND ', $value);
        }
    }


    $query = "SELECT * FROM `$table` WHERE $array";
    #echo $query;
    $q = mysqli_query($mysqli, $query);
    $count = mysqli_fetch_array($q);

    #echo count($count);


    return !($count > 0) ? 0 : $count;
}

if (isset($data)) {
    if ($q) {
        echo loop($id); // 'Nota Adicionada com sucesso !';
    } else {
        echo 'Ocorreu um erro !';
    }
}
