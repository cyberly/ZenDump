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
$prod = new zdCurl("production");
$lastPage = FALSE;
$ticketCount = FALSE;
$threadId = 1;
$dateArray = array(
    "2015-12-31" => "2016-02-01",
    "2016-01-31" => "2016-03-01",
    "2016-02-27" => "2016-04-01",
    "2016-03-31" => "2016-05-01",
    "2016-04-30" => "2016-06-01",
    "2016-05-31" => "2016-07-01",
    "2016-06-30" => "2016-08-01",
    "2016-07-31" => "2016-08-14",
    "2016-08-13" => "2016-09-01",
    "2016-08-31" => "2016-10-01",
    "2016-09-30" => "2016-11-01",
    "2016-10-31" => "2016-12-01",
    "2016-11-30" => "2017-01-01",
    "2016-12-31" => "2017-02-01",
    "2017-01-31" => "2017-03-01",
    "2017-02-28" => "2017-04-01",
    "2017-03-31" => "2017-05-01",
    "2017-04-30" => "2017-05-31",
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
