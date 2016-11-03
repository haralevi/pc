<?php
namespace photocommunity\mobile;

require dirname(__FILE__) . '/../classes/Init.php';
require dirname(__FILE__) . '/../controllers/CommController.php';

# build comments page
CommController::inst('comm')->build();