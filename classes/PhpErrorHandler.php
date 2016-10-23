<?php
namespace photocommunity\mobile;

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
            if (Auth::inst()->getIdAuth() != -1) $error .= 'ID_AUTH: ' . Auth::inst()->getIdAuth() . ' | ';
            $error .= $errstr . ' | LINE: ' . $errline . ' | IN: ' . $errfile . ' | http://' . Config::$http_host . Config::$request_uri;
            Utils::errorWriter($error);
        }
    }
}

if (!Config::getDebug()) {
    $o =& new PhpErrorHandler();
    set_error_handler(array($o, 'errorHandler'));
}