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
//$search = "type:user role:agent role:admin";
//$endpoint = "/search.json?query=" . urlencode($search);
$endpoint = "/groups.json";


while(!$lastPage){
    $data = $prod->get($endpoint)->response;
    foreach($data["groups"] as $g){
        $group = Group::find($g["id"]);
        if ($group === NULL){
            $group = new Group;
            $group->id = $g["id"];
        }
        $group->name = $g["name"];
        $group->created_at = $g["created_at"];
        $group->updated_at = $g["updated_at"];
        $group->save();
    }

    if (!$data["next_page"]){
        $lastPage = TRUE;
    } else {
        $endpoint = $data["next_page"];
    }
}
$prod = new zdCurl("production");
$groupIds = Group::select("id")->get();
foreach($groupIds as $id){
    $lastPage = FALSE;
    $groupId = $id->id;
    $endpoint = "/groups/$groupId/memberships.json";
    while(!$lastPage){
        //echo $groupId;
        $data = $prod->get($endpoint)->response;
        foreach($data["group_memberships"] as $m){
            $membership = Membership::find($m["id"]);
            if ($membership === NULL){
                $membership = new Membership;
                $membership->id = $m["id"];
            }
            $membership->group_id = $m["group_id"];
            $membership->default = $m["default"];
            $membership->created_at = $m["created_at"];
            $membership->updated_at = $m["updated_at"];
            $membership->save();
        }
        if (!$data["next_page"]){
            $lastPage = TRUE;
        } else {
            $endpoint = $data["next_page"];
        }
    }
    //var_dump($data);
}
//$endTime = round((microtime(true) - $startTime), 2);
//echo "Processed $agentCount agents in $endTime seconds.", PHP_EOL;
