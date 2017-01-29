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
$startTime = microtime(true);
$prod = new zdCurl("production");
//$ticketList = TicketList::all();
//$ticketList = array("1169149");
$ticketList = array(1169149,979845,805163,1207888,980757,1068198,943677,987127,911438,1227778,980996,1177379,981052,919449,981310,869705,981340,1015747,981448,981540,919073,981558,979765,1261293,920602,1009966,981592,981606,981618,981636,981639,994504,981650,1129955,1229079,981668,1070670,981672,981681,1012302,944061,808883,1248830,1208022,981687,981692,807424,1071337,981700,981718);
//$ticketList = array(1206049);
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
                $ticket->id = $ticketData["id"];
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
                    $endUser = EndUser::find($user["id"]);
                    if ($endUser === NULL) {
                        $endUser = new EndUser;
                        $endUser->id = $user["id"];
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
                $event->id = $t_event["id"];
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
                     /*$action = Action::find($t_action["id"]);
                    if ($action === NULL){
                        $action = new Action;
                    }
                    $action->id = $t_action["id"];
                    $action->event_id = $t_event["id"];
                    $action->type = $t_action["type"];
                    if ($t_action["type"] == "Comment"){
                        $action->body = $t_action["body"];
                        $actionPublic = $t_action["public"];
                        $action->author_id = $t_action["author_id"];
                        if(!empty($t_action["attachments"])){
                            foreach($t_action["attachments"] as $a){
                                $attachment = Attachment::find($a["id"]);
                                if ($attachment === NULL){
                                    $attachment = new Attachment;
                                    $attachment->id = $a["id"];
                                    $attachment->ticket_id = $ticketData["id"];
                                    $attachment->event_id = $t_event["id"];
                                    $attachment->action_id = $t_action["id"];
                                    $attachment->file_name = $a["file_name"];
                                    $attachment->url = $a["url"];
                                    $attachment->content_url = $a["content_url"];
                                    $attachment->content_type = $a["content_type"];
                                    $attachment->save();
                                    unset($a);
                                    unset($attachment);

                                }
                            }
                        }
                    }
                    if ($t_action["type"] == "Create" || $t_action["type"] == "Change"){
                        $action->field_name = $t_action["field_name"];
                        if (is_array($t_action["value"])) {
                            $action->value = join(',', $t_action["value"]);
                        } else {
                            $action->value = $t_action["value"];
                        }
                        if ($t_action["type"] == "Change"){
                            if (is_array($t_action["previous_value"])){
                                $action->previous_value = join(',',$t_action["previous_value"]);
                            } else {
                                $action->previous_value = $t_action["previous_value"];
                            }
                        }
                        if (isset($t_action["via"])){
                            if ($t_action["via"]["channel"] == "rule" || $t_action["via"]["channel"] == "automation"){
                                if(isset($t_action["via"]["source"]["rel"])){
                                    $action->channel = $t_action["via"]["source"]["rel"];
                                }
                                $action->channel_id = $t_action["via"]["source"]["from"]["id"];
                                $action->channel_name = $t_action["via"]["source"]["from"]["title"];
                            }
                        }
                    }
                    //Save the event here to prevent saving empty events if this doesn't trip.
                    $event->save();
                    $action->save();
                    unset($action); */
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
