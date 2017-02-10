<?php
namespace ZenDump;

class Attachment extends \Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    public $table = 'attachments';
    public $primaryKey = 'attachment_id';
}

class Comment extends \Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    public $table = 'comments';
    public $primaryKey = 'comment_id';
}

class Error extends \Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    public $table = 'errors';
}

class Group extends \Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    public $table = 'groups';
    public $primaryKey = 'group_id';
}

class Membership extends \Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    public $table = 'group_memberships';
    public $primaryKey = 'membership_id';
}

class Meta extends \Illuminate\database\Eloquent\Model {
    public $timestamps = false;
    public $table = 'meta';
}

class Ticket extends \Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    public $table = 'tickets';
    public $primaryKey = 'ticket_id';
}

class TicketList extends \Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    public $table = 'ticket_list';
}

class Trigger extends \Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    public $table = 'triggers';
    public $primaryKey = 'trigger_id';
}

class TriggerAction extends \Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    public $table = 'trigger_actions';
}

class TriggerCondition extends \Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    public $table = 'trigger_conditions';
}

class User extends \Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    public $table = 'users';
    public $primaryKey = 'user_id';
}
 ?>
