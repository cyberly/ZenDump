<?php
/**
*
*   Build an incremantal ticket list. Exciting.
*
*	This is probably going to be an utter shitshow.
* This is actually unused due to Gears being useless.
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
TicketsActive::truncate();
$prod = new zdCurl("production");
$lastPage = FALSE;
$startQuery = Meta::select("start_time")
  ->orderBy("start_time", "desc")
  ->first()
  ->toArray();
$start_time = $startQuery["start_time"];
Helper::startJob("incremental");
echo "using " . date('r', $start_time) . " as starting point.", PHP_EOL;
$endpoint = "/incremental/tickets.json?start_time=$start_time";

while (!$lastPage){
    $data = $prod->get($endpoint)->response;
    $ticketCount = $data["count"];
    foreach($data["tickets"] as $t){
        $ticket = new TicketsActive;
        $ticket->id = $t["id"];
        $ticket->save();
    }
    sleep(5);
    if (!$data["count"] = 1000){
        $lastPage = TRUE;
    } else {
        $endpoint = $data["next_page"];
    }
}
echo "Added $ticketCount tickets for indexing.", PHP_EOL;
