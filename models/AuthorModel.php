<?php
/**
 * Created by Andre Haralevi
 * Date: 10/24/2016
 * Time: 5:21 AM
 */

namespace Photocommunity\Mobile;

class AuthorModel
{
    public static function inst()
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new AuthorModel();
        }
        return $instance;
    }

    private function __construct()
    {
    }

    public static function getAuthor($id_auth_photo = 1)
    {
        $sql_author = "SELECT id_auth, auth_premium, auth_name, auth_name_en,
            auth_avatar, auth_gender, auth_dom,
            auth_img_cnt, auth_img_cnt_norate, auth_rating
            FROM ds_authors
            WHERE id_auth=" . $id_auth_photo . "
            LIMIT 1";
        $author_cache_tag = array('ds_authors=' . $id_auth_photo);
        $res_author = Mcache::cacheDbi($sql_author, 300, $author_cache_tag);

        if (!sizeof($res_author)) {
            return array();
        } else {
            $auth_premium_photo = $res_author[0]['auth_premium'];
            $id_auth_photo = $res_author[0]['id_auth'];
            $auth_avatar_src = Utils::parseAvatar($res_author[0]['id_auth'], $res_author[0]['auth_avatar'], $res_author[0]['auth_gender'], 'small');
            $auth_name_photo = $res_author[0][Localizer::$col_auth_name];
            $auth_img_cnt_total = $res_author[0]['auth_img_cnt'] + $res_author[0]['auth_img_cnt_norate'];
            if ($auth_img_cnt_total > Utils::getWorkGallLimit($res_author[0]['auth_premium']))
                $auth_img_cnt_total = Utils::getWorkGallLimit($res_author[0]['auth_premium']);

            $auth_rating_work = $res_author[0]['auth_rating'];

            if (Auth::getIdAuth() != -1 && Auth::getIdAuth() != $id_auth_photo) {
                $is_display_follow_btn = '';
                $sql_follow = "SELECT id_auth FROM ds_followers WHERE id_auth=" . $id_auth_photo . " AND id_auth_follower=" . Auth::getIdAuth() . " LIMIT 1";
                $follow_cache_tag = array('ds_followers=' . Auth::getIdAuth());
                $res_follow = Mcache::cacheDbi($sql_follow, 300, $follow_cache_tag);
                if (sizeof($res_follow)) {
                    $follow_btn_id = 'unfollowBtn';
                    $follow_btn_class = 'undoBtn';
                    $follow_btn_val = Localizer::$loc['del_favorite_loc'];
                } else {
                    $follow_btn_id = 'followBtn';
                    $follow_btn_class = '';
                    $follow_btn_val = Localizer::$loc['add_favorite_loc'];
                }

            } else {
                $follow_btn_id = '';
                $follow_btn_class = '';
                $follow_btn_val = '';
                $is_display_follow_btn = ' display:none;';
            }

            $tpl_author_header_var['home_url'] = Config::$home_url;
            $tpl_author_header_var['id_auth_photo'] = $id_auth_photo;
            $tpl_author_header_var['auth_avatar_src'] = $auth_avatar_src;
            $tpl_author_header_var['auth_premium_badge'] = Utils::getPremiumBadge($res_author[0]['auth_premium']);
            $tpl_author_header_var['auth_name_photo'] = $auth_name_photo;
            $tpl_author_header_var['auth_img_cnt_total'] = $auth_img_cnt_total;
            $tpl_author_header_var['works_loc'] = Localizer::$loc['works_loc'];
            $tpl_author_header_var['auth_rating_work'] = $auth_rating_work;
            $tpl_author_header_var['rating_loc'] = Localizer::$loc['rating_loc'];

            $tpl_author_header_var['is_display_follow_btn'] = $is_display_follow_btn;
            $tpl_author_header_var['follow_btn_id'] = $follow_btn_id;
            $tpl_author_header_var['follow_btn_class'] = $follow_btn_class;
            $tpl_author_header_var['follow_btn_val'] = $follow_btn_val;

            $tpl_author_header_var['portfolio_a'] = Config::$http_scheme . $res_author[0]['auth_dom'] . '.' . Config::$SiteDom . '.' . Config::$domainEnd . '/portfolio';
            $tpl_author_header_var['portfolio_loc'] = Localizer::$loc['portfolio_loc'];

            $author = Utils::getTpl('author_header', $tpl_author_header_var);
        }

        return array(
            'auth_premium_photo' => $auth_premium_photo,
            'auth_name_photo' => $auth_name_photo,
            'author' => $author,
        );
    }
}