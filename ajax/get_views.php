<?php
namespace photocommunity\mobile;

require dirname(__FILE__) . '/../classes/Init.php';

if (isset($_REQUEST['id']))
    $id_photo = Request::getParam('id', 'integer', 5);
else
    die();

$ph_view_cnt_c = Mcache::get('ph' . $id_photo);
if ($ph_view_cnt_c)
    Mcache::set('ph' . $id_photo, ($ph_view_cnt_c + 1));
else
    Mcache::set('ph' . $id_photo, 1);

if (Auth::getIdAuth() == -1)
    die();

$sql = "SELECT PH.id_auth
        FROM ds_photos PH
        WHERE PH.id_photo=" . $id_photo . "
        LIMIT 1";
$res_photos = Mcache::cacheDbi($sql); #utils::printArr($res_photos);

if (sizeof($res_photos))
    $id_auth_photo = $res_photos[0]['id_auth'];
else
    die();

$sql = "SELECT VS.id_auth
        FROM ds_views VS
        WHERE VS.id_photo=" . $id_photo . " AND id_auth=" . Auth::getIdAuth() . "
        LIMIT 1";
$res_views = Db::execute($sql); #utils::printArr($res_views);

$result = 0;
if (!sizeof($res_views)) {
    if ($id_auth_photo != Auth::getIdAuth()) {
        $sql = "INSERT INTO ds_views (id_photo, id_auth) VALUES ($id_photo, " . Auth::getIdAuth() . ")";
        Db::execute($sql);
        $result = 1;
    }
}

$json = '{"result": ' . $result . '}';

# parse page
require dirname(__FILE__) . '/../classes/ParseJson.php';
ParseJson::inst($json);