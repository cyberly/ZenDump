<?php
namespace ZenDump;

class Helper{

    public function __constructor(){
        //stuff lol
        $stuff = "lol";
    }

    public static function saveAction($a, $e, $t_id){
        $action = Action::find($a["id"]);
        if ($action === NULL){
            $action = new Action;
        }
       $action->action_id = $a["id"];
       $action->event_id = $e["id"];
       $action->type = $a["type"];
       if ($a["type"] == "Comment"){
           $action->body = $a["body"];
           $actionPublic = $a["public"];
           $action->author_id = $a["author_id"];
           if(!empty($a["attachments"])){
               foreach($a["attachments"] as $att){
                   $attachment = Attachment::find($att["id"]);
                   if ($attachment === NULL){
                       $attachment = new Attachment;
                       $attachment->attachment_id = $att["id"];
                       $attachment->ticket_id = $t_id;
                       $attachment->event_id = $e["id"];
                       $attachment->action_id = $action->id;
                       $attachment->file_name = $att["file_name"];
                       $attachment->url = $att["url"];
                       $attachment->content_url = $att["content_url"];
                       $attachment->content_type = $att["content_type"];
                       $attachment->save();
                       unset($a);
                       unset($attachment);
                   }
               }
           }
           $action->save();
           return TRUE;
       }
       if ($a["type"] == "Create" || $a["type"] == "Change"){
           $action->field_name = $a["field_name"];
           if (is_array($a["value"])) {
               $action->value = join(',', $a["value"]);
           } else {
               $action->value = $a["value"];
           }
           if ($a["type"] == "Change"){
               if (is_array($a["previous_value"])){
                   $action->previous_value = join(',',$a["previous_value"]);
               } else {
                   $action->previous_value = $a["previous_value"];
               }
           }
           if (isset($a["via"])){
               if ($a["via"]["channel"] == "rule"){
                   if(isset($a["via"]["source"]["rel"])){
                       $action->channel = $a["via"]["source"]["rel"];
                   }
                   $action->channel_id = $a["via"]["source"]["from"]["id"];
                   $action->channel_name = $a["via"]["source"]["from"]["title"];
               }
           }
           if ($e["via"]["channel"] == "rule"){
               if(isset($e["via"]["source"]["rel"])){
                   $action->channel = $e["via"]["source"]["rel"];
               }
               $action->channel_id = $e["via"]["source"]["from"]["id"];
               $action->channel_name = $e["via"]["source"]["from"]["title"];
           }
           $action->save();
           return TRUE;
       } else {
           return FALSE;
       }
       //Save the event here to prevent saving empty events if this doesn't trip.
    }
}
