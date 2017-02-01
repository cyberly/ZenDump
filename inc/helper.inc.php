<?php
namespace ZenDump;

class Helper{

    public function __constructor(){
        $lol = "wut";
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

    public static function saveError ($severity, $id, $status){
        $error = new Error;
        $error->severity = $severity;
        $error->request = $id;
        $error->status = $status;
        $error->time = date("Y-m-d H:i:s");
        $error->save();
    }

    public static function saveEvents ($events_array, $id){
        foreach ($events_array as $t_event){
            $event = Event::find($t_event["id"]);
            if ($event === NULL){
                $event = new Event;
            }
            $event->event_id = $t_event["id"];
            $event->ticket_id = $t_event["ticket_id"];
            $event->created_at = $t_event["created_at"];
            $event->channel = $t_event["via"]["channel"];
            if (isset($t_event["metadata"]["system"]["ip_address"])){
                $event->source_ip = $t_event["metadata"]["system"]["ip_address"];
            }
            //$ticketId needs to go in the events table here as well.
            foreach ($t_event["events"] as $t_action){
                $actionSaved = Helper::saveAction($t_action, $t_event, $id);
                if ($actionSaved){
                    $event->save();
                }
            }
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
            $ticket->subject = $ticket_array["subject"];
            $ticket->submitter_id = $ticket_array["submitter_id"];
            $ticket->requester_id = $ticket_array["requester_id"];
            $ticket->group_id = $ticket_array["group_id"];
            $ticket->assignee_id = $ticket_array["assignee_id"];
        }
        $ticket->status = $ticket_array["status"];
        $ticket->updated_at = $ticket_array["updated_at"];
        $ticket->type = $ticket_array["type"];
        $ticket->save();
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
}
