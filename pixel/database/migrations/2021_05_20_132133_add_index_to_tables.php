<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexToTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->index('session_id');
            $table->index('user_id');
            $table->index('domain_id');
        });

        Schema::table('sessions', function (Blueprint $table) {
            // Default indexes
            $table->index('user_id');
            $table->index('domain_id');
            // Composite indexes
            $table->index(['user_id', 'domain_id']);
        });

        Schema::table('user_domain', function (Blueprint $table) {
            // Default indexes
            $table->index('user_id');
            $table->index('domain_id');
            $table->index('created_at');
            // Composite indexes
            $table->index(['user_id', 'domain_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropIndex('session_id');
            $table->dropIndex('user_id');
            $table->dropIndex('domain_id');
        });

        Schema::table('sessions', function (Blueprint $table) {
            // Default indexes
            $table->dropIndex('user_id');
            $table->dropIndex('domain_id');
            // Composite indexes
            $table->dropIndex(['user_id', 'domain_id']);
        });

        Schema::table('user_domain', function (Blueprint $table) {
            // Default indexes
            $table->dropIndex('user_id');
            $table->dropIndex('domain_id');
            $table->dropIndex('created_at');
            // Composite indexes
            $table->dropIndex(['user_id', 'domain_id', 'created_at']);
        });
    }
}
