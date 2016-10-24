<?php
namespace photocommunity\mobile;

require dirname(__FILE__) . '/../classes/Init.php';
require dirname(__FILE__) . '/../controllers/CommController.php';

# build comments json
CommController::inst('comm')->buildJson();