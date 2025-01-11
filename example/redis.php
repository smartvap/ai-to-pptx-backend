<?php

exit;

require_once('../config.inc.php');

for ($i = 0; $i < 130; $i++) {
    $date = date('Ymd', strtotime("-$i day"));
    
    $result = $redis->del("PPTX_CONTENT_" . $date);
    $result = $redis->del("PPTX_OUTLINE_" . $date);

    if ($result) {
        echo "键 PPTX_CONTENT_$date 已成功删除。\n";
    } else {
        echo "键 PPTX_CONTENT_$date 不存在或删除失败。\n";
    }
}

$Data = $redis->del("PPTX_DOWNLOAD");
$Data = $redis->hGetAll("PPTX_DOWNLOAD");
print_R($Data);




?>
