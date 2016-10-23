<?php
namespace photocommunity\mobile;

require dirname(__FILE__) . '/../classes/Init.php';

# build Work
require dirname(__FILE__) . '/../models/WorkBuilder.php';
$work = WorkBuilder::inst()->build(false);

# build json
$json = '{';
if ($work) {
    if (Config::getDebug()) $json .= '"debug": "#debug#", ';
    $json .= '"hrefPrev": "' . Utils::prepareJson($work['hrefPrev']) . '", ';
    $json .= '"hrefNext": "' . Utils::prepareJson($work['hrefNext']) . '", ';
    $json .= '"title": "' . Utils::prepareJson($work['ph_name'] . ' / ' . $work['auth_name_photo']) . '", ';
    $json .= '"ajaxBody": "' . Utils::prepareJson($work['work'] . '<div class="wrapContent">' . $work['comments']) . '</div>" ';
}
$json .= '}';
# /build json

# parse page
require dirname(__FILE__) . '/../classes/ParseJson.php';
ParseJson::inst($json);