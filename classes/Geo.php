<?php
/**
 * Created by Andre Haralevi
 * Date: 27.12.2015
 * Time: 01:30
 */

namespace photocommunity\mobile;


class Geo
{
    public static $is_robot = false;

    # USA - default country
    public static $CountryCode = 'US';
    public static $CountryName = '';
    public static $RegionCode = '';
    public static $RegionName = '';
    public static $City = '';
    public static $Gmtoffset = -14400; # New York

    public static function inst()
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new Geo();
        }
        return $instance;
    }

    public static function setGeo()
    {
        if (!isset($_SESSION['CountryCode']) || !isset($_SESSION['Gmtoffset'])) {

            require_once dirname(__FILE__) . '/../../maxmindme/index.php';
            $record = getGeoIp(Config::$remote_addr);

            Geo::$CountryCode = '';
            Geo::$CountryName = '';
            Geo::$RegionCode = '';
            Geo::$RegionName = '';
            Geo::$City = '';

            if ($record) {
                Geo::$CountryCode = $record->country->isoCode;
                Geo::$CountryName = $record->country->names['en'];
                Geo::$RegionCode = $record->mostSpecificSubdivision->isoCode;
                Geo::$RegionName = $record->mostSpecificSubdivision->name;
                Geo::$City = $record->city->names['en'];
            }

            if ($record && $record->location->timeZone != '') {
                if ($record->location->timeZone == 'Asia/Barnaul' || $record->location->timeZone == 'Asia/Tomsk')
                    $timezone = 'Asia/Krasnoyarsk';
                else if ($record->location->timeZone == 'Europe/Kirov' || $record->location->timeZone == 'Europe/Ulyanovsk')
                    $timezone = 'Europe/Moscow';
                else
                    $timezone = $record->location->timeZone;
                $dateTimeZone = new \DateTimeZone($timezone);
                $dateTime = new \DateTime("now", $dateTimeZone);
                Geo::$Gmtoffset = $dateTime->format('Z'); # 'Z' is UTC Offset in seconds

                if (strstr(Geo::$RegionName, 'Novosibirskaya Oblast') || strstr(Geo::$RegionName, 'Ulyanovsk Oblast') || strstr(Geo::$RegionName, 'Samarskaya Oblast') || strstr(Geo::$RegionName, 'Kemerovskaya Oblast') || Geo::$RegionName == 'Udmurtskaya Respublika') {
                    if (Config::$is_winter_time) Geo::$Gmtoffset += 3600; # todo - check in winter
                    else Geo::$Gmtoffset += 3600;
                } else if (Geo::$RegionName == 'Republic of Crimea' || Geo::$RegionName == 'Gorod Sevastopol') {
                    if (Config::$is_winter_time) Geo::$Gmtoffset -= 3600; # todo - check in winter
                    else Geo::$Gmtoffset -= 3600;
                } else if (Geo::$CountryCode == 'AZ') {
                    if (Config::$is_winter_time) Geo::$Gmtoffset -= 3600; # todo - check in winter
                    else Geo::$Gmtoffset -= 3600;
                }
            } else if (Geo::$CountryCode == 'RU') { # Moscow
                if (Config::$is_winter_time) Geo::$Gmtoffset = 14400; # todo - check in winter
                else Geo::$Gmtoffset = 14400;
            } else if (Geo::$CountryCode == 'BY') { # Minsk
                if (Config::$is_winter_time) Geo::$Gmtoffset = 10800; # todo - check in winter
                else Geo::$Gmtoffset = 10800;
            } else if (Geo::$CountryCode == 'UA') { # Kiev
                if (Config::$is_winter_time) Geo::$Gmtoffset = 7200; # todo - check in winter
                else Geo::$Gmtoffset = 7200;
            } else if (Geo::$CountryCode == 'CA') { # Toronto
                if (Config::$is_winter_time) Geo::$Gmtoffset = -14400; # todo - check in winter
                else Geo::$Gmtoffset = -14400;
            } else if (Geo::$CountryCode == 'US') { # New York
                if (Config::$is_winter_time) Geo::$Gmtoffset = -14400; # todo - check in winter
                else Geo::$Gmtoffset = -14400;
            } else { # Berlin
                if (Config::$is_winter_time) Geo::$Gmtoffset = 3600; # todo - check in winter
                else Geo::$Gmtoffset = 7200;
            }

            if (isset($_SESSION['auth']['id_auth']) || (!isset($_SESSION['auth']['id_auth']) && isset($_COOKIE['X']))) {
                if (Geo::$City == '' || Geo::$CountryCode == '') { # if unknown City or Country, try "ipinfodb.com"
                    $geo_url = 'http://api.ipinfodb.com/v3/ip-city/?key=690cd62dd09b3da2ef3b47e0a156e4e40ff1309dbc3d8777ff285ff1c3140d3c&ip=' . Config::$remote_addr;
                    #echox($geo_url);
                    $curl = curl_init();
                    curl_setopt($curl, CURLOPT_URL, $geo_url);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl, CURLOPT_TIMEOUT_MS, 10000);
                    $geo_content = curl_exec($curl);
                    curl_close($curl);
                    $geo_content_arr = explode(';', $geo_content); #printArr($geo_content_arr);

                    if (isset($geo_content_arr[3]) && $geo_content_arr[3] != '') { # if country code from "ipinfodb.com" not empty
                        Geo::$CountryCode = $geo_content_arr[3];
                        Geo::$CountryName = $geo_content_arr[4];
                        Geo::$RegionCode = '';
                        Geo::$RegionName = $geo_content_arr[5];
                        Geo::$City = $geo_content_arr[6];
                        $Gmtoffset_arr = explode(':', $geo_content_arr[10]);
                        Geo::$Gmtoffset = $Gmtoffset_arr[0] * 3600 + $Gmtoffset_arr[1] * 60;

                        if (Geo::$CountryCode == 'RU') {
                            if (Config::$is_winter_time) Geo::$Gmtoffset += 3600; # todo - check in winter
                            else Geo::$Gmtoffset += 3600;

                            if (Geo::$RegionName == 'Kemerovo' || Geo::$RegionName == 'Udmurt' || Geo::$RegionName == 'Altaisky krai') {
                                if (Config::$is_winter_time) Geo::$Gmtoffset += 7200; # todo - check in winter
                                else Geo::$Gmtoffset += 7200;
                            }
                        } else if (Geo::$CountryCode == 'BY') {
                            if (Config::$is_winter_time) Geo::$Gmtoffset += 3600; # todo - check in winter
                            else Geo::$Gmtoffset += 3600;
                        }
                    }
                }
            }

            $_SESSION['CountryCode'] = Geo::$CountryCode;
            $_SESSION['CountryName'] = Geo::$CountryName;
            $_SESSION['RegionCode'] = Geo::$RegionCode;
            $_SESSION['RegionName'] = Geo::$RegionName;
            $_SESSION['City'] = Geo::$City;
            $_SESSION['Gmtoffset'] = Geo::$Gmtoffset;

            if (Config::$http_referrer != '' && !strstr(Config::$http_referrer, 'http://googleads.') && !strstr(Config::$http_referrer, 'http://' . Config::SITE_DOMAIN ) && !strstr(Config::$http_referrer, 'http://' . Config::SITE_DOMAIN_BY)) $_SESSION['Referer'] = Config::$http_referrer;
            else $_SESSION['Referer'] = '';

            if(Config::$SiteDom)
                setcookie('ccode', Geo::$CountryCode, Config::$cookie_expires, '/', '.' . Config::$SiteDom . '.' . Config::$domainEnd);
        } else {
            Geo::$CountryCode = $_SESSION['CountryCode'];
            Geo::$CountryName = $_SESSION['CountryName'];
            Geo::$RegionCode = $_SESSION['RegionCode'];
            Geo::$RegionName = $_SESSION['RegionName'];
            Geo::$City = $_SESSION['City'];
            Geo::$Gmtoffset = $_SESSION['Gmtoffset'];
        }
    }

    public static function isRobot()
    {
        $ip_long = ip2long(Config::$remote_addr);

        if (Auth::getIdAuth()) {
            #ok - user is logged
        } else if (Config::$remote_addr == Config::SERVER_IP) {
            Geo::$is_robot = true;
        } else if (false
            || ($ip_long >= ip2long('66.102.1.1') && $ip_long <= ip2long('66.102.12.255')) #google
            || ($ip_long >= ip2long('66.249.64.1') && $ip_long <= ip2long('66.249.95.255')) #google
            || ($ip_long >= ip2long('72.14.192.1') && $ip_long <= ip2long('72.14.255.255')) #google
            || ($ip_long >= ip2long('74.125.1.1') && $ip_long <= ip2long('74.125.255.255')) #google
            || ($ip_long >= ip2long('209.85.132.1') && $ip_long <= ip2long('209.85.255.255')) #google
        ) {
            Geo::$is_robot = true;
        } else if (false
            || ($ip_long >= ip2long('77.88.41.177') && $ip_long <= ip2long('77.88.44.206')) #yandex
            || ($ip_long >= ip2long('93.158.128.1') && $ip_long <= ip2long('93.158.191.255')) #yandex
            || ($ip_long >= ip2long('95.108.128.1') && $ip_long <= ip2long('95.108.255.255')) #yandex
            || ($ip_long >= ip2long('100.43.64.1') && $ip_long <= ip2long('100.43.95.255')) #yandex
            || ($ip_long >= ip2long('141.8.128.1') && $ip_long <= ip2long('141.8.191.254')) #yandex
            || ($ip_long >= ip2long('178.154.128.1') && $ip_long <= ip2long('178.155.255.255')) #yandex
            || ($ip_long >= ip2long('199.21.96.1') && $ip_long <= ip2long('199.21.100.255')) #yandex
            || ($ip_long >= ip2long('213.180.198.177') && $ip_long <= ip2long('213.180.198.188')) #yandex
        ) {
            Geo::$is_robot = true;
        } else if (false
            || ($ip_long >= ip2long('62.213.92.4') && $ip_long <= ip2long('62.213.92.4')) #uniplace
            || ($ip_long >= ip2long('62.213.126.12') && $ip_long <= ip2long('62.213.126.12')) #uniplace
            || ($ip_long >= ip2long('78.142.224.1') && $ip_long <= ip2long('78.142.229.255')) #uniplace
            || ($ip_long >= ip2long('94.77.64.50') && $ip_long <= ip2long('94.77.117.255')) #uniplace
        ) {
            Geo::$is_robot = true;
        } else if (false
            || ($ip_long >= ip2long('69.31.80.1') && $ip_long <= ip2long('69.31.87.255')) #trustlink
            || ($ip_long >= ip2long('88.208.14.1') && $ip_long <= ip2long('88.208.59.255')) #trustlink
            || ($ip_long >= ip2long('91.243.116.6') && $ip_long <= ip2long('91.243.116.6')) #trustlink
            || ($ip_long >= ip2long('178.162.208.227') && $ip_long <= ip2long('178.162.208.232')) #trustlink
        ) {
            Geo::$is_robot = true;
        } else if (false
            || ($ip_long >= ip2long('65.52.1.1') && $ip_long <= ip2long('65.55.254.255')) #bing
            || ($ip_long >= ip2long('66.220.144.1') && $ip_long <= ip2long('66.220.159.254')) #facebook
            || ($ip_long >= ip2long('67.195.1.1') && $ip_long <= ip2long('67.195.255.255')) #yahoo
            || ($ip_long >= ip2long('68.180.225.1') && $ip_long <= ip2long('68.180.225.255')) #yahoo
            || ($ip_long >= ip2long('69.171.224.1') && $ip_long <= ip2long('69.171.255.254')) #facebook
            || ($ip_long >= ip2long('72.30.1.1') && $ip_long <= ip2long('74.6.255.255')) #yahoo
            || strstr(Config::$remote_addr, '95.211.81.86') #solomono.ru
            || ($ip_long >= ip2long('98.136.1.1') && $ip_long <= ip2long('98.139.255.255')) #yahoo
            || ($ip_long >= ip2long('109.207.13.19') && $ip_long <= ip2long('109.207.13.51')) #e-government
            || ($ip_long >= ip2long('131.253.32.0') && $ip_long <= ip2long('131.253.47.255')) #bing
            || strstr(Config::$remote_addr, '144.76.63.12') #ingots.ru
            || ($ip_long >= ip2long('157.55.16.23') && $ip_long <= ip2long('157.59.255.255')) #msn
            || ($ip_long >= ip2long('188.72.80.204') && $ip_long <= ip2long('188.72.80.220')) #sape
            || ($ip_long >= ip2long('188.165.15.1') && $ip_long <= ip2long('188.165.15.255')) #ahrefs
            || ($ip_long >= ip2long('193.232.121.204') && $ip_long <= ip2long('193.232.121.220')) #sape
            || ($ip_long >= ip2long('199.16.156.1') && $ip_long <= ip2long('199.16.159.254')) #twitter
            || ($ip_long >= ip2long('199.30.16.1') && $ip_long <= ip2long('199.30.31.255')) #msn
            || ($ip_long >= ip2long('199.96.56.1') && $ip_long <= ip2long('199.96.63.254')) #twitter
            || ($ip_long >= ip2long('207.46.1.1') && $ip_long <= ip2long('207.46.254.255')) #msn
            || ($ip_long >= ip2long('209.222.8.1') && $ip_long <= ip2long('209.222.8.255')) #ahrefs
            || ($ip_long >= ip2long('217.69.128.1') && $ip_long <= ip2long('217.69.143.255')) #mail.ru
        ) {
            Geo::$is_robot = true;
        } else if (false
            || strstr(Config::$http_user_agent, 'vkShare') || strstr(Config::$http_user_agent, 'vk.com')
            || strstr(Config::$http_user_agent, 'facebookexternalhit') || strstr(Config::$http_user_agent, 'facebook.com')
            || strstr(Config::$http_user_agent, 'Googlebot')
            || strstr(Config::$http_user_agent, 'Yandex')
            || strstr(Config::$http_user_agent, 'Mail.Ru')
            || strstr(Config::$http_user_agent, 'Yahoo')
            || strstr(Config::$http_user_agent, 'Rambler')
            || strstr(Config::$http_user_agent, 'msnbot')
            || strstr(Config::$http_user_agent, 'TinEye')
            || strstr(Config::$http_user_agent, 'W3C_Validator')
            || strstr(Config::$http_user_agent, 'picsearch')
            || strstr(Config::$http_user_agent, 'bingbot')
        ) {
            Geo::$is_robot = true;
        }
    }

    /* change Language param */
    private static function getChangeLangCookie()
    {

        if (isset($_COOKIE['chla'])) $chla = 1;
        else $chla = 0;
        if (isset($_GET['chla'])) {
            $chla = 1;
            if(Config::$SiteDom)
                setcookie('chla', $chla, Config::$cookie_expires, '/', '.' . Config::$SiteDom . '.' . Config::$domainEnd);
        }
        return $chla;
    }

    public static function redirectToAllowedDomain()
    {
        if (Geo::getChangeLangCookie() === 1 || Geo::$is_robot) {
            # allow to use any domain if user was logged from this domain
        } else {
            if (Config::$domainEnd == 'de') {
                if (Geo::$CountryCode != 'DE' && Geo::$CountryCode != 'AT' && Geo::$CountryCode != 'CH') {
                    header('location: http://m.' . Config::SITE_DOMAIN . '.ru' . Config::$request_uri);
                    #return false;
                }
            } else if (Config::$domainEnd == 'com') {
                # ok do nothing
            } else if (Config::$domainEnd == 'ru' || Config::$domainEnd == 'by') { # .ru .by
                if (Geo::$CountryCode == 'DE' || Geo::$CountryCode == 'AT' || Geo::$CountryCode == 'CH') {
                    header('location: http://m.' . Config::SITE_DOMAIN . '.de' . Config::$request_uri);
                    #return false;
                }
            }
        }
        #return true;
    }
}