<?php

// database/migrations/2025_10_04_000002_add_destination_to_addresses.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::table('addresses', function (Blueprint $t) {
      if (!Schema::hasColumn('addresses','destination_id')) {
        $t->unsignedBigInteger('destination_id')->nullable()->after('postal_code');
      }
      if (!Schema::hasColumn('addresses','destination_label')) {
        $t->string('destination_label')->nullable()->after('destination_id');
      }
    });
  }
  public function down(): void {
    Schema::table('addresses', function (Blueprint $t) {
      $t->dropColumn(['destination_id','destination_label']);
    });
  }
};
