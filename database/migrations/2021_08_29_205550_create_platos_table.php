<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlatosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //Schema::create('platos', function (Blueprint $table) {
        //    $table->Increments('id');
        //    $table->Integer('user_id');
        //    $table->foreign('user_id')->references('id')->on('users');
        //    $table->string('name');
        //    $table->integer('price');
        //    $table->string('description');
        //    $table->string('img');
        //    $table->string('latitude');
        //    $table->string('longitude');
        //    $table->timestamps();
        //});
        // Agregar rating, ratingTotal, quantityVoting sin que se elimine la tabla y sus datos solo agregar los campos mencionados.
        Schema::table('platos', function (Blueprint $table){
            $table->boolean('is_favorite')->default(0);
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    //public function down()
    //{
    //    Schema::dropIfExists('platos');
    //}
}
