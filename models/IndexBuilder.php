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

    function build($isHtml)
    {
        # handle request
        $page = Request::getParam('page', 'integer', 1);
        $all = Request::getParam('all', 'integer', 0);
        $fineart = Request::getParam('fineart', 'integer', 0);
        $popular = Request::getParam('popular', 'integer', 0);
        $favorites = Request::getParam('favorites', 'integer', 0);
        # /handle request

        # parse works
        require dirname(__FILE__) . '/WorkModel.php';

        $params = array();
        if($all) {
            $params['all'] = 1;
            $header = '';
            $page_type = 'all_works';
        }
        else if($fineart) {
            $params['fineart'] = 1;
            $header = '';
            $page_type = 'fineart';
        }
        else if($popular) {
            $params['popular'] = 1;
            $header = '';
            $page_type = 'popular';
        }
        else if($favorites) {
            $params['favorites'] = 1;
            $header = ''; #$header = localizer::$loc['auth_works_you_follow_loc'];
            $page_type = 'fav_auth_works';
        }
        else {
            $header = ''; #$header = localizer::$loc['recomm_works_loc'];
            $page_type = 'recomm_works';
        }

        $res_works = WorkModel::inst()->getWorks($page, $params);
        if (!sizeof($res_works)) {
            if ($isHtml)
                header('location: index.php');
            #return false;
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
        else if($fineart) {
            $hrefPrev .= '&amp;fineart=1';
            $hrefNext .= '&amp;fineart=1';
        }
        else if($popular) {
            $hrefPrev .= '&amp;popular=1';
            $hrefNext .= '&amp;popular=1';
        }
        else if($favorites) {
            $hrefPrev .= '&amp;favorites=1';
            $hrefNext .= '&amp;favorites=1';
        }
        $hrefPrev .= Pager::inst()->getHrefPrev($page);
        $hrefNext .= Pager::inst()->getHrefNext($page);
        if(Utils::endsWith($hrefPrev, '?')) $hrefPrev = 'index.php';
        # /parse pager

        return array(
            'header' => $header,
            'page_type' => $page_type,
            'hrefPrev' => $hrefPrev,
            'hrefNext' => $hrefNext,
            'works' => $works,
        );
    }

}