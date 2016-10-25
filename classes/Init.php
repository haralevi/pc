<?php
/**
 * Created by Andre Haralevi
 * Date: 06.11.13
 * Time: 18:11
 */

namespace photocommunity\mobile;

# static classes
require dirname(__FILE__) . '/Utils.php';
require dirname(__FILE__) . '/Request.php';

# singletons
require dirname(__FILE__) . '/Timer.php';
require dirname(__FILE__) . '/Config.php';

#require('/var/www/vhosts/' . Config::SITE_DOMAIN . '.ru/httpdocs/down.php'); die();

require dirname(__FILE__) . '/PhpErrorHandler.php';
require dirname(__FILE__) . '/Localizer.php';
require dirname(__FILE__) . '/Consta.php';
require dirname(__FILE__) . '/Db.php';
require dirname(__FILE__) . '/Mcache.php';
require dirname(__FILE__) . '/Auth.php';
require dirname(__FILE__) . '/Geo.php';
require dirname(__FILE__) . '/Controller.php';
require dirname(__FILE__) . '/Parse.php';

# multiple object
require dirname(__FILE__) . '/Tpl.php';

class Init
{
    public static function inst()
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new Init();
        }
        return $instance;
    }

    /**
     * Private __construct so nobody else can instance it
     */
    private function __construct()
    {
        ob_start();
        Init::setEncoding();

        Init::startSession();

        Db::inst()->connect();
        Mcache::inst()->connect();

        Auth::inst()->login();
        Auth::inst()->setWorkGallLimit();

        Geo::inst()->isRobot();
        Geo::inst()->setGeo();
        Geo::inst()->redirectToAllowedDomain();

        Auth::inst()->updateOnliners();
    }

    private static function startSession()
    {
        ini_set('session.cookie_domain', '.' . Config::$SiteDom . '.' . Config::$domainEnd);
        session_start();
    }

    private static function setEncoding()
    {
        mb_internal_encoding('UTF-8');
        mb_regex_encoding('UTF-8');
    }
}

# init page
Init::inst();