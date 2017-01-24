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

        $this->schema->create('meta', function(Illuminate\Database\Schema\Blueprint $table){
            $table->integer('id');
            //Not honestly sure what this is going ot be used for yet.
            //$table->timestamps();
        });

        $this->schema->create('tickets', function(Illuminate\Database\Schema\Blueprint $table){
            $table->integer('id');
            $table->integer('serial_number');
            $table->string('name');
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
