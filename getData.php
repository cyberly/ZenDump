<?php
namespace ZenDump;
include("inc/curl.inc.php");
include("inc/threads.inc.php");

$startTime = microtime(true);
$worker = new ApiWorker();
$worker->start();
$threadId = 1;
$threads = 28;
$ticketList = array();
$job = new CreateJob(1, "full");
if($job->start()){
    $job->join();
}
$query = new QueryList(1);
if ($query->start()){
    $query->join();
    $ticketIds = $query->ticketList;
    foreach($ticketIds as $t){
        $ticketList[] = $t["id"];
    }
}
//$ticketList = array(1288184,1286761,1169149000,979845,805163,1207888,980757,1068198,943677,1288184);
//$ticketList = array(1277497);
$arrMax = count($ticketList);
$chunkSize = ceil($arrMax / $threads);
echo "Threads: $threads", PHP_EOL;
echo "Initial array: $arrMax", PHP_EOL;
echo "Chunk size: $chunkSize", PHP_EOL;
$chunkArray = array_chunk($ticketList, $chunkSize);
$pool = new \Pool($threads, ApiWorker::class);
foreach ($chunkArray as $tickets){
    $pool->submit(new ApiRequest($tickets, $threadId));
    $threadId++;
}
$pool->shutdown();
$endTime = round((microtime(true) - $startTime), 2);
$avgTime = round($endTime / $arrMax, 2);
echo "Total run time: $endTime seconds.", PHP_EOL;
echo "Processed $arrMax tickets, averaging $avgTime seconds per ticket.", PHP_EOL;
