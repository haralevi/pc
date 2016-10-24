<?php
namespace photocommunity\mobile;

class IndexBuilder
{
    public static function inst()
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new IndexBuilder();
        }
        return $instance;
    }

    private function __construct()
    {

    }

    public static function build($isHtml)
    {
        # handle request
        $page = Request::getParam('page', 'integer', 1);
        $all = Request::getParam('all', 'integer', 0);
        $special = Request::getParam('special', 'integer', 0);
        $popular = Request::getParam('popular', 'integer', 0);
        $favorites = Request::getParam('favorites', 'integer', 0);
        # /handle request

        # parse works
        require dirname(__FILE__) . '/WorkModel.php';

        $params = array();
        if($all) {
            $params['all'] = 1;
            $title = Localizer::$loc['all_works_loc'];
            $port_seo_title = Localizer::$loc['all_works_loc'];
            $page_type = 'all';
        }
        else if($special) {
            $params['special'] = 1;
            $title = Localizer::$loc['special_works_loc'];
            $port_seo_title = Localizer::$loc['special_works_loc'];
            $page_type = 'special';
        }
        else if($popular) {
            $params['popular'] = 1;
            $title = Localizer::$loc['popular_loc'];
            $port_seo_title = Localizer::$loc['popular_loc'];
            $page_type = 'popular';
        }
        else if($favorites) {
            $params['favorites'] = 1;
            $title = Localizer::$loc['fav_auth_works_loc'];
            $port_seo_title = Localizer::$loc['fav_auth_works_loc'];
            $page_type = 'favorites';
        }
        else {
            $title = Localizer::$loc['recomm_works_loc'];
            $port_seo_title = Localizer::$loc['recomm_works_loc'];
            $page_type = '';
        }

        $res_works = WorkModel::getWorks($page, $params);
        if (!sizeof($res_works)) {
            if ($isHtml)
                header('location: index.php');
            return false;
        }
        $works = $res_works['works'];
        # /parse works

        # parse pager
        require dirname(__FILE__) . '/../classes/Pager.php';

        $hrefPrev = 'index.php?';
        $hrefNext =  'index.php?';
        if($all) {
            $hrefPrev .= '&amp;all=1';
            $hrefNext .= '&amp;all=1';
        }
        else if($special) {
            $hrefPrev .= '&amp;special=1';
            $hrefNext .= '&amp;special=1';
        }
        else if($popular) {
            $hrefPrev .= '&amp;popular=1';
            $hrefNext .= '&amp;popular=1';
        }
        else if($favorites) {
            $hrefPrev .= '&amp;favorites=1';
            $hrefNext .= '&amp;favorites=1';
        }
        $hrefPrev .= Pager::getHrefPrev($page);
        $hrefNext .= Pager::getHrefNext($page);
        if(Utils::endsWith($hrefPrev, '?')) $hrefPrev = 'index.php';
        # /parse pager

        return array(
            'title' => $title,
            'port_seo_title' => $port_seo_title,
            'page_type' => $page_type,
            'hrefPrev' => $hrefPrev,
            'hrefNext' => $hrefNext,
            'works' => $works,
        );
    }

}