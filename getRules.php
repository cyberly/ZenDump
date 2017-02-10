<?php
/**
*   Steal some triggers and throw them in a DB I guess.
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
$endpoint = "/triggers/active.json";

$data = $prod->get($endpoint)->response;
$triggers = $data["triggers"];
foreach ($triggers as $t){
    Helper::saveRule($t, "trigger");
    Helper::dumpJson($t, "triggers");
}

$endpoint = "/automations/active.json";
$data = $prod->get($endpoint)->response;
$automations = $data["automations"];
foreach ($automations as $a){
    Helper::saveRule($a, "automation");
    Helper::dumpJson($a, "automations");
}
