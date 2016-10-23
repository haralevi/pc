<?php
namespace photocommunity\mobile;

class CommBuilder
{

    public static function inst()
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new CommBuilder();
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
        if($page > 100)
            $page = 100;
        # /handle request

        # /parse comments
        require dirname(__FILE__) . '/../models/CommModel.php';

        $res_comm = CommModel::getComm($page);
        if (!sizeof($res_comm)) {
            if($isHtml)
                header('location: comm.php');
            return false;
        }

        $comm = $res_comm['comm'];
        # /parse comments

        # parse pager
        require dirname(__FILE__) . '/../classes/Pager.php';

        $hrefPrev = 'comm.php?';
        $hrefNext =  'comm.php?';
        $hrefPrev .= Pager::inst()->getHrefPrev($page);
        $hrefNext .= Pager::inst()->getHrefNext($page);
        if(Utils::endsWith($hrefPrev, '?')) $hrefPrev = 'comm.php';
        # /parse pager

        return array(
            'hrefPrev' => $hrefPrev,
            'hrefNext' => $hrefNext,
            'comm' => $comm,
        );
    }

}