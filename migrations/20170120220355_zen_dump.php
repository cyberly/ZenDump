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
            $table->bigInteger('comment_id')->nullable();
            $table->longText('file_name')->nullable();
            $table->longText('url')->nullable();
            $table->longText('content_url')->nullable();
            $table->string('content_type')->nullable();
            $table->primary('attachment_id');
            //May need to add a column for groups, but will need a separate API call.
            //$table->timestamps();
        });

        $this->schema->create('comments', function(Illuminate\Database\Schema\Blueprint $table){
            $table->engine = 'InnoDB';
            $table->bigInteger('comment_id');
            $table->bigInteger('ticket_id')->nullable();
            $table->longText('body')->nullable();
            $table->boolean('public')->nullable();
            $table->bigInteger('author_id')->nullable();
            $table->boolean('has_attachments')->nullable();
            $table->dateTime('created_at');
            $table->primary('comment_id');
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
            $table->string('tags')->nullable();
            $table->string('type')->nullable();
            $table->longText('subject')->nullable();
            $table->string('priority')->nullable();
            $table->string('status')->nullable();
            $table->string('account')->nullable();
            $table->longText('description')->nullable();
            $table->string('satisfaction_score')->nullable();
            $table->longText('satisfaction_comment')->nullable();
            $table->boolean('is_public')->nullable();
            $table->primary('ticket_id');
            //$table->foreign('user_id')->references('id')->on('users');
            //$table->timestamps();
        });

        $this->schema->create('ticket_list', function(Illuminate\Database\Schema\Blueprint $table){
            $table->engine = 'InnoDB';
            $table->bigInteger('id');
            $table->primary('id');
            //$table->timestamps();
        });

        $this->schema->create('triggers', function(Illuminate\Database\Schema\Blueprint $table){
            $table->engine = 'InnoDB';
            $table->bigInteger('trigger_id');
            $table->string('title')->nullable();
            $table->boolean('active')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->bigInteger('position')->nullable();
            $table->primary('trigger_id');
            //$table->timestamps();
        });

        $this->schema->create('trigger_actions', function(Illuminate\Database\Schema\Blueprint $table){
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->bigInteger('trigger_id');
            $table->string('field')->nullable();
            $table->string('recipient')->nullable();
            $table->string('subject')->nullable();
            $table->longText('value')->nullable();
        });

        $this->schema->create('trigger_conditions', function(Illuminate\Database\Schema\Blueprint $table){
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->bigInteger('trigger_id');
            $table->string('type')->nullable();
            $table->string('field')->nullable();
            $table->string('operator')->nullable();
            $table->string('value')->nullable();
        });



        $this->schema->create('users', function(Illuminate\Database\Schema\Blueprint $table){
                $table->engine = 'InnoDB';
                $table->bigInteger('user_id');
                $table->string('name')->nullable();
                $table->string('alias')->nullable();
                $table->string('email')->nullable();
                $table->string('role')->nullable();
                $table->longText('signature')->nullable();
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
