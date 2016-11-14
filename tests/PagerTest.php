<?php
/**
 * Created by Andre Haralevi
 * Date: 06.10.11
 * Time: 5:21 AM
 */

namespace Photocommunity\Mobile;

require_once dirname(__FILE__) . '/../classes/Pager.php';

class PagerTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        # init necessary objects
    }

    public function testGetHrefPrev()
    {
        $this->assertEmpty(Pager::getHrefPrev(0));
        $this->assertEmpty(Pager::getHrefPrev(1));
        $this->assertEmpty(Pager::getHrefPrev(2));
        $this->assertEquals('&amp;page=2', Pager::getHrefPrev(3));
    }

    public function testGetHrefNext()
    {
        $this->assertEmpty(Pager::getHrefNext(0));
        $this->assertEquals('&amp;page=2', Pager::getHrefNext(1));
        $this->assertEquals('&amp;page=3', Pager::getHrefNext(2));
        $this->assertEquals('&amp;page=4', Pager::getHrefNext(3));
    }

    protected function tearDown()
    {
        # delete unnecessary objects
    }
}
