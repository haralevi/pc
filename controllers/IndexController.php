<?php
namespace photocommunity\mobile;

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
        } else if ($special) {
            $params['special'] = 1;
            $title = Localizer::$loc['special_works_loc'];
            $port_seo_title = Localizer::$loc['special_works_loc'];
            $page_type = 'special';
        } else if ($popular) {
            $params['popular'] = 1;
            $title = Localizer::$loc['popular_loc'];
            $port_seo_title = Localizer::$loc['popular_loc'];
            $page_type = 'popular';
        } else if ($favorites) {
            $params['favorites'] = 1;
            $title = Localizer::$loc['fav_auth_works_loc'];
            $port_seo_title = Localizer::$loc['fav_auth_works_loc'];
            $page_type = 'favorites';
        } else {
            $title = Localizer::$loc['recomm_works_loc'];
            $port_seo_title = Localizer::$loc['recomm_works_loc'];
            $page_type = '';
        }

        $res_works = WorkModel::getWorks($page, $params);
        if (!sizeof($res_works)) {
            if (!IndexController::$isJson)
                header('location: index.php');
            return false;
        }
        $works = $res_works['works'];
        # /parse works

        # parse pager
        require dirname(__FILE__) . '/../classes/Pager.php';

        $hrefPrev = 'index.php?';
        $hrefNext = 'index.php?';
        if ($all) {
            $hrefPrev .= '&amp;all=1';
            $hrefNext .= '&amp;all=1';
        } else if ($special) {
            $hrefPrev .= '&amp;special=1';
            $hrefNext .= '&amp;special=1';
        } else if ($popular) {
            $hrefPrev .= '&amp;popular=1';
            $hrefNext .= '&amp;popular=1';
        } else if ($favorites) {
            $hrefPrev .= '&amp;favorites=1';
            $hrefNext .= '&amp;favorites=1';
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
        );

        if (!IndexController::$isJson)
            IndexController::parse($index);
        else
            IndexController::parseJson($index);

        return true;
    }

    private static function parse($index)
    {
        if (!$index)
            die();

        if ($index['title'])
            IndexController::$tpl_var['title'] = $index['title'];
        else
            IndexController::$tpl->clear('TITLE_BLK');

        IndexController::$tpl_var['works'] = $index['works'];

        if ($index['page_type'])
            IndexController::$tpl_var['page_type_param'] = $index['page_type'] . '=1';
        else
            IndexController::$tpl_var['page_type_param'] = '';

        IndexController::$tpl->parse(IndexController::$tpl_var);

        IndexController::$tpl_main_var['content'] = IndexController::$tpl->get();

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
            $json .= '"ajaxBody": "' . Utils::prepareJson($index['works']) . '" ';
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
        }
        else {
            if($page_type == '')
                $page_type = 'featured';
            else if($page_type == 'popular')
                $page_type = 'rated';
            $canonicalUrl = Config::$http_scheme . Config::$SiteDom . '.' . Config::$domainEnd . '/gallery.php#' . $page_type . '=1&range=7&page=' . Pager::getCanonicalPageIndex ($page);
        }
        return $canonicalUrl;
    }
}