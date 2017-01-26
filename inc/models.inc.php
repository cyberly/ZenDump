<?php
namespace ZenDump;

class Action extends \Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    public $table = 'event_actions';
}

class Agent extends \Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    public $table = 'agents';
}

class Attachment extends \Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    public $table = 'attachments';
}

class Comment extends \Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    public $table = 'comments';
}

class EndUser extends \Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    public $table = 'endusers';
}
class Error extends \Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    public $table = 'errors';
}

class Event extends \Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    public $table = 'ticket_events';
}

class Group extends \Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    public $table = 'groups';
}

class Membership extends \Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    public $table = 'group_memberships';
}

class Meta extends \Illuminate\database\Eloquent\Model {
    public $timestamps = false;
    public $table = 'meta';
}

class Ticket extends \Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    public $table = 'tickets';
}

class TicketList extends \Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    public $table = 'ticket_list';
}
 ?>
