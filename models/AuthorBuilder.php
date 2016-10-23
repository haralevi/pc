<?php
namespace photocommunity\mobile;

class AuthorBuilder
{

    public static function inst()
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new AuthorBuilder();
        }
        return $instance;
    }

    private function __construct()
    {

    }

    function build($isHtml)
    {
        # handle request
        $id_auth_photo = Request::getParam('id_auth', 'integer', 0);

        if (isset($_REQUEST['auth_dom'])) {
            $auth_dom_work = Request::getParam('auth_dom', 'string');
            $sql_works = "SELECT id_auth FROM ds_authors WHERE auth_dom='" . $auth_dom_work . "' LIMIT 1";
            $author_cache_tag = array('ds_authors=' . $auth_dom_work);
            $res_auth_work = Mcache::inst()->cacheDbi($sql_works, 300, $author_cache_tag);
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
        if ($isHtml) {
            require dirname(__FILE__) . '/AuthorModel.php';
            $res_author = AuthorModel::inst()->getAuthor($id_auth_photo);
            if (!sizeof($res_author)) {
                if ($isHtml)
                    header('location: index.php');
                return false;
            }
            $auth_name_photo = $res_author['auth_name_photo'];
            $author = $res_author['author'];
        }
        # /parse author

        # parse works
        require dirname(__FILE__) . '/WorkModel.php';

        $res_works = WorkModel::inst()->getWorks($page, array('id_auth_photo' => $id_auth_photo));
        if (!sizeof($res_works)) {
            if($isHtml)
                $works = '';
            else
                die();
        } else
            $works = $res_works['works'];
        # /parse works

        # parse pager
        require dirname(__FILE__) . '/../classes/Pager.php';

        $hrefPrev = 'author.php?id_auth=' . $id_auth_photo . Pager::inst()->getHrefPrev($page);
        $hrefNext = 'author.php?id_auth=' . $id_auth_photo . Pager::inst()->getHrefNext($page);
        # /parse pager

        return array(
            'hrefPrev' => $hrefPrev,
            'hrefNext' => $hrefNext,
            'auth_name_photo' => $auth_name_photo,
            'id_auth_photo' => $id_auth_photo,
            'author' => $author,
            'works' => $works,
        );
    }

}