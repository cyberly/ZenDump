<?php
namespace ZenDump;

class ApiWorker extends \Worker {

    public function __construct() {
        $this->lol = "lol";
    }

    public function run() {}
}

class ApiRequest extends \Threaded {
    public $ticketId;
    public $ticketList;

    public function __construct($ticketList, $threadId){
        $this->ticketList = $ticketList;
        $this->threadId = $threadId;
    }

    public function run() {
        require 'vendor/autoload.php';
        include("inc/curl.inc.php");
        include("inc/database.inc.php");
        include("inc/models.inc.php");
        include("inc/helper.inc.php");
        $prod = new zdCurl("production");
        $sleepDefault = 3000000;
        if ($this->ticketList){
            $tCount = count($this->ticketList);
            foreach ($this->ticketList as $t){
                $searchId = $t;//->id;
                $lastPage = FALSE;
                $reqStart = microtime(true);
                $endpoint = "/tickets/$searchId/audits.json?include=users,groups,tickets";
                $errorCount = 0;
                while(!$lastPage){
                    $data = $prod->get($endpoint)->response;
                    if ($prod->status != "200"){
                        if ($errorCount <= 4) {
                            Helper::saveError("soft", $searchId, $prod->status);
                            usleep(500000);
                            $errorCount++;
                        } else {
                            Helper::saveError("hard", $searchId, $prod->status);
                            break;
                        }
                    } else {
                        //Let's build the ticket data.
                        $ticketData = $data["tickets"][0];
                        Helper::saveTicket($ticketData);
                        Helper::saveUser($data["users"]);
                        //Build event data to iterate through actions.
                        $events = $data["audits"];
                        Helper::saveEvents($events, $ticketData["id"]);
                        $reqTime = (microtime(true) - $reqStart) * 1000000;
                        if ($reqTime < $sleepDefault){
                            $sleepTime = $sleepDefault - $reqTime;
                            //echo "Sleep: $sleepTime, Req: $reqTime", PHP_EOL;
                            usleep($sleepTime);
                        }
                        if (!$data["next_page"]){
                            $lastPage = TRUE;
                        } else {
                            $endpoint = $data["next_page"];
                        }
                    }

                }

                //echo "Thread ID: " . $this->threadId . ", Ticket: $ticket", PHP_EOL;
                //$string = "Thread ID: " . $this->threadId . ", Ticket: $ticket";
                //file_put_contents($fileName, $string, FILE_APPEND);
                //echo $string, PHP_EOL;
                //Helper::saveError("soft", $this->threadId, $ticket);
            }
            //$sleepTime = mt_rand(1,10);
            //echo "Thread {$this->threadId} completed, sleeping for $sleepTime seconds.", PHP_EOL;
            //sleep($sleepTime);
            echo "Thread {$this->threadId} processed $tCount tickets, exiting.", PHP_EOL;
        }
    }
}

class TicketWork extends \Threaded{

    public function __construct() {

    }

    public function run() {
        
    }

}
