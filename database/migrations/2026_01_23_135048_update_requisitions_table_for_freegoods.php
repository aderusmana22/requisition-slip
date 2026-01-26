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
        Schema::table('requisitions', function (Blueprint $table) {
            // 1. Ubah customer_id agar boleh kosong (NULL)
            // Pastikan kamu punya package doctrine/dbal jika pakai Laravel versi lama. 
            // Jika error, hapus baris ini dan lakukan manual di database, tapi biasanya di Laravel modern ini aman.
            $table->unsignedBigInteger('customer_id')->nullable()->change();

            // 2. Tambahkan kolom Recipient Name (Max 30 chars)
            $table->string('recipient_name', 30)->nullable()->after('requester_nik');

            // 3. Tambahkan kolom Address (Text/Optional)
            $table->text('recipient_address')->nullable()->after('recipient_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('requisitions', function (Blueprint $table) {
            // Kembalikan ke settingan awal (opsional, untuk rollback)
            $table->dropColumn(['recipient_name', 'recipient_address']);
            
            // Hati-hati mengembalikan customer_id ke nullable(false) jika datanya sudah ada yang NULL
            // $table->unsignedBigInteger('customer_id')->nullable(false)->change(); 
        });
    }
};