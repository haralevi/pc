<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 10/24/2016
 * Time: 5:21 AM
 */

namespace photocommunity\mobile;

class Controller
{
    /**
     * @var Tpl
     */
    public static $tpl_main;
    public static $tpl_main_var;
    /**
     * @var Tpl
     */
    public static $tpl;
    public static $tpl_var;

    /**
     * Builder constructor.
     * @param $tpl_name string
     */
    public function initTpl($tpl_name)
    {
        Controller::$tpl_main = new Tpl();
        Controller::$tpl_main->open('main');
        Controller::$tpl_main_var = array();

        Controller::$tpl = new Tpl();
        Controller::$tpl->open($tpl_name);
        Controller::$tpl_var['home_url'] = Config::$home_url;
    }
}