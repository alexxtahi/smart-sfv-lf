<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOptionCanalReabonnementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('option_canal_reabonnement', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('option_canal_id');
            $table->unsignedBigInteger('reabonnement_id');
            $table->foreign('option_canal_id')->references('id')->on('option_canals')->onDelete('cascade');
            $table->foreign('reabonnement_id')->references('id')->on('reabonnements')->onDelete('cascade');
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
        Schema::dropIfExists('option_canal_reabonnement');
    }
}
