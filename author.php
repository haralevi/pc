<?php
namespace photocommunity\mobile;

require dirname(__FILE__) . '/classes/Init.php';
require dirname(__FILE__) . '/models/AuthorBuilder.php';

# build Author
AuthorBuilder::inst('author')->build();