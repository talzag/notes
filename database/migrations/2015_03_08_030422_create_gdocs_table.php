<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGdocsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('gdocs', function($table) {
            $table->increments('id');
            $table->string('link');
            $table->string('gdocs_id');
            $table->text('note');
            $table->timestamps();
            $table->integer('note_id')->unsigned();
            $table->foreign('note_id')->references('id')->on('notes');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('gdocs');
	}

}
