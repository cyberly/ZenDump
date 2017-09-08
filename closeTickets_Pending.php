<?php
namespace ZenDump;
include("inc/curl.inc.php");

set_time_limit(0);
$startTime = microtime(true);
$prod = new zdCurl("production");
$lastPage = FALSE;
$ticketCount = FALSE;
$searchArgs = "type:ticket created>2015-12-31 updated_at<2017-08-01 " .
              "status:pending -tags:lwsupervisor";
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
$chunkArray = array_chunk($tickets, 1);
foreach ($chunkArray as $array){
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
        "test_tag", "lw_close_silent"
      )
    )
  );
  $data = $prod->put($endpoint, json_encode($payload));
}
$endTime = round((microtime(true) - $startTime), 2);
echo "Closed $ticketTotal tickets in $endTime seconds.", PHP_EOL;
