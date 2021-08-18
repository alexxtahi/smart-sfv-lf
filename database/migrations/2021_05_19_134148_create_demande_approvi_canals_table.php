<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDemandeApproviCanalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('demande_approvi_canals', function (Blueprint $table) {
            $table->id();
            $table->string('numero_demande');
            $table->string('deposant');
            $table->string('reference_versement')->nullable();
            $table->date('date_demande');
            $table->bigInteger('montant_depose')->unsigned();
            $table->integer('type_caution_id')->unsigned();
            $table->string('recu_versement')->nullable();
            $table->boolean('approvisionne')->default(0);
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
        Schema::dropIfExists('demande_approvi_canals');
    }
}
