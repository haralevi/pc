<?php
/**
 * Created by Andre Haralevi
 * Date: 27.12.2015
 * Time: 01:30
 */

namespace Photocommunity\Mobile;

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
                else if ($record->location->timeZone == 'Europe/Kirov')
                    $timezone = 'Europe/Moscow';
                else if ($record->location->timeZone == 'Europe/Ulyanovsk' || $record->location->timeZone == 'Europe/Astrakhan')
                    $timezone = 'Europe/Samara';
                else
                    $timezone = $record->location->timeZone;

                try {
                    $dateTimeZone = new \DateTimeZone($timezone);
                } catch (\Exception $e) {
                    # error timezone is not found
                }
                $dateTime = new \DateTime("now", $dateTimeZone);
                Geo::$Gmtoffset = $dateTime->format('Z'); # 'Z' is UTC Offset in seconds

                if (Geo::$RegionName == 'Republic of Crimea' || Geo::$RegionName == 'Gorod Sevastopol') {
                    Geo::$Gmtoffset -= 0;
                } else if (Geo::$CountryCode == 'AZ') {
                    if (Config::$is_winter_time) Geo::$Gmtoffset -= 0;
                    else Geo::$Gmtoffset -= 0;
                }
            } else if (Geo::$CountryCode == 'RU') { # Moscow
                Geo::$Gmtoffset = 14400;
            } else if (Geo::$CountryCode == 'BY') { # Minsk
                Geo::$Gmtoffset = 10800;
            } else if (Geo::$CountryCode == 'UA') { # Kiev
                if (Config::$is_winter_time) Geo::$Gmtoffset = 7200;
                else Geo::$Gmtoffset = 10800;
            } else if (Geo::$CountryCode == 'CA') { # Toronto
                Geo::$Gmtoffset = -14400;
            } else if (Geo::$CountryCode == 'US') { # New York
                Geo::$Gmtoffset = -14400;
            } else { # Berlin
            	if (Config::$is_winter_time) Geo::$Gmtoffset = 3600;
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
                            Geo::$Gmtoffset += 0;
                            if (Geo::$RegionName == 'Kemerovo' || Geo::$RegionName == 'Udmurt' || Geo::$RegionName == 'Altaisky krai')
                                Geo::$Gmtoffset += 7200;
                            else if (Geo::$RegionName == 'Tomskaya oblast\'')
                            	Geo::$Gmtoffset += 14400;
                        } else if (Geo::$CountryCode == 'BY') {
                             Geo::$Gmtoffset += 0;
                        } else if (Geo::$CountryCode == 'LV') {
                            Geo::$Gmtoffset -= 3600;
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

            if (Config::$http_referer != '' && !strstr(Config::$http_referer, 'http://googleads.') && !strstr(Config::$http_referer, 'http://' . Config::SITE_DOMAIN) && !strstr(Config::$http_referer, 'http://' . Config::SITE_DOMAIN_BY)) $_SESSION['Referer'] = Config::$http_referer;
            else $_SESSION['Referer'] = '';

            if (Config::$SiteDom)
                setcookie('ccode', Geo::$CountryCode, Config::$cookie_expires, '/', '.' . Config::$SiteDom . '.' . Config::$domainEnd);
        } else {
            Geo::$CountryCode = $_SESSION['CountryCode'];
            Geo::$CountryName = $_SESSION['CountryName'];
            Geo::$RegionCode = $_SESSION['RegionCode'];
            Geo::$RegionName = $_SESSION['RegionName'];
            Geo::$City = $_SESSION['City'];
            Geo::$Gmtoffset = $_SESSION['Gmtoffset'];
        }

        # Timezone offset manual
        if (isset($_SESSION['auth']['id_auth'])) {
            if ($_SESSION['auth']['id_auth'] == 21419) # Bluejay - wants to be in europe
                $_SESSION['Gmtoffset'] = 3600;
            else if ($_SESSION['auth']['id_auth'] == 29057) # Sever - wants to upload at 21:00
                $_SESSION['Gmtoffset'] = 18000;
            else if ($_SESSION['auth']['id_auth'] == 26702) # 8ele8 - wants to be in moscow
                $_SESSION['Gmtoffset'] = 14400;
            
            if ($_SESSION['Gmtoffset'] == 1 || $_SESSION['Gmtoffset'] == 24) {
                Config::$remote_addr = '78.53.8.66';
                Geo::$CountryName = 'Germany';
                Geo::$CountryCode = 'DE';
                Geo::$City = 'Berlin';
                if (Config::$is_winter_time) Geo::$Gmtoffset = 3600;
                else Geo::$Gmtoffset = 7200;
            }
            /**/
            Geo::$Gmtoffset = $_SESSION['Gmtoffset'];
        }
    }

    public static function isRobot()
    {
        if (!isset($_SESSION['is_robot'])) {
            $ip_long = ip2long(Config::$remote_addr);
            if (isset($_SESSION['auth']['id_auth'])) {
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
                Geo::$is_robot = true; #$is_robot = false;
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
                Geo::$is_robot = true; #$is_robot = false;
            } else if (false
                || ($ip_long >= ip2long('62.213.92.4') && $ip_long <= ip2long('62.213.92.4')) #uniplace
                || ($ip_long >= ip2long('62.213.126.12') && $ip_long <= ip2long('62.213.126.12')) #uniplace
                || ($ip_long >= ip2long('78.142.224.1') && $ip_long <= ip2long('78.142.229.255')) #uniplace
                || ($ip_long >= ip2long('92.242.36.222') && $ip_long <= ip2long('92.242.36.222')) #uniplace
                || ($ip_long >= ip2long('94.77.64.50') && $ip_long <= ip2long('94.77.117.255')) #uniplace
                || ($ip_long >= ip2long('178.20.235.164') && $ip_long <= ip2long('178.20.235.165')) #uniplace
            ) {
                Geo::$is_robot = true; #$is_robot = false;
            } else if (false
                || ($ip_long >= ip2long('69.31.80.1') && $ip_long <= ip2long('69.31.87.255')) #trustlink
                || ($ip_long >= ip2long('88.208.14.1') && $ip_long <= ip2long('88.208.59.255')) #trustlink
                || ($ip_long >= ip2long('91.243.116.6') && $ip_long <= ip2long('91.243.116.6')) #trustlink
                || ($ip_long >= ip2long('178.162.208.227') && $ip_long <= ip2long('178.162.208.232')) #trustlink
                || ($ip_long >= ip2long('185.61.216.1') && $ip_long <= ip2long('185.79.139.255')) #trustlink
            ) {
                Geo::$is_robot = true; #$is_robot = false;
            } else if (false
                || strstr(Config::$remote_addr, '5.79.68.55') || strstr(Config::$remote_addr, '5.79.68.56') #linkpad.ru
                || ($ip_long >= ip2long('5.143.224.1') && $ip_long <= ip2long('5.143.231.255')) #sputnik
                || strstr(Config::$remote_addr, '64.79.85.205') #similartech
                || ($ip_long >= ip2long('65.52.1.1') && $ip_long <= ip2long('65.55.254.255')) #bing
                || ($ip_long >= ip2long('66.220.144.1') && $ip_long <= ip2long('66.220.159.254')) #facebook
                || ($ip_long >= ip2long('67.195.1.1') && $ip_long <= ip2long('67.195.255.255')) #yahoo
                || ($ip_long >= ip2long('68.180.127.1') && $ip_long <= ip2long('68.180.255.255')) #yahoo
                || ($ip_long >= ip2long('69.171.224.1') && $ip_long <= ip2long('69.171.255.254')) #facebook
                || ($ip_long >= ip2long('72.30.1.1') && $ip_long <= ip2long('74.6.255.255')) #yahoo
                || strstr(Config::$remote_addr, '78.46.98.236') #LinkFeatureBot
                || strstr(Config::$remote_addr, '95.211.81.86') #solomono.ru
                || ($ip_long >= ip2long('98.136.1.1') && $ip_long <= ip2long('98.139.255.255')) #yahoo
                || ($ip_long >= ip2long('109.207.13.19') && $ip_long <= ip2long('109.207.13.51')) #e-government
                || ($ip_long >= ip2long('131.253.32.0') && $ip_long <= ip2long('131.253.47.255')) #bing
                || ($ip_long >= ip2long('136.243.36.83') && $ip_long <= ip2long('136.243.36.93')) #BLEXBot
                || strstr(Config::$remote_addr, '144.76.63.12') #ingots.ru
                || strstr(Config::$remote_addr, '148.251.136.8') #BLEXBot
                || ($ip_long >= ip2long('157.55.16.23') && $ip_long <= ip2long('157.59.255.255')) #msn
                || ($ip_long >= ip2long('185.53.44.1') && $ip_long <= ip2long('185.53.47.255')) #xovibot
                || ($ip_long >= ip2long('188.72.80.204') && $ip_long <= ip2long('188.72.80.220')) #sape
                || ($ip_long >= ip2long('193.232.121.204') && $ip_long <= ip2long('193.232.121.220')) #sape
                || ($ip_long >= ip2long('199.16.156.1') && $ip_long <= ip2long('199.16.159.254')) #twitter
                || ($ip_long >= ip2long('199.30.16.1') && $ip_long <= ip2long('199.30.31.255')) #msn
                || ($ip_long >= ip2long('199.96.56.1') && $ip_long <= ip2long('199.96.63.254')) #twitter
                || ($ip_long >= ip2long('207.46.1.1') && $ip_long <= ip2long('207.46.254.255')) #msn
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
                || strstr(Config::$http_user_agent, 'MJ12bot')
                || strstr(Config::$http_user_agent, 'AhrefsBot')
                || strstr(Config::$http_user_agent, 'semrush.com')
                || strstr(Config::$http_user_agent, 'megaindex')
            ) {
                Geo::$is_robot = true;
            }
            $_SESSION['is_robot'] = Geo::$is_robot;
        } else
            Geo::$is_robot = $_SESSION['is_robot'];
    }

    /* change Language param */
    public static function getChangeLangCookie()
    {
        if (isset($_COOKIE['chla'])) $chla = 1;
        else $chla = 0;
        if (isset($_GET['chla'])) {
            $chla = 1;
            if (Config::$SiteDom)
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
                    header('Location: ' . Utils::getChangeLangUrl('ru'));
                    die();
                }
            } else if (Config::$domainEnd == 'com') {
                # ok do nothing
            } else if (Config::$domainEnd == 'ru' || Config::$domainEnd == 'by') { # .ru .by
                if (Geo::$CountryCode == 'DE' || Geo::$CountryCode == 'AT' || Geo::$CountryCode == 'CH') {
                    header('Location: ' . Utils::getChangeLangUrl('de'));
                    die();
                }
            }
        }
    }
}