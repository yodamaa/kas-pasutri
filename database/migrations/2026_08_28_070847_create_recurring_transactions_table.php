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
        Schema::create('recurring_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('couple_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('peruntukan_id')->constrained('categories')->cascadeOnDelete();
            $table->foreignId('metode_pembayaran_id')->constrained('payment_methods')->cascadeOnDelete();
            $table->enum('tipe', ['pemasukan', 'pengeluaran']);
            $table->decimal('jumlah', 15, 2);
            $table->string('deskripsi')->nullable();
            $table->enum('frequency', ['daily', 'weekly', 'monthly', 'yearly'])->default('monthly');
            $table->tinyInteger('day_of_week')->nullable();
            $table->tinyInteger('day_of_month')->nullable();
            $table->tinyInteger('month')->nullable();
            $table->date('starts_at');
            $table->date('ends_at')->nullable();
            $table->date('last_generated_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'starts_at', 'ends_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recurring_transactions');
    }
};
