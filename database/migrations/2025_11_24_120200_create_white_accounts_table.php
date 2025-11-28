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
        Schema::create('white_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained('white_account_batches')->cascadeOnDelete();
            $table->string('account_name');
            $table->string('password');
            $table->string('login_method')->default('Garena');
            $table->unsignedBigInteger('unit_price')->nullable();
            $table->enum('status', ['available', 'reserved', 'sold'])->default('available');
            $table->foreignId('buyer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('purchase_id')->nullable()->constrained('white_account_purchases')->nullOnDelete();
            $table->timestamp('sold_at')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('white_accounts');
    }
};





