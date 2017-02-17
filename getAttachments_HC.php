<?php
/**
*   Pulling attachments with threads, both HC and ZD.
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
$lastPage = FALSE;
$agentCount = FALSE;
$search = "type:user role:agent role:admin";
$fileUrl = "https://liquidweb.zendesk.com/hc/article_attachments/218333887/wpcaching5.png";

$fileData = $prod->getFile($fileUrl)->response;
//var_dump($fileData);
//echo $prod->status, PHP_EOL;
//var_dump($prod);
$fp = fopen("dumps/wpcaching5.png", "x");
fwrite($fp, $fileData);
fclose($fp);
