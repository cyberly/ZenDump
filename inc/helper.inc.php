<?php
namespace ZenDump;

class Helper{

    public function __constructor(){
        $lol = "wut";
    }

    public static function dumpJson($array, $folder){
        if (!file_exists("./dumps/$folder")){
            mkdir("./dumps/$folder", 0755);
        }
        $s =   array(" ","::",":","(",")","/");
        $r =  array("_","-"."","","","_");
        $file = "./dumps/$folder/" . (str_replace($s, $r, strtolower($array["title"])));
        $string = json_encode($array, JSON_PRETTY_PRINT);
        file_put_contents($file, $string);
    }

    public static function getAttachment($a, $baseDir){
        $prod = new zdCurl("production");
        $sleepDefault = 3000000;
        $reqStart = microtime(true);
        $url = $a["content_url"];
        $file = $a["file_name"];
        $ticketId = $a["ticket_id"];
        $attachmentId = $a["attachment_id"];
        $dir = $baseDir . "/" . $ticketId;
        $path = $baseDir . "/" . $ticketId . "/" . $attachmentId . "-" . $file;
        if (file_exists($path)){
            return 0;
        }
        $errorCount = 0;
        while ($prod->status != "200"){
            $fileData = $prod->getFile($url)->response;
            if ($prod->status != 200){
                Helper::saveError("att-soft", $attachmentId, $prod->status);
                $errorCount++;
                sleep(2);
            }
            if ($errorCount >= 4){
                Helper::saveError("att-hard", $attachmentId, $prod->status);
                continue;
            }
        }
        if (!file_exists($dir)){
            mkdir($dir, 0755, true);
        }
        $fp = fopen($path, "w");
        fwrite($fp, $fileData);
        fclose($fp);
        Helper::saveError("att-saved", $attachmentId, $prod->status);
        $reqTime = (microtime(true) - $reqStart) * 1000000;
        if ($reqTime < $sleepDefault){
            $sleepTime = $sleepDefault - $reqTime;
            usleep($sleepTime);
        }
        //var_dump($prod);
    }

    public static function saveComments ($events_array, $t_id){
        foreach ($events_array as $t_event){
            foreach ($t_event["events"] as $a){
                if ($a["type"] == "Comment"){
                    $comment = Comment::find($a["id"]);
                    if ($comment === NULL){
                        $comment = new Comment;
                        $comment->comment_id = $a["id"];
                        $comment->ticket_id = $t_id;
                        $comment->body = $a["body"];
                        $comment->public = $a["public"];
                        $comment->author_id = $a["author_id"];
                        $comment->created_at = $t_event["created_at"];
                        if(!empty($a["attachments"])){
                            foreach($a["attachments"] as $att){
                                $attachment = Attachment::find($att["id"]);
                                if ($attachment === NULL){
                                    $attachment = new Attachment;
                                    $attachment->attachment_id = $att["id"];
                                    $attachment->ticket_id = $t_id;
                                    //$attachment->event_id = $t_event["id"];
                                    $attachment->comment_id = $a["id"];
                                    $attachment->file_name = $att["file_name"];
                                    $attachment->url = $att["url"];
                                    $attachment->content_url = $att["content_url"];
                                    $attachment->size = $att["size"];
                                    $attachment->content_type = $att["content_type"];
                                    $attachmentSaved = $attachment->save();
                                    if ($attachmentSaved){
                                        $comment->has_attachments = TRUE;
                                    }
                                }
                            }
                        }
                        $comment->save();
                    }
                }
            }
        }
    }

    public static function saveError ($severity, $id, $status){
        $error = new Error;
        $error->severity = $severity;
        $error->request = $id;
        $error->status = $status;
        $error->time = date("Y-m-d H:i:s");
        $error->save();
    }

    public static function saveMacro ($m){
        $macro = Macro::find($m["id"]);
        if ($macro === NULL){
            $macro = new Macro;
            $macro->macro_id = $m["id"];
        }
        $macro->title = $m["title"];
        $macro->active = $m["active"];
        $macro->position = $m["position"];
        $macro->description = $m["description"];
        $macro->created_at = $m["created_at"];
        $macro->updated_at = $m["updated_at"];
        $macro->save();
        foreach ($m["actions"] as $a){
            $action = new MacroAction;
            $action->macro_id = $m["id"];
            $action->field = $a["field"];
            if ($action->field == "comment_value" && is_array($a["value"])){
                $action->value = $a["value"][1];
                $action->channel = $a["value"][0];
            } else {
                $action->value = $a["value"];
            }
            $action->save();
        }
        if (is_array($m["restriction"])){
            $restriction = new MacroRestriction;
            $restriction->macro_id = $m["id"];
            $restriction->type = $m["restriction"]["type"];
            $restriction->allowed_id = $m["restriction"]["id"];
            $restriction->save();
        }
    }

    public static function saveTicket($ticket_array){
        $ticket = Ticket::find($ticket_array["id"]);
        if ($ticket === NULL){
            $ticket = new Ticket;
            $ticket->ticket_id = $ticket_array["id"];
            $ticket->channel = $ticket_array["via"]["channel"];
            if ($ticket->channel == "email"){
                $ticket->recieved_from = $ticket_array["via"]["source"]["from"]["address"];
            }
            $ticket->created_at = $ticket_array["created_at"];
        }
        $ticket->status = $ticket_array["status"];
        $ticket->updated_at = $ticket_array["updated_at"];
        $ticket->type = $ticket_array["type"];
        $ticket->subject = $ticket_array["subject"];
        $ticket->submitter_id = $ticket_array["submitter_id"];
        $ticket->requester_id = $ticket_array["requester_id"];
        $ticket->group_id = $ticket_array["group_id"];
        $ticket->assignee_id = $ticket_array["assignee_id"];
        foreach ($ticket_array["custom_fields"] as $field){
            if ($field["id"] == "23268158"){
                $ticket->account = $field["value"];
            }
        }
        if (is_array($ticket_array["tags"])) {
            $ticket->tags = join(',', $ticket_array["tags"]);
        }
        $ticket->description = $ticket_array["description"];
        $ticket->priority = $ticket_array["priority"];
        if (isset($ticket_array["satisfaction_rating"]["score"])){
            $ticket->satisfaction_score = $ticket_array["satisfaction_rating"]["score"];
        }
        if (isset($ticket_array["satisfaction_rating"]["comment"])){
            $ticket->satisfaction_comment = $ticket_array["satisfaction_rating"]["comment"];
        }
        $ticket->is_public = $ticket_array["is_public"];
        $ticket->save();
    }

    public static function saveRule($a, $type){
        if ($type == "trigger"){
            $rule = Trigger::find($a["id"]);
            if ($rule === NULL){
                $rule = new Trigger;
                $rule->trigger_id = $a["id"];
            }
        }
        if ($type == "automation"){
            $rule = Automation::find($a["id"]);
            if ($rule === NULL){
                $rule = new Automation;
                $rule->automation_id = $a["id"];
            }
        }
        $rule->title = $a["title"];
        $rule->active = $a["active"];
        $rule->updated_at = $a["updated_at"];
        $rule->created_at = $a["created_at"];
        $rule->position = $a["position"];
        $rule->save();
        foreach ($a["conditions"]["all"] as $c){
            if ($type == "trigger"){
                $condition = new TriggerCondition;
                $condition->trigger_id = $a["id"];
            }
            if ($type == "automation"){
                $condition = new AutomationCondition;
                $condition->automation_id = $a["id"];
            }
            $condition->type = "all";
            $condition->field = $c["field"];
            $condition->operator = $c["operator"];
            $condition->value = $c["value"];
            $condition->save();
        }
        foreach ($a["conditions"]["any"] as $c){
            if ($type == "trigger"){
                $condition = new TriggerCondition;
                $condition->trigger_id = $a["id"];
            }
            if ($type == "automation"){
                $condition = new AutomationCondition;
                $condition->automation_id = $a["id"];
            }
            $condition->type = "any";
            $condition->field = $c["field"];
            $condition->operator = $c["operator"];
            $condition->value = $c["value"];
            $condition->save();
        }
        foreach ($a["actions"] as $act){
            //echo "Trigger ID: " . $t["id"] . " Field: " . $a["field"], PHP_EOL;
            if ($type == "trigger"){
                $action = new TriggerAction;
                $action->trigger_id = $a["id"];
            }
            if ($type == "automation"){
                $action = new AutomationAction;
                $action->automation_id = $a["id"];
            }
            $action->field = $act["field"];
            if (is_array($act["value"])) {
                if ($act["field"] == "notification_target"){
                    if (is_array($act["value"][1])){
                        $action->value = implode(',', $act["value"][1][0]);
                    } else {
                        $action->value = $act["value"][1];
                        $action->recipient = $act["value"][0];
                    }
                } else {
                    $action->value = $act["value"][2];
                    $action->recipient = $act["value"][0];
                    $action->subject = $act["value"][1];
                }
            } else {
                $action->value = $act["value"];
            }
            $action->save();
        }
    }


    public static function saveUser($user_array){
        foreach ($user_array as $u) {
            if ($u["role"] == "end-user") {
                $user = User::find($u["id"]);
                if ($user === NULL) {
                    $user = new User;
                }
                    $user->user_id = $u["id"];
                    $user->name = $u["name"];
                    $user->email = $u["email"];
                    $user->role = $u["role"];
                    $user->save();
            }
        }
    }

    public static function startJob($type){
        $job = new Meta;
        $epoch = new \DateTime();
        $job->type = $type;
        $job->start_time = $epoch->format('U');
        $job->save();
    }
}
