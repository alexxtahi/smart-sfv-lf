<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReabonnementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reabonnements', function (Blueprint $table) {
            $table->id();
            $table->string('abonnement_id');
            $table->date('date_reabonnement');
            $table->string('duree');
            $table->date('date_debut');
            $table->bigInteger('montant_reabonnement')->unsigned()->nullable();
            $table->integer('type_abonnement_id')->unsigned();
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
        Schema::dropIfExists('reabonnements');
    }
}
