<?php
/**
*	Build ticket data, as well as associated end-user accounts.
*   This is going t o likely be a long and slowww run.
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
//Search params when I need it: type:ticket created>2016-01-01 fieldvalue:accnt*

$prod = new zdCurl("production");
//$search = "type:ticket created>2016-01-01 fieldvalue:accnt*?include=users";
//$endpoint = "/search.json?query=" . urlencode($search);
$searchId = "1277497"; //SFDC Demo #3
//$searchId = "1277477"; //SFDC Demo #1
$endpoint = "/tickets/$searchId/audits.json?include=users,groups,tickets";
$data = $prod->get($endpoint)->response;

//Let's build the ticket data.
$ticketData = $data["tickets"][0];
$ticketId = $ticketData["id"];
$ticketChannel = $ticketData["via"]["channel"];
if ($ticketChannel == "email"){
    $ticketFrom = $ticketData["via"]["source"]["from"]["address"];
}
$ticketCreated = $ticketData["created_at"];
$ticketUpdated = $ticketData["updated_at"];
$ticketType = $ticketData["type"];
$ticketSubject = $ticketData["subject"];

//Let's build the end-user data.
$users = $data["users"];
foreach ($users as $user) {
    if ($user["role"] == "end-user") {
        //Instantiate DB lolz
        //Put it in the DB if it doesn't exist.
    }
}
//Here come das events. Run to the hills.

$events = $data["audits"];
foreach ($events as $event){
    $eventId = $event["id"];
    //$ticketId needs to go in the events table here as well.
    $eventCreated = $event["created_at"];
    echo "Event: $eventId, Created: $eventCreated", PHP_EOL;
    foreach ($event["events"] as $action){
        WRITE A SWITCH HERE YOU FUCKING IDIOT.
        $actionId = $action["id"];
        $actionType = $action["type"];
        echo "\t$actionType", PHP_EOL;
        if ($actionType == "Comment"){
            $actionBody = $action["body"];
            $actionPublic = $action["public"];
            echo "\t\tPublic: $actionPublic", PHP_EOL;
            echo "\t\tBody: " . strlen($actionBody), PHP_EOL;
        } else {
            $actionField = $action["field_name"];
            $actionValue = $action["value"];
            echo "\t\tField: $actionField, Value: $actionValue", PHP_EOL;
            if ($actionType != "Create"){
                $actionPrevious = $action["previous_value"];
                echo "\t\tPrevious: $actionPrevious", PHP_EOL;
            }
        }


    }
}

//var_dump($events);
//$events = $eventsData["events"];
//foreach ($events as $event){
    //var_dump($event);
//}
//var_dump($userData);
