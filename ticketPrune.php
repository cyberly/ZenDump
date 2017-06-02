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
$pruned = 0;
$ticketList = Ticket::select("ticket_id", "tags")->get()->toArray();
echo "Parsing " . count($ticketList) .
    " tickets for unanswer monnotes.", PHP_EOL;
foreach ($ticketList as $t){
    $tagArray = str_getcsv($t["tags"]);
    if (!in_array("monitoring", $tagArray)){
        //Kill the iteration here if there's no monitoring tag.
        continue;
    }
    $comments = Comment::select(
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
            $role = User::select("role")
                ->where("user_id", "=", $id)
                ->get()
                ->toArray();
            if (isset($role["role"]) && $role["role"] == "end-user"){
                $customeReply = TRUE;
            }
        }
        if (!$customerReply){
            $pruned++;
            //echo "Ticket" . $t["ticket_id"] .
            //    "will be removed.", PHP_EOL;
            //Ticket::where("ticket_id", "=", $t["ticket_id"])->delete();
            //Comment::where("ticket_id", "=", $t["ticket_id"])->delete();
            //Attachment::where("ticket_id", "=", $t["ticket_id"])->delete();
        } else {
            echo "Ticket " . $t["ticket_id"] . "excluded from prune.", PHP_EOL;
        }
    }
}
echo "Pruned $pruned unanswered monnotes.", PHP_EOL;
