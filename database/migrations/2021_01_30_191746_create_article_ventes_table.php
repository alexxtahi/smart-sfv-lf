<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticleVentesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_ventes', function (Blueprint $table) {
            $table->id();
            $table->string('quantite');
            $table->integer('divers_id')->unsigned()->nullable();
            $table->integer('article_id')->unsigned()->nullable();
            $table->integer('depot_id')->unsigned()->nullable();
            $table->integer('unite_id')->unsigned()->nullable();
            $table->integer('vente_id');
            $table->integer('prix');
            $table->boolean('retourne')->default(0);
            $table->integer('remise_sur_ligne')->unsigned()->default(0);
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
        Schema::dropIfExists('article_ventes');
    }
}
