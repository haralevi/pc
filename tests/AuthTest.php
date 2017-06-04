<?php
/**
 * Created by Andre Haralevi
 * Date: 06.10.11
 * Time: 5:21 AM
 */

namespace Photocommunity\Mobile;

require_once dirname(__FILE__) . '/../classes/Utils.php';
require_once dirname(__FILE__) . '/../classes/Request.php';
require_once dirname(__FILE__) . '/../classes/Timer.php';
require_once dirname(__FILE__) . '/../classes/Config.php';
require_once dirname(__FILE__) . '/../classes/Localizer.php';
require_once dirname(__FILE__) . '/../classes/Consta.php';
require_once dirname(__FILE__) . '/../classes/Auth.php';

class AuthTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        # init necessary objects
    }

    protected function login($id_auth, $auth_premium)
    {
        $_SESSION['auth']['id_auth'] = $id_auth;
        $_SESSION['auth']['auth_key'] = '';
        $_SESSION['auth']['auth_facebook_id'] = '';
        $_SESSION['auth']['auth_login'] = '';
        $_SESSION['auth']['auth_type'] = '';
        $_SESSION['auth']['auth_port_lang'] = '';
        $_SESSION['auth']['auth_premium'] = $auth_premium;
        $_SESSION['auth']['auth_birth_time'] = '';
        $_SESSION['auth']['auth_rating'] = '';
        $_SESSION['auth']['auth_img_cnt'] = '';
        $_SESSION['auth']['auth_name'] = '';
        $_SESSION['auth']['auth_name_com'] = '';
        $_SESSION['auth']['auth_dom'] = '';
        $_SESSION['auth']['auth_email'] = '';
        $_SESSION['auth']['auth_power'] = '';
        $_SESSION['auth']['auth_gender'] = '';
        $_SESSION['auth']['auth_avatar'] = '';
        $_SESSION['auth']['auth_avatar_w'] = '';
        $_SESSION['auth']['auth_avatar_h'] = '';
        $_SESSION['auth']['auth_mood'] = '';
        $_SESSION['auth']['auth_mood_com'] = '';
        $_SESSION['auth']['auth_mood_de'] = '';
        $_SESSION['auth']['auth_blog_favor_cnt'] = '';
        $_SESSION['auth']['auth_country_id'] = '';
        $_SESSION['auth']['auth_region_id'] = '';
        $_SESSION['auth']['auth_fineart_gall'] = '';
        $_SESSION['auth']['auth_square_gall'] = '';
        $_SESSION['auth']['auth_nu_gall'] = '';
        $_SESSION['auth']['auth_window_gall'] = '';
        $_SESSION['auth']['auth_index_layout'] = '';
        $_SESSION['auth']['auth_featured_rating'] = '';
        $_SESSION['auth']['auth_featured_link'] = '';
        $_SESSION['auth']['auth_show_all_comms'] = '';
        $_SESSION['auth']['auth_port_dom'] = '';
        $_COOKIE['Y'] = '3571324985440';
        Auth::login();
    }

    public static function providerIdToIsPremium()
    {
        return array(
            array(1, Consta::AUTH_PREMIUM_0),
            array(111, Consta::AUTH_PREMIUM_1),
            array(111, Consta::AUTH_PREMIUM_2),
            array(111, Consta::AUTH_PREMIUM_3),
            array(111, Consta::AUTH_PREMIUM_4),
        );
    }

    /**
     * Testing addValues returns sum of two values
     * @dataProvider providerIdToIsPremium
     * @param $id int
     * @param $is_premium int
     */
    public function testIsPremium($id, $is_premium)
    {
        $this->assertTrue(Auth::isPremium($id, $is_premium));
    }

    public static function providerIdToIsNotPremium()
    {
        return array(
            array(111, Consta::AUTH_PREMIUM_0),
            array(-1, Consta::AUTH_PREMIUM_0),
        );
    }

    /**
     * Testing addValues returns sum of two values
     * @dataProvider providerIdToIsNotPremium
     * @param $id int
     * @param $is_premium int
     */
    public function testIsNotPremium($id, $is_premium)
    {
        $this->assertFalse(Auth::isPremium($id, $is_premium));
    }

    public function testSetWorkGallLimit()
    {
        $this->login(1, Consta::AUTH_PREMIUM_4);
        Auth::setWorkGallLimit();
        $expected = Consta::WORK_GALL_LIMIT_1;
        $result = Auth::getWorkGallLimit();
        $this->assertEquals($expected, $result);
    }

    protected function tearDown()
    {
        # delete unnecessary objects
    }

}
