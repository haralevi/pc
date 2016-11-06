<?php
namespace photocommunity\mobile;

class AuthorController extends Controller
{
    private static $isJson = false;

    public static function inst($tpl_name)
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new AuthorController($tpl_name);
        }
        return $instance;
    }

    private function __construct($tpl_name)
    {
        Controller::initTpl($tpl_name);
    }

    public static function buildJson()
    {
        AuthorController::$isJson = true;
        AuthorController::build();
    }

    public static function build()
    {
        # handle request
        $id_auth_photo = Request::getParam('id_auth', 'integer', 0);

        if (isset($_REQUEST['auth_dom'])) {
            $auth_dom_work = Request::getParam('auth_dom', 'string');
            $sql_works = "SELECT id_auth FROM ds_authors WHERE auth_dom='" . $auth_dom_work . "' LIMIT 1";
            $author_cache_tag = array('ds_authors=' . $auth_dom_work);
            $res_auth_work = Mcache::cacheDbi($sql_works, 300, $author_cache_tag);
            if (sizeof($res_auth_work)) {
                $id_auth_photo = $res_auth_work[0]['id_auth'];
            } else {
                $id_auth_photo = 1;
            }
        }

        $page = Request::getParam('page', 'integer', 1);
        # /handle request

        # parse author
        require dirname(__FILE__) . '/../models/AuthorModel.php';
        $res_author = AuthorModel::getAuthor($id_auth_photo);
        if (!sizeof($res_author)) {
            header('location: index.php');
            return false;
        }
        $auth_premium_photo = $res_author['auth_premium_photo'];
        $auth_name_photo = $res_author['auth_name_photo'];
        $author = $res_author['author'];

        // don't show more than WORK_GALL_LIMIT_0 photos for not premium author on '.de'
        $work_gall_limit = Utils::getWorkGallLimit($auth_premium_photo);
        if (Consta::WORKS_PER_PAGE * $page > $work_gall_limit)
            return false;

        # /parse author

        # parse works
        require dirname(__FILE__) . '/../models/WorkModel.php';

        $res_works = WorkModel::getWorks($page, array('id_auth_photo' => $id_auth_photo));
        if (!sizeof($res_works)) {
            if (!AuthorController::$isJson)
                $works = '';
            else
                return false;
        } else
            $works = $res_works['works'];
        # /parse works

        # parse pager
        require dirname(__FILE__) . '/../classes/Pager.php';

        $hrefPrev = 'author.php?id_auth=' . $id_auth_photo . Pager::getHrefPrev($page);
        $hrefNext = 'author.php?id_auth=' . $id_auth_photo . Pager::getHrefNext($page);
        # /parse pager

        $author = array(
            'hrefPrev' => $hrefPrev,
            'hrefNext' => $hrefNext,
            'auth_name_photo' => $auth_name_photo,
            'id_auth_photo' => $id_auth_photo,
            'author' => $author,
            'works' => $works,
            'page' => $page,
        );

        if (!AuthorController::$isJson)
            AuthorController::parse($author);
        else
            AuthorController::parseJson($author);

        return true;

    }

    private static function parse($author)
    {
        if (!$author)
            die();

        AuthorController::$tpl_var['id_auth_photo'] = $author['id_auth_photo'];
        AuthorController::$tpl_var['author'] = $author['author'];
        AuthorController::$tpl_var['works'] = $author['works'];

        AuthorController::$tpl->parse(AuthorController::$tpl_var);

        AuthorController::$tpl_main_var['content'] = AuthorController::$tpl->get();
        AuthorController::$tpl_main_var['href_prev_page'] = $author['hrefPrev'];
        AuthorController::$tpl_main_var['href_next_page'] = $author['hrefNext'];


        # set menu style
        if ($author['id_auth_photo'] == Auth::getIdAuth())
            $page_type = 'my_profile';
        else
            $page_type = '';
        AuthorController::$tpl_main_var = Utils::setMenuStyles(AuthorController::$tpl_main_var, $page_type);

        # set seo vars
        AuthorController::$tpl_main_var['canonical_url'] = AuthorController::getCanonicalUrl($author['id_auth_photo'], $author['page']);
        AuthorController::$tpl_main_var['port_seo_title'] = $author['auth_name_photo'] . ' / ' . Utils::getSiteName();

        # parse page
        Parse::inst(AuthorController::$tpl_main, AuthorController::$tpl_main_var);
    }

    private static function parseJson($author)
    {
        # build json
        $json = '{';
        if ($author) {
            if (Config::getDebug()) $json .= '"debug": "#debug#", ';
            $json .= '"canonicalUrl": "' . AuthorController::getCanonicalUrl($author['id_auth_photo'], $author['page']) . '", ';
            $json .= '"hrefPrev": "' . Utils::prepareJson($author['hrefPrev']) . '", ';
            $json .= '"hrefNext": "' . Utils::prepareJson($author['hrefNext']) . '", ';
            $json .= '"ajaxBody": "' . Utils::prepareJson($author['works']) . '" ';
        }
        $json .= '}';
        # /build json

        # parse page
        require dirname(__FILE__) . '/../classes/ParseJson.php';
        ParseJson::inst($json);
    }

    private static function getCanonicalUrl($id_auth, $page)
    {
        if ($page <= 1) {
            $canonicalUrl = Config::$http_scheme . Config::$SiteDom . '.' . Config::$domainEnd . '/author.php?id_auth=' . $id_auth;
        } else {
            $canonicalUrl = Config::$http_scheme . Config::$SiteDom . '.' . Config::$domainEnd . '/author.php?id_auth=' . $id_auth . '&works=1#id_auth_photo=' . $id_auth . '&page=' . Pager::getCanonicalPageIndex($page);
        }
        return $canonicalUrl;
    }
}