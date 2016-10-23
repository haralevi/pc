<?php
namespace photocommunity\mobile;

require dirname(__FILE__) . '/../classes/Init.php';

# build Index
require dirname(__FILE__) . '/../models/CommBuilder.php';
$comm = CommBuilder::inst()->build(true);

# build json
$json = '{';
if($comm) {
    if (Config::getDebug()) $json .= '"debug": "#debug#", ';
    $json .= '"hrefPrev": "' . Utils::prepareJson($comm['hrefPrev']) . '", ';
    $json .= '"hrefNext": "' . Utils::prepareJson($comm['hrefNext']) . '", ';
    $json .= '"ajaxBody": "' . Utils::prepareJson($comm['comm']) . '" ';
}
$json .= '}';
# /build json

# parse page
require dirname(__FILE__) . '/../classes/ParseJson.php';
ParseJson::inst($json);