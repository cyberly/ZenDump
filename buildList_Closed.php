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
$job = new CreateJob(1, "full_active");
if($job->start()){
    $job->join();
}
$prod = new zdCurl("production");
$lastPage = FALSE;
$ticketCount = FALSE;
$threadId = 1;
$dateArray = array(
    "2015-12-31" => "2016-02-03", //25,527
    "2016-02-02" => "2016-03-04", //25,515
    "2016-03-03" => "2016-04-08", //25,353
    "2016-04-07" => "2016-05-09", //25,439
    "2016-05-08" => "2016-06-13", //25,383
    "2016-06-12" => "2016-07-20", //25,193
    "2016-07-19" => "2016-08-20", //25,137
    "2016-08-19" => "2016-09-01", //23,749
    "2016-08-31" => "2016-10-04", //25,775
    "2016-10-03" => "2016-11-08", //25,639
    "2016-11-07" => "2016-12-13", //25,137
    "2016-11-07" => "2016-12-13", //25,137
    "2016-12-12" => "2017-01-20", //25,339
    "2017-01-19" => "2017-02-19", //25,523
    "2017-02-18" => "2017-03-22", //25,523
    "2017-03-21" => "2017-04-07", //25,787
    "2017-04-06" => "2017-04-27", //25,260
    "2017-04-26" => "2017-05-24", //25,603
    "2017-05-23" => "2017-06-19", //25,249
    "2017-06-18" => "2017-07-14", //26,xxx
    "2017-07-13" => "2017-09-11", //24,914
    "2017-09-10" => "2017-09-30", //24,914

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
