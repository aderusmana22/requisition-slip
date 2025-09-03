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
        Schema::create('requisitions', function (Blueprint $table) {
            $table->id();
            $table->string('requester_nik');
            $table->unsignedBigInteger('customer_id');
            $table->string('no_srs');
            $table->string('account');
            $table->string('cost_center');
            $table->date('request_date');
            $table->unsignedBigInteger('revision_id')->unique();
            $table->string('category');
            $table->string('sub_category')->nullable();
            $table->string('route_to');
            $table->string('status');
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('revision_id')->references('id')->on('revisions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requisitions');
    }
};
