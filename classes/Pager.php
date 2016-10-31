<?php
namespace photocommunity\mobile;

class Pager
{

    public static function inst()
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new Pager();
        }
        return $instance;
    }

    private function __construct()
    {

    }

    public static function getHrefPrev($page = 1)
    {
        $page -= 1;
        if ($page <= 1)
            return '';
        else
            return '&amp;page=' . $page;
    }

    public static function getHrefNext($page = 1)
    {
        $page += 1;
        if ($page <= 1)
            return '';
        else
            return '&amp;page=' . $page;
    }

    public static function getCanonicalPageIndex ($page) {
        return floor(($page - 1) / (Consta::WORKS_PER_PAGE_CANONICAL / Consta::WORKS_PER_PAGE)) + 1;
    }
}