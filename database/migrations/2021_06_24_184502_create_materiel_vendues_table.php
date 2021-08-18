<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaterielVenduesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('materiel_vendues', function (Blueprint $table) {
            $table->id();
            $table->integer('materiel_id');
            $table->bigInteger('prix');
            $table->integer('quantite');
            $table->integer('vente_materiel_id');
            $table->boolean('retourne')->default(0);
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
        Schema::dropIfExists('materiel_vendues');
    }
}
