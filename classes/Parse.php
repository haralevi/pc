<?php
/**
 * Created by Andre Haralevi
 * Date: 07.11.13
 * Time: 12:35
 */

namespace Photocommunity\Mobile;

class Parse
{
    public static function inst($tpl, $tpl_var)
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new Parse($tpl, $tpl_var);
        }
        return $instance;
    }

    private function __construct($tpl, $tpl_var)
    {
        Db::disconnect();

        Parse::parseHtml($tpl, $tpl_var);
        Utils::sendHeaders();
        Parse::printHtml();
        Utils::logVisits();
    }

    /**
     * @param Tpl $tpl
     * @param $tpl_var
     */
    private static function parseHtml($tpl, $tpl_var)
    {
        # set template urls
        $tpl_var = Parse::setUrls($tpl_var);

        # set template vars
        $tpl_var = Parse::setTplVars($tpl_var);

        # set seo vars
        $tpl_var = Parse::setSeoVars($tpl_var);

        # set goole vars
        if (Auth::getAuthType() == Consta::AUTH_TYPE_ADMIN)
            $tpl->clear('GO_BLK');
        $tpl_var = Parse::setGoogleVars($tpl_var);

        $tpl_var['id_auth'] = Auth::getIdAuth();
        $tpl_var['auth_premium_name_ga'] = Utils::getAuthPremiumName(Auth::getAuthPremium(), true);
        $tpl_var['auth_name'] = str_replace('"', '&quot;', Auth::getAuthName());
        $tpl_var['auth_avatar'] = Utils::parseAvatar(Auth::getIdAuth(), Auth::getAuthAvatar(), Auth::getAuthGender(), 'square');
        $tpl_var['auth_url'] = Config::$home_url . 'author.php?id_auth=' . Auth::getIdAuth();

        if (Auth::getIdAuth() == -1) {
            $tpl->clear('UNLOGGED_BLK');
        } else {
            $tpl->clear('LOGGED_BLK');
        }

        if (!isset($_REQUEST['wrn_login']) || Auth::getIdAuth() != -1)
            $tpl->clear('WRONG_LOGIN_BLK');

        $tpl->parse($tpl_var);
        $html = $tpl->get();

        Timer::stopTiming('Total');
        $debug = '';
        if (Config::getDebug()) {
            $debug .= '<div id="debug">';
            $totalTime = Timer::getATimings()['Total']['elapsed'];
            if ($totalTime >= 0.1)
                $debug .= 'Total Time: <b>' . $totalTime . '</b> sec';
            if (Db::getTotalTime() >= 0.1) $debug .= '<br>Mysql Time: <b>' . Db::getTotalTime() . '</b>';
            if (Db::getQueries() != '') $debug .= '<br>' . Db::getQueries();
            $debug .= '</div>';
            #$debug = '';
        } else
            $html = preg_replace('/<\!--\[[a-z\d_]+\]-->/i', '', $html);
        $html = str_replace('#debug#', $debug, $html);
        echo $html;
    }

    private static function printHtml()
    {
        $contents = ob_get_contents();
        ob_end_clean();
        echo $contents;
    }

    private static function setUrls($tpl_var)
    {
        $tpl_var['port_icon'] = 'favicon.ico';

        if (Config::$domainEnd == 'by') $tpl_var['logo_img'] = 'logo_' . Config::SITE_DOMAIN_BY . '.png';
        else $tpl_var['logo_img'] = 'logo_' . Config::SITE_DOMAIN . '.png';

        $tpl_var['home_url'] = Config::$home_url;
        $tpl_var['css_url'] = Config::$css_url;
        $tpl_var['css_ver'] = Config::$css_ver;
        $tpl_var['js_url'] = Config::$js_url;
        $tpl_var['js_ver'] = Config::$js_ver;
        $tpl_var['css_type'] = Config::$css_type;
        $tpl_var['js_type'] = Config::$js_type;

        if (Auth::getIdAuth() == -1)
            $tpl_var['fb_login_url'] = Auth::inst()->getFacebookLogin();

        return $tpl_var;
    }

    private static function setSeoVars($tpl_var)
    {
        $site_name = Utils::getSiteName();
        if (!isset($tpl_var['canonical_url'])) $tpl_var['canonical_url'] = Config::$canonical_url;
        if (!isset($tpl_var['port_seo_title'])) $tpl_var['port_seo_title'] = $site_name . ' / ' . Localizer::$loc['main_title_loc'];
        if (!isset($tpl_var['port_seo_desc'])) $tpl_var['port_seo_desc'] = $site_name . ' / ' . Localizer::$loc['main_title_loc'];
        if (!isset($tpl_var['port_seo_keys'])) $tpl_var['port_seo_keys'] = $site_name . ' / ' . Localizer::$loc['main_title_loc'];
        if (!isset($tpl_var['port_robots'])) $tpl_var['port_robots'] = 'index, follow';
        if (!isset($tpl_var['og_url'])) $tpl_var['og_url'] = '';
        if (!isset($tpl_var['og_image'])) $tpl_var['og_image'] = '';
        if (!isset($tpl_var['og_site_name'])) $tpl_var['og_site_name'] = '';
        return $tpl_var;
    }

    private static function setTplVars($tpl_var)
    {
        $tpl_var['wrong_login_pass_short_loc'] = Localizer::$loc['wrong_login_pass_short_loc'];
        $tpl_var['recomm_works_loc'] = Localizer::$loc['recomm_works_loc'];
        $tpl_var['all_works_loc'] = Localizer::$loc['all_works_loc'];
        $tpl_var['special_works_loc'] = Localizer::$loc['special_works_loc'];
        $tpl_var['popular_loc'] = Localizer::$loc['popular_loc'];
        $tpl_var['fav_auth_works_loc'] = Localizer::$loc['fav_auth_works_loc'];
        $tpl_var['comm_loc'] = Localizer::$loc['comm_loc'];
        $tpl_var['profile_title_loc'] = Localizer::$loc['profile_title_loc'];
        $tpl_var['logout_loc'] = Localizer::$loc['logout_loc'];
        $tpl_var['login_short_loc'] = Localizer::$loc['login_short_loc'];
        $tpl_var['pass_loc'] = Localizer::$loc['pass_loc'];
        $tpl_var['enter_short_loc'] = Localizer::$loc['enter_short_loc'];
        $tpl_var['site_full_ver_loc'] = Localizer::$loc['site_full_ver_loc'];
        $tpl_var['http_host'] = Config::$SiteDom . '.' . Config::$domainEnd;
        $tpl_var['id_auth_log'] = Auth::getIdAuth();
        return $tpl_var;
    }

    private static function setGoogleVars($tpl_var)
    {
        $tpl_var['page_level_goad'] = Utils::getPageLevelGoad();

        if (Config::$domainEnd == 'com')
            $google_id = Config::GOOGLE_UA_COM;
        else if (Config::$domainEnd == 'de')
            $google_id = Config::GOOGLE_UA_DE;
        else
            $google_id = Config::GOOGLE_UA_RU;
        $tpl_var['google_id'] = $google_id;

        return $tpl_var;
    }
}