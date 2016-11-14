<?php
/**
 * Created by Andre Haralevi
 * Date: 06.10.11
 * Time: 5:21 AM
 */

namespace Photocommunity\Mobile;

require_once dirname(__FILE__) . '/../classes/Db.php';
require_once dirname(__FILE__) . '/../classes/Request.php';

class requestTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Request
     */
    public $request;

    protected function setUp()
    {
        # init necessary objects
        $this->request = new Request();
    }

    public function testSetParam()
    {
        // Arrange
        $test_param_name = 'test_param_name';
        $test_param_val = 'test_param_val';

        // Act
        $this->request->setParam($test_param_name, $test_param_val);

        // Assert
        $this->assertEquals($test_param_val, $this->request->getParam($test_param_name));
    }
}