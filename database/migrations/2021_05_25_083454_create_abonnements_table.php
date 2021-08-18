<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbonnementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('abonnements', function (Blueprint $table) {
            $table->id();
            $table->string('numero_abonnement');
            $table->string('numero_decodeur');
            $table->string('adresse_decodeur');
            $table->date('date_abonnement');
            $table->string('duree');
            $table->date('date_debut');
            $table->bigInteger('payement_abonnement')->unsigned()->nullable();
            $table->bigInteger('payement_equipement')->unsigned()->nullable();
            $table->integer('type_abonnement_id')->unsigned();
            $table->integer('abonne_id')->unsigned();
            $table->integer('agence_id')->unsigned();
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
        Schema::dropIfExists('abonnements');
    }
}
