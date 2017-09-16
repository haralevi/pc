<?php
/**
 * Created by Andre Haralevi
 * Date: 10/24/2016
 * Time: 5:21 AM
 */

namespace Photocommunity\Mobile;

class PhpErrorHandler
{
    function __construct()
    {
        set_error_handler(array(&$this, 'errorHandler'));
    }

    function errorHandler($errno, $errstr, $errfile, $errline)
    {
        if ($errno != '') {
            $error = date("d.m.Y H:i:s") . ' | ';
            if (Config::$remote_addr) $error .= Config::$remote_addr . ' | ';
            if (Auth::getIdAuth() != -1) $error .= 'ID_AUTH: ' . Auth::getIdAuth() . ' | ';
            $error .= $errstr . ' | LINE: ' . $errline . ' | IN: ' . $errfile . ' | ' . Config::$http_scheme . '//' . Config::$http_host . Config::$request_uri;
            Utils::errorWriter($error);
        }
    }
}

if (!Config::getDebug()) {
    #$o =& new PhpErrorHandler();
    #set_error_handler(array($o, 'errorHandler'));
}