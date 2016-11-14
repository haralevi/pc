<?php
/**
 * Created by Andre Haralevi
 * Date: 06.11.13
 * Time: 5:21 AM
 */

namespace Photocommunity\Mobile;

require dirname(__FILE__) . '/../classes/Init.php';
require dirname(__FILE__) . '/../controllers/CommController.php';

# build comments page
CommController::inst('comm')->build();