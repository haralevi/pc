<?php
/**
 * Created by Andre Haralevi
 * Date: 10/24/2016
 * Time: 5:21 AM
 */

namespace Photocommunity\Mobile;

class CommController extends Controller
{
    private static $isJson = false;

    public static function inst($tpl_name)
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new CommController($tpl_name);
        }
        return $instance;
    }

    private function __construct($tpl_name)
    {
        Controller::initTpl($tpl_name);
    }

    public static function buildJson()
    {
        CommController::$isJson = true;
        CommController::build();
    }

    public static function build()
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
            if (!CommController::$isJson)
                header('location: comm.php');
            return false;
        }

        $comm = $res_comm['comm'];
        # /parse comments

        # parse pager
        require dirname(__FILE__) . '/../classes/Pager.php';

        $hrefPrev = 'comm.php?';
        $hrefNext = 'comm.php?';
        $hrefPrev .= Pager::getHrefPrev($page);
        $hrefNext .= Pager::getHrefNext($page);
        if (Utils::endsWith($hrefPrev, '?')) $hrefPrev = 'comm.php';
        # /parse pager

        $comm = array(
            'hrefPrev' => $hrefPrev,
            'hrefNext' => $hrefNext,
            'comm' => $comm,
            'page' => $page,
        );

        if (!CommController::$isJson)
            CommController::parse($comm);
        else
            CommController::parseJson($comm);

        return true;
    }

    private static function parse($comm)
    {
        if (!$comm)
            die();

        CommController::$tpl_var['comm'] = $comm['comm'];

        CommController::$tpl->parse(CommController::$tpl_var);

        CommController::$tpl_main_var['content'] = CommController::$tpl->get();

        CommController::$tpl_main_var['href_prev_page'] = $comm['hrefPrev'];
        CommController::$tpl_main_var['href_next_page'] = $comm['hrefNext'];

        # set menu style
        CommController::$tpl_main_var = Utils::setMenuStyles(CommController::$tpl_main_var, 'comm');

        # set seo vars
        CommController::$tpl_main_var['canonical_url'] = CommController::getCanonicalUrl($comm['page']);
        CommController::$tpl_main_var['port_seo_title'] = Localizer::$loc['comm_loc'] . ' / ' . Utils::getSiteName();

        # parse page
        Parse::inst(CommController::$tpl_main, CommController::$tpl_main_var);
    }

    private static function parseJson($comm)
    {
        # build json
        $json = '{';
        if ($comm) {
            if (Config::getDebug()) $json .= '"debug": "#debug#", ';
            $json .= '"canonicalUrl": "' . CommController::getCanonicalUrl($comm['page']) . '", ';
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

    private static function getCanonicalUrl($page)
    {
        if ($page <= 1) {
            $canonicalUrl = Config::$http_scheme . Config::$SiteDom . '.' . Config::$domainEnd . '/comm.php';
        } else {
            $canonicalUrl = Config::$http_scheme . Config::$SiteDom . '.' . Config::$domainEnd . '/comm.php?page=' . $page;
        }
        return $canonicalUrl;
    }
}