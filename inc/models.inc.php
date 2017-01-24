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
 ?>
