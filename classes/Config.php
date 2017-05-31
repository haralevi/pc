<?php
/**
 * Created by Andre Haralevi
 * Date: 06.11.13
 * Time: 16:39
 */

namespace Photocommunity\Mobile;

class Config
{
    const SITE_ROOT = '';
    const SITE_DOMAIN = 'photocommunity';
    const SITE_DOMAIN_BY = 'photocommunity';
    const SITE_NAME = 'photocommunity';
    const SITE_NAME_BY = 'photocommunity';
    const SITE_SUBDOMAIN = 'm.';
    const SERVER_IP = 'localhost';
    const CHARSET = 'utf8';
    const DB_PORT = '3306';
    const DB_TYPE = 'mysql';
    const DB_HOST = 'localhost';
    const DB_USER = 'photocommunity';
    const DB_PASSWORD = 'photocommunity';
    const DB_NAME = 'photocommunity';

    const VK_API_ID_RU = '1';
    const VK_API_ID_BY = '1';

    const FACEBOOK_APP_ID_RU = '1';
    const FACEBOOK_SECRET_RU = '1';
    const FACEBOOK_APP_ID_BY = '1';
    const FACEBOOK_SECRET_BY = '1';

    const GOOGLE_UA_COM = '1';
    const GOOGLE_UA_DE = '1';
    const GOOGLE_UA_RU = '1';

    public static $css_ver = 1;
    public static $js_ver = 1;

    public static $http_scheme = 'https:';
    public static $http_host;
    public static $script_name;
    public static $request_uri;
    public static $remote_addr;
    public static $http_referer;
    public static $http_user_agent;
    public static $cookie_expires;

    public static $lang = 'ru';

    public static $templatePath = 'tpl/';
    public static $templateExt = 'tpl';

    public static $errorLogFile = '/../../andlog/php.error.log';
    public static $visitsLogFile = '/../../andlog/php.visits.mobile.log';
    public static $jsErrorLogFile = '/../../../andlog/jserror.html';

    public static $ImgPath = 'img/';
    public static $theme = 'black';
    public static $home_url;
    public static $canonical_url;
    public static $css_url;
    public static $js_url;
    public static $css_type = 'min.';
    public static $js_type = 'min.';

    public static $noreply_email;

    public static $is_winter_time = false;
    public static $cur_time;

    private static $debug = 0;

    /**
     * @return int
     */
    public static function getDebug()
    {
        return Config::$debug;
    }

    public static $isSubDomain;
    public static $subDomain;
    public static $SiteDom;
    public static $domainEnd;
    public static $domainEndImg;

    public static function inst()
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new Config();
        }
        return $instance;
    }

    /**
     * Private __construct so nobody else can instance it
     */
    private function __construct()
    {
        Config::setEncoding();
        Config::initVars();
        if (Config::SITE_SUBDOMAIN)
            Config::checkAllowedSubdomain();

        Config::getLang();
    }

    private static function checkAllowedSubdomain()
    {
        if (Config::$subDomain != Config::SITE_SUBDOMAIN) {
            $redirect_url = '//' . Config::SITE_SUBDOMAIN . Config::$SiteDom . '.' . Config::$domainEnd . '/' . Config::SITE_ROOT;
            header('location: ' . $redirect_url);
            return false;
        }
        return true;
    }

    private static function setEncoding()
    {
        mb_internal_encoding('UTF-8');
        mb_regex_encoding('UTF-8');
        ob_start();
    }

    private static function initVars()
    {
        if (isset($_COOKIE['DebugLevel']))
            Config::$debug = $_COOKIE['DebugLevel'];
        else
            Config::$debug = 0;

        if (Config::$debug) {
            error_reporting(30719);
            ini_set("display_errors", 1);
        }

        date_default_timezone_set("Europe/Berlin");
        if (!date('I', time()))
            Config::$is_winter_time = true;
        date_default_timezone_set("UTC");

        Config::$cur_time = time();
        Config::$cookie_expires = Config::$cur_time + 2592000;

        if (isset($_SERVER['HTTP_HOST'])) Config::$http_host = $_SERVER['HTTP_HOST'];
        else Config::$http_host = '';
        if (isset($_SERVER['SCRIPT_NAME'])) Config::$script_name = $_SERVER['SCRIPT_NAME'];
        else Config::$script_name = '';
        if (isset($_SERVER['REQUEST_URI'])) Config::$request_uri = $_SERVER['REQUEST_URI'];
        else Config::$request_uri = '';
        if (isset($_SERVER['REMOTE_ADDR'])) Config::$remote_addr = $_SERVER['REMOTE_ADDR'];
        else Config::$remote_addr = '';
        if (isset($_SERVER['HTTP_REFERER'])) Config::$http_referer = $_SERVER['HTTP_REFERER'];
        else Config::$http_referer = '';
        if (isset($_SERVER['HTTP_USER_AGENT'])) Config::$http_user_agent = $_SERVER['HTTP_USER_AGENT'];
        else Config::$http_user_agent = '';

        Config::$SiteDom = substr(Config::$http_host, 0, (strrpos(Config::$http_host, '.')));
        Config::$isSubDomain = false;
        if (strrpos(Config::$SiteDom, '.')) {
            Config::$isSubDomain = true;
            Config::$subDomain = substr(Config::$http_host, 0, (strpos(Config::$http_host, '.')) + 1); // sub domain with "." at  the end, easy to use
            Config::$SiteDom = substr(Config::$SiteDom, (strrpos(Config::$SiteDom, '.') + 1));
        }
        Config::$domainEnd = substr(Config::$http_host, (strrpos(Config::$http_host, '.') + 1));

        Config::$home_url = '//' . Config::$subDomain . Config::$SiteDom . '.' . Config::$domainEnd . '/' . Config::SITE_ROOT;
        Config::$canonical_url = '//' . Config::$SiteDom . '.' . Config::$domainEnd . Config::$request_uri;
        Config::$css_url = Config::$home_url . 'css/';
        Config::$js_url = Config::$home_url . 'js/';

        Config::$domainEndImg = Config::$domainEnd;

        Config::$ImgPath = '//' . Config::SITE_DOMAIN  . '.' . Config::$domainEndImg . '/' . Config::$ImgPath;

        Config::$noreply_email = 'noreply@' . Config::$SiteDom . '.' . Config::$domainEnd;
    }

    private static function getLang()
    {
        if (isset($_GET['auth_dom_ext'])) {
            if (isset($_GET['lang'])) {
                if ($_GET['lang'] == 'ru') Config::$lang = 'ru';
                else if ($_GET['lang'] == 'com') Config::$lang = 'com';
                else if ($_GET['lang'] == 'de') Config::$lang = 'de';
            }
        } else {
            if (isset($_COOKIE['lang'])) {
                if (Config::$domainEnd == $_COOKIE['lang'] || (Config::$domainEnd == 'com' && $_COOKIE['lang'] == 'com')) Config::$lang = $_COOKIE['lang'];
                else {
                    if (Config::$domainEnd == 'ru') Config::$lang = 'ru';
                    else if (Config::$domainEnd == 'by') Config::$lang = 'by';
                    else if (Config::$domainEnd == 'com') Config::$lang = 'com';
                    else if (Config::$domainEnd == 'de') Config::$lang = 'de';
                    if (Config::$SiteDom)
                        setcookie('lang', Config::$lang, Config::$cookie_expires, '/', '.' . Config::$SiteDom . '.' . Config::$domainEnd);
                }
            } else {
                if (Config::$domainEnd == 'ru') Config::$lang = 'ru';
                else if (Config::$domainEnd == 'by') Config::$lang = 'by';
                else if (Config::$domainEnd == 'com') Config::$lang = 'com';
                else if (Config::$domainEnd == 'de') Config::$lang = 'de';
                if (Config::$SiteDom)
                    setcookie('lang', Config::$lang, Config::$cookie_expires, '/', '.' . Config::$SiteDom . '.' . Config::$domainEnd);
            }
        }
    }
}

Config::inst();