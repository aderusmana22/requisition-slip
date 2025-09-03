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
            $table->unsignedBigInteger('item_master_id');
            $table->string('material_type');
            $table->string('item_detail_code')->unique();
            $table->string('item_detail_name');
            $table->string('unit');
            $table->decimal('net_weight', 10, 2);
            $table->timestamps();

            $table->foreign('item_master_id')->references('id')->on('item_masters')->onDelete('cascade');
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
