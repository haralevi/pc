<?php

namespace photocommunity\mobile;

class JsErrorHandler
{
    public static function inst()
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new JsErrorHandler();
        }
        return $instance;
    }

    private function __construct()
    {
        if (!isset($_REQUEST['jserror']))
            die();

        $jserror = trim($_REQUEST['jserror']);

        if (JsErrorHandler::isReallyJsError($jserror))
            JsErrorHandler::writeJsError($jserror);
    }

    private static function isReallyJsError($jserror)
    {
        $isReal = true;
        if ($jserror == '')
            $isReal = false;
        else if (strstr($jserror, 'mecash'))
            $isReal = false;
        return $isReal;
    }

    private static function writeJsError($jserror)
    {
        $fp = fopen(__DIR__ . '/../../classes/jserror.html', 'a');
        fwrite($fp, strval($jserror) . "<br>------------------------<br>\n");
        fclose($fp);
    }
}

JsErrorHandler::inst();