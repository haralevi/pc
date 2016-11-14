<?php
/**
 * Created by Andre Haralevi
 * Date: 06.10.11
 * Time: 5:21 AM
 */

namespace Photocommunity\Mobile;

require_once dirname(__FILE__) . '/../classes/Utils.php';

class Test extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        # init necessary objects
    }

    public function testIsValidEmail()
    {
        $email = 'admin@localhost';
        $this->assertFalse(Utils::isValidEmail($email));
        $email = 'admin@localhost.com';
        $this->assertTrue(Utils::isValidEmail($email));
    }

    public function testCleanRequest()
    {
        $email = 'admin@localhost';
        $this->assertFalse(Utils::isValidEmail($email));
        $email = 'admin@localhost.com';
        $this->assertTrue(Utils::isValidEmail($email));
    }


}
