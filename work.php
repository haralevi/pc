<?php
namespace photocommunity\mobile;

require dirname(__FILE__) . '/classes/Init.php';
require dirname(__FILE__) . '/models/WorkBuilder.php';

# build Work
WorkBuilder::inst('work')->build();