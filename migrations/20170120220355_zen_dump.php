<?php

use \ZenDump\Migration\Migration;

class ZenDump extends Migration
{
    public $timestamps = false;
    public function up()
    {
        $this->schema->create('agents', function(Illuminate\Database\Schema\Blueprint $table){
            $table->engine = 'InnoDB';
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

        $this->schema->create('attachments', function(Illuminate\Database\Schema\Blueprint $table){
            $table->engine = 'InnoDB';
            $table->bigInteger('id');
            $table->bigInteger('ticket_id');
            $table->bigInteger('event_id');
            $table->bigInteger('action_id');
            $table->longText('file_name');
            $table->longText('url');
            $table->longText('content_url');
            $table->string('content_type');
            $table->primary('id');
            //May need to add a column for groups, but will need a separate API call.
            //$table->timestamps();
        });

        $this->schema->create('endusers', function(Illuminate\Database\Schema\Blueprint $table){
            $table->engine = 'InnoDB';
            $table->integer('id');
            $table->string('name');
            $table->string('email');
            $table->primary('id');
            //$table->timestamps();
        });

        $this->schema->create('errors', function(Illuminate\Database\Schema\Blueprint $table){
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('severity');
            $table->longText('request');
            $table->string('status');
            $table->dateTime('time');
            //$table->primary('id');
            //$table->timestamps();
        });

        $this->schema->create('event_actions', function(Illuminate\Database\Schema\Blueprint $table){
            $table->engine = 'InnoDB';
            $table->bigInteger('id');
            $table->bigInteger('event_id');
            $table->string('type');
            $table->string('field_name');
            $table->longText('value')->nullable();
            $table->longText('previous_value')->nullable();
            $table->longText('body');
            $table->boolean('public');
            $table->bigInteger('author_id');
            $table->string('channel')->nullable();
            $table->string('channel_id')->nullable();
            $table->text('channel_name')->nullable();
            $table->primary('id');
            //$table->timestamps();
        });

        $this->schema->create('groups', function(Illuminate\Database\Schema\Blueprint $table){
            $table->engine = 'InnoDB';
            $table->bigInteger('id');
            $table->string('name');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            //$table->primary('job_id');
            //$table->timestamps();
        });

        $this->schema->create('group_memberships', function(Illuminate\Database\Schema\Blueprint $table){
            $table->engine = 'InnoDB';
            $table->bigInteger('id');
            $table->bigInteger('user_id');
            $table->bigInteger('group_id');
            $table->boolean("default");
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            //$table->primary('job_id');
            //$table->timestamps();
        });

        $this->schema->create('meta', function(Illuminate\Database\Schema\Blueprint $table){
            $table->engine = 'InnoDB';
            $table->integer('job_id');
            $table->primary('job_id');
            //Not honestly sure what this is going ot be used for yet.
            //$table->timestamps();
        });

        $this->schema->create('tickets', function(Illuminate\Database\Schema\Blueprint $table){
            $table->engine = 'InnoDB';
            $table->bigInteger('id');
            $table->string('channel');
            $table->string('recieved_from');
            $table->bigInteger('submitter_id');
            $table->bigInteger('requester_id');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            $table->string('type');
            $table->longText('subject');
            $table->string("status");
            $table->primary('id');
            //$table->foreign('user_id')->references('id')->on('users');
            //$table->timestamps();
        });

        $this->schema->create('ticket_events', function(Illuminate\Database\Schema\Blueprint $table){
            $table->engine = 'InnoDB';
            $table->bigInteger('id');
            $table->bigInteger('ticket_id');
            $table->dateTime('created_at');
            $table->string('channel');
            $table->string('source_ip')->nullable();
            $table->primary('id');
            //$table->timestamps();
        });

        $this->schema->create('ticket_list', function(Illuminate\Database\Schema\Blueprint $table){
            $table->engine = 'InnoDB';
            $table->bigInteger('id');
            $table->primary('id');
            //$table->timestamps();
        });

    }
    public function down()
    {
        $this->schema->drop('tickets');
        $this->schema->drop('agents');
    }
}
