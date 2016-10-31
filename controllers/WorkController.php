<?php
namespace photocommunity\mobile;

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
        } else if ($special) {
            $params['special'] = 1;
        } else if ($popular) {
            $params['popular'] = 1;
        } else if ($favorites) {
            $params['favorites'] = 1;
        } else if ($id_auth_photo) {
            $params['id_auth_photo'] = $id_auth_photo;
        }

        $res_work = WorkModel::getWork($id_photo, $params, $prev, $next);
        if (!sizeof($res_work)) {
            if (!WorkController::$isJson)
                header('location: index.php');
            return false;
        }

        $id_photo = $res_work['id_photo'];
        $work = $res_work['work'];
        $ph_name = $res_work['ph_name'];
        $auth_name_photo = $res_work['auth_name_photo'];
        # /parse work

        # parse comments
        require dirname(__FILE__) . '/../models/CommModel.php';

        $res_comments = CommModel::getComments($id_photo, $res_work['id_auth_photo'], $res_work['is_ph_anon']);
        $comments = $res_comments['comments'];
        # /parse comments

        # parse pager
        $workHref = 'work.php?id_photo=' . $id_photo;
        if ($all) {
            $workHref .= '&amp;all=1';
        } else if ($special) {
            $workHref .= '&amp;special=1';
        } else if ($popular) {
            $workHref .= '&amp;popular=1';
        } else if ($favorites) {
            $workHref .= '&amp;favorites=1';
        } else if ($id_auth_photo) {
            $workHref .= '&id_auth_photo=' . $id_auth_photo;
        }
        $hrefPrev = $workHref . '&amp;prev=1';
        $hrefNext = $workHref . '&amp;next=1';
        # /parse pager

        $work = array(
            'id_photo' => $id_photo,
            'hrefPrev' => $hrefPrev,
            'hrefNext' => $hrefNext,
            'work' => $work,
            'ph_name' => $ph_name,
            'auth_name_photo' => $auth_name_photo,
            'comments' => $comments,
        );

        if (!WorkController::$isJson)
            WorkController::parse($work);
        else
            WorkController::parseJson($work);

        return true;
    }

    private static function parse($work)
    {
        if (!$work)
            die();

        WorkController::$tpl_var['work'] = $work['work'];
        WorkController::$tpl_var['comments'] = $work['comments'];

        WorkController::$tpl->parse(WorkController::$tpl_var);

        WorkController::$tpl_main_var['content'] = WorkController::$tpl->get();


        WorkController::$tpl_main_var['href_prev_page'] = $work['hrefPrev'];
        WorkController::$tpl_main_var['href_next_page'] = $work['hrefNext'];

        # set menu style
        WorkController::$tpl_main_var = Utils::setMenuStyles(WorkController::$tpl_main_var);

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
            $json .= '"title": "' . Utils::prepareJson($work['ph_name'] . ' / ' . $work['auth_name_photo']) . '", ';
            $json .= '"ajaxBody": "' . Utils::prepareJson($work['work'] . '<div class="wrapContent">' . $work['comments']) . '</div>" ';
        }
        $json .= '}';
        # /build json

        # parse page
        require dirname(__FILE__) . '/../classes/ParseJson.php';
        ParseJson::inst($json);
    }

    private static function getCanonicalUrl ($id_photo) {
        return Config::$http_scheme . Config::$SiteDom . '.' . Config::$domainEnd . '/work/' . $id_photo;
    }
}