<?php

// database/migrations/2025_09_18_000000_create_addresses_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('addresses', function (Blueprint $table) {
      $table->id();
      $table->foreignId('user_id')->constrained()->cascadeOnDelete();

      // Info penerima
      $table->string('label')->nullable();                 // "Rumah", "Kantor", dll (opsional)
      $table->string('recipient_name');                    // default: user->name, boleh edit
      $table->string('phone', 30)->nullable();

      // Alamat detail (sederhana; bisa dipisah jika pakai API ongkir)
      $table->string('address_line');                      // Jalan/RT/RW/dll
      $table->string('province')->nullable();
      $table->string('city')->nullable();
      $table->string('district')->nullable();              // kecamatan
      $table->string('village')->nullable();               // kelurahan/desa
      $table->string('postal_code', 10)->nullable();

      // Opsional koordinat (kalau butuh)
      $table->decimal('lat', 10, 7)->nullable();
      $table->decimal('lng', 10, 7)->nullable();

      $table->boolean('is_default')->default(false);

      $table->timestamps();
    });

    // Tambahan kolom phone di users (kalau belum ada)
    Schema::table('users', function (Blueprint $table) {
      if (!Schema::hasColumn('users', 'phone')) {
        $table->string('phone', 30)->nullable()->after('email');
      }
    });
  }

  public function down(): void {
    Schema::dropIfExists('addresses');
    Schema::table('users', function (Blueprint $table) {
      if (Schema::hasColumn('users', 'phone')) {
        $table->dropColumn('phone');
      }
    });
  }
};
