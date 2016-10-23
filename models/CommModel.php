<?php
namespace photocommunity\mobile;

class CommModel
{
    public static function inst()
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new CommModel();
        }
        return $instance;
    }

    private function __construct()
    {

    }

    public static function getComm($page = 1)
    {
        $where = '';
        if (Config::$domainEnd == 'ru' || Config::$domainEnd == 'by')
            $where .= ' AND LENGTH(' . Localizer::$col_comm_text . ')>40 AND COMM.comm_vote>=0';
        else
            $where .= ' AND COMM.' . Localizer::$col_comm_text . '!=""';

        $sql_comm = "SELECT
                PH.id_photo, PH.id_auth id_auth_photo, PH.id_cat_new, PH.ph_main_w, PH.ph_main_h, PH.ph_anon, PH.ph_date, PH.id_comp,
                COMM.id_comm, COMM.comm_text, COMM.comm_text_en, COMM.comm_text_de, COMM.comm_date, COMM.comm_status,
                AU_COM." . Localizer::$col_auth_name . " comm_auth_name, AU_COM.id_auth comm_id_auth
            FROM (" . Localizer::$tbl_ds_comments . " COMM, ds_photos PH, ds_authors AU)
            JOIN ds_authors AU_COM ON (COMM.id_auth=AU_COM.id_auth)
            WHERE
                COMM.id_photo=PH.id_photo AND PH.id_auth=AU.id_auth
                AND comm_status IN ('1') AND PH.id_cat_new<" . Consta::FIRST_SPEC_CAT . "
                " . $where . "
            ORDER BY id_comm DESC
            LIMIT " . ($page - 1) * Consta::COMM_PER_PAGE . ", " . Consta::COMM_PER_PAGE;
        $comm_cache_tag = array(Localizer::$tbl_ds_comments . '=' . $page);
        $res_comm = Mcache::inst()->cacheDbi($sql_comm, 300, $comm_cache_tag); #utils::printArr($res_comm);

        if (!sizeof($res_comm)) {
            return array();
        } else {
            $tpl_comm_row_var['home_url'] = Config::$home_url;
            $tpl_comm_row_content = Utils::getTpl('comm_row', $tpl_comm_row_var);

            $comm = '<table id="commTbl">';
            foreach ($res_comm as $v) {

                # skip comments of ignored authors
                if (Auth::isAuthIgnored($v['comm_id_auth']))
                    continue;

                # skip photos of ignored authors
                if (Auth::isAuthIgnored($v['id_auth_photo']))
                    continue;

                $comm_text = $v[Localizer::$col_comm_text];
                if ($comm_text == '' || strstr($comm_text, '.ru'))
                    continue;

                if ($v['comm_status'] == 1)
                    $comm_text = Utils::parseComm($comm_text, false, false);
                else
                    continue;

                $id_auth_comm = $v['comm_id_auth'];
                $auth_name_comm = $v['comm_auth_name'];
                $id_photo = $v['id_photo'];
                $id_auth_photo = $v['id_auth_photo'];

                $workImg = Utils::parseWorkImg($id_photo, $v['id_auth_photo'], $v['id_cat_new'], $v['ph_main_w'], $v['ph_main_h'], false, true);
                $workImg = str_replace('mobile', 'thumb', $workImg);

                $is_ph_anon = Utils::isAnon($v['ph_anon'], $v['ph_date'], $v['id_comp']);
                if ($is_ph_anon && $id_auth_comm == $id_auth_photo) {
                    $auth_name_str = Localizer::$loc['author_loc'];
                } else {
                    $auth_name_str = '<a href="' . Config::$home_url . 'author.php?id_auth=' . $id_auth_comm . '">' . $auth_name_comm . '</a>';
                }

                $tpl_comm_row_var['id_photo'] = $id_photo;
                $tpl_comm_row_var['work_img'] = $workImg;
                $tpl_comm_row_var['auth_name_str'] = $auth_name_str;
                $tpl_comm_row_var['comm_text'] = $comm_text;

                $comm .= Utils::parseTpl($tpl_comm_row_content, $tpl_comm_row_var);
            }
            $comm .= '</table>';
        }
        #if($comments == '') $comments = '<div style="padding: 6px 6px;">No comments yet</div>';

        return array(
            'comm' => $comm,
        );
    }

    public static function getComments($id_photo, $id_auth_photo, $is_ph_anon = 0, $ph_critique = 0)
    {
        $sql_comments = "SELECT
                COMM.id_comm, COMM.id_auth, COMM.comm_text, COMM.comm_text_en, COMM.comm_text_de, COMM.comm_status,
                AU." . Localizer::$col_auth_name . ", AU.auth_gender, AU.auth_avatar
              FROM " . Localizer::$tbl_ds_comments . " COMM, ds_authors AU
              WHERE COMM.id_auth=AU.id_auth AND id_photo=" . $id_photo . " 
              ORDER BY id_comm";
        $comments_cache_tag = array(Localizer::$tbl_ds_comments . '=' . $id_photo);
        $res_comments = Mcache::inst()->cacheDbi($sql_comments, 300, $comments_cache_tag); #utils::printArr($res_comments);

        $is_self_photo = false;
        if ($id_auth_photo == Auth::inst()->getIdAuth())
            $is_self_photo = true;

        $comments = '';
        $is_comm_deleted = false;
        if (sizeof($res_comments)) {
            $tpl_work_comm_row_content = Utils::getTpl('work_comm_row');

            $comments = '<table id="commTbl">';
            $comm_cnt = 0;
            foreach ($res_comments as $v) {

                # skip comments of ignored authors
                if (Auth::isAuthIgnored($v['id_auth']))
                    continue;

                $auth_avatar_src = Utils::parseAvatar($v['id_auth'], $v['auth_avatar'], $v['auth_gender'], 'square');
                $comm_text = $v[Localizer::$col_comm_text];
                if ($comm_text == '') {
                    continue;
                } else if ($v['comm_status'] == 2) {
                    $comm_text = '<span style="font-size: 12px;">' . Localizer::$loc['comm_del_by_admin_loc'] . '</span>';
                    if (!$is_self_photo && $v['id_auth'] == Auth::inst()->getIdAuth())
                        $is_comm_deleted = true;
                } else if ($v['comm_status'] == 3) {
                    $comm_text = '<span style="font-size: 12px;">' . Localizer::$loc['comm_del_by_author_loc'] . '</span>';
                    if (!$is_self_photo && $v['id_auth'] == Auth::inst()->getIdAuth())
                        $is_comm_deleted = true;
                } else
                    $comm_text = Utils::parseComm($comm_text, true, false);

                $comm_cnt++;

                $id_auth_comm = $v['id_auth'];
                $auth_name_comm = $v[Localizer::$col_auth_name];

                if ($is_ph_anon && $id_auth_comm == $id_auth_photo) {
                    $auth_avatar_str = '<img src="' . Config::$css_url . Config::$theme . '/male.png" width="31" height="31" alt="">';
                    $auth_name_str = '<a id="authName' . $comm_cnt . '" onclick="app.emoticon(' . $comm_cnt . '); return false;" href="#">' . Localizer::$loc['author_loc'] . '</a>';
                } else {
                    $auth_avatar_str = '<a href="' . Config::$home_url . 'author.php?id_auth=' . $id_auth_comm . '"><img src="' . $auth_avatar_src . '" alt=""></a>';
                    $auth_name_str = '<a id="authName' . $id_auth_comm . '" onclick="app.emoticon(' . $id_auth_comm . '); return false;" href="' . Config::$home_url . 'author.php?id_auth=' . $id_auth_comm . '">' . $auth_name_comm . '</a>';
                }

                $tpl_work_comm_row_var['auth_avatar_str'] = $auth_avatar_str;
                $tpl_work_comm_row_var['auth_name_str'] = $auth_name_str;
                $tpl_work_comm_row_var['comm_text'] = $comm_text;
                $comments .= Utils::parseTpl($tpl_work_comm_row_content, $tpl_work_comm_row_var);
            }
            $comments .= '</table>';
        }

        if ($ph_critique == Consta::PH_NO_COMM || $is_comm_deleted || CommModel::isBannedAuthor($id_auth_photo)) {
            $comments .= '<div style="margin: 10px 0 16px 0; font-size: 12px;">' . Localizer::$loc['forbidden_write_comm_loc'] . '</div>';
        } else if (Auth::inst()->getIdAuth() != -1 && Auth::inst()->getAuthType() != Consta::AUTH_TYPE_VIEWER) {
            $tpl_work_comm_form_var['add_comm_loc'] = Localizer::$loc['add_comm_loc'];
            $tpl_work_comm_form_var['css_url'] = Config::$css_url;
            $tpl_work_comm_form_var['theme'] = Config::$theme;
            $comments .= Utils::getTpl('work_comm_form', $tpl_work_comm_form_var);
        }

        return array(
            'comments' => $comments,
        );
    }

    private static function isBannedAuthor($id_auth_photo)
    {
        $is_banned_author = false;
        if (Auth::inst()->getIdAuth() == -1 || $id_auth_photo == Auth::inst()->getIdAuth()) {
            $is_banned_author = false;
        } else {
            $sql = "SELECT id_auth_banned FROM ds_banned_authors
                WHERE id_auth=" . $id_auth_photo . " AND id_auth_banned=" . Auth::inst()->getIdAuth() . " LIMIT 1";
            $banned_authors_cache_tag = array('ds_banned_authors=' . $id_auth_photo);
            $res = Mcache::inst()->cacheDbi($sql, 300, $banned_authors_cache_tag);
            if (sizeof($res))
                $is_banned_author = true;
        }
        return $is_banned_author;
    }
}