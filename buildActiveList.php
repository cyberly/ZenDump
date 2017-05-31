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
    //This entire range is less than 5k tickets.
/*    "2015-12-31" => "2016-02-01",
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
    "2017-01-31" => "2017-03-01", */
    "2015-12-31" => "2017-03-01",
    //These three need to split out way more refined.
    "2017-02-28" => "2017-03-08",
    "2017-03-07" => "2017-03-15",
    "2017-03-14" => "2017-03-22",
    "2017-03-21" => "2017-03-29",
    "2017-03-28" => "2017-04-05",
    "2017-04-04" => "2017-04-12",
    "2017-04-11" => "2017-04-19",
    "2017-04-18" => "2017-04-26",
    "2017-04-25" => "2017-05-03",
    "2017-05-02" => "2017-05-10",
    "2017-05-09" => "2017-05-17",
    "2017-05-16" => "2017-05-24",
    "2017-05-23" => "2017-05-31",
    "2017-05-30" => "2017-06-07",
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
