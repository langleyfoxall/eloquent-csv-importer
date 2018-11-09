<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCsvDefinitionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('csv_definitions', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name');
            $table->string('description')->nullable();

            $table->json('mappings');

            $table->timestamps();
            $table->softDeletes();

            $table->string('mappable_type');

            $table->unsignedInteger('assigned_id')->nullable();
            $table->string('assigned_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('csv_definitions');
    }
}
