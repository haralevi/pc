<?php
namespace photocommunity\mobile;

require dirname(__FILE__) . '/../../classes/Init.php';
require dirname(__FILE__) . '/../../controllers/WorkController.php';

# build Work
WorkController::inst('work')->buildJson();