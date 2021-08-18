<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRebisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rebis', function (Blueprint $table) {
            $table->id();
            $table->date('date_operation');
            $table->string('concerne');
            $table->integer('demande_approvi_canal_id')->unsigned()->nullable();
            $table->integer('caution_agence_id')->unsigned()->nullable();
            $table->integer('abonnement_id')->unsigned()->nullable();
            $table->integer('reabonnement_id')->unsigned()->nullable();
            $table->integer('vente_materiel_id')->unsigned()->nullable();
            $table->bigInteger('montant_recharge')->unsigned()->default(0);
            $table->bigInteger('montant_recharge_agence')->unsigned()->default(0);
            $table->bigInteger('montant_recharge_client')->unsigned()->default(0);
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
        Schema::dropIfExists('rebis');
    }
}
