<?php
/**
*   ZD APIs are horrible, so this only does HC.
*
*	This is probably going to be an utter shitshow.
*
*	@author 	cbyerly <cbyerly@liquidweb.com>
*	@license	MIT maybe idk
*	@package	ZenDump
*	@link 		https://git.liquidweb.com/cbyerly/ZenDump
*
*	¯\_(ツ)_/¯
*/
namespace ZenDump;
require 'vendor/autoload.php';
include("inc/curl.inc.php");
include("inc/database.inc.php");
include("inc/models.inc.php");

set_time_limit(0);
$startTime = microtime(true);
$prod = new zdCurl("production");
$baseDir = "attachments";
$attachments = ArticleAttachment::select('content_url', 'relative_path')->get()->toArray();
//var_dump($attachments);

foreach ($attachments as $a){
    $fileUrl = $a["content_url"];
    $relative = $a["relative_path"];
    $dir = pathinfo($relative, PATHINFO_DIRNAME);
    $file = pathinfo($relative, PATHINFO_BASENAME);
    $fileData = $prod->getFile($fileUrl)->response;
    $attPath = $baseDir . $dir;
    $fullPath = $attPath . "/" . $file;
    if (!file_exists($attPath)){
        mkdir($attPath, 0755, true);
    }
    $fp = fopen($fullPath, "x");
    fwrite($fp, $fileData);
    fclose($fp);
}
