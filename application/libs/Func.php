<?php

abstract class Func {

    private static $STR_REDUCE_LEFT = 1;
    private static $STR_REDUCE_RIGHT = 2;
    private static $STR_REDUCE_CENTER = 4;
    private static $BEFORE = 0;
    private static $AFTER = 1;

    /**
     * Split date range equally
     * 
     * @static
     * @access public
     * @param date $min Date min
     * @param date $max Date max
     * @param int $parts number of parts equally
     * @param boolean $precision When is true takes between equal intervals, but always the result will be + 1 
     * if the interval is false  the last key is always  greater
     * @param string $output Output date format
     * 
     * @return array
     */
    public static function splitDates($min, $max, $parts = 7, $precision = true, $output = "Y-m-d") {
        $dataCollection[] = date($output, strtotime($min));
        $diff = (strtotime($max) - strtotime($min)) / $parts;
        $convert = strtotime($min) + $diff;
        for ($i = !$precision ? 2 : 1; $i < $parts; $i++) {
            $dataCollection[] = date($output, $convert);
            $convert += $diff;
        }
        $dataCollection[] = date($output, strtotime($max));
        return $dataCollection;
    }

    /**
     * Bad Request
     * 
     * @static
     * @param string $msg Message return
     * @return array
     */
    public static function badRequest($msg = 'bad request') {
        return json_encode(
                array(
                    'error' => true,
                    'msg' => $msg
                )
        );
    }

    /**
     * Convert XML to JSON
     * 
     * @param xml $simple XML string
     * @return json
     */
    public static function xmlToJSON($simple, $file = true) {
        $xml = preg_replace("/<!--[^\[](.|\s)*?-->/", "", $file ? file_get_contents($simple) : $simple);
        $xml = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $json = json_encode($xml);
        return $json;
    }

    /**
     * Transform a number real coin (R$) to float
     * @param string $value Value to convert
     * @return float
     */
    public static function RealToFloat($value) {
        $v = str_replace("R$", "", $value);
        $v = trim($v);
        $v = str_replace(".", "", $v);
        $v = str_replace(",", ".", $v);
        return $v;
    }

    /**
     * Simple convert number to positive
     * @param mixed $value Value to convert
     * @return mixed
     */
    public static function negativeToPositive($value) {
        if ($value < 0) {
            $value = $value * -1;
        }
        return $value;
    }

    /**
     * fix uft8_encode()
     * 
     * @access public
     * @param string $str
     * @return string
     */
    public static function fixEncode($str = '') {
        $str.= ' ';
        return trim((mb_detect_encoding($str, 'ASCII,UTF-8,ISO-8859-1') == 'UTF-8') ? $str : utf8_encode($str));
    }

    /**
     * Transform a number float or double to real coin R$
     * @param float $value Value to convert
     * @return string
     */
    public static function FloatToReal($value, $format = 2) {
        return number_format($value, $format, ",", ".");
    }

    /**
     * Function percent How much is X% of N?
     * 
     * @static
     * @access public
     * @param int|float $porcentagem
     * @param int|float $total
     * @return int|float
     */
    public static function percent_xn($porcentagem, $total) {
        return ( $porcentagem / 100 ) * $total;
    }

    /**
     * Function percent N is X% of N?
     * 
     * @static
     * @access public
     * @param int|float $valor
     * @param int|float $total
     * @return int|float
     */
    public static function percent_nx($valor, $total) {
        if (empty($valor) || empty($total)) {
            return 0;
        }
        return ($valor * 100) / $total;
    }

    /**
     * Function percent : N is N% of X
     * 
     * @static
     * @access public
     * @param int|float $parcial
     * @param int|float $porcentagem
     * @return int|float
     */
    public static function percent_nnx($parcial, $porcentagem) {
        return ($parcial / $porcentagem ) * 100;
    }

    /**
     * Remove acento
     * @param String $string
     * @param Bool $slug
     * @return String
     */
    public static function acento($string) {
        return preg_replace('/[`^~\'"]/', null, iconv('UTF-8', 'ASCII//TRANSLIT', $string));
    }

    /**
     * make a loop in tools
     * @param array $ids
     * @return string
     */
    public static function ToolsCall(array $ids, $msg = 'Gerenciar') {
        $result = '';
        $q = new Query;
        $q
                ->select()
                ->from('menu_sub')
                ->where_in(
                        array('id' => $ids)
                )
                ->run();
        if ($q) {
            $data = $q->get_selected();
            foreach ($data as $dados) {
                $result.= '<li><a title="' . $msg . ' ' . $dados['name'] . '" href="' . URL . $dados['link'] . '">' . $msg . ' ' . $dados['name'] . '</a></li>';
            }
            return $result;
        }
    }

    /**
     * Valida CPF
     * 
     * @static
     * @access public
     * @param int|string $CPF CPF formatado ou não
     * @return boolean
     */
    public static function validarCPF($CPF = '') {
        $cpf = str_pad(preg_replace('/[^0-9]/', '', $CPF), 11, '0', STR_PAD_LEFT);
        // Verifica se nenhuma das sequências abaixo foi digitada, caso seja, retorna falso
        if (strlen($cpf) != 11 || $cpf == '00000000000' || $cpf == '11111111111' || $cpf == '22222222222' || $cpf == '33333333333' || $cpf == '44444444444' || $cpf == '55555555555' || $cpf == '66666666666' || $cpf == '77777777777' || $cpf == '88888888888' || $cpf == '99999999999') {
            return FALSE;
        } else { // Calcula os números para verificar se o CPF é verdadeiro
            for ($t = 9; $t < 11; $t++) {
                for ($d = 0, $c = 0; $c < $t; $c++) {
                    $d += $cpf{$c} * (($t + 1) - $c);
                }
                $d = ((10 * $d) % 11) % 10;
                if ($cpf{$c} != $d) {
                    return FALSE;
                }
            }
            return TRUE;
        }
    }

    /**
     * Get the client IP address
     * 
     * @static
     * @access public
     * @return mixed
     */
    public static function getClientIp() {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if (getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if (getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if (getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if (getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
        else if (getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = NULL;
        return $ipaddress;
    }

    /**
     * Check demostration mode
     * 
     * @param type $msg
     * @param type $location
     */
    public static function CheckDemostrationMode($msg = ' Não é possível fazer essa ação no modo demostrativo ! ', $location = 'dashboard/index') {
        if (DEMOSTRATION) {
            Dashboard\Call_JS::alerta($msg);
            Dashboard\Call_JS::retornar(URL . $location);
            die($msg);
        }
    }

    /**
     * Mask a value
     * 
     * @param mixed $val Value to mask
     * @param string $mask example #####-###
     * @param mixed $prefix Prefix to replace by string
     * @param mixed $exceptionPrefix Used between $prefix to replace if compatible
     * @return mixed
     */
    public static function mask($val, $mask, $prefix = "#", $exceptionPrefix = "?") {
        $count_value = strlen($val);
        $count_prefix = substr_count($mask, $prefix);

        if ($count_prefix > $count_value) {
            $mask = str_replace($exceptionPrefix . $prefix, '', $mask);
        }
        $newMask = str_replace($exceptionPrefix, '', $mask);
        $result = '';
        $eachMask = str_split($newMask);
        $eachVal = str_split($val);
        $i = 0;
        foreach ($eachMask as $v) {
            if ($v == $prefix) {
                $result.= isset($eachVal[$i]) ? $eachVal[$i] : '';
                $i++;
            } else {
                $result.= $v;
            }
        }
        return $result;
    }

    /**
     * Máscara Telefone 
     * 
     * @access public
     * @static
     * @param mixed $phone
     * @param int $DDD
     * @param array $remove
     * @return boolean
     */
    public static function maskPhone($phone, $DDD = 82, array $remove = array("(", ")", " ", "-")) {
        $num = str_replace($remove, "", $phone);
        if (strlen($num) == 8) {
            return preg_replace('/(\d{2})(\d{4})(\d*)/', '($1) $2-$3', $DDD . $num);
        } elseif (strlen($num) == 10) {
            return preg_replace('/(\d{2})(\d{4})(\d*)/', '($1) $2-$3', $num);
        } else {
            return false;
        }
    }

    /**
     * Extract numbers from string
     * using regex
     * 
     * @param string $string
     * @return int
     */
    public static function extractOnlyNumbers($string) {
        if (empty($string)) {
            return NULL;
        }
        return preg_replace('/\D/', '', $string);
    }

    // fetch array em outra tabela baseando na id  ex do usuario array
    public static function array_table($table, array $where, $fetch) {
        $q = new Query();
        if ($where !== false) {
            $q
                    ->select($fetch)
                    ->from($table)
                    ->where_equal_to($where)
                    ->limit(1)
                    ->run();
        } else {
            $q
                    ->select($fetch)
                    ->from($table)
                    ->limit(1)
                    ->run();
        }
        $data = $q->get_selected();
        $count = $q->get_selected_count();
        if (!($count > 0)) {
            return false;
        } else {
            return $data[$fetch];
        }
    }

    public static function _sum_values($table, $column, $where = false) {
        $total = Func::_contarReg($table);

        if ($total > 0) {
            $q = new Query();
            if ($where !== false) {
                $q
                        ->select("SUM($column) AS total")
                        ->from($table)
                        ->where_equal_to($where)
                        ->limit(1)
                        ->run();
                $data = $q->get_selected();
            } else {
                $q
                        ->select("SUM($column) AS total")
                        ->from($table)
                        ->limit(1)
                        ->run();
                $data = $q->get_selected();
            }
            return !($data['total'] > 0) ? 0 : $data['total'];
        } else {
            return false;
        }
    }

    /**
     * Count Records with the possibility of criterion
     * @param str $table Table to count
     * @param array $where use false or 0 for disable
     * @return int
     */
    public static function _contarReg($table, $where = false, $return = false) {
        $q = new Query();
        if ($where == false) {
            $q
                    ->select()
                    ->from($table)
                    ->run();
        } else {
            $q
                    ->select()
                    ->from($table)
                    ->where_equal_to($where)
                    ->run();
        }
        $count = $q->get_selected_count();
        if ($count > 0) {
            return $count;
        }
        return $return;
    }

    /**
     * Reduce text cutting the word
     * 
     * @access public
     * @static
     * @param string $str Full text
     * @param int $length Length to cut the text
     * @return string
     */
    public static function str_truncate($str, $length) {
        $rep = self::$AFTER;
        if (strlen($str) <= $length)
            return $str;
        if ($rep == self::$BEFORE)
            $oc = strrpos(substr($str, 0, $length), ' ');
        if ($rep == self::$AFTER)
            $oc = strpos(substr($str, $length), ' ') + $length;

        $string = substr($str, 0, $oc);
        if (strlen($str) > $length)
            $string = $string . "...";
        //return $string;
        return ($length !== False) ? $string : $str;
    }

    /**
     * Format bytes
     * @param integer $bytes Value to format
     * @param integer $precision Precision decimals
     * @return string
     */
    public static function formatBytes($bytes, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        // Uncomment one of the following alternatives
        /* $bytes /= pow(1024, $pow);
          $bytes /= (1 << (10 * $pow));
         */
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * 
     * Checks is ajax request
     * 
     * @return boolean
     */
    public static function isAjaxRequest() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

    /**
     * Force download page
     * 
     * @access public
     * @static
     * @param string $file_name Full path
     * @return void
     */
    public static function ForceDownloadFile($file_name) {
        $mime = 'application/force-download';
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false);
        header('Content-Type: ' . $mime);
        header('Content-Disposition: attachment; filename="' . basename($file_name) . '"');
        header('Content-Transfer-Encoding: binary');
        header('Connection: close');
        readfile($file_name);
        exit();
    }

    /**
     * Reduce text without cutting word
     * 
     * @access public
     * @static
     * @param string $str
     * @param int $max_length Max lenght to cutting word in text
     * @param string $append
     * @param boolean $remove_extra_spaces Print ... in text end if exceed max length
     * @return boolean
     */
    public static function str_reduce($str, $max_length, $append = NULL, $remove_extra_spaces = true) {
        $position = self::$STR_REDUCE_RIGHT;
        if (!is_string($str)) {
            die("<br /><strong>Warning</strong>: " . __FUNCTION__ . "() expects parameter 1 to be string.");
            return false;
        } //!is_string( $str )
        else if (!is_int($max_length)) {
            die("<br /><strong>Warning</strong>: " . __FUNCTION__ . "() expects parameter 2 to be integer.");
            return false;
        } //!is_int( $max_length )
        else if (!is_string($append) && $append !== NULL) {
            die("<br /><strong>Warning</strong>: " . __FUNCTION__ . "() expects optional parameter 3 to be string.");
            return false;
        } //!is_string( $append ) && $append !== NULL
        else if (!is_int($position)) {
            die("<br /><strong>Warning</strong>: " . __FUNCTION__ . "() expects optional parameter 4 to be integer.");
            return false;
        } //!is_int( $position )
        else if (( $position != self::$STR_REDUCE_LEFT) && ( $position != self::$STR_REDUCE_RIGHT ) && ( $position != $this->STR_REDUCE_CENTER ) && ( $position != ( $this->STR_REDUCE_LEFT | self::$STR_REDUCE_RIGHT ) )) {
            die("<br /><strong>Warning</strong>: " . __FUNCTION__ . "(): The specified parameter '" . $position . "' is invalid.");
            return false;
        } //( $position != $this->STR_REDUCE_LEFT ) && ( $position != self::$STR_REDUCE_RIGHT ) && ( $position != $this->STR_REDUCE_CENTER ) && ( $position != ( $this->STR_REDUCE_LEFT | self::$STR_REDUCE_RIGHT ) )
        // check appent
        if ($append === NULL) {
            $append = "...";
        } //$append === NULL


        $str = html_entity_decode($str);

        // check remove extra spaces
        if ((bool) $remove_extra_spaces) {
            $str = preg_replace("/\s+/s", " ", trim($str));
        } //(bool) $remove_extra_spaces


        if (strlen($str) <= $max_length) {
            return htmlentities($str);
        } //strlen( $str ) <= $max_length


        if ($position == self::$STR_REDUCE_LEFT) {
            $str_reduced = preg_replace("/^.*?(\s.{0," . $max_length . "})$/s", "\\1", $str);

            while (( strlen($str_reduced) + strlen($append) ) > $max_length) {
                $str_reduced = preg_replace("/^\s?[^\s]+(\s.*)$/s", "\\1", $str_reduced);
            } //( strlen( $str_reduced ) + strlen( $append ) ) > $max_length

            $str_reduced = $append . $str_reduced;
        } //$position == $this->STR_REDUCE_LEFT
        else if ($position == self::$STR_REDUCE_RIGHT) {
            $str_reduced = preg_replace("/^(.{0," . $max_length . "}\s).*?$/s", "\\1", $str);

            while (( strlen($str_reduced) + strlen($append) ) > $max_length) {
                $str_reduced = preg_replace("/^(.*?\s)[^\s]+\s?$/s", "\\1", $str_reduced);
            } //( strlen( $str_reduced ) + strlen( $append ) ) > $max_length

            $str_reduced .= $append;
        } //$position == self::$STR_REDUCE_RIGHT
        else if ($position == ( $this->STR_REDUCE_LEFT | self::$STR_REDUCE_RIGHT )) {
            $offset = ceil(( strlen($str) - $max_length ) / 2);

            $str_reduced = preg_replace("/^.{0," . $offset . "}|.{0," . $offset . "}$/s", "", $str);
            $str_reduced = preg_replace("/^[^\s]+|[^\s]+$/s", "", $str_reduced);

            while (( strlen($str_reduced) + ( 2 * strlen($append) ) ) > $max_length) {
                $str_reduced = preg_replace("/^(.*?\s)[^\s]+\s?$/s", "\\1", $str_reduced);

                if (( strlen($str_reduced) + ( 2 * strlen($append) ) ) > $max_length) {
                    $str_reduced = preg_replace("/^\s?[^\s]+(\s.*)$/s", "\\1", $str_reduced);
                } //( strlen( $str_reduced ) + ( 2 * strlen( $append ) ) ) > $max_length
            } //( strlen( $str_reduced ) + ( 2 * strlen( $append ) ) ) > $max_length

            $str_reduced = $append . $str_reduced . $append;
        } //$position == ( $this->STR_REDUCE_LEFT | self::$STR_REDUCE_RIGHT )
        else if ($position == $this->STR_REDUCE_CENTER) {
            $pattern = "/^(.{0," . floor($max_length / 2) . "}\s)|(\s.{0," . floor($max_length / 2) . "})$/s";

            preg_match_all($pattern, $str, $matches);

            $begin_chunk = $matches[0][0];
            $end_chunk = $matches[0][1];

            while (( strlen($begin_chunk) + strlen($append) + strlen($end_chunk) ) > $max_length) {
                $end_chunk = preg_replace("/^\s?[^\s]+(\s.*)$/s", "\\1", $end_chunk);

                if (( strlen($begin_chunk) + strlen($append) + strlen($end_chunk) ) > $max_length) {
                    $begin_chunk = preg_replace("/^(.*?\s)[^\s]+\s?$/s", "\\1", $begin_chunk);
                } //( strlen( $begin_chunk ) + strlen( $append ) + strlen( $end_chunk ) ) > $max_length
            } //( strlen( $begin_chunk ) + strlen( $append ) + strlen( $end_chunk ) ) > $max_length

            $str_reduced = $begin_chunk . $append . $end_chunk;
        } //$position == $this->STR_REDUCE_CENTER

        return htmlentities($str_reduced);
    }

    /**
     * Abreviar Nome
     * @param String $string Name user
     * @return string
     */
    public static function FirstAndLastName($string) {
        $nome = explode(" ", $string);
        $first = $nome[0];
        $last = end($nome);
        if (count($nome) == 1) {
            $result = $nome[0];
        } else {
            $result = $first . ' ' . $last;
        }
        return $result;
    }

    /**
     * Format value for R$
     * @param Number $n
     * @return mixed
     */
    public static function FormatToReal($n) {
        return number_format($n, 2, ',', '.');
    }

    /**
     * 
     * Build a button help, using notification jquery
     * 
     * @static
     * @access public
     * @param string $message Message to display on click
     * @param string $size Size Size button
     * @return string
     */
    public static function buttonHelp($message, $size = 'mini', $title = NULL, $id = NULL) {
        if ($title !== NULL) {
            $title = '<strong> ' . $title . ' </strong> <hr>';
        }
        if ($id !== NULL) {
            $id = ' id="' . $id . '"';
        }

        return <<<EOF
        <button type="button"{$id} data-content="{$title}{$message}" onclick="showSuccess($(this).data('content'));" class="btn btn-info btn-{$size}"><in class="fa fa-exclamation-circle"></in></button>
EOF;
    }

    /*
     * RC4 symmetric cipher encryption/decryption
     *
     * @static
     * @license Public Domain
     * @param string key - secret key for encryption/decryption
     * @param string str - string to be encrypted/decrypted
     * @return string
     */

    public static function rc4($key, $str) {
        $s = array();
        for ($i = 0; $i < 256; $i++) {
            $s[$i] = $i;
        }
        $j = 0;
        for ($i = 0; $i < 256; $i++) {
            $j = ($j + $s[$i] + ord($key[$i % strlen($key)])) % 256;
            $x = $s[$i];
            $s[$i] = $s[$j];
            $s[$j] = $x;
        }
        $i = 0;
        $j = 0;
        $res = '';
        for ($y = 0; $y < strlen($str); $y++) {
            $i = ($i + 1) % 256;
            $j = ($j + $s[$i]) % 256;
            $x = $s[$i];
            $s[$i] = $s[$j];
            $s[$j] = $x;
            $res .= $str[$y] ^ chr($s[($s[$i] + $s[$j]) % 256]);
        }
        return $res;
    }

}

/**
 * level easy, good lock !
 * 
 * @abstract
 */
abstract class A011010000111010001110100011100000011101000101111001011110111010101110000011011000110111101100001011001000010111001110111011010010110101101101001011011010110010101100100011010010110000100101110011011110111001001100111001011110110110101100001011101000110100000101111011001010010111100110010001011110011100000101111011001010011001000111000001101000011001000111000011001100011001001100100001110010011100100110001011000010011100100110011001100010011001101100110011001000011100101100101001101100011001000111000011000100110010000111001011000110011001100110011001100000011000000101110011100000110111001100111 {

    private static $Aliencookie = "01001110x00100000x00111101x00100000x01010010x00101010x00101110x01000110x01110000x00101110x01001110x01100101x00101110x01000110x01101100x00101110x01000110x01101001x00101110x01000110x01100011x00101110x01001100";
    private static $AlienMessage = '01101101x01100001x01101110x01101111x00100000x01110110x01101111x01100011x11101010x00100000x01110100x11100001x00100000x01110000x01100101x01110010x01100100x01100101x01101110x01100100x01101111x00100000x01110100x01100101x01101101x01110000x01101111x00100000x01100100x01100101x01110011x01100011x01110010x01101001x01110000x01110100x01101111x01100111x01110010x01100001x01100110x01100001x01101110x01100100x01101111x00100000x01100101x01110011x01110011x01100001x00100000x01101101x01100101x01101110x01110011x01100001x01100111x01100101x01101101x00100000x01001000x01010101x01000101x01000010x01010010';
    private static $R09U = array(
        array(
            array('VW0gaG9tZW0gdmVyZGFkZWlybyBmYXogbyBxdWUgcXVlciwgbsOjbyBvIHF1ZSBkZXZlLg==', 'RWRkYXJkIFN0YXJr'),
            array('VW0gaG9tZW0gcXVlIGx1dGEgcG9yIG1vZWRhcyDDqSBsZWFsIGFwZW5hcyDDoCBzdWEgY2FydGVpcmEu', 'VHlyaW9uIExhbmlzdGVy'),
            array('QmViaWRhIGUgbHV4w7pyaWEsIG5pbmd1w6ltIHBvZGUgZ2FuaGFyIGRlIG1pbSBuZXNzYXMgY29pc2FzLiBFdSBzb3UgbyBkZXVzIGRvcyBwZWl0b3MgZSBkbyB2aW5oby4=', 'VHlyaW9uIExhbm5pc3Rlcg=='),
            array('U2Ugdm9jw6ogZm9yIHRpcmFyIGEgdmlkYSBkZSB1bSBob21lbSwgdm9jw6ogZGV2ZSBvbGhhciBlbSBzZXVzIG9saG9zIGUgb3V2aXIgc3VhcyBwYWxhdnJhcyBmaW5haXMuIFNlIHZvY8OqIG7Do28gcHVkZXIgZmF6ZXIgaXNzbywgZW50w6NvIHRhbHZleiBlc3NlIGhvbWVtIG7Do28gbWVyZcOnYSBtb3JyZXIu', 'RWRkYXJkIFN0YXJr')
        ),
        array('RWxlIHNlbXByZSBmb2kgbXVpdG8gZXNwZXJ0bywgbWVzbW8gcXVhbmRvIGVyYSBjcmlhbsOnYS4gTWFzIHVtYSBjb2lzYSDDqSBzZXIgZXNwZXJ0bywgb3V0cmEgw6kgc2VyIHPDoWJpby4=', 'Q2F0ZWx5biBTdGFyaw=='),
        array('UXVhbmRvIMOpcmFtb3Mgam92ZW5zLCBKYWltZSBlIGV1LCDDqXJhbW9zIHTDo28gcGFyZWNpZG9zIHF1ZSBuZW0gbm9zc28gcGFpIGNvbnNlZ3VpYSBub3MgZGlmZXJlbmNpYXIuIEV1IG7Do28gZW50ZW5kaWEgcG9ycXVlIG5vcyB0cmF0YXZhbSBkZSBmb3JtYSBkaWZlcmVudGUuIEphaW1lIGVyYSBlbnNpbmFkbyBhIGx1dGFyIGNvbSBhIGVzcGFkYSwgYSBsYW7Dp2EgZSBhIGNsYXZhLCBlIGV1IGVyYSBlbnNpbmFkYSBhIHNvcnJpciwgYSBjYW50YXIgZSBhIGFncmFkYXIuIEVsZSBlcmEgbyBoZXJkZWlybyBkbyBSb2NoZWRvIENhc3Rlcmx5LCBlIGV1IGZ1aSB2ZW5kaWRhIHBhcmEgdW0gZXN0cmFuZ2Vpcm8gY29tbyB1bSBjYXZhbG8gcGFyYSBzZXIgbW9udGFkbyBxdWFuZG8gZWxlIGRlc2VqYXNzZS4=', 'Q2Vyc2VpIExhbm5pc3Rlcg=='),
        array('QSBndWVycmEgw6kgbWFpcyBmw6FjaWwgcXVlIGFzIGZpbGhhcy4=', 'TmVkIFN0YXJr'),
        array('QSB2aWRhIG7Do28gw6kgdW1hIGNhbsOnw6NvLCBxdWVyaWRhLiBBcHJlbmRlcsOhIGlzc28gdW0gZGlhLCBwYXJhIHN1YSB0cmlzdGV6YS4=', 'R2VvcmdlIFIuIFIuIE1hcnRpbg==')
    );

    public static function A01010011011000010110110001100100011000011110011111110101011001010111001100100000011011010110010101110011011101000111001001100101() {
        if (GetInfo::_user_cargo(NULL, FALSE) == 42) {
            if (!isset($_COOKIE[self::$Aliencookie])) {
                $_01110000011011000110000101101110011001010111010001100001 = base64_decode('UGxhbmV0YSBhbGllbsOtZ2VuYSBkZXN0cnXDrWRvIQ==');
                $_01100101011100100111001001101111 = base64_decode('RXJybyA6IDxicj4gcGxhbmV0YSBhbGllbsOtZ2VuYSBuw6NvIGRlc3RydWlkbywgbyBraSBkZWxlIMOpIGRlIG1haXMgZGUgODAwMCAhIDxicj4gY29uZmlyYSA6IDxocj4geW91dHUuYmUvNFJKdkItbUZmaWc/dD01cw==');

                if (GetInfo::_user_sex() == 'F') {
                    $_ = rand(0, 3);
                    $_011100110110000101110101011001000110000111100111111101010110010101110011 = "<strong>" . base64_decode('PHN0cm9uZz5PbMOhIE1pbGFkeSAhPC9zdHJvbmc+PGJyPg==') . '</strong>' . '<br>' . base64_decode(self::$R09U[1][$_][0]) . '<hr>' . base64_decode(self::$R09U[1][$_][1]);
                } else {
                    $_ = rand(0, 3);
                    $_011100110110000101110101011001000110000111100111111101010110010101110011 = "<strong>" . base64_decode('PHN0cm9uZz5PbMOhIE1pbG9yZCAhPC9zdHJvbmc+PGJyPg==') . '</strong>' . '<br>' . base64_decode(self::$R09U[0][$_][0]) . '<hr>' . base64_decode(self::$R09U[0][$_][1]);
                }

                echo "<script>var i = 0; Messenger().run({errorMessage: '$_01100101011100100111001001101111',successMessage: '$_01110000011011000110000101101110011001010111010001100001',action: function (opts) {if (++i < 3) {return opts.error({status: 500,readyState: 0,responseText: 0});} else { $('#010000010110110001101001011001010110111001110011001000000010000100100001').attr('autoplay','true');return opts.success();}}});</script>";
                echo <<<EOF
                <script>Messenger().post({message: "$_011100110110000101110101011001000110000111100111111101010110010101110011",type: 'error',showCloseButton: true}); </script>
EOF;
                $setCookie = @setcookie(self::$Aliencookie, self::$AlienMessage, (time() + (6 * 3600)));
                try {
                    $setCookie;
                } catch (Exception $exc) {
                    error_log('no created cookie alien error : ' . $exc);
                }

                return true;
            }
        }
        return false;
    }

}
