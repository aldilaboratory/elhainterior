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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // snapshot alamat & kontak
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('email');
            $table->string('phone');
            $table->string('address1');
            $table->string('address2')->nullable();
            $table->string('postal_code');

            // ringkasan biaya
            $table->unsignedBigInteger('subtotal');    // total barang
            $table->unsignedBigInteger('shipping');    // ongkir
            $table->string('shipping_courier')->nullable();
            $table->string('shipping_service')->nullable();
            $table->unsignedBigInteger('total');       // subtotal + shipping

            // status pembayaran & pesanan
            $table->enum('payment_status', ['pending','paid','failed','refunded'])->default('pending');
            $table->enum('order_status', ['unconfirmed','packing','shipped','completed','cancelled','rejected'])->default('unconfirmed');

            // kolom Midtrans (nanti dipakai)
            $table->string('order_code')->unique();     // mis: INV-2025-0001
            $table->string('midtrans_order_id')->nullable();
            $table->string('midtrans_transaction_id')->nullable();
            $table->string('midtrans_payment_type')->nullable(); // gopay, bca_va, dsb
            $table->json('midtrans_raw')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
