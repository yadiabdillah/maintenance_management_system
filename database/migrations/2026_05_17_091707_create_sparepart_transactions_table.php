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
        Schema::create('sparepart_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sparepart_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['IN', 'OUT']);
            $table->integer('qty');
            $table->string('supplier_name')->nullable(); // Only for IN transactions
            $table->decimal('unit_price', 15, 2)->nullable(); // Only for IN transactions
            $table->text('remarks')->nullable();
            $table->string('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sparepart_transactions');
    }
};
