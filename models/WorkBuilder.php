<?php
namespace photocommunity\mobile;

class WorkBuilder
{

    public static function inst()
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new WorkBuilder();
        }
        return $instance;
    }

    private function __construct()
    {

    }

    function build($isHtml)
    {
        # handle request
        $id_photo = Request::getParam('id_photo', 'integer', 1);
        $id_auth_photo = Request::getParam('id_auth_photo', 'integer', 0);

        if (isset($_REQUEST['prev'])) $prev = true;
        else $prev = false;
        if (isset($_REQUEST['next'])) $next = true;
        else $next = false;

        $all = Request::getParam('all', 'integer', 0);
        $fineart = Request::getParam('fineart', 'integer', 0);
        $popular = Request::getParam('popular', 'integer', 0);
        $favorites = Request::getParam('favorites', 'integer', 0);
        # /handle request

        # parse work
        require dirname(__FILE__) . '/../models/WorkModel.php';

        $params = array();
        if ($all) {
            $params['all'] = 1;
        }
        else if ($fineart) {
            $params['fineart'] = 1;
        }
        else if ($popular) {
            $params['popular'] = 1;
        }
        else if ($favorites) {
            $params['favorites'] = 1;
        }
        else if ($id_auth_photo) {
            $params['id_auth_photo'] = $id_auth_photo;
        }

        $res_work = WorkModel::getWork($id_photo, $params, $prev, $next);
        if (!sizeof($res_work)) {
            if ($isHtml)
                header('location: index.php');
            #return false;
        }

        $id_photo = $res_work['id_photo'];
        $work = $res_work['work'];
        $ph_name = $res_work['ph_name'];
        $auth_name_photo = $res_work['auth_name_photo'];
        # /parse work

        # parse comments
        require dirname(__FILE__) . '/../models/CommModel.php';

        $res_comments = CommModel::getComments($id_photo, $res_work['id_auth_photo'], $res_work['is_ph_anon']);
        $comments = $res_comments['comments'];
        # /parse comments

        # parse pager
        $workHref = 'work.php?id_photo=' . $id_photo;
        if($all) {
            $workHref .= '&amp;all=1';
        }
        else if($fineart) {
            $workHref .= '&amp;fineart=1';
        }
        else if($popular) {
            $workHref .= '&amp;popular=1';
        }
        else if($favorites) {
            $workHref .= '&amp;favorites=1';
        }
        else if ($id_auth_photo) {
            $workHref .= '&id_auth_photo=' . $id_auth_photo;
        }
        $hrefPrev = $workHref . '&amp;prev=1';
        $hrefNext = $workHref . '&amp;next=1';
        # /parse pager

        return array(
            'hrefPrev' => $hrefPrev,
            'hrefNext' => $hrefNext,
            'work' => $work,
            'ph_name' => $ph_name,
            'auth_name_photo' => $auth_name_photo,
            'comments' => $comments,
        );
    }
}