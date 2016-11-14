<?php
/**
 * Created by Andre Haralevi
 * Date: 06.11.13
 * Time: 5:21 AM
 */

namespace Photocommunity\Mobile;

require dirname(__FILE__) . '/../classes/Init.php';
require dirname(__FILE__) . '/../controllers/WorkController.php';

# build Work
WorkController::inst('work')->build();