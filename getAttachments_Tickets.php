<?php
/**
*   Get ZD attachments, probably going to be threaded idk.
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

$worker = new ApiWorker();
$worker->start();
$threadId = 1;
$threads = 25;
$query = new QueryAttachments(1);
if ($query->start()){
    $query->join();
    $attachments = $query->attachments;
}
$arrMax = count($attachments);
$chunkSize = ceil($arrMax / $threads);
echo "Threads: $threads", PHP_EOL;
echo "Initial array: $arrMax", PHP_EOL;
echo "Chunk size: $chunkSize", PHP_EOL;
$chunkArray = array_chunk($attachments, $chunkSize);
$pool = new \Pool($threads, ApiWorker::class);
foreach ($chunkArray as $a){
    $pool->submit(new AttachmentWork($a, $threadId));
    $threadId++;
}
$pool->shutdown();
