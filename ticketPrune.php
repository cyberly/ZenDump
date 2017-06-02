<?php
/**
*   Use to prune unwanted tickets from a built data sset.
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
require 'vendor/autoload.php';
include("inc/curl.inc.php");
include("inc/database.inc.php");
include("inc/models.inc.php");


/* Prune tickets with Monitoring tag without a customer response */
$testString =   "auth_by_customer_ticketlink,autoclose_disable," .
                "enterprise,system_credit_card_redaction,team_falcon," .
                "monitoring";

$tagArray = str_getcsv($testString);
$ticketList = Tickets::select("ticket_id", "tags")->get()->toArray();
foreach ($ticketList as $t){
    $tagArray = str_getcsv($t["tags"]);
    if (!in_array("monitoring", $tagArray)){
        //Kill the iteration here if there's no monitoring tag.
        continue;
    }
    $comments = Comments::select(
        "comment_id",
        "ticket_id",
        "public",
        "author_id")
        ->where([
            ['ticket_id', '=', $t["ticket_id"]],
            ['public', '=', 'TRUE']
            ])->get()->toArray();
    //Assume less than 2 commments is inactive
    //Check user role if more than two.
    if (count($comments) > 2){
        $customerReply = FALSE;
        foreach ($comments as $c){
            $id = $c["author_id"];
            $role = Users::select("role")
                ->where("user_id", "=", $id)
                ->get()
                ->toArray();
            if ($role["role"] == "end-user"){
                $customeReply = TRUE;
            }
        }
        if (!$customerReply){
            echo "Removing ticket" . $t["ticket_id"] .
                ", no customer replies.", PHP_EOL;
            //Tickets::where("ticket_id", "=", $t["ticket_id"])->delete();
            //Comments::where("ticket_id", "=", $t["ticket_id"])->delete();
            //Attachments::where("ticket_id", "=", $t["ticket_id"])->delete();
        }
    }
}
