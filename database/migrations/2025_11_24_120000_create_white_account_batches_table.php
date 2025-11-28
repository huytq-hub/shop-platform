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
        Schema::create('white_account_batches', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('account_type', ['white', 'reroll_white'])->default('white');
            $table->unsignedBigInteger('default_price');
            $table->string('login_method')->default('Garena');
            $table->unsignedInteger('stock_warning_threshold')->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('white_account_batches');
    }
};





