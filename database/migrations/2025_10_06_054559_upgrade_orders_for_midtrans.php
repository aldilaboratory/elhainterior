<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /** Cek apakah index/unique sudah ada */
    private function hasIndex(string $table, string $index): bool
    {
        $rows = DB::select("SHOW INDEX FROM `{$table}`");
        foreach ($rows as $r) {
            if (isset($r->Key_name) && $r->Key_name === $index) return true;
        }
        return false;
    }

    public function up(): void
    {
        // 1) UNIQUE order_code (tambah hanya jika belum ada)
        if (!$this->hasIndex('orders', 'orders_order_code_unique')) {
            Schema::table('orders', function (Blueprint $t) {
                $t->unique('order_code'); // nama default: orders_order_code_unique
            });
        }

        // 2) UNIQUE midtrans_transaction_id (juga cek dulu)
        if (!$this->hasIndex('orders', 'orders_midtrans_transaction_id_unique')) {
            Schema::table('orders', function (Blueprint $t) {
                $t->unique('midtrans_transaction_id');
            });
        }

        // 3) INDEX user_id (cek dulu)
        if (!$this->hasIndex('orders', 'orders_user_id_index')) {
            Schema::table('orders', function (Blueprint $t) {
                $t->index('user_id');
            });
        }

        // ===== kolom-kolom tambahan (tambahkan hanya jika belum ada) =====
        Schema::table('orders', function (Blueprint $t) {
            if (!Schema::hasColumn('orders', 'midtrans_status')) {
                $t->string('midtrans_status', 32)->nullable()->after('payment_status');
            }
            if (!Schema::hasColumn('orders', 'fraud_status')) {
                $t->string('fraud_status', 16)->nullable()->after('midtrans_status');
            }
            if (!Schema::hasColumn('orders', 'snap_token')) {
                $t->string('snap_token', 64)->nullable()->after('order_code');
            }
            if (!Schema::hasColumn('orders', 'snap_redirect_url')) {
                $t->string('snap_redirect_url', 255)->nullable()->after('snap_token');
            }
            if (!Schema::hasColumn('orders', 'snap_token_expired_at')) {
                $t->dateTime('snap_token_expired_at')->nullable()->after('snap_redirect_url');
            }
            if (!Schema::hasColumn('orders', 'paid_at')) {
                $t->dateTime('paid_at')->nullable()->after('payment_status');
            }
            if (!Schema::hasColumn('orders', 'cancelled_at')) {
                $t->dateTime('cancelled_at')->nullable()->after('paid_at');
            }
            // Opsional:
            if (!Schema::hasColumn('orders', 'va_number')) {
                $t->string('va_number', 30)->nullable()->after('midtrans_payment_type');
            }
            if (!Schema::hasColumn('orders', 'va_bank')) {
                $t->string('va_bank', 15)->nullable()->after('va_number');
            }
            if (!Schema::hasColumn('orders', 'payment_code')) {
                $t->string('payment_code', 50)->nullable()->after('va_bank');
            }
        });
    }

    public function down(): void
    {
        // Turunkan hanya bila ada
        if ($this->hasIndex('orders', 'orders_order_code_unique')) {
            Schema::table('orders', fn (Blueprint $t) => $t->dropUnique('orders_order_code_unique'));
        }
        if ($this->hasIndex('orders', 'orders_midtrans_transaction_id_unique')) {
            Schema::table('orders', fn (Blueprint $t) => $t->dropUnique('orders_midtrans_transaction_id_unique'));
        }
        if ($this->hasIndex('orders', 'orders_user_id_index')) {
            Schema::table('orders', fn (Blueprint $t) => $t->dropIndex('orders_user_id_index'));
        }

        Schema::table('orders', function (Blueprint $t) {
            foreach ([
                'midtrans_status','fraud_status',
                'snap_token','snap_redirect_url','snap_token_expired_at',
                'paid_at','cancelled_at',
                'va_number','va_bank','payment_code',
            ] as $col) {
                if (Schema::hasColumn('orders', $col)) $t->dropColumn($col);
            }
        });
    }
};