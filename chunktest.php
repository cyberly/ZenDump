<?php
namespace ZenDump;
require 'vendor/autoload.php';
include("inc/curl.inc.php");
include("inc/database.inc.php");
include("inc/models.inc.php");
include("inc/helper.inc.php");
        use lluminate\Support\Str;
set_time_limit(0);

class ZenDemon extends \Thread {
    private $lockFile;

    public function __construct($thread) {
        $this->thread = $thread;
    }

    public function run() {

        if ($this->thread) {
            $sleep = mt_rand(1, 10);
            printf('%s: %s  -start -sleeps %d' . "\n", date("g:i:sa"), $this->thread, $sleep);
            sleep($sleep);
            printf('%s: %s  -finish' . "\n", date("g:i:sa"), $this->thread);
            $searchId = 1234;
            $status = "500";
            Helper::saveError("soft", $searchId, $status);
        }
    }



}

$array = array();
foreach (range(1,309076) as $num){
    $array[] = $num;
}
$threads = 28;
$arrMax = count($array);
$chunkSize = ceil($arrMax / $threads);
echo "Initial array: $arrMax", PHP_EOL;
echo "Chunk size: $chunkSize", PHP_EOL;
$chunkArray = array_chunk($array, $chunkSize);

$procTable = array();
foreach(range(1,$threads) as $thread){
    $procTable[] = new ZenDemon($thread);
}
//var_dump($procTable);

foreach ($procTable as $proc){
    $proc->start();
}
