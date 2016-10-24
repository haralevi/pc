<?php
namespace photocommunity\mobile;

require dirname(__FILE__) . '/../classes/Builder.php';

class CommBuilder extends Builder
{
    public static function inst($tpl_name)
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new CommBuilder($tpl_name);
        }
        return $instance;
    }

    public function __construct($tpl_name)
    {
        parent::__construct($tpl_name);
    }

    /**
     * @param $isHtml boolean
     * @return boolean
     */
    public static function build($isHtml)
    {
        # handle request
        $page = Request::getParam('page', 'integer', 1);
        if ($page > 100)
            $page = 100;
        # /handle request

        # /parse comments
        require dirname(__FILE__) . '/../models/CommModel.php';

        $res_comm = CommModel::getComm($page);
        if (!sizeof($res_comm)) {
            if ($isHtml)
                header('location: comm.php');
            return false;
        }

        $comm = $res_comm['comm'];
        # /parse comments

        # parse pager
        require dirname(__FILE__) . '/../classes/Pager.php';

        $hrefPrev = 'comm.php?';
        $hrefNext = 'comm.php?';
        $hrefPrev .= Pager::inst()->getHrefPrev($page);
        $hrefNext .= Pager::inst()->getHrefNext($page);
        if (Utils::endsWith($hrefPrev, '?')) $hrefPrev = 'comm.php';
        # /parse pager

        $comm = array(
            'hrefPrev' => $hrefPrev,
            'hrefNext' => $hrefNext,
            'comm' => $comm,
        );

        if ($isHtml)
            CommBuilder::parse($comm);
        else
            CommBuilder::parseJson($comm);

        return true;
    }

    private static function parse($comm)
    {
        if (!$comm)
            die();

        CommBuilder::$tpl_var['comm'] = $comm['comm'];

        CommBuilder::$tpl->parse(CommBuilder::$tpl_var);

        CommBuilder::$tpl_main_var = Utils::setMenuStyles(CommBuilder::$tpl_main_var, 'comm');

        CommBuilder::$tpl_main_var['href_prev_page'] = $comm['hrefPrev'];
        CommBuilder::$tpl_main_var['href_next_page'] = $comm['hrefNext'];
        CommBuilder::$tpl_main_var['content'] = CommBuilder::$tpl->get();

        # set seo vars
        CommBuilder::$tpl_main_var['port_seo_title'] = Localizer::$loc['comm_loc'] . ' / ' . Utils::getSiteName();

        Parse::inst(CommBuilder::$tpl_main, CommBuilder::$tpl_main_var);
    }

    private static function parseJson($comm)
    {
        # build json
        $json = '{';
        if ($comm) {
            if (Config::getDebug()) $json .= '"debug": "#debug#", ';
            $json .= '"hrefPrev": "' . Utils::prepareJson($comm['hrefPrev']) . '", ';
            $json .= '"hrefNext": "' . Utils::prepareJson($comm['hrefNext']) . '", ';
            $json .= '"ajaxBody": "' . Utils::prepareJson($comm['comm']) . '" ';
        }
        $json .= '}';
        # /build json

        # parse page
        require dirname(__FILE__) . '/../classes/ParseJson.php';
        ParseJson::inst($json);
    }
}