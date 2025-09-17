<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('orders', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->unsignedBigInteger('subtotal')->default(0);
            $t->string('shipping_courier')->nullable();
            $t->string('shipping_service')->nullable();
            $t->unsignedBigInteger('shipping_cost')->default(0);
            $t->unsignedBigInteger('total')->default(0);

            // alamat
            $t->string('receiver_name');
            $t->string('phone');
            $t->unsignedInteger('province_id');
            $t->string('province');
            $t->unsignedInteger('city_id');
            $t->string('city');
            $t->string('postal_code')->nullable();
            $t->text('address');

            $t->string('status')->default('pending'); // pending|paid|shipped|completed|canceled
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('orders'); }
};
