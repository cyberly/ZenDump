<?php
namespace ZenDump;

class Helper{

    public function __constructor(){
        $lol = "wut";
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
