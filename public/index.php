<?php
namespace photocommunity\mobile;

require dirname(__FILE__) . '/../classes/Init.php';
require dirname(__FILE__) . '/../controllers/IndexController.php';

# build Index
IndexController::inst('index')->build();