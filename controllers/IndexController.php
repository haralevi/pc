<?php
/**
 * Created by Andre Haralevi
 * Date: 10/24/2016
 * Time: 5:21 AM
 */

namespace Photocommunity\Mobile;

class IndexController extends Controller
{
    private static $isJson = false;

    public static function inst($tpl_name)
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new IndexController($tpl_name);
        }
        return $instance;
    }

    private function __construct($tpl_name)
    {
        Controller::initTpl($tpl_name);
        IndexController::handleUnsubscribe();
    }

    private static function handleUnsubscribe()
    {
        $success_unsubscribe = Auth::handleUnsubscribe();
        if ($success_unsubscribe == '')
            Controller::$tpl->clear('UNSUBSCRIBE_BLK');
        else
            Controller::$tpl_var['success_unsubscribe'] = $success_unsubscribe;
    }

    public static function buildJson()
    {
        IndexController::$isJson = true;
        IndexController::build();
    }

    public static function build()
    {
        # handle request
        $page = Request::getParam('page', 'integer', 1);

        $all = Request::getParam('all', 'integer', 0);
        $special = Request::getParam('special', 'integer', 0);
        $popular = Request::getParam('popular', 'integer', 0);
        $favorites = Request::getParam('favorites', 'integer', 0);
        # /handle request

        # parse works
        require dirname(__FILE__) . '/../models/WorkModel.php';

        $params = array();
        if ($all) {
            $params['all'] = 1;
            $title = Localizer::$loc['all_works_loc'];
            $port_seo_title = Localizer::$loc['all_works_loc'];
            $page_type = 'all';
            $param_nav = '&all=1';
        } else if ($special) {
            $params['special'] = 1;
            $title = Localizer::$loc['special_works_loc'];
            $port_seo_title = Localizer::$loc['special_works_loc'];
            $page_type = 'special';
            $param_nav = '&special=1';
        } else if ($popular) {
            $params['popular'] = 1;
            $title = Localizer::$loc['popular_loc'];
            $port_seo_title = Localizer::$loc['popular_loc'];
            $page_type = 'popular';
            $param_nav = '&popular=1';
        } else if ($favorites) {
            $params['favorites'] = 1;
            $title = Localizer::$loc['fav_auth_works_loc'];
            $port_seo_title = Localizer::$loc['fav_auth_works_loc'];
            $page_type = 'favorites';
            $param_nav = '&favorites=1';
        } else {
            $title = Localizer::$loc['recomm_works_loc'];
            $port_seo_title = Localizer::$loc['recomm_works_loc'];
            $page_type = '';
            $param_nav = '';
        }

        $res_works = WorkModel::getWorks($params, $page);
        if (!sizeof($res_works)) {
            if ($param_nav != '')
                $param_nav = '?' . $param_nav;
            if (!IndexController::$isJson)
                header('Location: ' . Config::$home_url . $param_nav);
            else
                echo 'Location: ' . Config::$home_url . $param_nav;
            die();
        }
        $works = $res_works['works'];
        # /parse works

        # parse pager
        require dirname(__FILE__) . '/../classes/Pager.php';

        $hrefPrev = 'index.php?';
        $hrefNext = 'index.php?';
        if ($all) {
            $hrefPrev .= '&all=1';
            $hrefNext .= '&all=1';
        } else if ($special) {
            $hrefPrev .= '&special=1';
            $hrefNext .= '&special=1';
        } else if ($popular) {
            $hrefPrev .= '&popular=1';
            $hrefNext .= '&popular=1';
        } else if ($favorites) {
            $hrefPrev .= '&favorites=1';
            $hrefNext .= '&favorites=1';
        }
        $hrefPrev .= Pager::getHrefPrev($page);
        $hrefNext .= Pager::getHrefNext($page);
        if (Utils::endsWith($hrefPrev, '?')) $hrefPrev = 'index.php';
        # /parse pager

        $index = array(
            'title' => $title,
            'port_seo_title' => $port_seo_title,
            'page_type' => $page_type,
            'hrefPrev' => $hrefPrev,
            'hrefNext' => $hrefNext,
            'works' => $works,
            'page' => $page,
            'goad' => Utils::getGoad(),
        );

        if (!IndexController::$isJson)
            IndexController::parse($index);
        else
            IndexController::parseJson($index);

        return true;
    }

    private static function parseCommTpl($index)
    {
        if ($index['title'])
            IndexController::$tpl_var['title'] = $index['title'];
        else
            IndexController::$tpl->clear('TITLE_BLK');

        IndexController::$tpl_var['works'] = $index['works'];
        IndexController::$tpl_var['goad'] = $index['goad'];

        if ($index['page_type'])
            IndexController::$tpl_var['page_type_param'] = '?' . $index['page_type'] . '=1';
        else
            IndexController::$tpl_var['page_type_param'] = '';

        IndexController::$tpl->parse(IndexController::$tpl_var);

        return IndexController::$tpl->get();
    }

    private static function parse($index)
    {
        if (!$index)
            die();

        IndexController::$tpl_main_var['content'] = IndexController::parseCommTpl($index);

        IndexController::$tpl_main_var['href_prev_page'] = $index['hrefPrev'];
        IndexController::$tpl_main_var['href_next_page'] = $index['hrefNext'];

        # set menu style
        IndexController::$tpl_main_var = Utils::setMenuStyles(IndexController::$tpl_main_var, $index['page_type']);

        # set seo vars
        IndexController::$tpl_main_var['canonical_url'] = IndexController::getCanonicalUrl($index['page'], $index['page_type']);
        if ($index['port_seo_title'])
            IndexController::$tpl_main_var['port_seo_title'] = $index['port_seo_title'] . ' / ' . Utils::getSiteName();

        # parse page
        Parse::inst(IndexController::$tpl_main, IndexController::$tpl_main_var);
    }

    private static function parseJson($index)
    {
        # build json
        $json = '{';
        if ($index) {
            if (Config::getDebug()) $json .= '"debug": "#debug#", ';
            $json .= '"canonicalUrl": "' . IndexController::getCanonicalUrl($index['page'], $index['page_type']) . '", ';
            $json .= '"hrefPrev": "' . Utils::prepareJson($index['hrefPrev']) . '", ';
            $json .= '"hrefNext": "' . Utils::prepareJson($index['hrefNext']) . '", ';
            $json .= '"ajaxBody": "' . Utils::prepareJson(IndexController::parseCommTpl($index)) . '" ';
        }
        $json .= '}';
        # /build json

        # parse page
        require dirname(__FILE__) . '/../classes/ParseJson.php';
        ParseJson::inst($json);
    }

    private static function getCanonicalUrl($page, $page_type)
    {
        if ($page <= 1 && $page_type == '') {
            $canonicalUrl = Config::$http_scheme . Config::$SiteDom . '.' . Config::$domainEnd;
        } else {
            if ($page_type == '')
                $page_type = 'featured';
            else if ($page_type == 'popular')
                $page_type = 'rated';
            $canonicalUrl = Config::$http_scheme . Config::$SiteDom . '.' . Config::$domainEnd . '/gallery.php#' . $page_type . '=1&range=7&page=' . Pager::getCanonicalPageIndex($page);
        }
        return $canonicalUrl;
    }
}