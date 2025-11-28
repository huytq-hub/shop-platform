<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('white_account_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('batch_id')->nullable()->constrained('white_account_batches')->nullOnDelete();
            $table->enum('account_type', ['white', 'reroll_white']);
            $table->unsignedInteger('quantity');
            $table->unsignedBigInteger('total_amount');
            $table->json('delivery_payload');
            $table->enum('status', ['completed', 'refunded'])->default('completed');
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('white_account_purchases');
    }
};





