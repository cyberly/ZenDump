<?php
/**
*	Build the ticket list using search.
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
$ticketCount = FALSE;
$search = "type:ticket created>2016-03-30 created<2016-07-01 fieldvalue:accnt*";
//$search = "type:ticket cbyerly@gmail.com";
$endpoint = "/search.json?query=" . urlencode($search);


while(!$lastPage){
    $data = $prod->get($endpoint)->response;
    if (!$ticketCount){
        $ticketCount = $data["count"];
    }
    foreach($data["results"] as $t){
        $ticket = TicketList::find($t["id"]);
        if ($ticket === NULL){
            $ticket = new TicketList;
            $ticket->id = $t["id"];
            $ticket->save();
        }


    }
    usleep(100000);
    if (!$data["next_page"]){
        $lastPage = TRUE;
    } else {
        $endpoint = $data["next_page"];
    }
}
$endTime = round((microtime(true) - $startTime), 2);
echo "Processed $ticketCount tickets in $endTime seconds.", PHP_EOL;
