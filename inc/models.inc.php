<?php
namespace ZenDump;

class Action extends \Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    public $table = 'event_actions';
    public $primaryKey = 'action_id';
}

class Attachment extends \Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    public $table = 'attachments';
    public $primaryKey = 'attachment_id';
}

class Error extends \Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    public $table = 'errors';
}

class Event extends \Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    public $table = 'ticket_events';
    public $primaryKey = 'event_id';
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

class User extends \Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    public $table = 'users';
    public $primaryKey = 'user_id';
}
 ?>
