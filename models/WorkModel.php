<?php
/**
 * Created by Andre Haralevi
 * Date: 10/24/2016
 * Time: 5:21 AM
 */

namespace Photocommunity\Mobile;

class WorkModel
{
    public static function inst()
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new WorkModel();
        }
        return $instance;
    }

    private function __construct()
    {

    }

    public static $works_cache_tag;

    private static function getWorksWhere($params)
    {
        $works_where = '1';
        if (isset($params['all'])) {
            $works_where .= ' AND id_cat_new<' . Consta::FIRST_SPEC_CAT;
            WorkModel::$works_cache_tag = array('ds_photos=all');
        } else if (isset($params['special'])) {
            $works_where .= WorkModel::getSpecialRecWhere();
            WorkModel::$works_cache_tag = array('ds_photos=special');
        } else if (isset($params['popular'])) {
            $works_where .= ' AND ph_rating>=' . Consta::POPULAR_PH_RATING;
            WorkModel::$works_cache_tag = array('ds_photos=popular');
        } else if (isset($params['favorites'])) {
            $works_where .= ' AND id_cat_new<' . Consta::FIRST_SPEC_CAT;
            $works_where .= WorkModel::getWhereFollowers();
            WorkModel::$works_cache_tag = array('ds_photos_id_auth_follower=' . Auth::getIdAuth());
        } else if (isset($params['id_auth_photo'])) {
            $works_where .= ' AND PH.id_auth=' . $params['id_auth_photo'];
            $works_where .= ' AND id_cat_new<' . Consta::PORTFOLIO_CAT;
            WorkModel::$works_cache_tag = array('ds_photos_id_auth=' . $params['id_auth_photo']);
        } else { #reccomeded case
            $works_where .= ' AND id_cat_new<' . Consta::FIRST_SPEC_CAT;
            if (Auth::getIdAuth() == 1)
                $works_where .= ' AND PH.ph_rating<=1';
            else
                $works_where .= ' AND PH.ph_rating>=' . Auth::getAuthFeaturedRating();
            WorkModel::$works_cache_tag = array('ds_photos_recomm_min_rating=' . Auth::getAuthFeaturedRating());
        }
        return $works_where;
    }

    private static function getSpecialRecWhere() {
        #$where .= ' AND ph_special_rec_cnt>=' . Consta::MIN_SPECIAL_REC_CNT;
        $min_ph_special_rec_rating = 5;
        if (in_array(Auth::getIdAuth(), Consta::$auth_fineart_arr))
            $min_ph_special_rec_rating = 0;
        return ' AND ph_special_rec_cnt>=-5 AND ((PH.ph_is_fineart=\'1\' AND ph_rating>=' . $min_ph_special_rec_rating . ') OR ph_special_rec_cnt>=' . Consta::MIN_SPECIAL_REC_CNT . ')';
    }

    private static function isWorkExist($id_photo)
    {
        $is_work_exist = true;
        if ($id_photo >= Consta::ID_PHOTO_LOCAL_FROM && !file_exists(dirname(__FILE__) . '/../../images/' . Utils::getImgName($id_photo, 'mobile')))
            $is_work_exist = false;
        return $is_work_exist;
    }

    private static function isInvalidWork($v)
    {
        $is_to_skip = false;
        if ($v['id_photo'] >= Consta::ID_PHOTO_LOCAL_FROM && $v['ph_council_rec'] == '')
            $is_to_skip = true;

        $is_ph_anon = Utils::isAnon($v['ph_anon'], $v['ph_date'], $v['id_comp']);
        if ($is_ph_anon && isset($params['id_auth_photo']))
            $is_to_skip = true;
        return $is_to_skip;
    }

    public static function getWorks($params, $page = 1)
    {
        $works_where = WorkModel::getWorksWhere($params);
        $works_where .= Utils::getWhereSkipIdPhotos($page);

        $sql_works = "SELECT PH.id_photo, PH.id_auth id_auth_photo, 
                        PH.auth_name, PH.auth_name_com,
                        PH.id_cat_new, PH.ph_main_w, PH.ph_main_h, PH.ph_date, PH.ph_anon, PH.id_comp, PH.ph_council_rec, 
                        PH.ph_rating, PH.ph_rec_cnt, PH.ph_comm_cnt, PH.ph_comm_cnt, PH.ph_comm_cnt_de, PH.ph_comm_cnt_com
                FROM ds_photos PH
                WHERE " . $works_where . " AND ph_status='1'
                ORDER BY id_photo DESC
                LIMIT " . ($page - 1) * Consta::WORKS_PER_PAGE . ", " . Consta::WORKS_PER_PAGE;

        $res_works = Mcache::cacheDbi($sql_works, 300, WorkModel::$works_cache_tag);

        if (sizeof($res_works)) {
            $tpl_work_row_var['home_url'] = Config::$home_url;
            $tpl_work_row_content = Utils::getTpl('work_row', $tpl_work_row_var);

            $prev_next_nav = '';
            $works = '';
            foreach ($res_works as $k => $v) {

                if (WorkModel::isInvalidWork($v))
                    continue;

                if ($k == 0 && !WorkModel::isWorkExist($v['id_photo']))
                    continue;

                $is_ph_anon = Utils::isAnon($v['ph_anon'], $v['ph_date'], $v['id_comp']);

                $id_photo = $v['id_photo'];

                # remember ids for navigation
                $prev_next_nav .= $id_photo . ',';


                $work_img = Utils::parseWorkImg($id_photo, $v['id_auth_photo'], $v['id_cat_new'], $v['ph_main_w'], $v['ph_main_h']);

                $param_nav = '';
                if (isset($params['all'])) {
                    $param_nav .= '&all=' . $params['all'];
                } else if (isset($params['special'])) {
                    $param_nav .= '&special=' . $params['special'];
                } else if (isset($params['popular'])) {
                    $param_nav .= '&popular=' . $params['popular'];
                } else if (isset($params['favorites'])) {
                    $param_nav .= '&favorites=' . $params['favorites'];
                } else if (isset($params['id_auth_photo'])) {
                    $param_nav .= '&id_auth_photo=' . $params['id_auth_photo'];
                }

                if (Config::$domainEnd == 'by') {
                    $work_href = Config::$home_url . 'work.php?id_photo=' . $id_photo . $param_nav;
                } else {
                    if ($param_nav != '')
                        $param_nav = '?' . $param_nav;
                    $work_href = Config::$home_url . 'work/' . $id_photo . $param_nav;
                }

                $tpl_work_row_var['work_href'] = $work_href;
                $tpl_work_row_var['work_img'] = $work_img;
                $works .= Utils::parseTpl($tpl_work_row_content, $tpl_work_row_var);

                if ($v['id_auth_photo'] == Auth::getIdAuth()) {
                    $works .= '<div class="imgMetrics">&nbsp;' . Localizer::$loc['rating_loc'] . ': <b>' . $v['ph_rating'] . '</b> &nbsp;' . Localizer::$loc['recs_loc'] . ': <b>' . $v['ph_rec_cnt'] . '</b> &nbsp;' . Localizer::$loc['comm_loc'] . ': <b>' . $v['ph_comm_cnt'] . '</b></div>';
                } else {
                    $works .= '<div class="imgMetrics">';
                    $works .= '<div class="phRating">&nbsp;' . Localizer::$loc['rating_loc'] . ': <b>' . $v['ph_rating'] . '</b></div>';
                    if (!isset($params['id_auth_photo'])) {
                        if ($is_ph_anon)
                            $works .= '<div class="authName">' . Localizer::$loc['anonymous_loc'] . '&nbsp;</div>';
                        else
                            $works .= '<div class="authName"><a href="' . Config::$home_url . 'author.php?id_auth=' . $v['id_auth_photo'] . '">' . $v[Localizer::$col_auth_name] . '&nbsp;</a></div>';
                    }
                    $works .= '</div>';
                }
            }

            $prev_next_nav = substr($prev_next_nav, 0, -1);
            setcookie('prev_next_nav', $prev_next_nav, Config::$cookie_expires, '/', '.' . Config::$SiteDom . '.' . Config::$domainEnd);
            $_COOKIE['prev_next_nav'] = $prev_next_nav;
            #$_SESSION['prev_next_nav'] = $prev_next_nav;

            return array(
                'works' => $works,
            );
        } else {
            return array();
        }
    }

    public static function updateNextPrevNav($id_photo, $direction = 'next', $params)
    {
        $works_where = WorkModel::getWorksWhere($params);
        $works_where .= Utils::getWhereSkipIdPhotos(1);

        if ($direction == 'next') {
            $works_where .= ' AND  id_photo<=' . $id_photo;
            $orderBy = 'id_photo DESC';
        } else {
            $works_where .= " AND  id_photo>=" . $id_photo;
            $orderBy = 'id_photo';
        }

        $sql_works = "SELECT PH.id_photo, PH.id_auth id_auth_photo, 
                        PH.ph_date, PH.ph_anon, PH.id_comp, PH.ph_council_rec 
                FROM ds_photos PH
                WHERE " . $works_where . " AND ph_status='1'
                ORDER BY " . $orderBy . "
                LIMIT " . Consta::WORKS_PER_PAGE;

        $res_works = Mcache::cacheDbi($sql_works, 300, WorkModel::$works_cache_tag);

        if (sizeof($res_works)) {
            $prev_next_nav = '';
            foreach ($res_works as $k => $v) {

                if (WorkModel::isInvalidWork($v))
                    continue;

                if ($k == 0 && !WorkModel::isWorkExist($v['id_photo']))
                    continue;

                # remember ids for navigation
                $prev_next_nav .= $v['id_photo'] . ',';
            }
            $prev_next_nav = substr($prev_next_nav, 0, -1);

            if ($direction == 'prev') {
                $prev_next_nav_arr = explode(',', $prev_next_nav);
                $prev_next_nav_arr = array_reverse($prev_next_nav_arr);
                $prev_next_nav = '';
                foreach ($prev_next_nav_arr as $v)
                    $prev_next_nav .= $v . ',';
                $prev_next_nav = substr($prev_next_nav, 0, -1);
            }

            setcookie('prev_next_nav', $prev_next_nav, Config::$cookie_expires, '/', '.' . Config::$SiteDom . '.' . Config::$domainEnd);
            $_COOKIE['prev_next_nav'] = $prev_next_nav;
            #$_SESSION['prev_next_nav'] = $prev_next_nav;
        }
    }

    public static function getWork($id_photo = 5, $params, $prev = false, $next = false)
    {
        $where = '1';
        $order_by = 'PH.id_photo DESC';
        if ($prev || $next) {
            if ($prev) {
                $where .= ' AND PH.id_photo>' . $id_photo;
                $order_by = 'PH.id_photo';
            } else {
                $where .= ' AND PH.id_photo<' . $id_photo;
            }
            $work_cache_tag = array('prev_next_work=' . $id_photo);
        } else {
            $where .= ' AND PH.id_photo=' . $id_photo;
            $work_cache_tag = array('ds_photos=' . $id_photo);
        }

        if (isset($params['all'])) {
            $where .= ' AND id_cat_new<' . Consta::FIRST_SPEC_CAT;
        } else if (isset($params['special'])) {
            $where .= WorkModel::getSpecialRecWhere();
        } else if (isset($params['popular'])) {
            $where .= ' AND ph_rating>=' . Consta::POPULAR_PH_RATING;
        } else if (isset($params['favorites'])) {
            $where .= ' AND id_cat_new<' . Consta::FIRST_SPEC_CAT;
            $where .= WorkModel::getWhereFollowers();
        } else if (isset($params['id_auth_photo'])) {
            $where .= ' AND PH.id_auth=' . $params['id_auth_photo'];
            $where .= ' AND id_cat_new<' . Consta::PORTFOLIO_CAT;
        } else {
            if ($prev || $next)
                $where .= ' AND PH.ph_rating>=' . Auth::getAuthFeaturedRating();
            $where .= ' AND id_cat_new<' . Consta::FIRST_SPEC_CAT;
        }

        $where .= Utils::getWhereSkipIdPhotos(1);

        $sql_work = "SELECT
            PH.id_photo, PH.id_cat_new, PH.ph_is_fineart, PH.ph_special_rec_cnt, PH.ph_name, PH.ph_name_com, PH.ph_name_de, PH.ph_main_w, PH.ph_main_h, PH.ph_comm, PH.ph_date, PH.ph_anon, PH.id_comp,
            PH.ph_rating, PH.ph_norating, ph_critique,
            PH.id_auth id_auth_photo, PH.auth_name, PH.auth_name_com, 
            PH.ph_comm_cnt, PH.ph_comm_cnt_de, PH.ph_comm_cnt_com
            FROM ds_photos PH
            WHERE " . $where . " AND ph_status='1'
            ORDER BY " . $order_by . "
            LIMIT 1";
        $res_work = Mcache::cacheDbi($sql_work, 300, $work_cache_tag); #utils::printArr($res_work);
        if (!sizeof($res_work)) {
            return array();
        } else {
            $id_photo = $res_work[0]['id_photo'];
            $ph_name = $res_work[0][Localizer::$col_ph_name];
            $ph_name = Utils::setEmptyName($ph_name);
            $ph_comm = $res_work[0]['ph_comm'];
            $ph_comm = Utils::parseComm($ph_comm, false, false);
            $ph_comm = Utils::hideRussian($ph_comm);
            if ($ph_comm == '***' || $ph_name == $ph_comm) $ph_comm = '';
            $ph_norating = $res_work[0]['ph_norating'];
            $ph_anon = $res_work[0]['ph_anon'];
            $is_ph_anon = Utils::isAnon($ph_anon, $res_work[0]['ph_date'], $res_work[0]['id_comp']);
            $ph_comm_cnt = $res_work[0][Localizer::$col_ph_comm_cnt];

            // skip photo, if it's anon and if navigation from author's page
            if ($is_ph_anon && isset($params['id_auth_photo']) && ($prev || $next)) {
                $skip_anon_url = Config::$home_url . 'work.php?id_photo=' . $id_photo . '&id_auth_photo=' . $params['id_auth_photo'];
                if ($prev) $skip_anon_url .= '&prev=1';
                else $skip_anon_url .= '&next=1';
                header('Location: ' . $skip_anon_url);
                return false;
            }

            $id_auth_photo = $res_work[0]['id_auth_photo'];

            $sql_author = "SELECT auth_avatar, auth_gender, auth_premium, auth_status
                FROM ds_authors
                WHERE id_auth=" . $id_auth_photo . "
                LIMIT 1";
            $author_cache_tag = array('ds_authors=' . $id_auth_photo);
            $res_author = Mcache::cacheDbi($sql_author, 300, $author_cache_tag);

            if (!sizeof($res_author)) {
                return array();
            }

            $sql_recs = "SELECT id_auth, rec_power FROM ds_recs
                WHERE id_photo = " . $id_photo;
            $recs_cache_tag = array('ds_recs=' . $id_photo);
            $res_recs = Mcache::cacheDbi($sql_recs, 300, $recs_cache_tag); #if(Config::getDebug()) utils::printArr($res_recs);

            $is_recommended = false;
            $ph_rating = 0;
            foreach ($res_recs as $v_rec) {
                if (Auth::getIdAuth() == $v_rec['id_auth'])
                    $is_recommended = true;
                $ph_rating += $v_rec['rec_power'];
            }
            $ph_rating = number_format($ph_rating, 2);
            $ph_rec_cnt = sizeof($res_recs);

            if ($res_author[0]['auth_status'])
                $work_img = Utils::parseWorkImg($id_photo, $res_work[0]['id_auth_photo'], $res_work[0]['id_cat_new'], $res_work[0]['ph_main_w'], $res_work[0]['ph_main_h'], true);
            else
                $work_img = '<div style="padding: 40px 0 40px 0;">' . Localizer::$loc['author_blocked_loc'] . '</div>';

            if (isset($params['all'])) {
                $param_nav = '&all=' . $params['all'];
            } else if (isset($params['special'])) {
                $param_nav = '&special=' . $params['special'];
            } else if (isset($params['popular'])) {
                $param_nav = '&popular=' . $params['popular'];
            } else if (isset($params['favorites'])) {
                $param_nav = '&favorites=' . $params['favorites'];
            } else if (isset($params['id_auth_photo'])) {
                $param_nav = '&id_auth_photo=' . $params['id_auth_photo'];
            } else {
                $param_nav = '';
            }

            if (Config::$domainEnd == 'by') {
                $work_href = Config::$home_url . 'work.php?id_photo=' . $id_photo . $param_nav;
            } else {
                if ($param_nav != '')
                    $param_nav = '?' . $param_nav;
                $work_href = Config::$home_url . 'work/' . $id_photo . $param_nav;
            }

            $tpl_work_main_img_var['work_href'] = $work_href;
            $tpl_work_main_img_var['work_img'] = $work_img;

            $ph_rating_str = '';
            if ($res_work[0]['id_cat_new'] < Consta::FIRST_SPEC_CAT) { # no rating category
                $ph_rating_str .= Localizer::$loc['rating_loc'] . ': <b id="phRating">' . $ph_rating . '</b>';
            }
            if (Auth::getIdAuth() == $id_auth_photo) {
                $ph_rating_str .= ' &nbsp;' . Localizer::$loc['recs_loc'] . ': <b id="phRating">' . $ph_rec_cnt . '</b>';
                $ph_rating_str .= ' &nbsp;' . Localizer::$loc['comm_loc'] . ': <b id="phRating">' . $ph_comm_cnt . '</b>';
            }
            $tpl_work_main_img_var['ph_rating_str'] = $ph_rating_str;

            // parse home album button
            $home_album_str = '';
            $is_home_album = false;
            if ($res_author[0]['auth_status'] && Auth::getIdAuth() != -1 && Auth::getIdAuth() != $id_auth_photo && !$is_recommended && $res_work[0]['id_cat_new'] < Consta::FIRST_SPEC_CAT && (Auth::getAuthRating() >= Consta::HOME_BTN_MIN_RATING || Auth::getAuthType() == Consta::AUTH_TYPE_ADMIN)) {
                $sql_home_album = "SELECT id_photo FROM ds_home_album WHERE id_photo=" . $id_photo . " AND id_auth=" . Auth::getIdAuth() . " LIMIT 1";
                $res_home_album = Mcache::cacheDbi($sql_home_album, 300, array('ds_home_album=' . $id_photo));
                if (!sizeof($res_home_album)) {
                    $is_home_album = true;
                    if (Config::$lang == 'de') {
                        $homeAlbumBtnBg = 'button_no_de.png';
                        $homeAlbumBtnW = 51;
                    } else {
                        $homeAlbumBtnBg = 'button_no.png';
                        $homeAlbumBtnW = 47;
                    }
                    $home_album_str .= '<a id="homeAlbumBtn" href="#" class="saveBtn" style="width: ' . $homeAlbumBtnW . 'px; background: url(/css/def/' . $homeAlbumBtnBg . ') 0 0 no-repeat;"></a>';
                }
            }
            $tpl_work_main_img_var['home_album_str'] = $home_album_str;

            // parse recommend button
            $add_rec_str = '';
            if ($res_author[0]['auth_status']) {
                if ($res_work[0]['id_cat_new'] >= Consta::FIRST_SPEC_CAT) { # no rating category
                    $add_rec_str .= '<span class="recNote">' . Localizer::$cat_names[$res_work[0]['id_cat_new']] . '</span>';
                } else if ($ph_norating || Auth::getIdAuth() == $id_auth_photo) {
                    # your work
                } else if ($is_recommended) { # already recommended
                    $add_rec_str .= '<span class="recNote">' . Localizer::$loc['already_rec_note_loc'] . '</span>';
                } else if (!WorkModel::isRecAllowed()) { # rec limit is achieved
                    $add_rec_str .= '<span class="recNote">' . Localizer::$loc['limit_recs_achieved_1_loc'] . '<br /><b>' . Utils::getRecPerDay(Auth::getAuthPremium()) . '</b> ' . Localizer::$loc['limit_recs_achieved_2_loc'] . ' <a href="//' . Config::$SiteDom . '.' . Config::$domainEnd . '/pricing.php">' . (Localizer::$loc['why_loc']) . '</a></span>';
                } else if (Auth::getIdAuth() != -1 && !$is_home_album && Auth::getAuthRating() >= Consta::HOME_BTN_MIN_RATING) {
                    # home album clicked
                } else if (Auth::getIdAuth() != -1) { # ok, logged author can recommend
                    $add_rec_str .= '<a id="addRecBtn" href="#" class="saveBtn">' . Localizer::$loc['add_rec_loc'] . '</a>';
                } else {
                    # author unlogged
                }
            }
            $tpl_work_main_img_var['add_rec_str'] = $add_rec_str;


            // parse fine-art button
            $fineart_str = '';
            if ($res_author[0]['auth_status'] && in_array(Auth::getIdAuth(), Consta::$auth_fineart_arr)) {
                if ($res_work[0]['ph_is_fineart'])
                    $fineart_str .= '<a id="fineartBtn" data-fineart="0" href="#" class="saveBtn">' . Localizer::$loc['remove_from_fineart'] . '</a>';
                else
                    $fineart_str .= '<a id="fineartBtn" data-fineart="1" href="#" class="saveBtn">' . Localizer::$loc['add_to_fineart'] . '</a>';
            }
            $tpl_work_main_img_var['fineart_str'] = $fineart_str;

            $authNameAnswerClass = '';
            if ($is_ph_anon) {
                $auth_name_photo = Localizer::$loc['author_loc'];
                $auth_avatar_str = '<img src="' . Config::$css_url . Config::$theme . '/male.png" alt="">';
                if (Auth::getIdAuth() != -1)
                    $authNameAnswerClass = 'class="authNameAnswer" data-id-auth="0"';
                $auth_name_str = '<a id="authName0" class="authNameAnswer" ' . $authNameAnswerClass . ' href="#">' . $auth_name_photo . '</a>';
                $auth_premium_badge = '';
            } else {
                $auth_name_photo = $res_work[0][Localizer::$col_auth_name];
                $auth_avatar_src = Utils::parseAvatar($id_auth_photo, $res_author[0]['auth_avatar'], $res_author[0]['auth_gender'], 'square');
                $auth_avatar_str = '<a href="' . Config::$home_url . 'author.php?id_auth=' . $id_auth_photo . '"><img src="' . $auth_avatar_src . '" alt=""></a>';
                if (Auth::getIdAuth() != -1)
                    $authNameAnswerClass = 'class="authNameAnswer" data-id-auth="' . $id_auth_photo . '"';
                $auth_name_str = '<a id="authName' . $id_auth_photo . '" ' . $authNameAnswerClass . ' href="' . Config::$home_url . 'author.php?id_auth=' . $id_auth_photo . '">' . $auth_name_photo . '</a>';
                $auth_premium_badge = Utils::getPremiumBadge($res_author[0]['auth_premium'], 'static');
            }
            $tpl_work_main_img_var['auth_avatar_str'] = $auth_avatar_str;
            $tpl_work_main_img_var['auth_name_str'] = $auth_name_str;
            $tpl_work_main_img_var['auth_premium_badge'] = $auth_premium_badge;
            $tpl_work_main_img_var['ph_name'] = $ph_name;
            $tpl_work_main_img_var['ph_comm'] = $ph_comm;
            $work = Utils::getTpl('work_main_img', $tpl_work_main_img_var);

            return array(
                'og_image' => Utils::getImgPath($id_photo) . Utils::getImgName($id_photo),
                'id_photo' => $id_photo,
                'ph_name' => $ph_name,
                'is_ph_anon' => $is_ph_anon,
                'id_auth_photo' => $id_auth_photo,
                'auth_status_photo' => $res_author[0]['auth_status'],
                'ph_critique' => $res_work[0]['ph_critique'],
                'auth_name_photo' => $auth_name_photo,
                'work' => $work,
            );
        }
    }

    private static function getWhereFollowers()
    {
        $authFollowLimit = Utils::getAuthFollowLimit(Auth::getAuthPremium());
        $where = '';
        $sql_follow = "SELECT id_auth FROM ds_followers WHERE id_auth_follower=" . Auth::getIdAuth();
        $follow_cache_tag = array('ds_followers=' . Auth::getIdAuth());
        $res_follow = Mcache::cacheDbi($sql_follow, 300, $follow_cache_tag);
        if (sizeof($res_follow)) {
            $where .= ' AND id_auth IN (';
            $i = 0;
            foreach ($res_follow as $v_follow) {
                $where .= $v_follow['id_auth'] . ', ';
                if (++$i >= $authFollowLimit) break;
            }
            $where = substr($where, 0, -2) . ')';
        }
        return $where;
    }

    private static function isRecAllowed()
    {
        if (Auth::getAuthLastRecsCnt() >= Utils::getRecPerDay(Auth::getAuthPremium()))
            return false;
        else
            return true;
    }
}