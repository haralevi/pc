<?php
/**
 * Created by Andre Haralevi
 * Date: 06.11.13
 * Time: 17:12
 */

namespace photocommunity\mobile;

class Auth
{
    private static $beta_users = array(23114, 1311, 21533, 24164, 23320, 14426, 22288, 3563, 19904, 5105, 19430, 4134, 17679, 1, 26, 8073, 5486, 2100, 1485, 1627, 1957, 1762, 3264, 3329, 940, 9220, 3686, 3906, 4154, 2645, 6141, 183, 140, 5250, 794, 3164);

    private static $id_auth = -1;
    private static $auth_key = '';
    private static $auth_facebook_id = '';
    private static $auth_login = '';
    private static $auth_type = Consta::AUTH_TYPE_DEF;
    private static $auth_port_lang = 'en';
    private static $auth_premium = Consta::AUTH_PREMIUM_0;
    private static $auth_birth_time = 0;
    private static $auth_rating = 0;
    private static $auth_img_cnt = 0;
    private static $auth_name = '';
    private static $auth_name_en = '';
    private static $auth_dom = '';
    private static $auth_email = '';
    private static $auth_power = Consta::MIN_AUTH_POWER;
    private static $auth_gender = 0;
    private static $auth_avatar = '';
    private static $auth_avatar_w = Consta::AVATAR_WIDTH;
    private static $auth_avatar_h = Consta::AVATAR_WIDTH;
    private static $auth_mood = '';
    private static $auth_mood_en = '';
    private static $auth_mood_de = '';
    private static $auth_blog_favor_cnt = 0;
    private static $auth_country_id = 0;
    private static $auth_region_id = 0;
    private static $auth_fineart_gall = 0;
    private static $auth_square_gall = 0;
    private static $auth_nu_gall = 0;
    private static $auth_window_gall = 0;
    private static $auth_index_layout = '';
    private static $auth_featured_rating = Consta::RECOMM_MIN_RATING;
    private static $auth_featured_link = '';
    private static $auth_show_all_comms = 0;
    private static $auth_port_dom = '';
    private static $auth_last_recs_cnt = 0;

    private static $work_gall_limit = 100;

    public static $guest_sess;

    public static function inst()
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new Auth();
        }
        return $instance;
    }

    /**
     * Private __construct so nobody else can instance it
     */
    private function __construct()
    {

    }

    public static function setWorkGallLimit()
    {
        if (Config::$domainEnd == 'ru' || Config::$domainEnd == 'by') {
            Auth::$work_gall_limit = Consta::WORK_GALL_LIMIT_4;
        } else {
            if (Auth::getAuthPremium() == Consta::AUTH_PREMIUM_1)
                Auth::$work_gall_limit = Consta::WORK_GALL_LIMIT_1;
            else if (Auth::getAuthPremium() == Consta::AUTH_PREMIUM_2)
                Auth::$work_gall_limit = Consta::WORK_GALL_LIMIT_2;
            else if (Auth::getAuthPremium() == Consta::AUTH_PREMIUM_3)
                Auth::$work_gall_limit = Consta::WORK_GALL_LIMIT_3;
            else if (Auth::getAuthPremium() == Consta::AUTH_PREMIUM_4)
                Auth::$work_gall_limit = Consta::WORK_GALL_LIMIT_4;
            else
                Auth::$work_gall_limit = Consta::WORK_GALL_LIMIT_0;
        }
    }

    public static function login()
    {
        $auth_login = Request::getParam('auth_login', 'string');
        $auth_pass = Request::getParam('auth_pass', 'string');
        if ($auth_login && $auth_pass) {
            if (Config::getDebug()) $where_pass = '';
            else $where_pass = "AND auth_pass='" . md5($auth_pass) . "'";
            $sql = "SELECT id_auth, auth_key, auth_facebook_id, auth_type, auth_port_lang, auth_premium, auth_status, auth_login, auth_pass, auth_birth_time, auth_rating, auth_img_cnt, auth_answers_cnt,
						auth_country_id, auth_region_id, auth_city_id,
						auth_name, auth_name_en, auth_dom, auth_email, auth_power, auth_last_recs_cnt,
						auth_gender, auth_avatar, auth_avatar_w, auth_avatar_h, auth_blog_favor_cnt, auth_mood, auth_mood_en, auth_mood_de,
						auth_fineart_gall, auth_square_gall, auth_nu_gall, auth_window_gall, auth_index_layout, auth_featured_rating, auth_featured_link, auth_show_all_comms, auth_port_dom
                    FROM ds_authors
                    WHERE (BINARY auth_login='" . $auth_login . "' OR BINARY auth_email='" . $auth_login . "') " . $where_pass . " LIMIT 1";
            $res_login = Db::execute($sql);
            if (sizeof($res_login))
                Auth::login_author($res_login);
            else {
                $request_uri_wrong_login = Auth::removeLoginParams(Config::$request_uri);
                $request_uri_wrong_login = Utils::addParam($request_uri_wrong_login, 'wrn_login', 1);
                $request_uri_wrong_login = str_replace('&amp;', '&', $request_uri_wrong_login);
                header('location: ' . Config::$http_scheme . Config::$subDomain . Config::$SiteDom . '.' . Config::$domainEnd . $request_uri_wrong_login);
                #return false;
            }
        } else if (!isset($_SESSION['auth']['id_auth']) || !isset($_SESSION['auth']['auth_name'])) {
            if (isset($_COOKIE['X'])) {
                $sql = "SELECT id_auth, auth_key, auth_facebook_id, auth_type, auth_port_lang, auth_premium, auth_status, auth_login, auth_pass, auth_birth_time, auth_rating, auth_img_cnt, auth_answers_cnt,
						auth_country_id, auth_region_id, auth_city_id,
						auth_name, auth_name_en, auth_dom, auth_email, auth_power, auth_last_recs_cnt,
						auth_gender, auth_avatar, auth_avatar_w, auth_avatar_h, auth_blog_favor_cnt, auth_mood, auth_mood_en, auth_mood_de,
						auth_fineart_gall, auth_square_gall, auth_nu_gall, auth_window_gall, auth_index_layout, auth_featured_rating, auth_featured_link, auth_show_all_comms, auth_port_dom
                    FROM ds_authors
                    WHERE auth_key='" . $_COOKIE['X'] . "' LIMIT 1";
                $res_login = Db::execute($sql);
                if (sizeof($res_login))
                    Auth::login_author($res_login);
            } else {
                if (!isset($_COOKIE['Y'])) {
                    $mark_key = rand(111111, 999999) . substr(Config::$cur_time, 3, 7);
                    if (Config::$SiteDom)
                        setcookie('Y', $mark_key, Config::$cookie_expires, '/', '.' . Config::$SiteDom . '.' . Config::$domainEnd);
                }
            }
        }

        if (isset($_SESSION['auth']['id_auth'])) {
            Auth::$id_auth = $_SESSION['auth']['id_auth'];
            Auth::$auth_key = $_SESSION['auth']['auth_key'];
            Auth::$auth_facebook_id = $_SESSION['auth']['auth_facebook_id'];
            Auth::$auth_login = $_SESSION['auth']['auth_login'];
            Auth::$auth_type = $_SESSION['auth']['auth_type'];
            Auth::$auth_port_lang = $_SESSION['auth']['auth_port_lang'];
            Auth::$auth_premium = $_SESSION['auth']['auth_premium'];
            Auth::$auth_birth_time = $_SESSION['auth']['auth_birth_time'];
            Auth::$auth_rating = $_SESSION['auth']['auth_rating'];
            Auth::$auth_img_cnt = $_SESSION['auth']['auth_img_cnt'];
            Auth::$auth_name = $_SESSION['auth']['auth_name'];
            Auth::$auth_name_en = $_SESSION['auth']['auth_name_en'];
            Auth::$auth_dom = $_SESSION['auth']['auth_dom'];
            Auth::$auth_email = $_SESSION['auth']['auth_email'];
            Auth::$auth_power = $_SESSION['auth']['auth_power'];
            Auth::$auth_gender = $_SESSION['auth']['auth_gender'];
            Auth::$auth_avatar = $_SESSION['auth']['auth_avatar'];
            Auth::$auth_avatar_w = $_SESSION['auth']['auth_avatar_w'];
            Auth::$auth_avatar_h = $_SESSION['auth']['auth_avatar_h'];
            Auth::$auth_mood = $_SESSION['auth']['auth_mood'];
            Auth::$auth_mood_en = $_SESSION['auth']['auth_mood_en'];
            Auth::$auth_mood_de = $_SESSION['auth']['auth_mood_de'];
            Auth::$auth_blog_favor_cnt = $_SESSION['auth']['auth_blog_favor_cnt'];
            Auth::$auth_country_id = $_SESSION['auth']['auth_country_id'];
            Auth::$auth_region_id = $_SESSION['auth']['auth_region_id'];
            Auth::$auth_fineart_gall = $_SESSION['auth']['auth_fineart_gall'];
            Auth::$auth_square_gall = $_SESSION['auth']['auth_square_gall'];
            Auth::$auth_nu_gall = $_SESSION['auth']['auth_nu_gall'];
            Auth::$auth_window_gall = $_SESSION['auth']['auth_window_gall'];
            Auth::$auth_index_layout = $_SESSION['auth']['auth_index_layout'];
            Auth::$auth_featured_rating = $_SESSION['auth']['auth_featured_rating'];
            Auth::$auth_featured_link = $_SESSION['auth']['auth_featured_link'];
            Auth::$auth_show_all_comms = $_SESSION['auth']['auth_show_all_comms'];
            Auth::$auth_port_dom = $_SESSION['auth']['auth_port_dom'];
        }

        # remove login information from url
        if ($auth_login || $auth_pass) {
            header('location: ' . Config::$http_scheme . Config::$subDomain . Config::$SiteDom . '.' . Config::$domainEnd . Auth::removeLoginParams(Config::$request_uri));
            #return false;
        }
        #return true;
    }

    private static function removeLoginParams($uri)
    {
        $uri = Utils::removeParam($uri, 'auth_login');
        $uri = Utils::removeParam($uri, 'auth_pass');
        if (Utils::endsWith($uri, '?'))
            $uri = substr($uri, 0, -1);
        $uri = str_replace('&amp;', '&', $uri);
        return $uri;
    }

    private static function login_author($res)
    {
        # check if user allowed to use this domain
        if (isset($_COOKIE['chla']) || isset($_GET['chla'])) {
            #ok domain is allowed
        } else if (Config::$domainEnd == $res[0]['auth_port_lang']) {
            #ok domain is allowed
        } else if ((Config::$domainEnd == 'ru' || Config::$domainEnd == 'by') && ($res[0]['auth_port_lang'] == 'ru' || $res[0]['auth_port_lang'] == 'by')) {
            #ok domain is allowed
        } else {
            if (Config::$domainEnd == 'de') {
                if ($res[0]['auth_port_lang'] != 'de') {
                    header('location: http://m.' . Config::SITE_DOMAIN . '.ru' . str_replace('&amp;', '&', Utils::addParam(Config::$request_uri, 'chla', 1)));
                    #return false;
                }
            } else if (Config::$domainEnd == 'com') {
                # ok do nothing
            } else if (Config::$domainEnd == 'ru' || Config::$domainEnd == 'by') { # .ru .by
                if ($res[0]['auth_port_lang'] == 'de') {
                    header('location: http://m.' . Config::SITE_DOMAIN . '.de' . str_replace('&amp;', '&', Utils::addParam(Config::$request_uri, 'chla', 1)));
                    #return false;
                }
            }
        }

        if (!$res[0]['auth_status']) {
            if (Config::$SiteDom) {
                setcookie('X', '', Config::$cur_time - 86400);
                setcookie('X', '', Config::$cur_time - 86400, '/', '.' . Config::$SiteDom . '.' . Config::$domainEnd);
            }

            header('location: ' . Config::$home_url . 'contact.php?blk_login=1');
            #return false;
        }

        if ($res[0]['auth_country_id'] == 0 || $res[0]['auth_region_id'] == 0 || $res[0]['auth_city_id'] == 0) $_SESSION['auth']['from'] = 0;
        else $_SESSION['auth']['from'] = 1;
        if ($res[0]['auth_birth_time'] == 0) $_SESSION['auth']['birth'] = 0;
        else $_SESSION['auth']['birth'] = 1;

        $_SESSION['auth']['id_auth'] = $res[0]['id_auth'];
        $_SESSION['auth']['auth_key'] = $res[0]['auth_key'];
        $_SESSION['auth']['auth_facebook_id'] = $res[0]['auth_facebook_id'];
        $_SESSION['auth']['auth_login'] = $res[0]['auth_login'];
        $_SESSION['auth']['auth_type'] = $res[0]['auth_type'];
        $_SESSION['auth']['auth_port_lang'] = $res[0]['auth_port_lang'];
        $_SESSION['auth']['auth_premium'] = $res[0]['auth_premium'];
        $_SESSION['auth']['auth_birth_time'] = $res[0]['auth_birth_time'];
        $_SESSION['auth']['auth_rating'] = $res[0]['auth_rating'];
        $_SESSION['auth']['auth_img_cnt'] = $res[0]['auth_img_cnt'];
        $_SESSION['auth']['auth_name'] = $res[0][Localizer::$col_auth_name];
        $_SESSION['auth']['auth_name_en'] = $res[0]['auth_name_en']; # needed for author's  friends actions
        $_SESSION['auth']['auth_dom'] = $res[0]['auth_dom'];
        $_SESSION['auth']['auth_email'] = $res[0]['auth_email'];
        $_SESSION['auth']['auth_power'] = $res[0]['auth_power'];
        $_SESSION['auth']['auth_gender'] = $res[0]['auth_gender'];
        $_SESSION['auth']['auth_avatar'] = $res[0]['auth_avatar'];
        $_SESSION['auth']['auth_avatar_w'] = $res[0]['auth_avatar_w'];
        $_SESSION['auth']['auth_avatar_h'] = $res[0]['auth_avatar_h'];
        $_SESSION['auth']['auth_mood'] = $res[0][Localizer::$col_auth_mood];
        $_SESSION['auth']['auth_mood_en'] = $res[0]['auth_mood_en']; # needed for onliners
        $_SESSION['auth']['auth_mood_de'] = $res[0]['auth_mood_en']; # needed for onliners
        $_SESSION['auth']['auth_blog_favor_cnt'] = $res[0]['auth_blog_favor_cnt'];
        $_SESSION['auth']['auth_country_id'] = $res[0]['auth_country_id'];
        $_SESSION['auth']['auth_region_id'] = $res[0]['auth_region_id'];

        $_SESSION['auth']['auth_fineart_gall'] = $res[0]['auth_fineart_gall'];
        $_SESSION['auth']['auth_square_gall'] = $res[0]['auth_square_gall'];
        $_SESSION['auth']['auth_nu_gall'] = $res[0]['auth_nu_gall'];
        $_SESSION['auth']['auth_window_gall'] = $res[0]['auth_window_gall'];
        $_SESSION['auth']['auth_index_layout'] = $res[0]['auth_index_layout'];
        $_SESSION['auth']['auth_featured_rating'] = $res[0]['auth_featured_rating'];
        $_SESSION['auth']['auth_featured_link'] = $res[0]['auth_featured_link'];
        $_SESSION['auth']['auth_show_all_comms'] = $res[0]['auth_show_all_comms'];
        $_SESSION['auth']['auth_port_dom'] = $res[0]['auth_port_dom'];

        if (Config::$SiteDom) {
            setcookie('auth_answers_cnt', $res[0]['auth_answers_cnt'], Config::$cookie_expires, '/', '.' . Config::$SiteDom . '.' . Config::$domainEnd);
            setcookie('X', $res[0]['auth_key'], Config::$cookie_expires, '/', '.' . Config::$SiteDom . '.' . Config::$domainEnd);
        }

        $sql = "SELECT COUNT(*) FROM ds_recs WHERE id_auth=" . $_SESSION['auth']['id_auth'] . " AND rec_date>" . (Consta::$cur_day - Geo::$Gmtoffset);
        $res_last_recs_cnt = Db::execute($sql);
        $_SESSION['auth']['auth_last_recs_cnt'] = $res_last_recs_cnt[0][0];
        $sql = "UPDATE ds_authors SET  auth_last_recs_cnt=" . $_SESSION['auth']['auth_last_recs_cnt'] . ", auth_last_click=" . Config::$cur_time . ", auth_last_ip='" . Config::$remote_addr . "' WHERE id_auth=" . $_SESSION['auth']['id_auth'] . " LIMIT 1";
        Db::execute($sql);

        $sql = "SELECT id_auth_ignored FROM ds_ignored_authors WHERE id_auth=" . $_SESSION['auth']['id_auth'];
        $res_ignored = Db::execute($sql);
        $auth_ignored = '';
        foreach ($res_ignored as $v)
            $auth_ignored .= $v['id_auth_ignored'] . ',';
        $_SESSION['auth']['auth_ignored'] = $auth_ignored;

        $sql = "SELECT port_group_type FROM ds_portfolios WHERE id_auth=" . $_SESSION['auth']['id_auth'] . " LIMIT 1";
        $port_cache_tag = array('ds_portfolios=' . $_SESSION['auth']['id_auth']);
        $res_port = Mcache::cacheDbi($sql, 300, $port_cache_tag); #utils::printArr($res_recs);
        if (sizeof($res_port))
            $_SESSION['port_group_type'] = $res_port['0']['port_group_type'];
        else
            $_SESSION['port_group_type'] = 'def';
        #return true;
    }

    public static function isPremium($id_auth, $auth_premium)
    {
        if (in_array($id_auth, Auth::$beta_users))
            $is_auth_premium = true;
        else if ($auth_premium == Consta::AUTH_PREMIUM_1 || $auth_premium == Consta::AUTH_PREMIUM_2 || $auth_premium == Consta::AUTH_PREMIUM_3 || $auth_premium == Consta::AUTH_PREMIUM_4)
            $is_auth_premium = true;
        else
            $is_auth_premium = false;
        return $is_auth_premium;
    }

    public static function isAuthIgnored($id_auth)
    {
        $auth_ignored_arr = array();
        if (isset($_SESSION['auth']['auth_ignored'])) {
            $auth_ignored_arr = explode(',', $_SESSION['auth']['auth_ignored']);
            if (trim($auth_ignored_arr[sizeof($auth_ignored_arr) - 1]) == '')
                unset($auth_ignored_arr[sizeof($auth_ignored_arr) - 1]);
        }
        return in_array($id_auth, $auth_ignored_arr) ? true : false;
    }

    public static function updateOnliners()
    {
        if (Auth::$id_auth != -1 && !strstr(Config::$request_uri, 'ajax/')) {
            $online_sess = array('id_auth' => Auth::$id_auth,
                'auth_name' => Auth::$auth_name, 'auth_name_en' => Auth::$auth_name_en, 'auth_type' => Auth::$auth_type,
                'cur_time' => Config::$cur_time,
                'guest_gmtoffset' => Geo::$Gmtoffset,
                'guest_ip' => Config::$remote_addr,
                'guest_agent' => 'MOB_VER | ' . Config::$http_user_agent,
                'guest_country' => Geo::$CountryName, 'guest_country_code' => Geo::$CountryCode, 'guest_city' => Geo::$City,
                'guest_uri' => str_replace('//', '/', Config::$home_url . Config::$request_uri),
                'online_theme' => Config::$theme,
                'auth_avatar' => Auth::$auth_avatar, 'auth_avatar_w' => Auth::$auth_avatar_w, 'auth_avatar_h' => Auth::$auth_avatar_h,
                'auth_mood' => Auth::$auth_mood, 'auth_mood_en' => Auth::$auth_mood_en, 'auth_mood_de' => Auth::$auth_mood_de, 'auth_gender' => Auth::$auth_gender,
                'guest_referrer' => '');

            $now_online_old = Mcache::get(md5('now_online'));
            $now_online[Auth::$guest_sess] = $online_sess;
            foreach ($now_online_old as $k => $v)
                if ($v['id_auth'] == Auth::$id_auth)
                    continue;
                else
                    $now_online[$k] = $v;


            Mcache::set(md5('now_online'), $now_online, 0);
            unset($now_online_old);
            unset($now_online);
        }
    }

    /**
     * @return int
     */
    public static function getIdAuth()
    {
        return Auth::$id_auth;
    }

    /**
     * @return int
     */
    public static function getAuthPremium()
    {
        return Auth::$auth_premium;
    }

    /**
     * @return int
     */
    public static function getAuthType()
    {
        return Auth::$auth_type;
    }

    /**
     * @return float
     */
    public static function getAuthRating()
    {
        return Auth::$auth_rating;
    }

    /**
     * @return string
     */
    public static function getAuthPortLang()
    {
        return Auth::$auth_port_lang;
    }

    /**
     * @return string
     */
    public static function getAuthName()
    {
        return Auth::$auth_name;
    }

    /**
     * @return string
     */
    public static function getAuthAvatar()
    {
        return Auth::$auth_avatar;
    }

    /**
     * @return string
     */
    public static function getAuthGender()
    {
        return Auth::$auth_gender;
    }

    /**
     * @return int
     */
    public static function getAuthNuGall()
    {
        return self::$auth_nu_gall;
    }

    /**
     * @return int
     */
    public static function getAuthLastRecsCnt()
    {
        return self::$auth_last_recs_cnt;
    }

    /**
     * @return int
     */
    public static function getAuthFeaturedRating()
    {
        return self::$auth_featured_rating;
    }

    /**
     * @return int
     */
    public static function getWorkGallLimit()
    {
        return self::$work_gall_limit;
    }
}
