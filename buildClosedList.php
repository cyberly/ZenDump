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
Helper::startJob("full_closed");
$startTime = microtime(true);
$prod = new zdCurl("production");
$lastPage = FALSE;
$ticketCount = FALSE;
$threadId = 1;
$dateArray = array(
    "2015-12-31" => "2016-01-19", //12,250
    "2016-01-18" => "2016-02-02", //12,199
    "2016-02-01" => "2016-02-18", //12,764
    "2016-02-17" => "2016-03-03", //12,640
    "2016-03-02" => "2016-03-20", //12,544
    "2016-03-19" => "2016-04-06", //12,183
    "2016-04-05" => "2016-04-24", //12,275
    "2016-04-23" => "2016-05-06", //12,811
    "2016-05-05" => "2016-05-23", //12,186
    "2016-05-22" => "2016-06-09", //12,381
    "2016-06-08" => "2016-06-26", //12,186
    "2016-06-25" => "2016-07-15", //12,319
    "2016-07-14" => "2016-07-31", //12,193
    "2016-07-30" => "2016-08-17", //12,173
    "2016-08-16" => "2016-08-31", //11,272
    "2016-08-30" => "2016-09-01", //15,923
    "2016-08-31" => "2016-09-15", //12,030
    "2016-09-14" => "2016-10-02", //12,372
    "2016-10-01" => "2016-10-20", //12,503
    "2016-10-19" => "2016-11-05", //12,298
    "2016-11-04" => "2016-11-23", //12,608
    "2016-11-22" => "2016-12-10", //12,335
    "2016-12-09" => "2016-12-29", //12,121
    "2016-12-28" => "2017-01-15", //12,319
    "2017-01-14" => "2017-01-30", //11,833
    "2017-01-29" => "2017-02-13", //12,312
    "2017-02-12" => "2017-02-28", //11,988
    "2017-02-27" => "2017-03-15", //12,275
    "2017-03-14" => "2017-03-29", //11,983
    "2017-03-28" => "2017-04-12", //12,554
    "2017-04-11" => "2017-04-26", //12,375
    "2017-04-25" => "2017-06-02", //4,645
);
foreach ($dateArray as $k => $v){
    $search = "type:ticket created>$k created<$v fieldvalue:accnt*" .
      " status:closed";
    $endpoints[] = "/search.json?query=" . urlencode($search);
}
$threads = count($dateArray);
$pool = new \Pool($threads, APIWorker::class);
foreach ($endpoints as $endpoint){
    $pool->submit(new ListWork($endpoint, $threadId, "TicketsClosed"));
    $threadId++;
}
$pool->shutdown();

$endTime = round((microtime(true) - $startTime), 2);
echo "Total run time: $endTime seconds.", PHP_EOL;
