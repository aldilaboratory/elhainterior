<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('postal_code')->nullable()->change();
        });
        Schema::table('addresses', function (Blueprint $table) {
            if (Schema::hasColumn('addresses', 'postal_code')) {
                $table->string('postal_code')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('postal_code')->nullable(false)->change();
        });
        Schema::table('addresses', function (Blueprint $table) {
            if (Schema::hasColumn('addresses', 'postal_code')) {
                $table->string('postal_code')->nullable(false)->change();
            }
        });
    }
};
