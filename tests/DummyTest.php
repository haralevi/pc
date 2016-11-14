<?php
/**
 * Created by Andre Haralevi
 * Date: 06.10.11
 * Time: 5:21 AM
 */

namespace Photocommunity\Mobile;

class DummyTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        # init necessary objects
    }

    public function testDummy()
    {
        $this->assertEquals(0, 0);
        $this->assertTrue(true);
        $this->assertFalse(false);
        $this->assertEmpty('');
    }

    protected function tearDown()
    {
        # delete unnecessary objects
    }
}
