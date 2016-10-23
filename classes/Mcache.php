<?php
/**
 * Created by Andre Haralevi
 * Date: 07.11.13
 * Time: 02:59
 */

namespace photocommunity\mobile;

class Mcache
{
    private static $flags;
    /**
     * @var \Memcached
     */
    private static $mcache;

    public static function inst()
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new Mcache();
        }
        return $instance;
    }

    /**
     * Private __construct so nobody else can instance it
     */
    private function __construct()
    {

    }

    public static function connect()
    {
        Mcache::$mcache = new \Memcached();
        Mcache::$mcache->addServer('localhost', 11211);
        Mcache::$flags = 0;

    }

    public static function printStat()
    {
        Mcache::$mcache->getStats();
    }

    /**
     * @param $id
     * @return mixed|null
     */
    public static function get($id)
    {
        $value = Mcache::$mcache->get($id);
        if ($value === FALSE)
            return null;
        if (!empty($value['tags']) && count($value['tags']) > 0) {
            $expired = false;
            foreach ($value['tags'] as $tag => $tag_stored_value) {
                $tag_current_value = Mcache::getTagValue($tag);

                if ($tag_current_value != $tag_stored_value) {
                    $expired = true;
                    break;
                }
            }
            if ($expired)
                return null;
        }
        if(!isset($value['data'])) $value['data'] = $value;
        return $value['data'];
    }

    /**
     * @param $tag
     * @return bool
     */
    public static function delCache($tag)
    {
        $key = "tag_" . $tag;
        Mcache::set($key, microtime(true), 60 * 60 * 24 * 30, null);
        return true;
    }

    /**
     * @param $tag
     * @return mixed|null
     */
    private static function getTagValue($tag)
    {
        $key = "tag_" . $tag;
        $tag_value = Mcache::get($key);
        if ($tag_value === null) {
            $tag_value = microtime(true);
            Mcache::set($key, $tag_value, 60 * 60 * 24 * 30, null);
        }
        return $tag_value;
    }

    /**
     * @param $id
     * @param $data
     * @param array $tag
     * @param int $lifetime
     * @return bool
     */
    public static function set($id, $data, $lifetime = 0, array $tag = null)
    {
        if (!empty($tag)) {
            $key_tags = array();

            foreach ($tag as $t) {
                $key_tags[$t] = Mcache::getTagValue($t);
            }
            $key['tags'] = $key_tags;
        }
        $key['data'] = $data;
        if ($lifetime !== 0)
            $lifetime += time();
        return Mcache::$mcache->set($id, $key, $lifetime);
    }

    public static function cacheDbi($sql, $lifetime = 0, $tag = array())
    {
        if (!($cache = Mcache::get(md5($sql)))) {
            $cache = Db::inst()->execute($sql);
            if (!sizeof($cache)) $cache = '#empty#';
            if (!Mcache::set(md5($sql), $cache, $lifetime, $tag)) {
                #if(Config::inst()->getDebug()) utils::echox('No Memcache daemon running or responding');
            }
            #if(Config::inst()->getDebug()) utils::echox('<b>DB:</b> '.$sql);
        } else {
            #if(Config::inst()->getDebug()) utils::echox('<b>Cache:</b> '.$sql);
        }
        if ($cache == '#empty#') $cache = array();
        return $cache;
    }
}

/*
function testMemTag() {
	error_reporting(30719);
	ini_set("display_errors", 1);

	$cache = mcache::inst();

	$lifetime = 60 * 60 * 24 * 30;
	# add to cache
	$cache->set('post_1', 'content1', array('posts'), $lifetime);
	$cache->set('post_2', 'content2', array('posts'), $lifetime);

	# get values
	echo $value = $cache->get('post_1').'<br>';
	echo $value = $cache->get('post_2').'<br>';

	# "delete" tag
	$cache->delCache('posts');

	# trying to get the value of key post_1
	# $value1 === null,
	$value1 = $cache->get('post_1');
	$value2 = $cache->get('post_2');

	if ($value1 === null)
		echo ('post_1 is deleted').'<br/>';
	else
		echo $value1.'<br/>';

	if ($value2 === null)
		echo ('post_2 is deleted').'<br/>';
	else
		echo $value2.'<br/>';

	echo 'Test, ok!';
}
testMemTag();
*/