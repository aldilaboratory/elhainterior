<?php

// database/migrations/2025_10_04_000004_add_shipping_cols_to_orders.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::table('orders', function (Blueprint $t) {
      if (!Schema::hasColumn('orders','courier_code'))   $t->string('courier_code')->nullable()->after('shipping');
      if (!Schema::hasColumn('orders','courier_service'))$t->string('courier_service')->nullable()->after('courier_code');
      if (!Schema::hasColumn('orders','shipping_etd'))   $t->string('shipping_etd')->nullable()->after('courier_service');
      if (!Schema::hasColumn('orders','weight_total_gram')) $t->unsignedInteger('weight_total_gram')->default(0)->after('subtotal');
      if (!Schema::hasColumn('orders','destination_id')) $t->unsignedBigInteger('destination_id')->nullable()->after('postal_code');
      if (!Schema::hasColumn('orders','ship_to_region_label')) $t->string('ship_to_region_label')->nullable()->after('destination_id');
    });
  }
  public function down(): void {
    Schema::table('orders', function (Blueprint $t) {
      $t->dropColumn(['courier_code','courier_service','shipping_etd','weight_total_gram','destination_id','ship_to_region_label']);
    });
  }
};
