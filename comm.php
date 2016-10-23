<?php
namespace photocommunity\mobile;

require dirname(__FILE__) . '/classes/Init.php';

# init templates
$tpl_main = new Tpl();
$tpl_main->open('main');
$tpl_main_var = array();

$tpl = new Tpl();
$tpl->open('comm');
$tpl_var['home_url'] = Config::$home_url;

# build Author
require dirname(__FILE__) . '/models/CommBuilder.php';
$comm = CommBuilder::inst()->build(true);
$tpl_var['comm'] = $comm['comm'];

$tpl->parse($tpl_var);

$tpl_main_var = Utils::setMenuStyles($tpl_main_var, 'comm');

$tpl_main_var['href_prev_page'] = $comm['hrefPrev'];
$tpl_main_var['href_next_page'] = $comm['hrefNext'];
$tpl_main_var['content'] = $tpl->get();

# set seo vars
$tpl_main_var['port_seo_title'] = Localizer::$loc['comm_loc'] . ' / ' . Utils::getSiteName();

# parse page
Parse::inst($tpl_main, $tpl_main_var);