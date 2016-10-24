<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 10/24/2016
 * Time: 5:21 AM
 */

namespace photocommunity\mobile;

class Builder
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
    public function __construct($tpl_name)
    {
        Builder::$tpl_main = new Tpl();
        Builder::$tpl_main->open('main');
        Builder::$tpl_main_var = array();

        Builder::$tpl = new Tpl();
        Builder::$tpl->open($tpl_name);
        Builder::$tpl_var['home_url'] = Config::$home_url;
    }
}