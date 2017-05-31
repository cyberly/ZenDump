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
        require_once 'vendor/autoload.php';
        include_once("inc/database.inc.php");
        include_once("inc/models.inc.php");
        include_once("inc/helper.inc.php");
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
                        $ticketData = $data["tickets"][0];
                        Helper::saveTicket($ticketData);
                        Helper::saveUser($data["users"]);
                        $events = $data["audits"];
                        Helper::saveComments($events, $ticketData["id"]);
                        $reqTime = (microtime(true) - $reqStart) * 1000000;
                        if ($reqTime < $sleepDefault){
                            $sleepTime = $sleepDefault - $reqTime;
                            usleep($sleepTime);
                        }
                        if (!$data["next_page"]){
                            $lastPage = TRUE;
                        } else {
                            $endpoint = $data["next_page"];
                        }
                    }

                }
            }
            echo "Thread {$this->threadId} processed $tCount tickets," .
            " exiting.", PHP_EOL;
        }
    }
}

class ListWork extends \Threaded{
    public $threadId;
    public $endpoint;
    public $listObj;

    public function __construct($endpoint, $threadId, $listObj) {
        $this->endpoint = $endpoint;
        $this->threadId = $threadId;
        $this->listObj = $listObj;
        $this->lastPage = FALSE;
    }

    public function run() {
        require 'vendor/autoload.php';
        include("inc/database.inc.php");
        include("inc/models.inc.php");
        include("inc/helper.inc.php");
        $prod = new zdCurl("production");
        $sleepDefault = 4000000;
        $errorCount = 0;
        while(!$this->lastPage){
            $reqStart = microtime(true);
            $data = $prod->get($this->endpoint)->response;
            if ($prod->status != "200"){
                if ($errorCount <= 4) {
                    Helper::saveError("soft", $this->endpoint, $prod->status);
                    usleep($sleepDefault);
                    $errorCount++;
                } else {
                    Helper::saveError("hard", $this->endpoint, $prod->status);
                    $errorCount = 0;
                    usleep($sleepDefault);
                    continue;
                }
            } else {
                if (!$this->ticketCount){
                    $this->ticketCount = $data["count"];
                    echo "Thread " . $this->threadId . "processing " .
                        $this->ticketCount . "tickets.";
                }
                foreach($data["results"] as $t){
                    $listType = "ZenDump\\" . $this->listObj;
                    $ticket = $listType::find($t["id"]);
                    if ($ticket === NULL){
                        $ticket = new $listType;
                        $ticket->id = $t["id"];
                        $ticket->save();
                    }
                }
                if (!$data["next_page"]){
                    $this->lastPage = TRUE;
                } else {
                    $this->endpoint = $data["next_page"];
                }
                $reqTime = (microtime(true) - $reqStart) * 1000000;
                if ($reqTime < $sleepDefault){
                    $sleepTime = $sleepDefault - $reqTime;
                    usleep($sleepTime);
                }
            }
        }
        echo "Thread " . $this->threadId .
          " completed, processed " . $this->ticketCount . " tickets.", PHP_EOL;
    }

}

class QueryList extends \Thread {
    public $ticketList;

    public function __construct($threadId, $listType) {
        $this->threadId = $threadId;
        $this->listType = $listType;
    }

    public function run() {
        require 'vendor/autoload.php';
        include("inc/database.inc.php");
        include("inc/models.inc.php");
        include("inc/helper.inc.php");

        $listType = "ZenDump\\" . $this->listType;
        $this->ticketList = $listType::select("id")->get()->toArray();
        //$this->ticketList = Error::select("id")->where('severity', '=', 'hard')->get()->toArray();
    }
}

class CreateJob extends \Thread {
    public $ticketList;

    public function __construct($threadId, $type) {
        $this->threadId = $threadId;
        $this->type = $type;
    }

    public function run() {
        require 'vendor/autoload.php';
        include("inc/database.inc.php");
        include("inc/models.inc.php");
        include("inc/helper.inc.php");

        Helper::startJob($this->type);
    }
}

class QueryAttachments extends \Thread {
    public $ticketList;

    public function __construct($threadId) {
        $this->threadId = $threadId;
    }

    public function run() {
        require 'vendor/autoload.php';
        include("inc/database.inc.php");
        include("inc/models.inc.php");
        include("inc/helper.inc.php");

        $this->attachments = Attachment::select('attachment_id', 'ticket_id', 'file_name', 'content_url')
                                ->get()
                                ->toArray();
    }
}

class AttachmentWork extends \Threaded{
    public $threadId;

    public function __construct($attachments, $threadId) {
        $this->attachments = $attachments;
        $this->threadId = $threadId;
    }

    public function run() {
        require 'vendor/autoload.php';
        include("inc/database.inc.php");
        include("inc/models.inc.php");
        include("inc/helper.inc.php");
        $prod = new zdCurl("production");
        $baseDir = "attachments";
        foreach($this->attachments as $a){
            Helper::getAttachment($a, $baseDir);
        }
    }
}
