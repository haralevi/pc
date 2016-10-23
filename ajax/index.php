<?php
namespace photocommunity\mobile;

require dirname(__FILE__) . '/../classes/Init.php';

# build Index
require dirname(__FILE__) . '/../models/IndexBuilder.php';
$index = IndexBuilder::inst()->build(true);

# build json
$json = '{';
if($index) {
    if (Config::getDebug()) $json .= '"debug": "#debug#", ';
    $json .= '"hrefPrev": "' . Utils::prepareJson($index['hrefPrev']) . '", ';
    $json .= '"hrefNext": "' . Utils::prepareJson($index['hrefNext']) . '", ';
    $json .= '"ajaxBody": "' . Utils::prepareJson($index['works']) . '" ';
}
$json .= '}';
# /build json

# parse page
require dirname(__FILE__) . '/../classes/ParseJson.php';
ParseJson::inst($json);