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
            // Kolom yang HILANG dari tabel Anda (WAJIB ditambahkan)
            if (!Schema::hasColumn('orders', 'midtrans_redirect_url')) {
                $table->string('midtrans_redirect_url')->nullable()->after('snap_redirect_url');
            }
            
            // Ubah kolom order_status menjadi enum yang lebih lengkap
            // Karena sudah ada, kita lewati saja atau gunakan raw SQL jika perlu update
            
            // Pastikan index untuk query cepat
            if (!Schema::hasColumn('orders', 'user_id')) {
                // user_id sudah ada di tabel Anda (kolom #2)
            }
            
            // Tambahkan index untuk performa
            $table->index('order_code');
            $table->index('payment_status');
            $table->index('order_status');
            $table->index('midtrans_order_id');
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('midtrans_redirect_url');
            
            // Drop indexes
            $table->dropIndex(['order_code']);
            $table->dropIndex(['payment_status']);
            $table->dropIndex(['order_status']);
            $table->dropIndex(['midtrans_order_id']);
            $table->dropIndex(['user_id', 'created_at']);
        });
    }
};