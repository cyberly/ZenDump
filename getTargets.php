<?php
/**
*   Put some targets in the DB. There are 7 of them, this isn't a big deal.
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
$endpoint = "/targets.json";
$data = $prod->get($endpoint)->response;
foreach ($data["targets"] as $t){
    $target = Target::find($t["id"]);
    if ($target === NULL){
        $target = new Target;
        $target->target_id = $t["id"];
    }
    foreach ($t as $k=>$v){
        if (!empty($v)){
            $target->$k = $v;
        }
    }
    unset($target->id);
    $target->save();
    //var_dump($target);
}
