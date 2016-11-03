<?php
namespace photocommunity\mobile;

error_reporting(30719);
ini_set("display_errors", 1);

require dirname(__FILE__) . '/../classes/Config.php';

Config::inst();

ini_set('session.cookie_domain', '.' . Config::$SiteDom . '.' . Config::$domainEnd);
session_start();

$guest_sess = session_id();

$mcache = new \Memcached();
$mcache->addServer('localhost', 11211);
$now_online = $mcache->get(md5('now_online'));
unset($now_online[$guest_sess]);
$mcache->set(md5('now_online'), $now_online, 0);

unset($_SESSION['auth']);

if(Config::$SiteDom) {
    setcookie('X', '', Config::$cur_time - 86400);
    setcookie('X', '', Config::$cur_time - 86400, '/', '.' . Config::$SiteDom . '.' . Config::$domainEnd);

    setcookie('auth_answers_cnt', '', Config::$cur_time - 86400);
    setcookie('auth_answers_cnt', '', Config::$cur_time - 86400, '/', '.' . Config::$SiteDom . '.' . Config::$domainEnd);

    setcookie('share_views_cnt', '', Config::$cur_time - 86400);
    setcookie('share_views_cnt', '', Config::$cur_time - 86400, '/', '.' . Config::$SiteDom . '.' . Config::$domainEnd);
}

session_unset();
session_destroy();

if (isset($_SERVER['HTTP_REFERER']))
    header('location: ' . $_SERVER['HTTP_REFERER']);
else
    header('location: index.php');