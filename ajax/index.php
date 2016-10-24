<?php
namespace photocommunity\mobile;

require dirname(__FILE__) . '/../classes/Init.php';
require dirname(__FILE__) . '/../models/IndexBuilder.php';

# build Index
IndexBuilder::inst('index')->buildJson();