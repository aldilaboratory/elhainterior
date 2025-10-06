<?php

// database/migrations/2025_10_04_000001_fix_product_weight.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::table('products', function (Blueprint $t) {
      if (!Schema::hasColumn('products','weight_gram')) {
        $t->unsignedInteger('weight_gram')->default(0)->after('stock');
      }
      if (Schema::hasColumn('products','weight')) {
        $t->dropColumn('weight'); // kolom lama float
      }
    });
  }
  public function down(): void {
    Schema::table('products', function (Blueprint $t) {
      $t->float('weight')->nullable()->after('stock');
      $t->dropColumn('weight_gram');
    });
  }
};
