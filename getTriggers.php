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
set_time_limit(0);
$startTime = microtime(true);

$prod = new ZdCurl("production");
$endpoint = "/triggers.json";

$data = $prod->get($endpoint)->response;
$triggers = $data["triggers"];
foreach ($triggers as $t){
    $trigger = Trigger::find($t["id"]);
    if ($trigger === NULL){
        $trigger = new Trigger;
        $trigger->trigger_id = $t["id"];
    }
    $trigger->title = $t["title"];
    $trigger->active = $t["active"];
    $trigger->updated_at = $t["updated_at"];
    $trigger->created_at = $t["created_at"];
    $trigger->position = $t["position"];
    $trigger->save();
    foreach ($t["conditions"]["all"] as $c){
        $condition = new TriggerCondition;
        $condition->trigger_id = $t["id"];
        $condition->type = "all";
        $condition->field = $c["field"];
        $condition->operator = $c["operator"];
        $condition->value = $c["value"];
        $condition->save();
    }
    foreach ($t["conditions"]["any"] as $c){
        $condition = new TriggerCondition;
        $condition->trigger_id = $t["id"];
        $condition->type = "any";
        $condition->field = $c["field"];
        $condition->operator = $c["operator"];
        $condition->value = $c["value"];
        $condition->save();
    }
    foreach ($t["actions"] as $a){
        //echo "Trigger ID: " . $t["id"] . " Field: " . $a["field"], PHP_EOL;
        $action = new TriggerAction;
        $action->field = $a["field"];
        if (is_array($a["value"])) {
            if ($a["field"] == "notification_target"){
                if (is_array($a["value"][1])){
                    $action->value = implode(',', $a["value"][1][0]);
                } else {
                    $action->value = $a["value"][1];
                    $action->recipient = $a["value"][0];
                }
            } else {
                $action->value = $a["value"][2];
                $action->recipient = $a["value"][0];
                $action->subject = $a["value"][1];
            }
        } else {
            $action->value = $a["value"];
        }
        $action->save();
    }
}
