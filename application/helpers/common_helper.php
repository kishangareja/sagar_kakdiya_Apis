<?php

/**
 * Returns stage message of given code.
 * 
 * @param   int         $status
 * @return  string
 */
function getStatusCodeMessage($status) {
    $codes = Array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => '(Unused)',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported'
    );
    return (isset($codes[$status])) ? $codes[$status] : '';
}

/**
 * Halt execution and generats fatal erorr with given code.
 * 
 * @param int       $code
 * @param string    $message
 */
function send_fatal($code, $message, $success = 0) {
    header('HTTP/1.1 ' . $code . ' ' . getStatusCodeMessage($code));
    $error = json_encode(array('message' => $message, 'success' => $success));
    log_message("error", $error);
    exit($error);
}

/**
 * Is Ajax check for ajax request
 */
function is_ajax() {
    $C = & get_instance();
    if (!$C->input->is_ajax_request())
        exit('No direct script access allowed');
}

/**
 * Return Currency Decimal Points
 * 
 * @param string $currency
 * @param integer 
 */
function getCurrencyDecimalPoints($currency) {
    $decimalPoint = 2;
    $arrCurrencies = array(
        'JOD' => 3,
        'KWD' => 3,
        'OMR' => 3,
        'TND' => 3,
        'BHD' => 3,
        'LYD' => 3,
        'IQD' => 3,
    );
    if (isset($arrCurrencies[$currency])) {
        $decimalPoint = $arrCurrencies[$currency];
    }
    return $decimalPoint;
}

function time_diff($time1, $time2) {
    $str = $hours = $minutes = $seconds = "";
    $t1 = strtotime($time1);
    $t2 = strtotime($time2);

    $delta_T = ($t2 - $t1);

    /* $days = floor(($delta_T % 604800) / 86400);
      if ($days > 0)
      //$str .= $days . ":";
      $hours = floor((($delta_T % 604800) % 86400) / 3600);
      if ($hours >= 0)
      $str .= ($days * 24 ) + $hours . ":";
      $minutes = floor(((($delta_T % 604800) % 86400) % 3600) / 60);
      if ($minutes >= 0)
      $str .= $minutes . ":";
      $seconds = floor((((($delta_T % 604800) % 86400) % 3600) % 60));
      if ($seconds >= 0)
      $str .= $seconds; */

    $hours = floor($delta_T / 3600);
    $minutes = floor(($delta_T / 60) % 60);
    $seconds = $delta_T % 60;

    if ($hours >= 0)
        $str .= $hours . ":";
    if ($minutes >= 0)
        $str .= $minutes . ":";
    if ($seconds >= 0)
        $str .= $seconds;
    return $str;
}

function mediaTimeDeFormater($seconds) {
    if (!is_numeric($seconds))
        throw new Exception("Invalid Parameter Type!");


    $ret = "";

    $hours = (string) floor($seconds / 3600);
    $secs = (string) $seconds % 60;
    $mins = (string) floor(($seconds - ($hours * 3600)) / 60);

    if (strlen($hours) == 1)
        $hours = "0" . $hours;
    if (strlen($secs) == 1)
        $secs = "0" . $secs;
    if (strlen($mins) == 1)
        $mins = "0" . $mins;

    if ($hours == 0)
        $ret = $mins . "m " . $secs . "s";
    else
        $ret = $hours . "h " . $mins . "m " . $secs . "s ";

    return $ret;
}

if (!function_exists('post_display')) {

    function post_display($str = '', $data = '') {
        if (isset($_POST[$str])) {
            return htmlspecialchars($_POST[$str]);
        } else {
            return htmlspecialchars($data);
        }
    }

}
