<?php
require 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

$dbFile = ".db.env";
$dbInfo = json_decode(file_get_contents($dbFile), true);
$capsule = new Capsule;

$capsule->addConnection(array(
    'driver'    => 'mysql',
    'host'      => $dbInfo["host"],
    'port'      => $dbInfo["port"],
    'database'  => $dbInfo["db"],
    'username'  => $dbInfo["user"],
    'password'  => $dbInfo["pass"],
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
));

$capsule->bootEloquent();
