<?php
/**
 * Created by Andre Haralevi
 * Date: 05.09.14
 * Time: 18:04
 */

namespace Photocommunity\Mobile;

class Request
{
    private static function _getDefParam($type = 'string', $min_val = null, $max_val = null)
    {
        if ($type == 'integer') {
            $val = 0;
            if ($min_val != null && $val < $min_val)
                $val = $min_val;
            if ($max_val != null && $val > $max_val)
                $val = $max_val;
        } else
            $val = '';
        return $val;
    }

    public static function getParam($param, $type = 'string', $min_val = null, $max_val = null, $pattern = null)
    {
        $val = Request::_getDefParam($type, $min_val, $max_val);
        if (isset($_REQUEST[$param])) {
            if ($type == 'integer') {
                $val = intval($_REQUEST[$param]);
                if ($min_val != null && $val < $min_val)
                    $val = $min_val;
                if ($max_val != null && $val > $max_val)
                    $val = $max_val;
            } else
                $val = Utils::cleanRequest($_REQUEST[$param]);

            if ($pattern != null && !preg_match($pattern, $val))
                $val = Request::_getDefParam($type, $min_val, $max_val);
        }
        return $val;
    }

    public static function setParam($param, $val)
    {
        $_REQUEST[$param] = $val;
    }

    public static function getParamVal($url, $param)
    {
        $val = '';
        $parts = parse_url($url);
        if (isset($parts['query'])) {
            parse_str($parts['query'], $query);
            if (isset($query[$param]))
                $val = $query[$param];
        }
        return $val;
    }
} 