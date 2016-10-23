<?php
namespace photocommunity\mobile;

require dirname(__FILE__) . '/classes/Init.php';

# init templates
$tpl_main = new Tpl();
$tpl_main->open('main');
$tpl_main_var = array();

$tpl = new Tpl();
$tpl->open('work');
$tpl_var['home_url'] = Config::$home_url;

# build Work
require dirname(__FILE__) . '/models/WorkBuilder.php';

$work = WorkBuilder::inst()->build(true);
if(!$work)
    die();

$tpl_var['work'] = $work['work'];
$tpl_var['comments'] = $work['comments'];

$tpl->parse($tpl_var);

$tpl_main_var = Utils::setMenuStyles($tpl_main_var);

$tpl_main_var['href_prev_page'] = $work['hrefPrev'];
$tpl_main_var['href_next_page'] = $work['hrefNext'];
$tpl_main_var['content'] = $tpl->get();

# set seo vars
$tpl_main_var['port_seo_title'] =  $work['ph_name'] . ' / ' . $work['auth_name_photo'] . ' / ' . Utils::getSiteName();

# parse page
Parse::inst($tpl_main, $tpl_main_var);