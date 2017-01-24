<?php
/**
*	Build out agent profiles. There is no redundancy check here as it is a
*   quick run. This require two searches for role:agent and role:admin.
*   I didn't bother rate limiting this, which could be a bad thing.
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
$endpoint = "/search.json?query=" . urlencode($search);


while(!$lastPage){
    $data = $prod->get($endpoint)->response;
    if (!$agentCount){
        $agentCount = $data["count"];
    }
    $agents = $data["results"];
    foreach ($agents as $agent){
        $user = new Agent;
        //All DB fields should be populated except signature and alias.
        $user->id = $agent["id"];
        $user->name = $agent["name"];
        $user->email = $agent["email"];
        if ($agent["alias"]){
            $user->alias = $agent["alias"];
        }
        $user->role = $agent["role"];
        if ($agent["signature"]){
            $user->signature = $agent["signature"];
        }
        $user->suspended = $agent["suspended"];
        $user->save();
    }
    if (!$data["next_page"]){
        $lastPage = TRUE;
    } else {
        $endpoint = $data["next_page"];
    }
}
$endTime = round((microtime(true) - $startTime), 2);
echo "Processed $agentCount agents in $endTime seconds.", PHP_EOL;
