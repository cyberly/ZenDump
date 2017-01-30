<?php

use \ZenDump\Migration\Migration;

class ZenDump extends Migration
{
    public $timestamps = false;
    public function up()
    {
        $this->schema->create('attachments', function(Illuminate\Database\Schema\Blueprint $table){
            $table->engine = 'InnoDB';
            $table->bigInteger('attachment_id');
            $table->bigInteger('ticket_id')->nullable();
            $table->bigInteger('event_id')->nullable();
            $table->bigInteger('action_id')->nullable();
            $table->longText('file_name')->nullable();
            $table->longText('url')->nullable();
            $table->longText('content_url')->nullable();
            $table->string('content_type')->nullable();
            $table->primary('attachment_id');
            //May need to add a column for groups, but will need a separate API call.
            //$table->timestamps();
        });

        $this->schema->create('errors', function(Illuminate\Database\Schema\Blueprint $table){
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('severity')->nullable();
            $table->longText('request')->nullable();
            $table->string('status')->nullable();
            $table->dateTime('time')->nullable();
            //$table->primary('id');
            //$table->timestamps();
        });

        $this->schema->create('event_actions', function(Illuminate\Database\Schema\Blueprint $table){
            $table->engine = 'InnoDB';
            $table->bigInteger('action_id');
            $table->bigInteger('event_id')->nullable();
            $table->string('type')->nullable();
            $table->string('field_name')->nullable();
            $table->longText('value')->nullable();
            $table->longText('previous_value')->nullable();
            $table->longText('body')->nullable();
            $table->boolean('public')->nullable();
            $table->bigInteger('author_id')->nullable();
            $table->string('channel')->nullable();
            $table->string('channel_id')->nullable();
            $table->text('channel_name')->nullable();
            $table->primary('action_id')->nullable();
            //$table->timestamps();
        });

        $this->schema->create('groups', function(Illuminate\Database\Schema\Blueprint $table){
            $table->engine = 'InnoDB';
            $table->bigInteger('group_id');
            $table->string('name')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->primary('group_id');
            //$table->timestamps();
        });

        $this->schema->create('group_memberships', function(Illuminate\Database\Schema\Blueprint $table){
            $table->engine = 'InnoDB';
            $table->bigInteger('membership_id');
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('group_id')->nullable();
            $table->boolean("default")->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->primary('membership_id');
            //$table->timestamps();
        });

        $this->schema->create('meta', function(Illuminate\Database\Schema\Blueprint $table){
            $table->engine = 'InnoDB';
            $table->integer('job_id')->nullable();
            $table->primary('job_id');
            //Not honestly sure what this is going ot be used for yet.
            //$table->timestamps();
        });

        $this->schema->create('tickets', function(Illuminate\Database\Schema\Blueprint $table){
            $table->engine = 'InnoDB';
            $table->bigInteger('ticket_id');
            $table->string('channel')->nullable();
            $table->string('recieved_from')->nullable();
            $table->bigInteger('submitter_id')->nullable();
            $table->bigInteger('requester_id')->nullable();
            $table->bigInteger('assignee_id')->nullable();
            $table->bigInteger('group_id')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->string('type')->nullable();
            $table->longText('subject')->nullable();
            $table->string("status")->nullable();
            $table->primary('ticket_id');
            //$table->foreign('user_id')->references('id')->on('users');
            //$table->timestamps();
        });

        $this->schema->create('ticket_events', function(Illuminate\Database\Schema\Blueprint $table){
            $table->engine = 'InnoDB';
            $table->bigInteger('event_id');
            $table->bigInteger('ticket_id')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->string('channel')->nullable();
            $table->string('source_ip')->nullable();
            $table->primary('event_id');
            //$table->timestamps();
        });

        $this->schema->create('ticket_list', function(Illuminate\Database\Schema\Blueprint $table){
            $table->engine = 'InnoDB';
            $table->bigInteger('id');
            $table->primary('id');
            //$table->timestamps();
        });

        $this->schema->create('users', function(Illuminate\Database\Schema\Blueprint $table){
                $table->engine = 'InnoDB';
                $table->bigInteger('user_id');
                $table->string('name')->nullable();
                $table->string('alias')->nullable();
                $table->string('email')->nullable();
                $table->string('role')->nullable();
                $table->text('signature')->nullable();
                $table->boolean('suspended')->nullable();
                $table->primary('user_id');
        });
    }
    public function down()
    {
        $this->schema->drop('tickets');
        $this->schema->drop('agents');
    }
}
