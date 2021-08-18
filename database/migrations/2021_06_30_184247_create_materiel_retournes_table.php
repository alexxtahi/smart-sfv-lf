<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaterielRetournesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('materiel_retournes', function (Blueprint $table) {
            $table->id();
            $table->integer('retour_article_id');
            $table->integer('materiel_id');
            $table->integer('quantite_vendue')->default(0);
            $table->integer('quantite');
            $table->integer('prix_unitaire');
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
        Schema::dropIfExists('materiel_retournes');
    }
}
