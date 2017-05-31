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
$threads = 20;
$threadId = 1;
$dateArray = array(
    "2015-12-31" => "2016-01-22",
/*    "2016-01-21" => "2016-02-04",
    "2016-02-03" => "2016-02-16",
    "2016-02-15" => "2016-03-08",
    "2016-03-07" => "2016-04-01",
    "2016-03-31" => "2016-04-22",
    "2016-04-21" => "2016-05-12",
    "2016-05-11" => "2016-06-01",
    "2016-05-30" => "2016-06-21",
    "2016-06-20" => "2016-07-12",
    "2016-07-11" => "2016-08-02",
    "2016-08-01" => "2016-08-15",
    "2016-08-14" => "2016-08-22",
    "2016-08-21" => "2016-09-03",
    "2016-09-02" => "2016-09-13",
    "2016-09-12" => "2016-10-02",
    "2016-10-01" => "2016-10-22",
    "2016-10-21" => "2016-11-11",
    "2016-11-12" => "2016-12-02",
    "2016-12-01" => "2016-12-22",
    "2016-12-21" => "2017-01-12",
    "2017-01-11" => "2017-02-02",
    "2017-02-01" => "2017-02-22",
    "2017-02-21" => "2017-03-12",
    "2017-03-11" => "2017-04-02",
    "2017-04-01" => "2017-04-18",
    "2017-04-17" => "2017-06-02",
    //"2017-05-11" => "2017-06-02", */
);
foreach ($dateArray as $k => $v){
    $search = "type:ticket created>$k created<$v fieldvalue:accnt*" .
      " status:closed";
    $endpoint = "/search.json?query=" . urlencode($search);
    $pages[] = $endpoint;
    $data = $prod->get($endpoint)->response;
    $pageCount = ceil($data["count"] / 100);
    foreach (range(2, $pageCount) as $page){
        $endPage = "/search.json?page=$page&query=" . urlencode($search);
        $pages[] = $endPage;
        //echo $endpoint, PHP_EOL;
    }
    $chunkSize = ceil($pageCount / $threads);
    echo "$endpoint", PHP_EOL;
    echo "Pages: $pageCount, Threads: $threads, Chunk Size: $chunkSize", PHP_EOL;
    echo "Building ticket list...", PHP_EOL;
    echo " ", PHP_EOL;
    $chunkArray = array_chunk($pages, $chunkSize);
    $pool = new \Pool($threads, ApiWorker::class);
    foreach ($chunkArray as $searches){
        $pool->submit(new ApiRequest($searches, $threadId));
        $threadId++;
    }
    $pool->shutdown();
    //var_dump($pages);
    /*foreach($pages as $page){
        $result = $prod->get($page)->response;
        echo $page, PHP_EOL;
        echo $prod->status, PHP_EOL;
        //var_dump($result);
        echo " ", PHP_EOL;
    } */
    //echo $data["next_page"], PHP_EOL;
    //echo "Endpoint returned $pages with " . $data["count"] . "count.", PHP_EOL;
}
/*
$threads = count($dateArray);
$pool = new \Pool($threads, APIWorker::class);
foreach ($endpoints as $endpoint){
    $pool->submit(new ListWork($endpoint, $threadId, "TicketsClosed"));
    $threadId++;
}
$pool->shutdown();

$endTime = round((microtime(true) - $startTime), 2);
echo "Total run time: $endTime seconds.", PHP_EOL;
*/
