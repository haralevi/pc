<?php
namespace photocommunity\mobile;

require dirname(__FILE__) . '/classes/Init.php';
require dirname(__FILE__) . '/models/CommBuilder.php';

# build comments page
CommBuilder::inst('comm')->build(true);