<?php
/**
 * Created by Andre Haralevi
 * Date: 10/24/2016
 * Time: 5:21 AM
 */

namespace Photocommunity\Mobile;

class Controller
{
    /**
     * @var Tpl
     */
    protected static $tpl_main;
    protected static $tpl_main_var;
    /**
     * @var Tpl
     */
    protected static $tpl;
    protected static $tpl_var;

    /**
     * Builder constructor.
     * @param $tpl_name string
     */
    protected function initTpl($tpl_name)
    {
        Controller::$tpl_main = new Tpl();
        Controller::$tpl_main->open('main');
        Controller::$tpl_main_var = array();

        Controller::$tpl = new Tpl();
        Controller::$tpl->open($tpl_name);
        Controller::$tpl_var['home_url'] = Config::$home_url;
    }
}