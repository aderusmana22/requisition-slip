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
        Schema::create('item_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_master_id')->constrained()->onDelete('cascade');
            $table->string('material_type');
            $table->string('item_detail_code')->unique();
            $table->string('item_detail_name');
            $table->string('unit');
            $table->decimal('net_weight', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('item_details', function (Blueprint $table) {
            $table->dropForeign(['item_master_id']);
        });
        Schema::dropIfExists('item_details');
    }
};
