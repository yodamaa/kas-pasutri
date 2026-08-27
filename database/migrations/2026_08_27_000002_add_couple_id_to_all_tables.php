<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // users
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('couple_id')->nullable()->after('is_active')->constrained('couples')->nullOnDelete();
        });

        // categories
        Schema::table('categories', function (Blueprint $table) {
            $table->foreignId('couple_id')->nullable()->after('is_active')->constrained('couples')->nullOnDelete();
        });

        // payment_methods
        Schema::table('payment_methods', function (Blueprint $table) {
            $table->foreignId('couple_id')->nullable()->after('is_active')->constrained('couples')->nullOnDelete();
        });

        // transactions
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('couple_id')->nullable()->after('user_id')->constrained('couples')->nullOnDelete();
        });

        // budgets
        Schema::table('budgets', function (Blueprint $table) {
            $table->foreignId('couple_id')->nullable()->after('tahun')->constrained('couples')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', fn (Blueprint $t) => $t->dropForeign(['couple_id'])->dropColumn('couple_id'));
        Schema::table('categories', fn (Blueprint $t) => $t->dropForeign(['couple_id'])->dropColumn('couple_id'));
        Schema::table('payment_methods', fn (Blueprint $t) => $t->dropForeign(['couple_id'])->dropColumn('couple_id'));
        Schema::table('transactions', fn (Blueprint $t) => $t->dropForeign(['couple_id'])->dropColumn('couple_id'));
        Schema::table('budgets', fn (Blueprint $t) => $t->dropForeign(['couple_id'])->dropColumn('couple_id'));
    }
};
