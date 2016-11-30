<?php
/**
 * Created by Andre Haralevi
 * Date: 06.11.13
 * Time: 16:39
 */

namespace Photocommunity\Mobile;

class Consta
{
    const VK_API_ID_RU = 2622057;
    const VK_API_ID_COM = 2622057;
    const VK_API_ID_DE = 2622057;
    const VK_API_ID_BY = 2364937;

    const AUTH_TYPE_DEF = 0;
    const AUTH_TYPE_AMATURE = 1;
    const AUTH_TYPE_MODEL = 2;
    const AUTH_TYPE_VIEWER = 3;
    const AUTH_TYPE_ADMIN = 100;

    const AUTH_PREMIUM_0 = 0;
    const AUTH_PREMIUM_1 = 10;
    const AUTH_PREMIUM_2 = 20;
    const AUTH_PREMIUM_3 = 30;
    const AUTH_PREMIUM_4 = 40; # forever premium, never expires, never need to pay

    const AUTH_PREMIUM_NAME_0 = 'Basic';
    const AUTH_PREMIUM_NAME_1 = 'Plus';
    const AUTH_PREMIUM_NAME_2 = 'Premium';
    const AUTH_PREMIUM_NAME_3 = 'Pro';
    const AUTH_PREMIUM_NAME_4 = 'Pro';

    const RECS_PER_DAY_0 = 18;
    const RECS_PER_DAY_1 = 50;
    const RECS_PER_DAY_2 = 100;
    const RECS_PER_DAY_3 = 200;
    const RECS_PER_DAY_4 = 200;

    const AUTH_FAVOR_PREMIUM_0 = 100000;
    const AUTH_FAVOR_PREMIUM_1 = 100000;
    const AUTH_FAVOR_PREMIUM_2 = 100000;
    const AUTH_FAVOR_PREMIUM_3 = 100000;
    const AUTH_FAVOR_PREMIUM_4 = 100000;

    const WORK_GALL_LIMIT_0 = 100;
    const WORK_GALL_LIMIT_1 = 100000;
    const WORK_GALL_LIMIT_2 = 100000;
    const WORK_GALL_LIMIT_3 = 100000;
    const WORK_GALL_LIMIT_4 = 100000;

    const ID_PHOTO_CDN_FROM = 0;
    const ID_PHOTO_LOCAL_FROM = 310000;

    const NUDE_CAT = 111;
    const FIRST_SPEC_CAT = 2200;
    const PORTFOLIO_CAT = 2300;
    const ID_COMP_PF = 40;

    const COUNCIL_WIDTH = 220;
    const AVATAR_WIDTH = 220;

    const EOL = "\n";

    const ANON_OFFSET = 86400;
    const RECOMM_MIN_RATING = 5;
    const WORKS_PER_PAGE = 10;
    const WORKS_PER_PAGE_CANONICAL = 30;
    const COMM_PER_PAGE = 20;
    const MOBILE_MAX_WIDTH = 400;
    const RECS_SEC_LIMIT = 84600;
    const MIN_AUTH_POWER = 0.2;
    const PH_NO_COMM = 1;
    const MIN_SPECIAL_REC_CNT = 2;
    const HOME_BTN_MIN_RATING = 2;
    const POPULAR_PH_RATING = 20;

    public static $cur_day;

    public static $icons_match;
    public static $icons_replace;
    public static $crop_pattern;

    public static $active_comp = array(-1); #array(-1);

    public static $auth_fineart_arr = array(1, 24);

    public static function inst()
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new Consta();
        }
        return $instance;
    }

    private function __construct()
    {
        Consta::initVars();
    }

    private static function initVars()
    {
        Consta::$cur_day = mktime(0, 0, 0, date('n'), date('j'), date('Y'));

        Consta::$icons_match = array(' :)', ' :wink', ' :curvesmile', ' :beer', ' :confused', ' :eek', ' :evil', ' :frown', ' :idea', ' :lamer', ' :lol', ' :puke', ' :rolleyes', ' :roof', ' :insane', ' :love', ' :moderator', ' :molotok:', ' :naezd:', ' :redtongue', ' :shuffle', ' :photo', ' :super', ' :weep', ' :znaika', ' :nopets', ' :dislike', ' :hairup', ' :buzz', ' :taz');
        Consta::$icons_replace = array('&nbsp;<img alt="' . Localizer::$loc['smile_loc'] . '" src="' . Config::$home_url . 'img/' . Config::$theme . '/icons/icon_smile.gif" width="15" height="15" border="0" /> ', '&nbsp;<img alt="' . Localizer::$loc['wink_loc'] . '" src="' . Config::$home_url . 'img/' . Config::$theme . '/icons/icon_wink.gif" width="15" height="15" border="0" /> ', '&nbsp;<img alt="' . Localizer::$loc['curve_smile_loc'] . '" src="' . Config::$home_url . 'img/' . Config::$theme . '/icons/curve_smile.gif" width="15" height="15" border="0" /> ', '&nbsp;<img alt="' . Localizer::$loc['beer_loc'] . '" src="' . Config::$home_url . 'img/' . Config::$theme . '/icons/icon_beer.gif" width="57" height="16" border="0" /> ', '&nbsp;<img alt="' . Localizer::$loc['confused_loc'] . '" src="' . Config::$home_url . 'img/' . Config::$theme . '/icons/icon_confused.gif" width="37" height="15" border="0" /> ', '&nbsp;<img alt="' . Localizer::$loc['eek_loc'] . '" src="' . Config::$home_url . 'img/' . Config::$theme . '/icons/icon_eek.gif" width="15" height="15" border="0" /> ', '&nbsp;<img alt="' . Localizer::$loc['evil_loc'] . '" src="' . Config::$home_url . 'img/' . Config::$theme . '/icons/icon_evil.gif" width="15" height="15" border="0" /> ', '&nbsp;<img alt="' . Localizer::$loc['frown_loc'] . '" src="' . Config::$home_url . 'img/' . Config::$theme . '/icons/icon_frown.gif" width="15" height="15" border="0" /> ', '&nbsp;<img alt="' . Localizer::$loc['idea_loc'] . '" src="' . Config::$home_url . 'img/' . Config::$theme . '/icons/icon_idea.gif" width="15" height="32" border="0" /> ', '&nbsp;<img alt="' . Localizer::$loc['lamer_loc'] . '" src="' . Config::$home_url . 'img/' . Config::$theme . '/icons/icon_lamer.gif" width="15" height="20" border="0" /> ', '&nbsp;<img alt="' . Localizer::$loc['lol_loc'] . '" src="' . Config::$home_url . 'img/' . Config::$theme . '/icons/icon_lol.gif" width="15" height="15" border="0" /> ', '&nbsp;<img alt="' . Localizer::$loc['puke_loc'] . '" src="' . Config::$home_url . 'img/' . Config::$theme . '/icons/icon_puke.gif" border="0" /> ', '&nbsp;<img alt="' . Localizer::$loc['rolleyes_loc'] . '" src="' . Config::$home_url . 'img/' . Config::$theme . '/icons/icon_rolleyes.gif" width="15" height="15" border="0" /> ', '&nbsp;<img alt="' . Localizer::$loc['roof_loc'] . '" src="' . Config::$home_url . 'img/' . Config::$theme . '/icons/icon_roof.gif" width="33" height="16" border="0" /> ', '&nbsp;<img alt="' . Localizer::$loc['insane_loc'] . '" src="' . Config::$home_url . 'img/' . Config::$theme . '/icons/insane.gif" width="15" height="15" border="0" /> ', '&nbsp;<img alt="' . Localizer::$loc['love_loc'] . '" src="' . Config::$home_url . 'img/' . Config::$theme . '/icons/love.gif" width="19" height="27" border="0" />', '&nbsp;<img alt="' . Localizer::$loc['moderator_loc'] . '" src="' . Config::$home_url . 'img/' . Config::$theme . '/icons/moderator.gif" width="31" height="15" border="0" /> ', '&nbsp;<img alt="' . Localizer::$loc['molotok_loc'] . '" src="' . Config::$home_url . 'img/' . Config::$theme . '/icons/molotok.gif" width="33" height="26" border="0" /> ', '&nbsp;<img alt="' . Localizer::$loc['naezd_loc'] . '" src="' . Config::$home_url . 'img/' . Config::$theme . '/icons/naezd.gif" width="36" height="15" border="0" /> ', '&nbsp;<img alt="' . Localizer::$loc['redtongue_loc'] . '" src="' . Config::$home_url . 'img/' . Config::$theme . '/icons/redtongue.gif" width="15" height="15" border="0" /> ', '&nbsp;<img alt="' . Localizer::$loc['shuffle_loc'] . '" src="' . Config::$home_url . 'img/' . Config::$theme . '/icons/shuffle.gif" width="15" height="20" border="0" /> ', '&nbsp;<img alt="' . Localizer::$loc['photo_smile_loc'] . '" src="' . Config::$home_url . 'img/' . Config::$theme . '/icons/smile_photo.gif" width="20" height="20" border="0" /> ', '&nbsp;<img alt="' . Localizer::$loc['super_loc'] . '" src="' . Config::$home_url . 'img/' . Config::$theme . '/icons/smile_super.gif" width="25" height="18" border="0" /> ', '&nbsp;<img alt="' . Localizer::$loc['weep_loc'] . '" src="' . Config::$home_url . 'img/' . Config::$theme . '/icons/weep.gif" width="21" height="15" border="0" /> ', '&nbsp;<img alt="' . Localizer::$loc['znaika_loc'] . '" src="' . Config::$home_url . 'img/' . Config::$theme . '/icons/znaika.gif" width="25" height="19" border="0" /> ', '&nbsp;<img alt="' . Localizer::$loc['nopets_loc'] . '" src="' . Config::$home_url . 'img/' . Config::$theme . '/icons/nopets.gif" width="35" height="38" border="0" /> ', '&nbsp;<img alt="' . Localizer::$loc['dontlike_loc'] . '" src="' . Config::$home_url . 'img/' . Config::$theme . '/icons/smile_dontlike.gif" width="27" height="21" border="0" /> ', '&nbsp;<img alt="' . Localizer::$loc['hair_loc'] . '" src="' . Config::$home_url . 'img/' . Config::$theme . '/icons/smile_hair.gif" width="19" height="20" border="0" /> ', '&nbsp;<img alt="' . Localizer::$loc['buzz_loc'] . '" src="' . Config::$home_url . 'img/' . Config::$theme . '/icons/smile_buzz.gif" width="43" height="20" border="0" /> ', '&nbsp;<img alt="' . Localizer::$loc['taz_loc'] . '" src="' . Config::$home_url . 'img/' . Config::$theme . '/icons/smile_taz.gif" width="70" height="20" border="0" /> ');
        Consta::$crop_pattern = '/\[Crop:(\d{1,4}):(\d{1,4}):(\d{1,4}):(\d{1,4}):?(\d{1,4})?:?(\d{1,4})?:?(\d{1,4})?\]/';
    }
}

Consta::inst();