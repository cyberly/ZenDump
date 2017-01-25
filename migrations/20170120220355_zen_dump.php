<?php

use \ZenDump\Migration\Migration;

class ZenDump extends Migration
{
    public $timestamps = false;
    public function up()
    {
        $this->schema->create('agents', function(Illuminate\Database\Schema\Blueprint $table){
            $table->bigInteger('id');
            $table->string('name');
            $table->string('alias');
            $table->string('email');
            $table->string('role');
            $table->text('signature');
            $table->boolean('suspended');
            $table->primary('id');
            //May need to add a column for groups, but will need a separate API call.
            //$table->timestamps();
        });

        $this->schema->create('endusers', function(Illuminate\Database\Schema\Blueprint $table){
            $table->integer('id');
            $table->string('name');
            $table->string('email');
            $table->primary('id');
            //$table->timestamps();
        });

        $this->schema->create('event_actions', function(Illuminate\Database\Schema\Blueprint $table){
            $table->bigInteger('action_id');
            $table->bigInteger('event_id');
            $table->string('type');
            $table->string('field_name');
            $table->longText('value');
            $table->longText('previous_value');
            $table->longText('body');
            $table->boolean('public');
            $table->dateTime('created_at');
            $table->bigInteger('author_id');
            $table->primary('action_id');
            //$table->timestamps();
        });

        $this->schema->create('meta', function(Illuminate\Database\Schema\Blueprint $table){
            $table->integer('job_id');
            $table->primary('job_id');
            //Not honestly sure what this is going ot be used for yet.
            //$table->timestamps();
        });

        $this->schema->create('tickets', function(Illuminate\Database\Schema\Blueprint $table){
            $table->bigInteger('ticket_id');
            $table->string('channel');
            $table->string('recieved_from');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            $table->string('type');
            $table->longText('subject');
            $table->primary('ticket_id');
            //$table->timestamps();
        });

        $this->schema->create('ticket_events', function(Illuminate\Database\Schema\Blueprint $table){
            $table->bigInteger('event_id');
            $table->bigInteger('ticket_id');
            $table->dateTime('created_at');
            $table->string('channel');
            $table->string('source_ip');
            $table->primary('event_id');
            //$table->timestamps();
        });

    }
    public function down()
    {
        $this->schema->drop('tickets');
        $this->schema->drop('agents');
    }
}
