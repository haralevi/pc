<?php
/**
 * Created by Andre Haralevi
 * Date: 06.11.13
 * Time: 5:21 AM
 */

namespace Photocommunity\Mobile;

require dirname(__FILE__) . '/../classes/Init.php';
require dirname(__FILE__) . '/../controllers/AuthorController.php';

# build Author
AuthorController::inst('author')->build();