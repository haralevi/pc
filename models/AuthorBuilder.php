<?php
namespace photocommunity\mobile;

class AuthorBuilder extends Builder
{
    private static $isJson = false;

    public static function inst($tpl_name)
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new AuthorBuilder($tpl_name);
        }
        return $instance;
    }

    public function __construct($tpl_name)
    {
        parent::__construct($tpl_name);
    }

    public static function buildJson()
    {
        AuthorBuilder::$isJson = true;
        AuthorBuilder::build();
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
        $auth_name_photo = '';
        $author = '';
        if (!AuthorBuilder::$isJson) {
            require dirname(__FILE__) . '/AuthorModel.php';
            $res_author = AuthorModel::getAuthor($id_auth_photo);
            if (!sizeof($res_author)) {
                header('location: index.php');
                return false;
            }
            $auth_name_photo = $res_author['auth_name_photo'];
            $author = $res_author['author'];
        }
        # /parse author

        # parse works
        require dirname(__FILE__) . '/WorkModel.php';

        $res_works = WorkModel::getWorks($page, array('id_auth_photo' => $id_auth_photo));
        if (!sizeof($res_works)) {
            if (!AuthorBuilder::$isJson)
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
        );

        if (!AuthorBuilder::$isJson)
            AuthorBuilder::parse($author);
        else
            AuthorBuilder::parseJson($author);

        return true;

    }

    private static function parse($author) {
        if(!$author)
            die();

        AuthorBuilder::$tpl_var['id_auth_photo'] = $author['id_auth_photo'];
        AuthorBuilder::$tpl_var['author'] = $author['author'];
        AuthorBuilder::$tpl_var['works'] = $author['works'];

        AuthorBuilder::$tpl->parse(AuthorBuilder::$tpl_var);

        AuthorBuilder::$tpl_main_var['content'] = AuthorBuilder::$tpl->get();
        AuthorBuilder::$tpl_main_var['href_prev_page'] = $author['hrefPrev'];
        AuthorBuilder::$tpl_main_var['href_next_page'] = $author['hrefNext'];


        # set menu style
        if($author['id_auth_photo'] == Auth::getIdAuth())
            $page_type = 'my_profile';
        else
            $page_type = '';
        AuthorBuilder::$tpl_main_var = Utils::setMenuStyles(AuthorBuilder::$tpl_main_var, $page_type);

        # set seo vars
        AuthorBuilder::$tpl_main_var['port_seo_title'] = $author['auth_name_photo'] . ' / ' . Utils::getSiteName();

        # parse page
        Parse::inst(AuthorBuilder::$tpl_main, AuthorBuilder::$tpl_main_var);
    }

    private static function parseJson($author)
    {
        # build json
        $json = '{';
        if($author) {
            if (Config::getDebug()) $json .= '"debug": "#debug#", ';
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
}