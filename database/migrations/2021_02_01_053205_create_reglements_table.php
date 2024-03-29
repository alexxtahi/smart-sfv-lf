<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReglementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reglements', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('montant_reglement');
            $table->bigInteger('reste_a_payer')->unsigned()->nullable();
            $table->integer('moyen_reglement_id');
            $table->date('date_reglement');
            $table->integer('caisse_ouverte_id')->unsigned()->nullable();
            $table->integer('commande_id')->unsigned()->nullable();
            $table->integer('vente_id')->unsigned()->nullable();
            $table->string('scan_cheque')->nullable();
            $table->string('numero_cheque_virement')->nullable();
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
        Schema::dropIfExists('reglements');
    }
}
