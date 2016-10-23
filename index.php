<?php
namespace photocommunity\mobile;

require dirname(__FILE__) . '/classes/Init.php';

# init templates
$tpl_main = new Tpl();
$tpl_main->open('main');
$tpl_main_var = array();

$tpl = new Tpl();
$tpl->open('index');
$tpl_var['home_url'] = Config::$home_url;

# build Index
require dirname(__FILE__) . '/models/IndexBuilder.php';

$index = IndexBuilder::inst()->build(true);
if(!$index)
    die();

if($index['title'])
    $tpl_var['title'] = $index['title'];
else
    $tpl->clear('TITLE_BLK');

$tpl_var['works'] = $index['works'];

if($index['page_type'])
    $tpl_var['page_type_param'] = $index['page_type'] . '=1';
else
    $tpl_var['page_type_param'] = '';

$tpl->parse($tpl_var);

$tpl_main_var = Utils::setMenuStyles($tpl_main_var, $index['page_type']);

$tpl_main_var['href_prev_page'] = $index['hrefPrev'];
$tpl_main_var['href_next_page'] = $index['hrefNext'];
$tpl_main_var['content'] = $tpl->get();

# set seo vars
if($index['port_seo_title']) {
    $tpl_main_var['port_seo_title'] = $index['title'] . ' / ' . Utils::getSiteName();
}

# parse page
Parse::inst($tpl_main, $tpl_main_var);