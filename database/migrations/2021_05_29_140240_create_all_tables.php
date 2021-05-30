<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAllTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');  
        });

        Schema::create('titles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->integer('id_owner')->references('id')->on('users');
        });

        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->integer('title_id')->references('id')->on('titles');
            $table->integer('id_owner')->references('id')->on('users');
            $table->integer('id_lessee')->references('id')->on('users');
            $table->date('start_date');
            $table->date('end_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('titles');
        Schema::dropIfExists('reservations');
    }
}
