<?php
namespace ZenDump;
include("inc/curl.inc.php");

set_time_limit(0);
$startTime = microtime(true);
$prod = new zdCurl("production");
$lastPage = FALSE;
$ticketCount = FALSE;
$searchArgs = "type:ticket created>2015-12-31 updated_at<96hours " .
              "status:solved -tags:lwsupervisor -tags:customer-advocacy";
$search = "/search.json?query=" . urlencode($searchArgs);
$updateMany = "/tickets/update_many.json?ids=";
while(!$lastPage){
    $data = $prod->get($search)->response;
    if (!$ticketCount){
      $ticketCount = $data["count"];
      echo "Processing " . $ticketCount . " tickets.", PHP_EOL;
    }
    foreach ($data["results"] as $ticket){
      $tickets[] = $ticket["id"];
    }
    if (!$data["next_page"]){
        $lastPage = TRUE;
    } else {
        $search = $data["next_page"];
    }
}
$ticketTotal = count($tickets);
$searchTime = round((microtime(true) - $startTime), 2);
echo "Retrieved $ticketTotal tickets in $searchTime seconds.", PHP_EOL;
$chunkArray = array_chunk($tickets, 100);
$arrays = count($chunkArray);
$processedArray = 1;
foreach ($chunkArray as $array){
  echo "Processing ticket set $processedArray of $arrays.", PHP_EOL;
  $ticketsStr = join(',', $array);
  $endpoint = $updateMany . $ticketsStr;
  $commentBody =  "This ticket has been closed in preparation of upcoming " .
                  "SFDC migration.";
  $payload = array(
    "ticket"            => array(
        "status"        =>  "closed",
        "comment"       =>  array(
            "body"      =>  $commentBody,
            "public"    => FALSE
        ),
      "additional_tags" => array(
        "lw_close_silent"
      )
    )
  );
  $data = $prod->put($endpoint, json_encode($payload));
  $processedArray++;
}
$endTime = round((microtime(true) - $startTime), 2);
echo "Closed $ticketTotal tickets in $endTime seconds.", PHP_EOL;
