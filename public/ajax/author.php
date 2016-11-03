<?php
namespace photocommunity\mobile;

require dirname(__FILE__) . '/../../classes/Init.php';
require dirname(__FILE__) . '/../../controllers/AuthorController.php';

# build Author
AuthorController::inst('author')->buildJson();