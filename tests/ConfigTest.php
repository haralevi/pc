<?php
namespace photocommunity\mobile;

require_once dirname(__FILE__) . '/../classes/Utils.php';
require_once dirname(__FILE__) . '/../classes/Request.php';
require_once dirname(__FILE__) . '/../classes/Timer.php';
require_once dirname(__FILE__) . '/../classes/Config.php';


class ConfigTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        # init necessary objects
    }

    public function testGetDebug()
    {
        $this->assertEquals(0, Config::getDebug());
    }

    protected function tearDown()
    {
        # delete unnecessary objects
    }

}
