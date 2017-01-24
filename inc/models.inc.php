<?php
namespace ZenDump;

class Agent extends \Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    public $table = 'agents';
}

class EndUser extends \Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    public $table = 'endusers';
}

class Meta extends \Illuminate\database\Eloquent\Model {
    public $timestamps = false;
    public $table = 'meta';
}

class Ticket extends \Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    public $table = 'tickets';
}
 ?>
