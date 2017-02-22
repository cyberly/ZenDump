<?php
namespace ZenDump;
require 'vendor/autoload.php';
include("inc/database.inc.php");
include("inc/models.inc.php");
include("inc/helper.inc.php");
include("inc/curl.inc.php");

$prod = new zdCurl("production");
$baseDir = "attachments";
$a = array( "attachment_id" => "1243",
            "ticket_id" => "1234",
            "file_name" => "lol.png",
            "content_url" => "https://liquidweb.zendesk.com/attachments/token/kWyW5Dxo7iIF9eWezLsOHtzjE/?name=unnamed_attachmewefweifwe"
        );
Helper::getAttachment($a, $baseDir);
