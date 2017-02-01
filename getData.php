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
                Helper::saveError("soft", $searchId, $prod->status);
                usleep(500000);
                $errorCount++;
            } else {
                Helper::saveError("hard", $searchId, $prod->status);
                break;
            }
        } else {
            //Let's build the ticket data.
            $ticketData = $data["tickets"][0];
            Helper::saveTicket($ticketData);
            Helper::saveUser($data["users"]);
            //Build event data to iterate through actions.
            $events = $data["audits"];
            Helper::saveEvents($events, $ticketData["id"]);
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
