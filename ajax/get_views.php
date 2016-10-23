<?php
namespace photocommunity\mobile;

require dirname(__FILE__) . '/../classes/Init.php';

if (Auth::inst()->getIdAuth() == -1)
    die();

if (isset($_REQUEST['id']))
    $id_photo = Request::getParam('id', 'integer', 5);
else
    die();

$ph_view_cnt_c = Mcache::inst()->get('ph' . $id_photo);
if ($ph_view_cnt_c)
    Mcache::inst()->set('ph' . $id_photo, ($ph_view_cnt_c + 1));
else
    Mcache::inst()->set('ph' . $id_photo, 1);

$sql = "SELECT PH.id_auth
        FROM ds_photos PH
        WHERE PH.id_photo=" . $id_photo . "
        LIMIT 1";
$res_photos = Mcache::inst()->cacheDbi($sql); #utils::printArr($res_photos);

if (sizeof($res_photos))
    $id_auth_photo = $res_photos[0]['id_auth'];
else
    die();

$sql = "SELECT VS.id_auth
        FROM ds_views VS
        WHERE VS.id_photo=" . $id_photo . " AND id_auth=" . Auth::inst()->getIdAuth() . "
        LIMIT 1";
$res_views = Db::inst()->execute($sql); #utils::printArr($res_views);

$result = 0;
if (!sizeof($res_views)) {
    if ($id_auth_photo != Auth::inst()->getIdAuth()) {
        $sql = "INSERT INTO ds_views (id_photo, id_auth) VALUES ($id_photo, " . Auth::inst()->getIdAuth() . ")";
        Db::inst()->execute($sql);
        $result = 1;
    }
}

$json = '{"result": ' . $result . '}';

# parse page
require dirname(__FILE__) . '/../classes/ParseJson.php';
ParseJson::inst($json);