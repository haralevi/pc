<?php
/**
 * Created by Andre Haralevi
 * Date: 10/24/2016
 * Time: 5:21 AM
 */

namespace Photocommunity\Mobile;

class WorkController extends Controller
{
    private static $isJson = false;

    public static function inst($tpl_name)
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new WorkController($tpl_name);
        }
        return $instance;
    }

    private function __construct($tpl_name)
    {
        Controller::initTpl($tpl_name);
    }

    public static function buildJson()
    {
        WorkController::$isJson = true;
        WorkController::build();
    }

    public static function build()
    {
        # handle request
        $id_photo = Request::getParam('id_photo', 'integer', 1);
        $id_auth_photo = Request::getParam('id_auth_photo', 'integer', 0);

        if (isset($_REQUEST['prev'])) $prev = true;
        else $prev = false;
        if (isset($_REQUEST['next'])) $next = true;
        else $next = false;

        $all = Request::getParam('all', 'integer', 0);
        $special = Request::getParam('special', 'integer', 0);
        $popular = Request::getParam('popular', 'integer', 0);
        $favorites = Request::getParam('favorites', 'integer', 0);
        # /handle request

        # parse work
        require dirname(__FILE__) . '/../models/WorkModel.php';

        $params = array();
        if ($all) {
            $params['all'] = 1;
            $page_type = 'all';
            $param_nav = '&all=1';
        } else if ($special) {
            $params['special'] = 1;
            $page_type = 'special';
            $param_nav = '&special=1';
        } else if ($popular) {
            $params['popular'] = 1;
            $page_type = 'popular';
            $param_nav = '&popular=1';
        } else if ($favorites) {
            $params['favorites'] = 1;
            $page_type = 'favorites';
            $param_nav = '&favorites=1';
        } else if ($id_auth_photo) {
            $params['id_auth_photo'] = $id_auth_photo;
            $page_type = 'id_auth_photo';
            $param_nav = '&id_auth_photo=' . $id_auth_photo;
        } else {
            $page_type = '';
            $param_nav = '';
        }

        $res_work = WorkModel::getWork($id_photo, $params, $prev, $next);
        if (!sizeof($res_work)) {
            if (strstr($param_nav, 'id_auth_photo'))
                $param_nav = 'author.php?' . str_replace(array('id_auth_photo='), array('id_auth='), $param_nav);
            else if ($param_nav != '')
                $param_nav = '?' . $param_nav;
            if (!WorkController::$isJson)
                header('Location: ' . Config::$home_url . $param_nav);
            else
                echo 'Location: ' . Config::$home_url . $param_nav;
            die();
        }

        $id_photo = $res_work['id_photo'];

        # redirect to canonical url, if prev or next in url
        if (!WorkController::$isJson && ($next || $prev)) {
            if (Config::$domainEnd == 'by') {
                $redirect_url = Config::$home_url . 'work.php?id_photo=' . $id_photo . $param_nav;
            } else {
                if ($param_nav != '')
                    $param_nav = '?' . $param_nav;
                $redirect_url = Config::$home_url . 'work/' . $id_photo . $param_nav;
            }
            header('HTTP/1.1 301 Moved Permanently');
            header('Location: ' . $redirect_url);
            die();
        }

        $og_image = $res_work['og_image'];
        $work = $res_work['work'];
        $ph_name = $res_work['ph_name'];
        $auth_name_photo = $res_work['auth_name_photo'];
        # /parse work

        # parse comments
        require dirname(__FILE__) . '/../models/CommModel.php';

        $res_comments = CommModel::getComments($id_photo, $res_work['id_auth_photo'], $res_work['is_ph_anon'], $res_work['ph_critique'], $res_work['auth_status_photo']);
        $comments = $res_comments['comments'];
        # /parse comments

        # parse navigation
        $prev_next_nav_arr = explode(',', $_COOKIE['prev_next_nav']);
        #$prev_next_nav_arr = explode(',', $_SESSION['prev_next_nav']);
        $id_photo_pos = array_search($id_photo, $prev_next_nav_arr);
        if ($id_photo_pos === false || $id_photo_pos === 0 || $id_photo_pos == (sizeof($prev_next_nav_arr) - 1)) {
            if (isset($_COOKIE['nav_dir']) && $_COOKIE['nav_dir'] == 'prev') {
                WorkModel::updateNextPrevNav($id_photo, 'prev', $params);
                $prev_next_nav_arr = explode(',', $_COOKIE['prev_next_nav']);
                #$prev_next_nav_arr = explode(',', $_SESSION['prev_next_nav']);
                $id_photo_pos = array_search($id_photo, $prev_next_nav_arr);
            } else {
                WorkModel::updateNextPrevNav($id_photo, 'next', $params);
                $prev_next_nav_arr = explode(',', $_COOKIE['prev_next_nav']);
                #$prev_next_nav_arr = explode(',', $_SESSION['prev_next_nav']);
                $id_photo_pos = array_search($id_photo, $prev_next_nav_arr);
            }
        }

        $id_photo_prev = $id_photo;
        $id_photo_next = $id_photo;

        if ($id_photo_pos !== false) {
            if (isset($prev_next_nav_arr[$id_photo_pos - 1]))
                $id_photo_prev = $prev_next_nav_arr[$id_photo_pos - 1];
            if (isset($prev_next_nav_arr[$id_photo_pos + 1]))
                $id_photo_next = $prev_next_nav_arr[$id_photo_pos + 1];
        }

        $param_nav_prev = '';
        $param_nav_next = '';
        if ($id_photo_prev == $id_photo)
            $param_nav_prev = '&prev=1';
        if ($id_photo_next == $id_photo)
            $param_nav_next = '&next=1';

        if (Config::$domainEnd == 'by') {
            $hrefPrev = Config::$home_url . 'work.php?id_photo=' . $id_photo_prev . $param_nav . $param_nav_prev;
            $hrefNext = Config::$home_url . 'work.php?id_photo=' . $id_photo_next . $param_nav . $param_nav_next;
        } else {
            $hrefPrev = Config::$home_url . 'work/' . $id_photo_prev;
            if ($param_nav != '' || $param_nav_prev != '')
                $hrefPrev .= '?';
            $hrefPrev .= $param_nav . $param_nav_prev;

            $hrefNext = Config::$home_url . 'work/' . $id_photo_next;
            if ($param_nav != '' || $param_nav_next != '')
                $hrefNext .= '?';
            $hrefNext .= $param_nav . $param_nav_next;
        }
        # /parse navigation

        $work = array(
            'og_image' => $og_image,
            'id_photo' => $id_photo,
            'hrefPrev' => $hrefPrev,
            'hrefNext' => $hrefNext,
            'work' => $work,
            'ph_name' => $ph_name,
            'auth_name_photo' => $auth_name_photo,
            'comments' => $comments,
            'page_type' => $page_type,
            'goad' => Utils::getGoad(),
        );

        if (!WorkController::$isJson)
            WorkController::parse($work);
        else
            WorkController::parseJson($work);

        return true;
    }

    private static function parseWorkTpl($work)
    {
        WorkController::$tpl_var['work'] = $work['work'];
        WorkController::$tpl_var['comments'] = $work['comments'];
        WorkController::$tpl_var['goad'] = $work['goad'];

        WorkController::$tpl->parse(WorkController::$tpl_var);
        return WorkController::$tpl->get();
    }

    private static function parse($work)
    {
        if (!$work)
            die();

        WorkController::$tpl_main_var['content'] = WorkController::parseWorkTpl($work);

        WorkController::$tpl_main_var['og_image'] = $work['og_image'];
        WorkController::$tpl_main_var['href_prev_page'] = $work['hrefPrev'];
        WorkController::$tpl_main_var['href_next_page'] = $work['hrefNext'];

        # set menu style
        WorkController::$tpl_main_var = Utils::setMenuStyles(WorkController::$tpl_main_var, $work['page_type']);

        # set seo vars
        WorkController::$tpl_main_var['canonical_url'] = WorkController::getCanonicalUrl($work['id_photo']);
        WorkController::$tpl_main_var['port_seo_title'] = $work['ph_name'] . ' / ' . $work['auth_name_photo'] . ' / ' . Utils::getSiteName();

        # parse page
        Parse::inst(WorkController::$tpl_main, WorkController::$tpl_main_var);
    }

    private static function parseJson($work)
    {
        # build json
        $json = '{';
        if ($work) {
            if (Config::getDebug()) $json .= '"debug": "#debug#", ';
            $json .= '"canonicalUrl": "' . WorkController::getCanonicalUrl($work['id_photo']) . '", ';
            $json .= '"hrefPrev": "' . Utils::prepareJson($work['hrefPrev']) . '", ';
            $json .= '"hrefNext": "' . Utils::prepareJson($work['hrefNext']) . '", ';
            $json .= '"title": "' . Utils::prepareJson(str_replace('&quot;', '"', $work['ph_name']) . ' / ' . $work['auth_name_photo']) . '", ';
            $json .= '"ajaxBody": "' . Utils::prepareJson(WorkController::parseWorkTpl($work)) . '" ';
        }
        $json .= '}';
        # /build json

        # parse page
        require dirname(__FILE__) . '/../classes/ParseJson.php';
        ParseJson::inst($json);
    }

    private static function getCanonicalUrl($id_photo)
    {
        if (Config::$domainEnd == 'by')
            $canonicalUrl = '//' . Config::$SiteDom . '.' . Config::$domainEnd . '/work.php?id_photo=' . $id_photo;
        else
            $canonicalUrl = '//' . Config::$SiteDom . '.' . Config::$domainEnd . '/work/' . $id_photo;
        return $canonicalUrl;
    }
}