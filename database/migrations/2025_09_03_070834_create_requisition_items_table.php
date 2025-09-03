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
        Schema::create('requisition_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requisition_id')->constrained('requisitions')->onDelete('cascade');
            $table->foreignId('item_master_id')->constrained('item_master')->onDelete('cascade');
            $table->integer('quantity');
            $table->integer('quantity_issued')->nullable();
            $table->string('batch_number')->nullable();
            $table->text('reason_for_replacement')->nullable();
            $table->text('objectives')->nullable();
            $table->string('estimated_potential')->nullable();
            $table->string('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requisition_items');
    }
};
