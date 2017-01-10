<?php
/**
 * Created by Andre Haralevi
 * Date: 06.11.13
 * Time: 16:47
 */

namespace Photocommunity\Mobile;

class Utils
{
    public static $jsonReplaces = array(
        array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'),
        array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"')
    );

    public static function echox($str)
    {
        if (Config::getDebug())
            echo $str . '<br/>' . Consta::EOL;
    }

    public static function printArr($arr)
    {
        if (Config::getDebug()) {
            print_r('<pre>');
            print_r($arr);
            print_r('</pre>');
            echo Consta::EOL;
        }
    }

    public static function prepareJson($str)
    {
        return str_replace(Utils::$jsonReplaces[0], Utils::$jsonReplaces[1], $str);
    }

    public static function sendHeaders($content_type = 'text/html', $charset = 'utf-8')
    {
        //Sent headers
        header('Content-Type: ' . $content_type . '; charset=' . $charset);
        $exp_minutes = -10;
        $exp_gmt = gmdate('D, d M Y H:i:s', Config::$cur_time + $exp_minutes * 60) . ' GMT';
        $mod_gmt = gmdate('D, d M Y H:i:s', Config::$cur_time) . 'GMT';
        header('Expires: ' . $exp_gmt);
        header('Last-Modified: ' . $mod_gmt);
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: no-cache');
        header('X-UA-Compatible: IE=edge');
    }

    public static function getImgPath($id_photo)
    {
        if ($id_photo >= Consta::ID_PHOTO_CDN_FROM && $id_photo < Consta::ID_PHOTO_LOCAL_FROM)
            $ImagesPathFunc = Config::$http_scheme . 'cdn.' . Config::SITE_DOMAIN . '.' . Config::$domainEndImg . '/images/';
        else
            if ($id_photo % 2) $ImagesPathFunc = Config::$http_scheme . 'i1.' . Config::SITE_DOMAIN . '.' . Config::$domainEndImg . '/images/';
            else $ImagesPathFunc = Config::$http_scheme . 'ii1.' . Config::SITE_DOMAIN . '.' . Config::$domainEndImg . '/images/';
        return $ImagesPathFunc;
    }

    public static function getImgName($id_photo, $img_type = 'main', $img_ext = 'jpg')
    {
        if ($id_photo >= 10000)
            $folder_num = substr($id_photo, 0, (strlen($id_photo) - 4));
        else
            $folder_num = '';
        $ImagesName = $img_type . $folder_num . '/' . $id_photo . '_' . $img_type . '.' . $img_ext;

        return $ImagesName;
    }

    public static function myHtmlspecialchars($str)
    {
        return preg_replace("/&amp;(#[0-9]+|[a-z]+);/i", "&$1;", htmlspecialchars($str));
    }

    public static function cleanRequestSimple($str)
    {
        if (is_array($str))
            $str = implode($str);
        return Utils::myHtmlspecialchars(strip_tags(trim($str)));
    }

    public static function cleanRequest($str, $replace_chars = '', $is_tags = false)
    {
        if (is_array($str))
            $str = implode($str);
        if ($replace_chars != '') {
            $search = str_split($replace_chars);
            if (($key = array_search(' ', $search)) !== false) unset($search[$key]);
            $str = str_replace($search, '', $str);
        }
        if ($is_tags) $str = str_replace(array("'", "\\"), array("&#39;", "&#92;"), strip_tags(trim($str), '<table><tr><td><strong><em><strike><b><i><u><br><a><img><span><ol><ul><li><p><h1><h2><h3><h4><h5><h6>'));
        else if (Db::getDbConn()) $str = mysqli_real_escape_string(Db::getDbConn(), Utils::myHtmlspecialchars(strip_tags(trim($str))));
        else $str = Utils::myMysqliRealEscapeString(Utils::myHtmlspecialchars(strip_tags(trim($str))));

        return $str;
    }

    public static function correctLangUrls($str)
    {
        if (Config::$lang == 'by') {
            $str = str_replace('' . Config::SITE_DOMAIN . '.ru', Config::SITE_DOMAIN_BY, $str);
            $str = str_replace('' . Config::SITE_DOMAIN . '.de', Config::SITE_DOMAIN_BY, $str);
            $str = str_replace('' . Config::SITE_DOMAIN . '.com', Config::SITE_DOMAIN_BY, $str);
        } else if (Config::$lang == 'ru') {
            $str = str_replace(Config::SITE_DOMAIN_BY, '' . Config::SITE_DOMAIN . '.ru', $str);
            $str = str_replace('' . Config::SITE_DOMAIN . '.de', '' . Config::SITE_DOMAIN . '.ru', $str);
            $str = str_replace('' . Config::SITE_DOMAIN . '.com', '' . Config::SITE_DOMAIN . '.ru', $str);
        } else if (Config::$lang == 'de') {
            $str = str_replace(Config::SITE_DOMAIN_BY, '' . Config::SITE_DOMAIN . '.de', $str);
            $str = str_replace('' . Config::SITE_DOMAIN . '.ru', '' . Config::SITE_DOMAIN . '.de', $str);
            $str = str_replace('' . Config::SITE_DOMAIN . '.com', '' . Config::SITE_DOMAIN . '.de', $str);
        } else if (Config::$lang == 'en') {
            $str = str_replace(Config::SITE_DOMAIN_BY, '' . Config::SITE_DOMAIN . '.com', $str);
            $str = str_replace('' . Config::SITE_DOMAIN . '.ru', '' . Config::SITE_DOMAIN . '.com', $str);
            $str = str_replace('' . Config::SITE_DOMAIN . '.de', '' . Config::SITE_DOMAIN . '.com', $str);
        }
        return $str;
    }

    public static function getSiteName()
    {
        if (Config::$domainEnd == 'by') $site_name = Config::SITE_DOMAIN_BY;
        else $site_name = Config::SITE_DOMAIN;
        return $site_name;
    }

    public static function sendMail($from_email, $to_email, $subject, $content)
    {
        $charset = 'UTF-8';
        $headers = 'MIME-Version: 1.0
Content-Type: text/html; charset=' . strtolower($charset) . '
From: ' . $from_email . '
';
        $subject = '=?' . $charset . '?B?' . base64_encode(str_replace(array('&quot;', '&lt;', '&gt;', '&amp;'), array('"', '<', '>', '&'), $subject)) . '?=';
        $content = nl2br($content);
        mail($to_email, $subject, $content, $headers);
        #mail('tessstttt@mail.ru', $subject, $to_email.'<br/><br/>'.$content, $headers);
    }

    public static function isValidEmail($email)
    {
        if (!preg_match('/^ *([a-z0-9_-]+\.)*[a-z0-9_-]+@(([a-z0-9-]+\.)+(com|net|org|mil|edu|gov|arpa|info|biz|inc|name|[a-z]{2})|[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}) *$/', $email)) return false;
        else return true;
    }

    public static function startsWith($str, $sub)
    {
        return strpos($str, $sub) === 0;
    }

    public static function endsWith($str, $sub)
    {
        $str = strtolower($str);
        return (substr($str, strlen($str) - strlen($sub)) == $sub);
    }

    public static function parseLinks($text, $class = '')
    {
        $tag_list = '\[b|\[i|\[u|\[left|\[center|\[right|\[indent|\[quote|\[highlight|\[\*|\[/b|\[/i|\[/u|\[/left|\[/center|\[/right|\[/indent|\[/quote|\[/highlight';
        $urlSearchArray = array("#(^|(?<=[^_a-z0-9-=\]\"'/@]|(?<=" . $tag_list . ")\]))((https?|ftp|gopher|news|telnet)://|www\.)((\[(?!/)|[^\s[^$!`\"'|{}<>])+)(?!\[/url|\[/img)(?=[,.]*(\)\s|\)$|[\s[]|$))#siU");
        if ($class != '') $urlReplaceArray = array('<a rel="nofollow" href="\\2\\4" target="_blank" class="' . $class . '">\\2\\4</a>');
        else $urlReplaceArray = array('<a rel="nofollow" href="\\2\\4" target="_blank">' . '\\2\\4' . '</a>');
        return preg_replace($urlSearchArray, $urlReplaceArray, $text);
    }

    public static function bbUrlParse($str)
    {
        while (preg_match_all('/\[(.+?)=?(.*?)\](.+?)\[\/\1\]/', $str, $matches)) {
            #utils::printArr($matches);
            foreach ($matches[0] as $key => $match) {
                list($tag, $param, $inner_text) = array($matches[1][$key], $matches[2][$key], $matches[3][$key]);
                $inner_text = trim($inner_text);
                switch ($tag) {
                    case 'url':
                        $param = str_replace('"', '', $param);
                        $replacement = '<a href="' . ($param ? $param : $inner_text) . "\" target=\"_blank\">$inner_text</a>";
                        break;
                    default:
                        $replacement = $inner_text;
                }
                $str = str_replace($match, $replacement, $str);
            }
        }
        return $str;
    }

    public static function bbImgParse($str)
    {
        preg_match_all('/\[img\](.+?)\[\/img\]/m', $str, $matches);
        foreach ($matches[1] as $k => $v) {
            if (strpos($v, 'http://') === 0) $str = str_replace($matches[0][$k], '<img src="' . $v . '" alt="" />', $str);
            else $str = str_replace($matches[0][$k], $v, $str);
        }
        return $str;
    }

    public static function bbParse($str)
    {
        if (preg_match('/^\s{1,}$/i', str_replace(array('[b]', '[/b]', '[i]', '[/i]', '[u]', '[/u]', '[s]', '[/s]', '[quote]', '[/quote]'), '', $str))) return '...';
        $str = str_replace(Consta::EOL, ':n:', $str);
        while (preg_match_all('/\[(.*)\](.+?)\[\/\1\]/', $str, $matches)) {
            foreach ($matches[0] as $key => $match) {
                list($tag, $inner_text) = array($matches[1][$key], $matches[2][$key]);
                $inner_text = trim($inner_text);
                switch ($tag) {
                    case 'b':
                        $replacement = '<strong>' . $inner_text . '</strong>';
                        break;
                    case 'i':
                        $replacement = '<em>' . $inner_text . '</em>';
                        break;
                    case 'u':
                        $replacement = '<span style="text-decoration: underline;">' . $inner_text . '</span>';
                        break;
                    case 's':
                        $replacement = '<del>' . $inner_text . '</del>';
                        break;
                    case 'quote':
                        $replacement = '<div class="commQuote">' . $inner_text . '</div>';
                        break;
                    case 'img':
                        if (strpos($inner_text, 'http://') === 0 && (Utils::endsWith($inner_text, '.jpg') || Utils::endsWith($inner_text, '.gif') || Utils::endsWith($inner_text, '.png'))) $replacement = '<img src="' . $inner_text . '" alt="" />';
                        else $replacement = $inner_text;
                        break;
                    case 'youtube':
                        if (strpos($inner_text, 'https://www.youtube.com/watch?v=') === 0 || strpos($inner_text, 'http://www.youtube.com/watch?v=') === 0) {
                            $video_url = parse_url($inner_text);
                            parse_str($video_url['query'], $video_query);
                            if (strpos($video_url['host'], 'youtube.com') !== false)
                                $replacement = '<iframe style="max-width:100%;" width="560" height="315" src="http://www.youtube.com/embed/' . $video_query['v'] . '" frameborder="0" allowfullscreen></iframe>';
                            else $replacement = $inner_text;
                        } else $replacement = $inner_text;
                        break;
                    case 'vimeo':
                        if (strpos($inner_text, 'https://vimeo.com/') === 0 || strpos($inner_text, 'http://vimeo.com/') === 0) {
                            preg_match('/https?:\/\/vimeo.com\/(\d+)/', $inner_text, $m);
                            if (sizeof($m) == 2)
                                $replacement = '<iframe style="max-width:100%;" width="500" height="281" src="http://player.vimeo.com/video/' . $m[1] . '?title=0&byline=0&portrait=0" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
                            else $replacement = $inner_text;
                        } else $replacement = $inner_text;
                        break;
                    default:
                        $replacement = $inner_text;
                }
                $str = str_replace($match, $replacement, $str);
            }
        }
        $str = str_replace(':n:', Consta::EOL, $str);
        return $str;
    }

    public static function bbClear($str)
    {
        $str = str_replace(Consta::EOL, '', $str);
        while (preg_match_all('/\[(.*)\](.+?)\[\/\1\]/', $str, $matches)) {
            foreach ($matches[0] as $key => $match) {
                list($tag, $inner_text) = array($matches[1][$key], $matches[2][$key]);
                $inner_text = trim($inner_text);
                switch ($tag) {
                    case 'b':
                        $replacement = '';
                        break;
                    case 'quote':
                        $replacement = '';
                        break;
                    default:
                        $replacement = $inner_text;
                }
                $str = str_replace($match, $replacement, $str);
            }
        }
        $str = str_replace(Consta::$icons_match, '', $str);
        return $str;
    }

    public static function parseComm($comm_text, $is_crop = true, $is_url_img = true, $is_quote = true)
    {
        if (!$is_url_img) {
            $comm_text = preg_replace(array('/\[url=[^\]]+\]/'), '', $comm_text);
            $comm_text = str_replace(array('[/url]', '[img]', '[/img]', '[youtube]', '[/youtube]', '[vimeo]', '[/vimeo]', '[mp3]', '[/mp3]'), '', $comm_text);
            $comm_text = preg_replace("/ {2,}/", ' ', $comm_text);
        }
        if (!$is_quote) {
            $comm_text = preg_replace('/\[quote\].+\[\/quote\]/isxmU', '', $comm_text);
            $comm_text = preg_replace("/(\r?\n){2,}/", Consta::EOL, $comm_text);
        }

        $comm_text = Utils::parseLinks($comm_text);
        $comm_text = Utils::bbParse($comm_text);
        $comm_text = Utils::bbUrlParse($comm_text);
        $comm_text = Utils::correctLangUrls($comm_text);

        if ($is_crop)
            $replacement = '<img class="cropIcon cropClick" data-crop-coordinates="$1;$2;$3;$4" alt="" src="' . Config::$home_url . 'img/tool_crop2.gif" border="0" width="16" height="16" />';
        else
            $replacement = '<img class="cropIcon" alt="" src="' . Config::$home_url . 'img/tool_crop2.gif" border="0" width="16" height="16" />';
        $comm_text = preg_replace(Consta::$crop_pattern, $replacement, $comm_text);

        $comm_text = ltrim(str_replace(Consta::$icons_match, Consta::$icons_replace, ' ' . $comm_text));
        $comm_text = str_replace('№', '#', $comm_text);
        $comm_text = nl2br($comm_text);
        $comm_text = str_replace('</div><br />', '</div>', $comm_text);
        return $comm_text;
    }

    public static function addParam($url, $key, $value, $hash = false)
    {
        $url = str_replace('&amp;', '&', $url);
        $url = preg_replace('/(.*)(\?|&)' . $key . '=[^&]+?(&)(.*)/i', '$1$2$4', $url . '&');
        $url = substr($url, 0, -1);
        if (!$hash) {
            if (strpos($url, '?') === false) return $url . '?' . $key . '=' . $value;
            else return $url . '&' . $key . '=' . $value;
        } else return $url . '&' . $key . '=' . $value;
    }

    public static function removeParam($url, $key)
    {
        $url = str_replace('&amp;', '&', $url);
        $url = preg_replace('/(.*)(\?|&)' . $key . '=[^&]+?(&)(.*)/i', '$1$2$4', $url . '&');
        $url = substr($url, 0, -1);
        return $url;
    }

    public static function hideRussian($str, $hider = '-')
    {
        if (Config::$lang != 'ru' && Config::$lang != 'by' && preg_match('/[\p{Cyrillic}]+/u', $str)) $str = $hider;
        return $str;
    }

    public static function logVisits()
    {
        #if (!Geo::$is_robot && !strstr(Config::$request_uri, 'get_views.php') && !in_array(Auth::getIdAuth(), array(1, 24, 26))) {
        if (!strstr(Config::$request_uri, 'get_views.php') && !in_array(Auth::getIdAuth(), array(1, 24, 26))) {
            $log = date("d.m.Y H:i:s", Config::$cur_time + 3600) . "\t| ";
            if (Config::$remote_addr) $log .= Config::$remote_addr;
            if (strlen(Config::$remote_addr) < 14) $log .= "\t";
            $log .= "\t| ";
            $log .= 'ID_AUTH: ' . Auth::getIdAuth();
            if (strlen(Auth::getIdAuth()) < 5) $log .= "\t";
            $log .= "\t| ";
            $log .= 'http://' . Config::$http_host . Config::$request_uri;
            #if (Config::$http_user_agent) $log .=  "\t| " .Config::$http_user_agent;
            $fp = fopen(dirname(__FILE__) . Config::$visitsLogFile, 'a');
            fwrite($fp, $log . "\n");
            fclose($fp);
        }
    }

    public static function parseAvatar($id_auth, $auth_avatar, $auth_gender, $avatar_type = 'big')
    {
        if ($avatar_type == 'small') $avatar_fld = 'avatars';
        else if ($avatar_type == 'square') $avatar_fld = 'avatars_square';
        else $avatar_fld = 'authors';

        if ($auth_avatar)
            $auth_avatar_src = Config::$ImgPath . $avatar_fld . '/' . $id_auth . '_author.jpg';
        else if ($auth_gender == 0)
            $auth_avatar_src = Config::$css_url . Config::$theme . '/male.png';
        else
            $auth_avatar_src = Config::$css_url . Config::$theme . '/female.png';
        $auth_avatar_src = str_replace('/' . Config::SITE_ROOT, '/', $auth_avatar_src);
        return $auth_avatar_src;
    }

    public static function parseWorkImg($id_photo, $id_auth_photo, $id_cat_new, $ph_main_w, $ph_main_h, $is_id = false, $is_thumb = false)
    {
        $dataIsAllowedNude = '';
        if (($id_cat_new != Consta::NUDE_CAT) || ($id_cat_new == Consta::NUDE_CAT && Auth::getAuthNuGall() && Utils::isAllowedNude())) {
            $srcAttr = Utils::getImgPath($id_photo);
            if ($is_thumb)
                $srcAttr .= Utils::getImgName($id_photo, 'thumb');
            else
                $srcAttr .= Utils::getImgName($id_photo, 'mobile');
            $classAttr = '';
            $styleAttr = '';
        } else {
            $srcAttr = Config::$ImgPath . '1.gif';
            if ($is_thumb) {
                $styleH = 50;
                $classAttr = 'nudePreviewThumb';
            } else {
                $styleH = Consta::MOBILE_MAX_WIDTH;
                $classAttr = 'nudePreview';
            }
            $styleAttr = 'width: ' . Consta::MOBILE_MAX_WIDTH . 'px; height: ' . $styleH . 'px; ';
            if (!Utils::isAllowedNude())
                $dataIsAllowedNude = ' data-is-allowed-nude="false"';
        }
        $ph_path = str_replace($id_photo . '_mobile.jpg', '', Utils::getImgPath($id_photo) . Utils::getImgName($id_photo, 'mobile'));

        if ($is_id) $idAttr = 'id="mainImage"';
        else $idAttr = '';
        $altAttr = '';

        $work_img = '<img ' . $idAttr . ' src="' . $srcAttr . '" class="' . $classAttr . '" ' . $dataIsAllowedNude . ' data-id-auth-photo="' . $id_auth_photo . '" data-id-photo="' . $id_photo . '" data-ph-main-w="' . $ph_main_w . '" data-ph-main-h="' . $ph_main_h . '" data-ph-path="' . $ph_path . '" style="' . $styleAttr . '" alt="' . $altAttr . '" itemprop="contentUrl">';
        $work_img = str_replace(array(' class=""', ' style=""'), '', $work_img);
        $work_img .= '<img id="mobileImage" src="' . Config::$ImgPath . '1.gif" style="display :none; width: 0; height 0;">';
        return str_replace('  ', ' ', $work_img);
    }

    public static function isAllowedNude()
    {
        if (!Auth::getAuthPremium() && Config::$domainEnd != 'ru' && Config::$domainEnd != 'by')
            return false;
        else
            return true;
    }

    public static function isActiveComp($id_comp)
    {
        $is_active_comp = false;
        foreach (Consta::$active_comp as $cv)
            if ($id_comp == $cv) {
                $is_active_comp = true;
                break;
            }
        return $is_active_comp;
    }

    public static function isAnon($ph_anon, $ph_date, $ph_id_comp)
    {
        $is_active_comp = Utils::isActiveComp($ph_id_comp);
        if (($is_active_comp && $ph_id_comp != Consta::ID_COMP_PF) || ($ph_anon == 1 && $ph_date > (Config::$cur_time - Consta::ANON_OFFSET)))
            return true;
        else
            return false;
    }

    public static function getPremiumBadge($auth_premium, $position = '')
    {
        $style_arr = array();
        if ($position == 'static')
            $style_arr['position'] = $position;
        $style = Utils::buildStyle($style_arr);

        $pricingHref = Config::$http_scheme . Config::$SiteDom . '.' . Config::$domainEnd . '/pricing.php';
        if ($auth_premium == Consta::AUTH_PREMIUM_1)
            $auth_badge = '<a href="' . $pricingHref . '" title="' . Consta::AUTH_PREMIUM_NAME_1 . ' Account"><img class="authBadge" ' . $style . ' src="' . Config::$css_url . Config::$theme . '/plus.gif" alt="' . Consta::AUTH_PREMIUM_NAME_1 . ' Account" /></a>';
        else if ($auth_premium == Consta::AUTH_PREMIUM_2)
            $auth_badge = '<a href="' . $pricingHref . '" title="' . Consta::AUTH_PREMIUM_NAME_2 . ' Account"><img class="authBadge" ' . $style . ' src="' . Config::$css_url . Config::$theme . '/premium.gif" alt="' . Consta::AUTH_PREMIUM_NAME_2 . ' Account" /></a>';
        else if ($auth_premium == Consta::AUTH_PREMIUM_3)
            $auth_badge = '<a href="' . $pricingHref . '" title="' . Consta::AUTH_PREMIUM_NAME_3 . ' Account"><img class="authBadge" ' . $style . ' src="' . Config::$css_url . Config::$theme . '/pro.gif" alt="' . Consta::AUTH_PREMIUM_NAME_3 . ' Account" /></a>';
        else if ($auth_premium == Consta::AUTH_PREMIUM_4)
            $auth_badge = '<a href="' . $pricingHref . '" title="' . Consta::AUTH_PREMIUM_NAME_4 . ' Account"><img class="authBadge" ' . $style . ' src="' . Config::$css_url . Config::$theme . '/pro.gif" alt="' . Consta::AUTH_PREMIUM_NAME_4 . ' Account" /></a>';
        else
            $auth_badge = '';
        return $auth_badge;
    }

    public static function getAuthPremiumName($auth_premium, $is_for_ga = false)
    {
        if ($auth_premium == Consta::AUTH_PREMIUM_1)
            $auth_premium_name = Consta::AUTH_PREMIUM_NAME_1;
        else if ($auth_premium == Consta::AUTH_PREMIUM_2)
            $auth_premium_name = Consta::AUTH_PREMIUM_NAME_2;
        else if ($auth_premium == Consta::AUTH_PREMIUM_3)
            $auth_premium_name = Consta::AUTH_PREMIUM_NAME_3;
        else if ($auth_premium == Consta::AUTH_PREMIUM_4) {
            $auth_premium_name = Consta::AUTH_PREMIUM_NAME_4;
            if ($is_for_ga) $auth_premium_name .= '+';
        } else {
            if (!$is_for_ga) $auth_premium_name = Consta::AUTH_PREMIUM_NAME_0;
            else $auth_premium_name = 'Guest';
        }
        return $auth_premium_name;
    }

    public static function getRecPerDay($auth_premium)
    {
        if ($auth_premium == Consta::AUTH_PREMIUM_4)
            $rec_per_day = Consta::RECS_PER_DAY_4;
        else if ($auth_premium == Consta::AUTH_PREMIUM_3)
            $rec_per_day = Consta::RECS_PER_DAY_3;
        else if ($auth_premium == Consta::AUTH_PREMIUM_2)
            $rec_per_day = Consta::RECS_PER_DAY_2;
        else if ($auth_premium == Consta::AUTH_PREMIUM_1)
            $rec_per_day = Consta::RECS_PER_DAY_1;
        else if ($auth_premium == Consta::AUTH_PREMIUM_0)
            $rec_per_day = Consta::RECS_PER_DAY_0;
        else
            $rec_per_day = Consta::RECS_PER_DAY_0;

        return $rec_per_day;
    }

    public static function getWorkGallLimit($auth_premium)
    {
        if (Config::$domainEnd == 'ru' || Config::$domainEnd == 'by') {
            $work_gall_limit = Consta::WORK_GALL_LIMIT_4;
        } else {
            if ($auth_premium == Consta::AUTH_PREMIUM_1)
                $work_gall_limit = Consta::WORK_GALL_LIMIT_1;
            else if ($auth_premium == Consta::AUTH_PREMIUM_2)
                $work_gall_limit = Consta::WORK_GALL_LIMIT_2;
            else if ($auth_premium == Consta::AUTH_PREMIUM_3)
                $work_gall_limit = Consta::WORK_GALL_LIMIT_3;
            else if ($auth_premium == Consta::AUTH_PREMIUM_4)
                $work_gall_limit = Consta::WORK_GALL_LIMIT_4;
            else
                $work_gall_limit = Consta::WORK_GALL_LIMIT_0;
        }
        return $work_gall_limit;
    }

    public static function getAuthFollowLimit($auth_premium)
    {
        if ($auth_premium == Consta::AUTH_PREMIUM_1)
            $auth_follow_limit = Consta::AUTH_FAVOR_PREMIUM_1;
        else if ($auth_premium == Consta::AUTH_PREMIUM_2)
            $auth_follow_limit = Consta::AUTH_FAVOR_PREMIUM_2;
        else if ($auth_premium == Consta::AUTH_PREMIUM_3)
            $auth_follow_limit = Consta::AUTH_FAVOR_PREMIUM_3;
        else if ($auth_premium == Consta::AUTH_PREMIUM_4)
            $auth_follow_limit = Consta::AUTH_FAVOR_PREMIUM_4;
        else
            $auth_follow_limit = Consta::AUTH_FAVOR_PREMIUM_0;
        return $auth_follow_limit;
    }

    public static function setMenuStyles($tpl_var, $page_type = '')
    {
        if ($page_type == '') $tpl_var['recomm_works_act_class'] = ' actMenuItem';
        else $tpl_var['recomm_works_act_class'] = '';
        if ($page_type == 'all') $tpl_var['all_works_act_class'] = ' actMenuItem';
        else $tpl_var['all_works_act_class'] = '';
        if ($page_type == 'special') $tpl_var['special_works_act_class'] = ' actMenuItem';
        else $tpl_var['special_works_act_class'] = '';
        if ($page_type == 'popular') $tpl_var['popular_act_class'] = ' actMenuItem';
        else $tpl_var['popular_act_class'] = '';
        if ($page_type == 'favorites') $tpl_var['fav_auth_works_act_class'] = ' actMenuItem';
        else $tpl_var['fav_auth_works_act_class'] = '';
        if ($page_type == 'comm') $tpl_var['comm_act_class'] = ' actMenuItem';
        else $tpl_var['comm_act_class'] = '';
        if ($page_type == 'my_profile') $tpl_var['my_profile_act_class'] = ' actMenuItem';
        else $tpl_var['my_profile_act_class'] = '';
        return $tpl_var;
    }

    public static function errorWriter($error)
    {
        $fp = fopen(dirname(__FILE__) . Config::$errorLogFile, 'a');
        fwrite($fp, $error . "\n");
        fclose($fp);
    }

    public static function myMysqliRealEscapeString($str)
    {
        return str_replace(
            array('\\', "\x00", "\0", "\n", "\r", "'", '"', "\x1a"),
            array('\\\\', "\\0", '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'),
            $str);
    }

    public static function getTpl($tpl_name, $tpl_var = array())
    {
        $tpl = new Tpl();
        $tpl->open($tpl_name);
        return Utils::parseTpl($tpl->get(), $tpl_var);
    }

    public static function parseTpl($content, $tpl_var)
    {
        foreach ($tpl_var as $k => $v)
            $content = str_replace('{' . $k . '}', $v, $content);
        return $content;
    }

    public static function get_headers_curl($url, $timeout = 1)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

        $r = curl_exec($ch);
        $r = explode("\n", $r);
        return $r;
    }

    public static function isEmptyName($ph_name)
    {
        return preg_match('/^[\.\*_\-\+\\/]+$/', $ph_name);
    }

    public static function setEmptyName($ph_name)
    {
        if (Utils::isEmptyName($ph_name))
            $ph_name = Localizer::$loc['without_name'];
        return $ph_name;
    }

    public static function buildStyle($style_arr)
    {
        $style = '';
        if (sizeof($style_arr)) {
            $style .= ' style="';
            foreach ($style_arr as $k => $v)
                $style .= $k . ': ' . $v . '; ';
            $style .= '"';
        }
        return $style;
    }

    public static function getChangeLangUrl($domainEnd, $isLogin = false)
    {
        $uri = Config::$request_uri;
        if ($isLogin) {
            $uri = Utils::addParam($uri, 'wrn_login', 1);
            $uri = Utils::addParam($uri, 'chla', 1);
        }
        $uri = str_replace('&amp;', '&', $uri);
        return Config::$http_scheme . Config::SITE_SUBDOMAIN . Config::SITE_DOMAIN . '.' . $domainEnd . $uri;
    }

    public static function getGoad()
    {
        $goad = '';
        if (Auth::getAuthPremium() == Consta::AUTH_PREMIUM_0)
            $goad = '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script><ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-6341745028943688" data-ad-slot="6506134659" data-ad-format="auto"></ins><script>(adsbygoogle = window.adsbygoogle || []).push({});</script>';
        return $goad;
    }
}