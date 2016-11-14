<?php
/**
 * Created by Andre Haralevi
 * Date: 06.11.13
 * Time: 23:52
 */

namespace Photocommunity\Mobile;

class Timer
{
    private static $aTimings;

    public static function getATimings()
    {
        return Timer::$aTimings;
    }

    public static function inst()
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new Timer();
        }
        return $instance;
    }

    private function __construct($key = 'Total')
    {
        Timer::$aTimings = array();
        Timer::$aTimings[$key]['start'] = gettimeofday();
    }

    public static function startTiming($key)
    {
        Timer::$aTimings[$key]['start'] = gettimeofday();
    }

    public static function stopTiming($key)
    {
        Timer::$aTimings[$key]['end'] = gettimeofday();
        $time_diff = (float)(Timer::$aTimings[$key]['end']['sec'] - Timer::$aTimings[$key]['start']['sec']) + ((float)(Timer::$aTimings[$key]['end']['usec'] - Timer::$aTimings[$key]['start']['usec']) / 1000000);
        Timer::$aTimings[$key]['elapsed'] = sprintf('%.8f', $time_diff);
    }

    public static function getSeconds($key)
    {
        return Timer::$aTimings[$key]['elapsed'];
    }
}

Timer::inst();