<?php
namespace ZenDump;
include("inc/curl.inc.php");

set_time_limit(0);
$startTime = microtime(true);
$prod = new zdCurl("production");
$lastPage = FALSE;
$ticketCount = FALSE;
$searchArgs =   "type:ticket updated_at<2017-08-01 updated_at>2017-07-029 " .
                "status:pending status:solved -group:terminations " .
                "-group:security";
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
foreach ($chunkArray as $array){
  $ticketStr = join(',', $array);
  $endpoint = $updateMany . $ticketsStr;
  $commentBody = "butts lol"
  $payload = array(
    "ticket"            => array(
        "status"        =>  "open",
        "comment"       =>  array(
            "body"      =>  $commentBody,
            "public"    => FALSE
        ),
      "additional_tags" => array(
        "test_tag"
      )
    )
  );
  //$data = $prod->put($endpoint, json_encode($payload));
  //echo $prod->status, PHP_EOL;
  echo $ticketStr, PHP_EOL;
}
$endTime = round((microtime(true) - $startTime), 2);
echo "Closed $ticketTotal tickets in $endTime seconds.", PHP_EOL;
