<?php
namespace ZenDump;
//require('inc/threads.inc.php');
//include("inc/database.inc.php");
require 'vendor/autoload.php';
include("inc/curl.inc.php");
include("inc/database.inc.php");
include("inc/models.inc.php");
include("inc/helper.inc.php");
include("inc/threads.inc.php");

/*
$ticketArray = array(
                array(1169149000,979845,805163,1207888,980757,1068198,943677,987127),
                array(911438,1227778,980996,1177379),
                array(981052,919449,981310,869705,981340,1015747,981448,981540));
*/
$startTime = microtime(true);
$worker = new ApiWorker();
$worker->start();
$threadId = 1;
$threads = 28;
//$ticketList = array(1169149,979845,805163,1207888,980757,1068198,943677,987127,911438,1227778,980996,1177379,981052,919449,981310,869705,981340,1015747,981448,981540,919073,981558,979765,1261293,920602,1009966,981592,981606,981618,981636,981639,994504,981650,1129955,1229079,981668,1070670,981672,981681,1012302,944061,808883,1248830,1208022,981687,981692,807424,1071337,981700,981718);
$ticketList = TicketList::select("id")->get();
$arrMax = count($ticketList);
$chunkSize = ceil($arrMax / $threads);
echo "Threads: $threads", PHP_EOL;
echo "Initial array: $arrMax", PHP_EOL;
echo "Chunk size: $chunkSize", PHP_EOL;
$chunkArray = array_chunk($ticketList, $chunkSize);

$taskArray = array();
$pool = new \Pool($threads, ApiWorker::class);
//var_dump($ticketArray);
foreach ($chunkArray as $tickets){
    $pool->submit(new ApiRequest($tickets, $threadId));

    /*
    $taskArray[$threadId] = new ApiRequest($tickets, $threadId);
    //var_dump($taskArray[$threadId]);
    $worker->stack($taskArray[$threadId]);
    */
    $threadId++;
}
//echo $worker->getStacked(), PHP_EOL;
//var_dump($worker);
//$worker->shutdown();
//echo "Worker has shut down.", PHP_EOL;
//var_dump($taskArray);
$pool->shutdown();
$endTime = round((microtime(true) - $startTime), 2);
$avgTime = round($endTime / $arrMax, 2);
echo "Total run time: $endTime seconds.", PHP_EOL;
echo "Processed $arrMax tickets, averaging $avgTime seconds per ticket.", PHP_EOL;
