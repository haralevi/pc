<!DOCTYPE html>
<html>
<head>
    <title>{port_seo_title}</title>
    <link rel="shortcut icon" href="{home_url}{port_icon}"/>
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="//{http_host}/img/apple-touch-icon-144x144-precomposed.png" />
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="//{http_host}/img/apple-touch-icon-114x114-precomposed.png" />
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="//{http_host}/img/apple-touch-icon-72x72-precomposed.png" />
    <link rel="apple-touch-icon-precomposed" href="//{http_host}/img/apple-touch-icon-57x57-precomposed.png" />
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0"/>
    <meta name="description" content="{port_seo_desc}"/>
    <meta name="keywords" content="{port_seo_keys}"/>
    <meta name="robots" content="{port_robots}"/>
    <meta property="og:title" content="{port_seo_title}"/>
    <meta property="og:type" content="website"/>
    <meta property="og:url" content="{og_url}"/>
    <meta property="og:image" content="{og_image}"/>
    <meta property="og:site_name" content="{og_site_name}"/>
    <meta property="og:description" content="{port_seo_desc}"/>
    <link rel="canonical" href="{canonical_url}">
    <meta name="theme-color" content="#333">
    <link href="{css_url}css.{css_type}css?v={css_ver}" rel="stylesheet" type="text/css">
    <link href="//fonts.googleapis.com/css?family=Open+Sans&subset=latin,cyrillic" rel="stylesheet" type="text/css">
    <link href='//fonts.googleapis.com/css?family=Open+Sans+Condensed:700,300&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
    {page_level_goad}
</head>
<body>
<script type="text/javascript">
    {js_redirect_url}
</script>
<div class="header">
    <div class="wrapContent clearfix">
        <div class="logo">
            <a href="{home_url}"><img src="{css_url}{logo_img}" alt=""/></a>
        </div>
        <div class="menuItem" id="menuLnk" style="border-right: 1px solid #111;">
            <span class="icon_bar" style="margin-top: 13px;"></span>
            <span class="icon_bar"></span>
            <span class="icon_bar"></span>
        </div>
        <div class="prevNext">
            <a rel="next" id="nextLnkKey" href="{href_next_page}" class="nextLnk menuItem">&nbsp;</a>
            <a rel="prev" id="prevLnkKey" href="{href_prev_page}" class="prevLnk menuItem">&nbsp;</a>
        </div>
    </div>
</div>

<!--[WRONG_LOGIN_BLK]-->
<div class="wrapContent clearfix">
    <div id="resultMsg" class="msg errMsg" style="display: block;">{wrong_login_pass_short_loc}</div>
</div>
<!--[WRONG_LOGIN_BLK]-->

<div class="wrapContent clearfix hidden" id="submenu" data-id-auth="{id_auth}" data-auth-name="{auth_name}" data-auth-avatar="{auth_avatar}" data-auth-url="{auth_url}">
    <a href="{home_url}" class="submenuItem{recomm_works_act_class}">{recomm_works_loc}</a>
    <a href="{home_url}?popular=1" class="submenuItem{popular_act_class}">{popular_loc}</a>
    <a href="{home_url}?all=1" class="submenuItem{all_works_act_class}">{all_works_loc}</a>
    <a href="{home_url}?special=1" class="submenuItem{special_works_act_class}">{special_works_loc}</a>

    <!--[UNLOGGED_BLK]-->
    <a href="{home_url}?favorites=1" class="submenuItem{fav_auth_works_act_class}">{fav_auth_works_loc}</a>
    <!--[UNLOGGED_BLK]-->
    <a href="{home_url}comm.php" class="submenuItem{comm_act_class}">{comm_loc}</a>
    <!--[UNLOGGED_BLK]-->
    <a href="{home_url}author.php?id_auth={id_auth}" class="submenuItem{my_profile_act_class}">{profile_title_loc}</a>
    <a href="{home_url}logout.php" class="submenuItem">{logout_loc}</a>
    <!--[UNLOGGED_BLK]-->
    <!--[LOGGED_BLK]-->
    <div class="submenuItem">
        <input type="text" name="auth_login" id="auth_login" class="loginFld" placeholder="{login_short_loc}" value="">
        <input type="password" name="auth_pass" id="auth_pass" class="loginFld" placeholder="{pass_loc}" value="">
        <a id="loginBtn" href="#" class="saveBtn">{enter_short_loc}</a>
        <a id="facebookBtn" href="{fb_login_url}" class="saveBtn">Facebook</a>
    </div>
    <!--[LOGGED_BLK]-->
</div>

<div id="ajaxBody">
    {content}
</div>

<div class="footer">
    <div class="wrapContent clearfix">
        <div style="float: left; width: 190px;">
            <a id="canonicalUrl" href="{canonical_url}" class="footerLnk">{site_full_ver_loc}</a>
        </div>
        <div class="prevNext">
            <a rel="next" href="{href_next_page}" class="nextLnk menuItem" style="border-right: 1px solid #111;">&nbsp;</a>
            <a rel="prev" href="{href_prev_page}" class="prevLnk menuItem">&nbsp;</a>
        </div>
    </div>
    #debug#
</div>

<script type="text/javascript">
    var id_auth_log = "{id_auth_log}";
</script>
<script type="text/javascript" src="{js_url}plugins.min.js?v={js_ver}"></script>
<script type="text/javascript" src="{js_url}js.{js_type}js?v={js_ver}"></script>
<!--[GO_BLK]-->
<script type="text/javascript">
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
ga("create", "{google_id}", "auto");
ga("set", "dimension1", "Mobile Version");
ga("set", "dimension2", "{auth_premium_name_ga}");
ga("set", "dimension3", "{http_host}");
ga('send', 'pageview');
</script>
<!--[GO_BLK]-->
</body>
</html>