<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGeoCitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('geo_cities', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->string('name');
            $table->string('ascii_name', 200)->nullable();
            $table->string('alternate_names', 10000)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->char('feature_class', 1)->nullable();
            $table->string('feature_code', 10)->nullable();
            $table->char('country_code', 2)->nullable();
            $table->char('cc2', 200)->nullable();
            $table->string('admin1_code', 20)->nullable();
            $table->string('admin2_code', 80)->nullable();
            $table->string('admin3_code', 20)->nullable();
            $table->string('admin4_code', 20)->nullable();
            $table->bigInteger('population')->nullable();
            $table->integer('elevation')->nullable();
            $table->integer('gtopo30')->nullable();
            $table->string('timezone', 100)->nullable();
            $table->date('modification_date')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('geo_cities');
    }
}
