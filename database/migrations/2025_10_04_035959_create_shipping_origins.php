<?php

// database/migrations/2025_10_04_000003_create_shipping_origins.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('shipping_origins', function (Blueprint $t) {
      $t->id();
      $t->unsignedBigInteger('origin_id')->nullable(); // ID lokasi Komerce
      $t->string('label')->nullable();                 // mis. Gudang Denpasar
      $t->string('address_line')->nullable();
      $t->string('postal_code')->nullable();
      $t->boolean('is_active')->default(true);
      $t->timestamps();
    });
  }
  public function down(): void { Schema::dropIfExists('shipping_origins'); }
};

