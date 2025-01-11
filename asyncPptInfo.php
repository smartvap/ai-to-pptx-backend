<?php
require_once('config.inc.php');
require_once('./AiToPPTX/include.inc.php');

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, cache-control, Authorization, X-Requested-With, satoken, Token");
header("Content-Type: application/json; charset=utf-8");
header('Cache-Control: no-cache');

// 处理 OPTIONS 请求
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit;
}

$个性化信息 = [];
$个性化信息['Author']       = "Ai-To-PPTX";
$个性化信息['LastPageText'] = "非常感谢大家聆听";

$templateId         = FilterString($_GET['templateId']);
$主题               = $Global_Templates[$templateId]['subject'];
$模板路径           = "./json/".$主题.".json";
if(!is_file($模板路径)) {
  print "模板不存在";
  exit;
}

// 导入原始数据
$JsonContent      	= file_get_contents($模板路径);
$JsonData          	= json_decode($JsonContent, true);
//print_R($JsonData);exit;

$pptId              = FilterString($_GET['pptId']);
$currentId          = FilterString($_GET['currentId']);

//得到在REDIS中缓存的Markdown数据
$MarkdownData       = $redis->hGet("PPTX_CONTENT_".date('Ymd'), $pptId);
$OutPutLastPageId   = $redis->hGet("PPTX_CurrentPage_".date('Ymd'), $pptId);
$MarkdownDataJson   = json_decode($MarkdownData, true);

$outlineMarkdown    = $redis->hGet("PPTX_OUTLINE_".date('Ymd'), $pptId);
if($MarkdownDataJson['data']=="")   {
  $MarkdownDataJson['current'] = 1;
  $TotalPagesNumber = 根据大纲得到PPTX页码($outlineMarkdown);
  $MarkdownDataJson['total'] = $TotalPagesNumber;
}

//Markdown转Json Data
$Markdown_To_JsonData_Data = Markdown_To_JsonData($outlineMarkdown, $MarkdownDataJson['data'], $JsonData, $MarkdownDataJson['current']==$MarkdownDataJson['total']?true:false, $个性化信息, $OutPutLastPageId);


//Json Data转Zip格式
$pptxProperty = base64_encode(gzencode(json_encode($Markdown_To_JsonData_Data)));

if($MarkdownDataJson['current'] == $MarkdownDataJson['total']) {
  $redis->hSet("PPTX_DOWNLOAD_".date('Ymd'), $pptId, $pptxProperty);
}
$redis->hSet("PPTX_CurrentPage_".date('Ymd'), $pptId, $currentId);

$RS             = [];
$RS['code']     = 0;
$RS['message']  = 'ok';
$RS['data']['current']      = $MarkdownDataJson['current'];
$RS['data']['total']        = $MarkdownDataJson['total'];
$RS['data']['markdown']     = $MarkdownDataJson['data'];
$RS['data']['pptxProperty'] = $pptxProperty;

//$RS['data']['outlineMarkdown']      = $outlineMarkdown;
//$RS['data']['JsonData']             = $JsonData;
//$RS['data']['OutPutLastPageId']     = $OutPutLastPageId;
//$RS['data']['json']                 = $Markdown_To_JsonData_Data;

print_R(json_encode($RS));
?>
