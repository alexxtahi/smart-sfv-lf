<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbonnesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('abonnes', function (Blueprint $table) {
            $table->id();
            $table->string('full_name_abonne');
            $table->string('civilite');
           $table->date('date_naissance_abonne');
            $table->string('adresse_abonne');
            $table->string('contact1');
            $table->integer('localite_id')->unsigned();
            $table->integer('nation_id')->unsigned()->nullable();
            $table->string('numero_piece')->nullable();
            $table->string('code_postal')->nullable();
            $table->string('email_abonne')->nullable();
            $table->string('contact2')->nullable();
            $table->string('contact_conjoint')->nullable();
            $table->integer('type_piece_id')->unsigned()->nullable();
            $table->dateTime('deleted_at')->nullable();
            $table->integer('deleted_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->integer('created_by')->unsigned()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('abonnes');
    }
}
