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
include("inc/curl.inc.php");
include("inc/threads.inc.php");

set_time_limit(0);
$startTime = microtime(true);
Helper::startJob("full_active");
$prod = new zdCurl("production");
$lastPage = FALSE;
$ticketCount = FALSE;
$threadId = 1;
$dateArray = array(
    "2015-12-31" => "2017-03-23", //3,888
    "2017-03-22" => "2017-03-24", //2,809
    "2017-03-23" => "2017-04-04", //3,802
    "2017-04-03" => "2017-04-05", //3,271
    "2017-04-04" => "2017-04-21", //3,829
    "2017-04-20" => "2017-04-22", //5,637
    "2017-04-21" => "2017-05-04", //4,182
    "2017-05-03" => "2017-05-09", //4431
    "2017-05-08" => "2017-05-13", //4,237
    "2017-05-12" => "2017-05-17", //3,794
    "2017-05-16" => "2017-05-22", //4,255
    "2017-05-22" => "2017-05-27", //4,346
    "2017-05-26" => "2017-06-01", //4,089
    "2017-05-31" => "2017-06-02", //500 or so, movce this forward.
);
foreach ($dateArray as $k => $v){
    $search = "type:ticket created>$k created<$v fieldvalue:accnt*" .
      " status<closed";
    $endpoints[] = "/search.json?query=" . urlencode($search);
}
$threads = count($dateArray);
$pool = new \Pool($threads, APIWorker::class);
foreach ($endpoints as $endpoint){
    $pool->submit(new ListWork($endpoint, $threadId, "TicketsActive"));
    $threadId++;
}
$pool->shutdown();

$endTime = round((microtime(true) - $startTime), 2);
echo "Total run time: $endTime seconds.", PHP_EOL;
