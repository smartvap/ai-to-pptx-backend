<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, cache-control, Authorization, X-Requested-With, satoken, Token");
header("Content-Type: application/json");
header('Cache-Control: no-cache');

require_once('config.inc.php');

$RS = [];
$RS['data'] = $Global_Templates;
$RS['code'] = 0;
$RS['message'] = 'ok';

print json_encode($RS);

?>
