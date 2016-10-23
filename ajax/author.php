<?php
namespace photocommunity\mobile;

require dirname(__FILE__) . '/../classes/Init.php';

# build Author
require dirname(__FILE__) . '/../models/AuthorBuilder.php';
$author = AuthorBuilder::inst()->build(false);

# build json
$json = '{';
if($author) {
    if (Config::getDebug()) $json .= '"debug": "#debug#", ';
    $json .= '"hrefPrev": "' . Utils::prepareJson($author['hrefPrev']) . '", ';
    $json .= '"hrefNext": "' . Utils::prepareJson($author['hrefNext']) . '", ';
    $json .= '"ajaxBody": "' . Utils::prepareJson($author['works']) . '" ';
}
$json .= '}';
# /build json

# parse page
require dirname(__FILE__) . '/../classes/ParseJson.php';
ParseJson::inst($json);