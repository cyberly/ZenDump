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
Helper::startJob("incremental");
TicketList::truncate();
$prod = new zdCurl("production");
$lastPage = FALSE;
$startQuery = Meta::select("start_time")
  ->orderBy("start_time")
  ->first()
  ->toArray();
$start_time = $startQuery["start_time"];
//$date = date("Y-m-d H:i:s");
//$date = "2017-02-21 14:28:00";
//$epoch = new \DateTime();
//$start_time = $epoch->format('U');
//echo $start_time, PHP_EOL;


//$search = "type:user role:agent role:admin";
$endpoint = "/incremental/tickets.json?start_time=$start_time";

while (!$lastPage){
    $data = $prod->get($endpoint)->response;
    var_dump($data["tickets"]);
    foreach($data["tickets"] as $t){
        $ticket = new TicketList;
        $ticket->id = $t["id"];
        //$ticket->save();
    }
    sleep(5);
    if (!$data["count"] < 1000){
        $lastPage = TRUE;
    } else {
        $endpoint = $data["next_page"];
    }
}

//$data = $prod->get($endpoint)->response;
//var_dump($data);
//foreach ($data["tickets"] as $ticket){
    //echo $ticket["id"], PHP_EOL;
//}
