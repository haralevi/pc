<?php
/**
 * Created by Andre Haralevi
 * Date: 06.11.13
 * Time: 16:36
 */

namespace Photocommunity\Mobile;

class Db
{
    private static $db_host;
    private static $db_name;
    private static $db_user;
    private static $db_password;
    private static $db_conn;

    /**
     * @return mixed
     */
    public static function getDbConn()
    {
        return Db::$db_conn;
    }

    private static $queries;
    private static $total_time;

    public static function getQueries()
    {
        return Db::$queries;
    }

    public static function getTotalTime()
    {
        return Db::$total_time;
    }

    public static function inst($db_host = '', $db_name = '', $db_user = '', $db_password = '')
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new Db($db_host, $db_name, $db_user, $db_password);
        }
        return $instance;
    }

    /**
     * Private __construct so nobody else can instance it
     * @param $db_host
     * @param $db_name
     * @param $db_user
     * @param $db_password
     */
    private function __construct($db_host, $db_name, $db_user, $db_password)
    {
        if ($db_host == '') Db::$db_host = Config::DB_HOST;
        else Db::$db_host = $db_host;
        if ($db_name == '') Db::$db_name = Config::DB_NAME;
        else Db::$db_name = $db_name;
        if ($db_user == '') Db::$db_user = Config::DB_USER;
        else Db::$db_user = $db_user;
        if ($db_password == '') Db::$db_password = Config::DB_PASSWORD;
        else Db::$db_password = $db_password;
    }

    public static function connect()
    {
        if (Db::$db_conn === null) {
            Db::$db_conn = mysqli_connect(Db::$db_host, Db::$db_user, Db::$db_password);
            if (!Db::$db_conn || mysqli_connect_errno()) {
                if (Config::getDebug()) Utils::echox(htmlspecialchars(mysqli_connect_errno()));
                else {
                    $error = date("d.m.Y H:i:s") . ' | ';
                    if (isset(Config::$remote_addr)) $error .= Config::$remote_addr . ' | ';
                    if (isset($_SESSION['auth']['id_auth'])) $error .= 'ID_AUTH: ' . $_SESSION['auth']['id_auth'] . ' | ';
                    $error .= mysqli_connect_errno() . Consta::EOL;
                    $error .= Config::$http_scheme. '//' . Config::$http_host . Config::$request_uri;
                    Utils::errorWriter($error);
                }
                require dirname(__FILE__) . '/../../down.php';
                die();
            }
            mysqli_set_charset(Db::$db_conn, Config::CHARSET);
            mysqli_select_db(Db::$db_conn, Db::$db_name);
        }
    }

    public static function disconnect()
    {
        if (Db::$db_conn) {
            mysqli_close(Db::$db_conn);
            Db::$db_conn = null;
        }
    }

    public static function execute($sql)
    {
        if (Db::$db_conn === null) # init connection
            Db::connect();
        $sql = str_replace(array('select ', 'desc '), array('SELECT ', 'DESC '), $sql);
        $time_start = gettimeofday();
        $result = mysqli_query(Db::$db_conn, $sql);
        $time_end = gettimeofday();
        if (Config::getDebug()) {
            $time = (float)($time_end['sec'] - $time_start['sec']) + ((float)($time_end['usec'] - $time_start['usec']) / 1000000);
            if ($time > 0.01) Db::$queries .= '<b style="color:red">' . $time . '</b> - ' . $sql . '<br/>';
            else Db::$queries .= '<b>' . $time . '</b> - ' . $sql . '<br/>';
            Db::$total_time += $time;
        }
        if (!$result) {
            if (Config::getDebug()) Utils::echox(htmlspecialchars(mysqli_error(Db::$db_conn)));
            else {
                $error = date("d.m.Y H:i:s") . ' | ';
                if (Config::$remote_addr) $error .= Config::$remote_addr . ' | ';
                if (isset($_SESSION['auth']['id_auth'])) $error .= 'ID_AUTH: ' . $_SESSION['auth']['id_auth'] . ' | ';
                $error .= $sql . Consta::EOL . mysqli_error(Db::$db_conn) . Consta::EOL;
                $error .= Config::$http_scheme . '//' . Config::$http_host . Config::$request_uri;
                Utils::errorWriter($error);
            }
            require dirname(__FILE__) . '/../../down.php';
            die();
        }
        if ((mb_substr($sql, 0, 6) != 'SELECT') && (mb_substr($sql, 0, 4) != 'DESC')) {
            return array();
        } else {
            $data = array();
            $i = 0;
            while ($row = mysqli_fetch_array($result)) $data[$i++] = $row;
            mysqli_free_result($result);
        }
        return $data;
    }
}