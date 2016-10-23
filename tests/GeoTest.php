<?php
namespace photocommunity\mobile;

require_once dirname(__FILE__) . '/../classes/Utils.php';
require_once dirname(__FILE__) . '/../classes/Request.php';
require_once dirname(__FILE__) . '/../classes/Timer.php';
require_once dirname(__FILE__) . '/../classes/Config.php';
require_once dirname(__FILE__) . '/../classes/Localizer.php';
require_once dirname(__FILE__) . '/../classes/Consta.php';
require_once dirname(__FILE__) . '/../classes/Auth.php';
require_once dirname(__FILE__) . '/../classes/Geo.php';

class GeoTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        Geo::inst();
    }

    public static function providerIdToIsPremium()
    {
        return array(
            array('37.44.113.158', 'Minsk'),
            array('174.117.128.31', 'Woodbridge'),
            array('46.35.247.103', 'Sevastopol'),
            array('217.168.64.242', 'Kaliningrad'),
            array('178.20.235.164', 'Moscow'),
            array('62.210.101.170', 'Fontenay-aux-Roses'),
            array('123.30.137.221', 'Hanoi'),
            array('202.46.56.188', 'Shenzhen'),
            array('54.176.144.3', 'San Jose'),
            array('181.140.45.234', 'Medellín'),
            array('68.108.11.116', 'Las Vegas'),
            array('173.208.173.26', 'Kansas City'),
            array('110.77.148.56', 'Bang Sue'),
            array('93.128.114.141', 'Saarbrücken'),
        );
    }

    /**
     * Testing addValues returns sum of two values
     * @dataProvider providerIdToIsPremium
     * @param $remote_addr string
     * @param $city string
     */
    public function testSetGeo($remote_addr, $city)
    {
        Config::$remote_addr = $remote_addr;
        Geo::setGeo();
        $this->assertEquals($city, Geo::$City);
        unset($_SESSION['CountryCode']);
    }

    protected function tearDown()
    {
        # delete unnecessary objects
    }

}
