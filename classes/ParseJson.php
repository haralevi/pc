<?php
/**
 * Created by Andre Haralevi
 * Date: 07.11.13
 * Time: 12:35
 */

namespace Photocommunity\Mobile;

class ParseJson
{
    public static function inst($html)
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new ParseJson($html);
        }
        return $instance;
    }

    private function __construct($html)
    {
        Db::disconnect();

        ParseJson::parseHtml($html);
        Utils::sendHeaders('application/json');
        ParseJson::printHtml();
        Utils::logVisits(ParseJson::$totalTime);
    }

    private static $totalTime = 0;
    /**
     * @param $html
     */
    private static function parseHtml($html)
    {
        Timer::stopTiming('Total');
        ParseJson::$totalTime = Timer::getATimings()['Total']['elapsed'];

        $debug = '';
        if (Config::getDebug()) {
            if (isset($_COOKIE['nav_dir']))
                $debug .= 'nav_dir: <b>' . $_COOKIE['nav_dir'] . '</b><br>';
            if(isset($_COOKIE['prev_next_nav']))
                $debug .= '<b>' . $_COOKIE['prev_next_nav'] . '</b><br>';
            if (ParseJson::$totalTime >= 0.1)
                $debug .= 'Total Time: <b>' . ParseJson::$totalTime . '</b> sec';
            if (Db::getTotalTime() >= 0.1) $debug .= '<br>Mysql Time: <b>' . Db::getTotalTime() . '</b>';
            if (Db::getQueries() != '') $debug .= '<br>' . Db::getQueries();
            #$debug = '';
        }
        $html = str_replace('#debug#', Utils::prepareJson($debug), $html);
        echo $html;
    }

    private static function printHtml()
    {
        #sleep(1);
        $contents = ob_get_contents();
        ob_end_clean();
        echo $contents;
    }
}