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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->enum('tipe', ['pemasukan', 'pengeluaran']);
            $table->decimal('jumlah', 15, 2);
            $table->date('tanggal');
            $table->string('deskripsi')->nullable();
            $table->foreignId('peruntukan_id')->constrained('categories')->cascadeOnDelete();
            $table->foreignId('metode_pembayaran_id')->constrained('payment_methods')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('lampiran')->nullable();
            $table->boolean('is_recurring')->default(false);
            $table->string('recurring_interval')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
