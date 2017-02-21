<?php
/**
*   Steal some macros, fuck it.
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
include("inc/helper.inc.php");
set_time_limit(0);
$startTime = microtime(true);

$prod = new ZdCurl("production");
$endpoint = "/macros/active.json";
$lastPage = FALSE;

while (!$lastPage){
    $data = $prod->get($endpoint)->response;
    foreach ($data["macros"] as $macro){
        Helper::saveMacro($macro);
        //Helper::dumpJson($macro, "macros");
    }

    if (!$data["next_page"]){
        $lastPage = TRUE;
    } else {
        $endpoint = $data["next_page"];
    }
}

//var_dump($data["macros"][0]);
