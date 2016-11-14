<?php
/**
 * Created by Andre Haralevi
 * Date: 06.10.16
 * Time: 5:21 AM
 */

namespace Photocommunity\Mobile;

require dirname(__FILE__) . '/../../classes/Config.php';

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

        $jserror = trim(mb_substr($_REQUEST['jserror'], 0, 10000));
        if (JsErrorHandler::isReallyJsError($jserror))
            JsErrorHandler::writeJsError($jserror);
    }

    private static function isReallyJsError($jserror)
    {
        $isReal = true;
        if ($jserror == '')
            $isReal = false;
        else if (
            strstr($jserror, 'mecash') || strstr($jserror, 'metabar') ||
            strstr($jserror, 'prod2016') || strstr($jserror, 'reckonstat')
        )
            $isReal = false;
        return $isReal;
    }

    private static function writeJsError($jserror)
    {
        $fp = fopen(__DIR__ . Config::$jsErrorLogFile, 'a');
        fwrite($fp, strval($jserror) . "<br>------------------------<br>\n");
        fclose($fp);
    }
}

JsErrorHandler::inst();