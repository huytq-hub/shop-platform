<?php
/**
 * Copyright (c) 2025 FPT University
 *
 * @author    Phạm Hoàng Tuấn
 * @email     phamhoangtuanqn@gmail.com
 * @facebook  fb.com/phamhoangtuanqn
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('game_accounts', function (Blueprint $table) {
            $table->enum('account_version', ['vietnam', 'global'])
                ->default('vietnam')
                ->after('status');
            $table->string('login_method')
                ->nullable()
                ->after('account_version');
            $table->unsignedBigInteger('team_value')
                ->nullable()
                ->after('login_method');
        });

        Schema::table('game_accounts', function (Blueprint $table) {
            $table->dropColumn(['server', 'registration_type', 'planet', 'earring']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('game_accounts', function (Blueprint $table) {
            $table->integer('server')->after('status');
            $table->enum('registration_type', ['virtual', 'real'])->after('server');
            $table->enum('planet', ['earth', 'namek', 'xayda'])->after('registration_type');
            $table->boolean('earring')->default(false)->after('planet');
        });

        Schema::table('game_accounts', function (Blueprint $table) {
            $table->dropColumn(['account_version', 'login_method', 'team_value']);
        });
    }
};

