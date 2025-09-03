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
        Schema::create('requisition_specials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requisition_id')->constrained('requisitions')->onDelete('cascade');
            $table->date('requested_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('products')->nullable();
            $table->string('weight_selection')->nullable();
            $table->string('packaging_selection')->nullable();
            $table->integer('sample_count')->nullable();
            $table->text('purpose')->nullable();
            $table->boolean('coa_required')->default(false);
            $table->string('shipment_method')->nullable();
            $table->string('source')->nullable();
            $table->text('sample_notes')->nullable();
            $table->date('production_date')->nullable();
            $table->text('preparation_method')->nullable();
            $table->string('description')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requisition_specials');
    }
};
