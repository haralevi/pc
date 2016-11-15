<?php
/**
 * Created by Andre Haralevi
 * Date: 06.11.13
 * Time: 18:11
 */

namespace Photocommunity\Mobile;

#require dirname(__FILE__) . '/../../down.php'; die();

require dirname(__FILE__) . '/Utils.php';
require dirname(__FILE__) . '/Request.php';

# singletons
require dirname(__FILE__) . '/Timer.php';
require dirname(__FILE__) . '/Config.php';
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
        Init::startSession();

        Init::showAutoDownUp();

        Db::inst()->connect();
        Mcache::inst()->connect();

        Auth::inst()->login();
        Auth::inst()->setWorkGallLimit();

        Geo::inst()->isRobot();
        Geo::inst()->setGeo();
        Geo::inst()->redirectToAllowedDomain();

        Auth::inst()->updateOnliners();
    }

    private static function my_session_start()
    {
        $sn = session_name();
        if (isset($_COOKIE[$sn]))
            $session_id = $_COOKIE[$sn];
        else if (isset($_GET[$sn]))
            $session_id = $_GET[$sn];
        else
            return session_start();
        # check if faked session
        if (!preg_match('/^[a-zA-Z0-9,\-]{22,40}$/', $session_id))
            return false;
        return session_start();
    }

    private static function startSession()
    {
        if (!isset(Auth::$guest_sess)) {
            ini_set('session.cookie_domain', '.' . Config::$SiteDom . '.' . Config::$domainEnd);
            if (!Init::my_session_start()) {
                session_id(uniqid());
                session_start();
                session_regenerate_id();
            }
            Auth::$guest_sess = session_id();
        }
        # init prev next navigation
        if(!isset($_COOKIE['prev_next_nav'])) {
            setcookie('prev_next_nav', -1, Config::$cookie_expires, '/', '.' . Config::$SiteDom . '.' . Config::$domainEnd);
            $_COOKIE['prev_next_nav'] = -1;
        }
    }

    # show down page if it exists locally or no db host
    private static function showAutoDownUp()
    {
        if (!isset($_SESSION['auth']['id_auth']) && !isset($_COOKIE['X'])) {
            $down_local_file = 'down.local.php';
            $is_down_exists = false;
            if (file_exists(dirname(__FILE__) . '/../../' . $down_local_file))
                $is_down_exists = true;
            else {
                $file_headers = Utils::get_headers_curl('http://cdn.' . Config::$SiteDom . '.' . Config::$domainEnd . '/' . $down_local_file, 10);
                if (isset($file_headers[0]) && $file_headers[0] != '' && !strstr($file_headers[0], '404 Not Found'))
                    $is_down_exists = true;
            }
            if ($is_down_exists) {
                require dirname(__FILE__) . '/../../down.php';
                die();
            }
        }
    }
}

# init page
Init::inst();