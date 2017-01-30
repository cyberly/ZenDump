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
include("inc/helper.inc.php");

set_time_limit(0);
$fileId = 0;
$chunkSize = 38650;
$skip = $chunkSize * $fileId;
$startTime = microtime(true);
$prod = new zdCurl("production");
$ticketList = array(1169149,979845,805163,1207888,980757,1068198,943677,987127,911438,1227778,980996,1177379,981052,919449,981310,869705,981340,1015747,981448,981540,919073,981558,979765,1261293,920602,1009966,981592,981606,981618,981636,981639,994504,981650,1129955,1229079,981668,1070670,981672,981681,1012302,944061,808883,1248830,1208022,981687,981692,807424,1071337,981700,981718);
//$ticketList = TicketList::select("id")->skip($skip)->take($chunkSize)->get();
$ticketCount = count($ticketList);
$currentRun = 0;
echo "Processing $ticketCount tickets.", PHP_EOL;
foreach($ticketList as $t){
    $currentRun++;
    if ($currentRun % 10 == 0 || $currentRun == 1) {
        $percentComplete = round(($currentRun / $ticketCount) * 100, 2);
        echo "Progress: $percentComplete%\r";
    }
    $searchId = $t;//->id;
    $lastPage = FALSE;
    $endpoint = "/tickets/$searchId/audits.json?include=users,groups,tickets";
    $errorCount = 0;
    while(!$lastPage){
        $data = $prod->get($endpoint)->response;
        if ($prod->status != "200"){
            if ($errorCount <= 4) {
                $error = new Error;
                $error->severity = "soft";
                $error->request = $searchId;
                $error->status = $prod->status;
                $error->time = date("Y-m-d H:i:s");
                $error->save();
                usleep(500000);
                $errorCount++;
            } else {
                $error = new Error;
                $error->severity = "hard";
                $error->request = $searchId;
                $error->status = $prod->status;
                $error->time = date("Y-m-d H:i:s");
                $error->save();
                break;
            }
        } else {
            //Let's build the ticket data.
            $ticketData = $data["tickets"][0];
            $ticket_id = $ticketData["id"];
            $ticket = Ticket::find($ticketData["id"]);
            if ($ticket === NULL){
                $ticket = new Ticket;
                $ticket->ticket_id = $ticketData["id"];
                $ticket->channel = $ticketData["via"]["channel"];
                if ($ticket->channel == "email"){
                    $ticket->recieved_from = $ticketData["via"]["source"]["from"]["address"];
                }
                $ticket->created_at = $ticketData["created_at"];
                $ticket->subject = $ticketData["subject"];
                $ticket->submitter_id = $ticketData["submitter_id"];
                $ticket->requester_id = $ticketData["requester_id"];
            }
            $ticket->status = $ticketData["status"];
            $ticket->updated_at = $ticketData["updated_at"];
            $ticket->type = $ticketData["type"];
            $ticket->save();

            //Let's build the end-user data.
            $users = $data["users"];
            foreach ($users as $user) {
                if ($user["role"] == "end-user") {
                    //Instantiate DB lolz
                    //Put it in the DB if it doesn't exist.
                    $endUser = User::find($user["id"]);
                    if ($endUser === NULL) {
                        $endUser = new User;
                        $endUser->user_id = $user["id"];
                        $endUser->name = $user["name"];
                        $endUser->email = $user["email"];
                        $endUser->save();
                    }
                }
                unset($user);
                unset($endUser);
            }
            //Build event data to iterate through actions.
            $events = $data["audits"];
            foreach ($events as $t_event){
                $event = Event::find($t_event["id"]);
                if ($event === NULL){
                    $event = new Event;
                }
                $event->event_id = $t_event["id"];
                $event->ticket_id = $t_event["ticket_id"];
                $event->created_at = $t_event["created_at"];
                $event->channel = $t_event["via"]["channel"];
                if (isset($t_event["metadata"]["system"]["ip_address"])){
                    $event->source_ip = $t_event["metadata"]["system"]["ip_address"];
                }
                //$ticketId needs to go in the events table here as well.
                foreach ($t_event["events"] as $t_action){

                    $actionSaved = Helper::saveAction($t_action, $t_event, $ticketData["id"]);
                    if ($actionSaved){
                        $event->save();
                    }
                }
            }
            if (!$data["next_page"]){
                $lastPage = TRUE;
            } else {
                $endpoint = $data["next_page"];
            }
        }
        usleep(150000);

    }
}
$endTime = round((microtime(true) - $startTime), 2);
$avgTime = round($endTime / $ticketCount, 2);
echo "Processed $ticketCount, averaging $avgTime seconds per ticket.", PHP_EOL;
