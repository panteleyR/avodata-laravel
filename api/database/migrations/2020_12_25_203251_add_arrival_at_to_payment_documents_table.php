<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddArrivalAtToPaymentDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_documents', function (Blueprint $table) {
            $table->dateTime('arrival_at')->default('2020-01-01 23:59');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payment_documents', function (Blueprint $table) {
            $table->dropColumn('arrival_at');
        });
    }
}
