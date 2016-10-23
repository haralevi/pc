<?php
namespace photocommunity\mobile;

require dirname(__FILE__) . '/classes/Init.php';

# init templates
$tpl_main = new Tpl();
$tpl_main->open('main');
$tpl_main_var = array();

$tpl = new Tpl();
$tpl->open('author');
$tpl_var['home_url'] = Config::$home_url;

# build Author
require dirname(__FILE__) . '/models/AuthorBuilder.php';

$author = AuthorBuilder::inst()->build(true);
if(!$author)
    die();

$tpl_var['id_auth_photo'] = $author['id_auth_photo'];
$tpl_var['author'] = $author['author'];
$tpl_var['works'] = $author['works'];

$tpl->parse($tpl_var);

if($author['id_auth_photo'] == Auth::inst()->getIdAuth())
    $page_type = 'my_profile';
else
    $page_type = '';
$tpl_main_var = Utils::setMenuStyles($tpl_main_var, $page_type);

$tpl_main_var['href_prev_page'] = $author['hrefPrev'];
$tpl_main_var['href_next_page'] = $author['hrefNext'];
$tpl_main_var['content'] = $tpl->get();

# set seo vars
$tpl_main_var['port_seo_title'] = $author['auth_name_photo'] . ' / ' . Utils::getSiteName();

# parse page
Parse::inst($tpl_main, $tpl_main_var);